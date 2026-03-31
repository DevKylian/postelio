#!/bin/bash
# =============================================================================
# Postelio — Setup Umami Analytics (Docker Version)
# Usage : sudo bash deploy/setup-umami-docker.sh
# =============================================================================
set -euo pipefail

# --- Couleurs ----------------------------------------------------------------
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; BOLD='\033[1m'; NC='\033[0m'

log()  { echo -e "${GREEN}[✓]${NC} $1"; }
info() { echo -e "${BLUE}[→]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
fail() { echo -e "${RED}[✗] ERREUR : $1${NC}" >&2; exit 1; }
hr()   { echo -e "${BOLD}────────────────────────────────────────────${NC}"; }
step() { echo ""; hr; info "$1"; hr; }

[[ $EUID -ne 0 ]] && fail "Lancer en root : sudo bash deploy/setup-umami-docker.sh"

hr
echo -e "${BOLD}  Postelio — Setup Umami Analytics (DOCKER)${NC}"
echo -e "  Zéro compilation locale, stabilité garantie."
hr

# --- 1. Installation Docker --------------------------------------------------
step "1/5 — Installation Docker"
if ! command -v docker &>/dev/null; then
    info "Installation de Docker..."
    curl -fsSL https://get.docker.com | sh
    systemctl enable --now docker
    log "Docker installé."
else
    log "Docker déjà présent."
fi

# --- 2. Chargement des Credentials -------------------------------------------
step "2/5 — Chargement des credentials"
CREDS_FILE="/root/umami-credentials.txt"
if [[ ! -f "$CREDS_FILE" ]]; then
    fail "Fichier $CREDS_FILE introuvable. Lance d'abord setup-umami.sh (meme s'il crash au build) pour generer les acces DB."
fi

UMAMI_DOMAIN=$(grep "URL" "$CREDS_FILE" | awk '{print $3}' | sed 's/https:\/\///')
UMAMI_DB_NAME=$(grep "MySQL DB" "$CREDS_FILE" | awk '{print $4}')
UMAMI_DB_USER=$(grep "MySQL user" "$CREDS_FILE" | awk '{print $4}')
UMAMI_DB_PASSWORD=$(grep "MySQL password" "$CREDS_FILE" | awk '{print $4}')
UMAMI_APP_SECRET=$(grep "App secret" "$CREDS_FILE" | awk '{print $4}')

info "Configuration pour : $UMAMI_DOMAIN"

# --- 3. Docker Compose -------------------------------------------------------
step "3/5 — Lancement d'Umami"
UMAMI_DOCKER_DIR="/opt/umami-docker"
mkdir -p "$UMAMI_DOCKER_DIR"

cat > "$UMAMI_DOCKER_DIR/docker-compose.yml" << EOF
version: '3'
services:
  umami:
    image: ghcr.io/umami-software/umami:mysql-latest
    restart: always
    environment:
      DATABASE_URL: mysql://$UMAMI_DB_USER:$UMAMI_DB_PASSWORD@host.docker.internal:3306/$UMAMI_DB_NAME
      APP_SECRET: $UMAMI_APP_SECRET
      DATABASE_TYPE: mysql
    ports:
      - "3000:3000"
    extra_hosts:
      - "host.docker.internal:host-gateway"
EOF

# S'assurer que MySQL accepte les connexions de Docker (souvent deja le cas sur localhost)
# Mais on utilise host.docker.internal qui pointe vers l'IP de la machine.

cd "$UMAMI_DOCKER_DIR"
docker compose pull
docker compose up -d

log "Conteneur Umami lancé sur le port 3000."

# --- 4. Nginx ----------------------------------------------------------------
step "4/5 — Configuration Nginx"
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
log "Nginx mis à jour."

# --- 5. SSL ------------------------------------------------------------------
step "5/5 — SSL"
certbot --nginx -d "$UMAMI_DOMAIN" --non-interactive --agree-tos --email "admin@$UMAMI_DOMAIN" --redirect --quiet
log "SSL OK."

hr
echo -e "${BOLD}${GREEN}✅ Umami est en ligne via Docker !${NC}"
echo "URL : https://$UMAMI_DOMAIN"
hr
