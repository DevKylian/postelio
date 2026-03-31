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
    UMAMI_DB_NAME="umami_db"
    UMAMI_DB_USER="umami_user"
    DB_PASS=$(grep "Postgres password" "$CREDS_FILE" | awk '{print $4}' || echo "")
    UMAMI_APP_SECRET=$(grep "App secret" "$CREDS_FILE" | awk '{print $4}')
    
    # Si le mot de passe Postgres n'est pas dans le fichier, on en genere un (compatibilite)
    if [[ -z "$DB_PASS" ]]; then
        DB_PASS=$(openssl rand -base64 12 | tr -dc 'a-zA-Z0-9' | head -c 12)
        printf 'Postgres password: %s\n' "$DB_PASS" >> "$CREDS_FILE"
    fi
else
    info "Nouveau déploiement détecté. Génération des accès..."
    read -p "Entrez le domaine (ex: analytics.domaine.com) : " UMAMI_DOMAIN
    read -p "Entrez votre email (pour SSL) : " LE_EMAIL
    
    UMAMI_DB_NAME="umami_db"
    UMAMI_DB_USER="umami_user"
    DB_PASS=$(openssl rand -base64 12 | tr -dc 'a-zA-Z0-9' | head -c 12)
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
step "2/8 — Installation Docker & MySQL"
apt-get update -qq
apt-get install -y -qq curl gnupg mysql-server certbot python3-certbot-nginx ufw jq >/dev/null

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

# --- 4. Docker Compose (Stack v3 avec PostgreSQL) ---------------------------
step "4/7 — Lancement Umami v3 (Stack PostgreSQL)"
mkdir -p "$UMAMI_DOCKER_DIR"

# On utilise le DB_PASS chargé au début
info "Utilisation du mot de passe base de données sauvegardé."

cat > "$UMAMI_DOCKER_DIR/docker-compose.yml" << EOF
services:
  umami:
    image: ghcr.io/umami-software/umami:latest
    restart: always
    environment:
      DATABASE_URL: postgresql://umami_user:$DB_PASS@db:5432/umami_db
      APP_SECRET: $UMAMI_APP_SECRET
      DATABASE_TYPE: postgresql
    ports:
      - "127.0.0.1:3000:3000"
    depends_on:
      db:
        condition: service_healthy
  db:
    image: postgres:16-alpine
    restart: always
    environment:
      POSTGRES_DB: umami_db
      POSTGRES_USER: umami_user
      POSTGRES_PASSWORD: $DB_PASS
    volumes:
      - umami-db-data:/var/lib/postgresql/data
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U umami_user -d umami_db"]
      interval: 5s
      timeout: 5s
      retries: 5

volumes:
  umami-db-data:
EOF

cd "$UMAMI_DOCKER_DIR"
docker compose pull
docker compose up -d --remove-orphans

info "Attente du démarrage (Postgres + Umami v3)..."
sleep 10
docker compose logs --tail 20 umami

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

# Suppression active de l'ancien lien s'il existe et creation propre
rm -f /etc/nginx/sites-enabled/umami
ln -s "$NGINX_CONF" /etc/nginx/sites-enabled/umami
nginx -t && systemctl reload nginx
log "Nginx configuré pour $UMAMI_DOMAIN."

# --- 7. SSL ------------------------------------------------------------------
step "7/8 — SSL (Let's Encrypt)"
EMAIL=$(grep "LE Email" "$CREDS_FILE" | awk '{print $4}' || echo "admin@$UMAMI_DOMAIN")
if ! certbot --nginx -d "$UMAMI_DOMAIN" --non-interactive --agree-tos --email "$EMAIL" --redirect --quiet; then
    warn "Certbot a echoué. Verifie que ton DNS $UMAMI_DOMAIN pointe bien sur l'IP du serveur."
else
    log "SSL activé et redirection HTTPS forcée."
fi

# --- 8. Auto-Enregistrement du site -------------------------------------------
step "8/8 — Enregistrement du site"
info "Attente que l'API Umami soit prête..."
RETRIES=0
while ! curl -s "http://127.0.0.1:3000/api/heartbeat" | grep -q "ok"; do
    ((RETRIES++))
    if [ $RETRIES -gt 30 ]; then fail "Umami n'a pas demarre a temps."; fi
    sleep 2
done

info "Connexion à l'API..."
TOKEN=$(curl -s -X POST "http://127.0.0.1:3000/api/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"username":"admin","password":"umami"}' | jq -r '.token')

if [ "$TOKEN" == "null" ] || [ -z "$TOKEN" ]; then
    warn "Impossible de se connecter a l'API (mot de passe deja change ?)."
else
    # On cree le site principal (postelio.fr par exemple)
    read -p "Domaine du site à tracker (ex: postelio.fr) : " SITE_DOMAIN
    SITE_NAME="Postelio"
    
    info "Création du site dans Umami..."
    SITE_DATA=$(curl -s -X POST "http://127.0.0.1:3000/api/websites" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d "{\"name\":\"$SITE_NAME\",\"domain\":\"$SITE_DOMAIN\"}")
    
    WEBSITE_ID=$(echo "$SITE_DATA" | jq -r '.id')
    
    if [ "$WEBSITE_ID" != "null" ]; then
        log "Site '$SITE_NAME' créé avec succès !"
        info "Voici ton code de tracking a inserer dans ton <head> :"
        echo -e "${YELLOW}<script async src=\"https://$UMAMI_DOMAIN/script.js\" data-website-id=\"$WEBSITE_ID\"></script>${NC}"
    else
        warn "Le site existe peut-etre déjà : $(echo $SITE_DATA | jq -r '.message')"
    fi
fi

hr
echo -e "${BOLD}${GREEN}✅ Umami v3 Déployé et Site Configuré !${NC}"
echo -e "URL : https://$UMAMI_DOMAIN"
echo -e "Credentials : $CREDS_FILE"
hr
