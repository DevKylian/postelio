#!/bin/bash
# =============================================================================
# Postelio — Industrial Umami Analytics Deployer (Docker v3)
# =============================================================================
# Ce script installe Umami v3 via Docker, configure MySQL sur l'hôte,
# gère le SSL et assure la persistance des accès.
# Compatible Ubuntu 22.04+
# =============================================================================

set -euo pipefail

# --- Configuration -----------------------------------------------------------
CREDS_FILE="/root/umami-credentials.txt"
UMAMI_DOCKER_DIR="/opt/umami-docker"
UMAMI_IMAGE="ghcr.io/umami-software/umami:mysql-v3-latest"

# --- Couleurs ----------------------------------------------------------------
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; BOLD='\033[1m'; NC='\033[0m'

log()  { echo -e "${GREEN}[✓]${NC} $1"; }
info() { echo -e "${BLUE}[→]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
fail() { echo -e "${RED}[✗] ERREUR : $1${NC}" >&2; exit 1; }
hr()   { echo -e "${BOLD}────────────────────────────────────────────${NC}"; }
step() { echo ""; hr; info "$1"; hr; }

[[ $EUID -ne 0 ]] && fail "Lancer en root : sudo bash setup-umami-docker.sh"

# --- 1. Arguments & Credentials ----------------------------------------------
step "1/7 — Préparation et Identifiants"

if [[ -f "$CREDS_FILE" ]]; then
    warn "Identifiants existants trouvés. Chargement..."
    UMAMI_DOMAIN=$(grep "URL" "$CREDS_FILE" | awk '{print $3}' | sed 's/https:\/\///')
    UMAMI_DB_NAME=$(grep "MySQL DB" "$CREDS_FILE" | awk '{print $4}')
    UMAMI_DB_USER=$(grep "MySQL user" "$CREDS_FILE" | awk '{print $4}')
    UMAMI_DB_PASSWORD=$(grep "MySQL password" "$CREDS_FILE" | awk '{print $4}')
    UMAMI_APP_SECRET=$(grep "App secret" "$CREDS_FILE" | awk '{print $4}')
else
    info "Nouveau déploiement détecté. Génération des accès..."
    read -p "Entrez le domaine (ex: analytics.domaine.com) : " UMAMI_DOMAIN
    read -p "Entrez votre email (pour SSL) : " LE_EMAIL
    
    UMAMI_DB_NAME="umami"
    UMAMI_DB_USER="umami_user"
    UMAMI_DB_PASSWORD=$(openssl rand -base64 16 | tr -dc 'a-zA-Z0-9' | head -c 16)
    UMAMI_APP_SECRET=$(openssl rand -base64 32)

    # Sauvegarde
    {
        printf 'URL              : https://%s\n' "$UMAMI_DOMAIN"
        printf 'MySQL DB         : %s\n' "$UMAMI_DB_NAME"
        printf 'MySQL user       : %s\n' "$UMAMI_DB_USER"
        printf 'MySQL password   : %s\n' "$UMAMI_DB_PASSWORD"
        printf 'App secret       : %s\n' "$UMAMI_APP_SECRET"
        [[ -n "$LE_EMAIL" ]] && printf 'LE Email         : %s\n' "$LE_EMAIL"
    } > "$CREDS_FILE"
    chmod 600 "$CREDS_FILE"
fi

log "Domaine : $UMAMI_DOMAIN"

# --- 2. Installation Dépendances ---------------------------------------------
step "2/7 — Installation Docker & MySQL"
apt-get update -qq
apt-get install -y -qq curl gnupg mysql-server certbot python3-certbot-nginx ufw >/dev/null

if ! command -v docker &>/dev/null; then
    curl -fsSL https://get.docker.com | sh >/dev/null
    systemctl enable --now docker
fi
log "Docker et MySQL installés."

# --- 3. Configuration Pare-feu -----------------------------------------------
step "3/7 — Sécurité (UFW)"
ufw allow OpenSSH >/dev/null
ufw allow 'Nginx Full' >/dev/null
echo "y" | ufw enable >/dev/null
log "Pare-feu configuré (SSH, HTTP, HTTPS)."

# --- 4. Configuration MySQL --------------------------------------------------
step "4/7 — Base de données"
# Patch bind-address
if [ -f /etc/mysql/mysql.conf.d/mysqld.cnf ]; then
    sed -i "s/bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/mysql.conf.d/mysqld.cnf
    systemctl restart mysql
fi

# Création DB et User (Local + Remote pour Docker)
mysql -u root << MYSQL_SCRIPT
CREATE DATABASE IF NOT EXISTS \`${UMAMI_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${UMAMI_DB_USER}'@'localhost' IDENTIFIED BY '${UMAMI_DB_PASSWORD}';
CREATE USER IF NOT EXISTS '${UMAMI_DB_USER}'@'%' IDENTIFIED BY '${UMAMI_DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${UMAMI_DB_NAME}\`.* TO '${UMAMI_DB_USER}'@'localhost';
GRANT ALL PRIVILEGES ON \`${UMAMI_DB_NAME}\`.* TO '${UMAMI_DB_USER}'@'%';
FLUSH PRIVILEGES;
MYSQL_SCRIPT
log "MySQL prêt (accès bridge Docker ouvert)."

# --- 5. Docker Compose -------------------------------------------------------
step "5/7 — Lancement Umami (Docker v3)"
mkdir -p "$UMAMI_DOCKER_DIR"
cat > "$UMAMI_DOCKER_DIR/docker-compose.yml" << EOF
services:
  umami:
    image: $UMAMI_IMAGE
    restart: always
    environment:
      DATABASE_URL: mysql://$UMAMI_DB_USER:$UMAMI_DB_PASSWORD@host.docker.internal:3306/$UMAMI_DB_NAME
      APP_SECRET: $UMAMI_APP_SECRET
      DATABASE_TYPE: mysql
    ports:
      - "127.0.0.1:3000:3000"
    extra_hosts:
      - "host.docker.internal:host-gateway"
EOF

cd "$UMAMI_DOCKER_DIR"
docker compose up -d --remove-orphans
log "Conteneur Umami v3 lancé."

# --- 6. Nginx ----------------------------------------------------------------
step "6/7 — Configuration Nginx"
cat > "/etc/nginx/sites-available/umami" << NGINXEOF
server {
    listen 80;
    server_name $UMAMI_DOMAIN;

    location / {
        proxy_pass http://127.0.0.1:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host \$host;
        proxy_cache_bypass \$http_upgrade;
    }
}
NGINXEOF

ln -sf /etc/nginx/sites-available/umami /etc/nginx/sites-enabled/umami
nginx -t && systemctl reload nginx
log "Nginx configuré."

# --- 7. SSL ------------------------------------------------------------------
step "7/7 — SSL (Let's Encrypt)"
EMAIL=$(grep "LE Email" "$CREDS_FILE" | awk '{print $4}' || echo "admin@$UMAMI_DOMAIN")
certbot --nginx -d "$UMAMI_DOMAIN" --non-interactive --agree-tos --email "$EMAIL" --redirect --quiet || warn "SSL auto echoué (verifie DNS)."

hr
echo -e "${BOLD}${GREEN}✅ Umami v3 Déployé avec succès !${NC}"
echo -e "URL : https://$UMAMI_DOMAIN"
echo -e "Accès : admin / umami"
echo -e "Credentials : $CREDS_FILE"
hr
