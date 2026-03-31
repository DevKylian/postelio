#!/bin/bash
# =============================================================================
# Postelio — Industrial Umami Analytics Deployer (Docker v3)
# =============================================================================
set -euo pipefail

CREDS_FILE="/root/umami-credentials.txt"
UMAMI_DOCKER_DIR="/opt/umami-docker"
UMAMI_IMAGE="ghcr.io/umami-software/umami:latest"

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; BOLD='\033[1m'; NC='\033[0m'

log()  { echo -e "${GREEN}[✓]${NC} $1"; }
info() { echo -e "${BLUE}[→]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
fail() { echo -e "${RED}[✗] ERREUR : $1${NC}" >&2; exit 1; }
hr()   { echo -e "${BOLD}────────────────────────────────────────────${NC}"; }
step() { echo ""; hr; info "$1"; hr; }

[[ $EUID -ne 0 ]] && fail "Lancer en root : sudo bash setup-umami-docker.sh"

step "1/8 — Identifiants et Configuration"

if [[ -f "$CREDS_FILE" ]]; then
    warn "Identifiants existants trouvés."
    UMAMI_DOMAIN=$(grep "URL" "$CREDS_FILE" | awk '{print $3}' | sed 's/https:\/\///')
    DB_PASS=$(grep "DB_PASSWORD" "$CREDS_FILE" | awk '{print $3}' || echo "")
    UMAMI_APP_SECRET=$(grep "APP_SECRET" "$CREDS_FILE" | awk '{print $3}' || echo "")
    LE_EMAIL=$(grep "EMAIL" "$CREDS_FILE" | awk '{print $3}' || echo "")
else
    info "Nouveau déploiement. Configuration initiale..."
    read -p "Domaine (ex: analytics.postelio.fr) : " UMAMI_DOMAIN
    read -p "Email (pour SSL) : " LE_EMAIL
    DB_PASS=$(openssl rand -base64 12 | tr -dc 'a-zA-Z0-9' | head -c 12)
    UMAMI_APP_SECRET=$(openssl rand -base64 32)

    cat > "$CREDS_FILE" << EOF
URL          : https://$UMAMI_DOMAIN
EMAIL        : $LE_EMAIL
DB_PASSWORD  : $DB_PASS
APP_SECRET   : $UMAMI_APP_SECRET
EOF
    chmod 600 "$CREDS_FILE"
fi

# Sécurité : On s'assure que tout est rempli
[[ -z "$DB_PASS" ]] && DB_PASS=$(openssl rand -base64 12 | tr -dc 'a-zA-Z0-9' | head -c 12) && echo "DB_PASSWORD  : $DB_PASS" >> "$CREDS_FILE"
[[ -z "$UMAMI_APP_SECRET" ]] && UMAMI_APP_SECRET=$(openssl rand -base64 32) && echo "APP_SECRET   : $UMAMI_APP_SECRET" >> "$CREDS_FILE"

log "Domaine : $UMAMI_DOMAIN"

step "2/8 — Installation Dépendances"
apt-get update -qq && apt-get install -y -qq curl gnupg certbot python3-certbot-nginx ufw jq >/dev/null
if ! command -v docker &>/dev/null; then
    curl -fsSL https://get.docker.com | sh >/dev/null
    systemctl enable --now docker
fi

step "3/8 — Sécurité (Firewall)"
ufw allow OpenSSH >/dev/null && ufw allow 'Nginx Full' >/dev/null
echo "y" | ufw enable >/dev/null

step "4/8 — Stack Docker (PostgreSQL + Umami)"
mkdir -p "$UMAMI_DOCKER_DIR"
cat > "$UMAMI_DOCKER_DIR/docker-compose.yml" << EOF
services:
  umami:
    image: $UMAMI_IMAGE
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
      retries: 10

volumes:
  umami-db-data:
EOF

cd "$UMAMI_DOCKER_DIR"
docker compose pull -q
docker compose up -d --remove-orphans

info "Démarrage des services (Postgres v16)..."
sleep 15

step "5/8 — Configuration Nginx"
NGINX_CONF="/etc/nginx/sites-available/umami"
cat > "$NGINX_CONF" << EOF
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
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
    }
}
EOF
ln -sf "$NGINX_CONF" /etc/nginx/sites-enabled/umami
nginx -t && systemctl reload nginx

step "6/8 — SSL"
certbot --nginx -d "$UMAMI_DOMAIN" --non-interactive --agree-tos --email "$LE_EMAIL" --redirect --quiet || warn "SSL échoué."

step "7/8 — Auto-Enregistrement"
info "Vérification API..."
RETRIES=0
while ! curl -s "http://127.0.0.1:3000/api/heartbeat" | grep -q "ok"; do
    ((RETRIES++))
    [[ $RETRIES -gt 30 ]] && fail "L'API ne répond pas."
    sleep 3
done

TOKEN=$(curl -s -X POST "http://127.0.0.1:3000/api/auth/login" -H "Content-Type: application/json" -d "{\"username\":\"admin\",\"password\":\"umami\"}" | jq -r '.token')

if [[ "$TOKEN" != "null" && -n "$TOKEN" ]]; then
    read -p "Nom du site à créer (ex: Postelio) : " SITE_NAME
    read -p "Domaine à tracker (ex: postelio.fr) : " SITE_DOMAIN
    SITE_DATA=$(curl -s -X POST "http://127.0.0.1:3000/api/websites" -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" -d "{\"name\":\"$SITE_NAME\",\"domain\":\"$SITE_DOMAIN\"}")
    W_ID=$(echo "$SITE_DATA" | jq -r '.id')
    if [[ "$W_ID" != "null" ]]; then
        log "Site créé ! ID: $W_ID"
        info "Tracking code : <script async src=\"https://$UMAMI_DOMAIN/script.js\" data-website-id=\"$W_ID\"></script>"
    fi
fi

hr
log "Umami v3 est prêt sur https://$UMAMI_DOMAIN"
hr
