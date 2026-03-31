#!/bin/bash
# =============================================================================
# Postelio — Setup Umami Analytics (Ubuntu 22.04)
# Usage : sudo bash deploy/setup-umami.sh [--domain DOMAIN] [--email EMAIL] [--force-build]
# =============================================================================
set -euo pipefail

# Trap — affiche exactement quelle ligne échoue
trap 'echo -e "\033[0;31m[✗] ERREUR ligne ${LINENO} : ${BASH_COMMAND}\033[0m" >&2' ERR

# --- Couleurs ----------------------------------------------------------------
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; BOLD='\033[1m'; NC='\033[0m'

log()  { echo -e "${GREEN}[✓]${NC} $1"; }
info() { echo -e "${BLUE}[→]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
fail() { echo -e "${RED}[✗] ERREUR : $1${NC}" >&2; exit 1; }
hr()   { echo -e "${BOLD}────────────────────────────────────────────${NC}"; }
step() { echo ""; hr; info "$1"; hr; }

# --- Vérifications -----------------------------------------------------------
[[ $EUID -ne 0 ]] && fail "Lancer en root : sudo bash deploy/setup-umami.sh"
command -v python3 &>/dev/null || fail "python3 requis."

hr
echo -e "${BOLD}  Postelio — Setup Umami Analytics (MySQL optimized)${NC}"
echo -e "  Persistence, CLI args et correctif Umami v3 inclus."
hr
echo ""

# --- Arguments CLI ------------------------------------------------------------
UMAMI_DOMAIN=""
LE_EMAIL=""
FORCE_REBUILD=false

while [[ "$#" -gt 0 ]]; do
    case $1 in
        --domain) UMAMI_DOMAIN="$2"; shift ;;
        --email) LE_EMAIL="$2"; shift ;;
        --force-build) FORCE_REBUILD=true ;;
    esac
    shift
done

# --- Persistance des Credentials ----------------------------------------------
CREDS_FILE="/root/umami-credentials.txt"
UMAMI_DB_NAME="umami"
UMAMI_DB_USER="umami_db"

# Valeurs par défaut (si on doit générer)
UMAMI_DB_PASSWORD=$(python3 -c "import secrets; print(secrets.token_hex(24))")
UMAMI_APP_SECRET=$(python3 -c "import secrets; print(secrets.token_hex(32))")
UMAMI_ADMIN_PASSWORD=$(python3 -c "import secrets; print(secrets.token_urlsafe(16))")

if [[ -f "$CREDS_FILE" ]]; then
    warn "Credentials existants trouvés dans $CREDS_FILE. Chargement..."
    EXISTING_URL=$(grep "URL" "$CREDS_FILE" | awk '{print $3}' | sed 's/https:\/\///' || echo "")
    [[ -n "$EXISTING_URL" ]] && UMAMI_DOMAIN=${UMAMI_DOMAIN:-$EXISTING_URL}

    EXISTING_PASS=$(grep "MySQL password" "$CREDS_FILE" | awk '{print $4}' || echo "")
    [[ -n "$EXISTING_PASS" ]] && UMAMI_DB_PASSWORD="$EXISTING_PASS"

    EXISTING_SECRET=$(grep "App secret" "$CREDS_FILE" | awk '{print $4}' || echo "")
    [[ -n "$EXISTING_SECRET" ]] && UMAMI_APP_SECRET="$EXISTING_SECRET"

    EXISTING_ADMIN=$(grep "Admin password" "$CREDS_FILE" | awk '{print $4}' || echo "")
    [[ -n "$EXISTING_ADMIN" ]] && UMAMI_ADMIN_PASSWORD="$EXISTING_ADMIN"
    
    log "Données chargées pour ${UMAMI_DOMAIN:-inconnu}."
fi

# Collecte interactive si non fourni
if [[ -z "$UMAMI_DOMAIN" ]]; then
    read -rp "  Sous-domaine Umami (ex: analytics.postelio.fr) : " UMAMI_DOMAIN
    [[ -z "$UMAMI_DOMAIN" ]] && fail "Sous-domaine requis."
fi
if [[ -z "$LE_EMAIL" ]]; then
    read -rp "  Email Let's Encrypt (pour le certificat SSL)   : " LE_EMAIL
    [[ -z "$LE_EMAIL" ]] && fail "Email requis."
fi

# Sauvegarde finale
{
    printf '# ============================================================\n'
    printf '# Umami Analytics — Credentials\n'
    printf '# Genere le %s\n' "$(date '+%d/%m/%Y a %H:%M:%S')"
    printf '# ============================================================\n\n'
    printf 'URL              : https://%s\n\n' "$UMAMI_DOMAIN"
    printf 'Admin login      : admin\n'
    printf 'Admin password   : %s\n\n' "$UMAMI_ADMIN_PASSWORD"
    printf 'MySQL DB         : %s\n' "$UMAMI_DB_NAME"
    printf 'MySQL user       : %s\n' "$UMAMI_DB_USER"
    printf 'MySQL password   : %s\n' "$UMAMI_DB_PASSWORD"
    printf 'App secret       : %s\n\n' "$UMAMI_APP_SECRET"
} > "$CREDS_FILE"
chmod 600 "$CREDS_FILE"

# --- Constantes ---------------------------------------------------------------
UMAMI_DIR="/opt/umami"
UMAMI_USER="umami"
UMAMI_PORT=3001
LOG_DIR="/var/log/umami"

# =============================================================================
# 1. PAQUETS SYSTÈME
# =============================================================================
step "1/7 — Paquets système"
export DEBIAN_FRONTEND=noninteractive
apt-get update -qq

if ! command -v node &>/dev/null || [[ "$(node -v | grep -oP '\d+' | head -1)" -lt 18 ]]; then
    info "Installation Node.js 20..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash - > /dev/null 2>&1
    apt-get install -y -qq nodejs
fi

if ! command -v certbot &>/dev/null; then
    apt-get install -y -qq certbot python3-certbot-nginx
fi

# =============================================================================
# 2. MYSQL — Base de données Umami
# =============================================================================
step "2/7 — MySQL (base umami)"
systemctl is-active mysql --quiet || systemctl start mysql

mysql -u root << MYSQL_SCRIPT
CREATE DATABASE IF NOT EXISTS \`${UMAMI_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${UMAMI_DB_USER}'@'localhost' IDENTIFIED BY '${UMAMI_DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${UMAMI_DB_NAME}\`.* TO '${UMAMI_DB_USER}'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT
log "MySQL prêt."

# =============================================================================
# 3. UTILISATEUR SYSTÈME
# =============================================================================
step "3/7 — Utilisateur système 'umami'"
if ! id "$UMAMI_USER" &>/dev/null; then
    useradd -r -m -s /bin/bash -d "$UMAMI_DIR" "$UMAMI_USER"
fi
mkdir -p "$LOG_DIR"
chown "$UMAMI_USER:$UMAMI_USER" "$LOG_DIR"

# =============================================================================
# 4. INSTALLATION D'UMAMI
# =============================================================================
step "4/7 — Installation Umami"
if [ -d "$UMAMI_DIR/.git" ]; then
    warn "Umami existe — mise à jour..."
    sudo -u "$UMAMI_USER" git -C "$UMAMI_DIR" pull --quiet
else
    [ -d "$UMAMI_DIR" ] && rm -rf "$UMAMI_DIR"
    git clone --depth 1 https://github.com/umami-software/umami.git "$UMAMI_DIR" --quiet
    chown -R "$UMAMI_USER:$UMAMI_USER" "$UMAMI_DIR"
fi

printf 'DATABASE_URL=mysql://%s:%s@127.0.0.1:3306/%s\n' "$UMAMI_DB_USER" "$UMAMI_DB_PASSWORD" "$UMAMI_DB_NAME" > "${UMAMI_DIR}/.env"
printf 'APP_SECRET=%s\n' "$UMAMI_APP_SECRET" >> "${UMAMI_DIR}/.env"
chown "$UMAMI_USER:$UMAMI_USER" "${UMAMI_DIR}/.env"

info "npm install..."
sudo -u "$UMAMI_USER" bash -c "cd $UMAMI_DIR && npm install --legacy-peer-deps --quiet" \
    >> "${LOG_DIR}/build.log" 2>&1 || fail "npm install échoué."

# Fix prisma
info "Migrations Prisma..."
sudo -u "$UMAMI_USER" bash -c "cd $UMAMI_DIR && npx prisma migrate deploy" \
    >> "${LOG_DIR}/build.log" 2>&1 || warn "Prisma migrate a échoué/déjà fait."

if [ ! -d "$UMAMI_DIR/.next" ] || [ "$FORCE_REBUILD" = true ]; then
    info "npm run build (3-5 min — peut être long)..."
    # Désactivation de --turbo (si présent dans package.json) via NODE_OPTIONS et forçage de RAM
    # SKIP_DB_CHECK=1 est vital pour Umami v3 (évite l'erreur 5b au build)
    sudo -u "$UMAMI_USER" bash -c "cd $UMAMI_DIR && \
        NODE_OPTIONS='--max-old-space-size=3072' \
        SKIP_DB_CHECK=1 \
        DATABASE_URL=mysql://${UMAMI_DB_USER}:${UMAMI_DB_PASSWORD}@127.0.0.1:3306/${UMAMI_DB_NAME} \
        npm run build" \
        >> "${LOG_DIR}/build.log" 2>&1 || fail "Build échoué. Vérifie la RAM (free -m) ou le log : ${LOG_DIR}/build.log"
    log "Umami compilé."
else
    log "Build présent, skip."
fi

# =============================================================================
# 5. SERVICE SYSTEMD
# =============================================================================
step "5/7 — Service 'umami'"
cat > /etc/systemd/system/umami.service << SVCEOF
[Unit]
Description=Umami Analytics
After=network.target mysql.service
Wants=mysql.service

[Service]
Type=simple
User=${UMAMI_USER}
Group=${UMAMI_USER}
WorkingDirectory=${UMAMI_DIR}
EnvironmentFile=${UMAMI_DIR}/.env
Environment=NODE_ENV=production
Environment=PORT=${UMAMI_PORT}
Environment=HOSTNAME=127.0.0.1
ExecStart=/usr/bin/node ${UMAMI_DIR}/node_modules/.bin/next start --port ${UMAMI_PORT} --hostname 127.0.0.1
Restart=always
RestartSec=10
StandardOutput=append:${LOG_DIR}/umami.log
StandardError=append:${LOG_DIR}/umami-error.log
KillSignal=SIGTERM
TimeoutStopSec=30

[Install]
WantedBy=multi-user.target
SVCEOF

systemctl daemon-reload
systemctl enable umami --quiet
systemctl restart umami

info "Attente Umami (90s)..."
for i in $(seq 1 90); do
    if curl -sf "http://127.0.0.1:${UMAMI_PORT}/api/heartbeat" > /dev/null 2>&1; then
        log "Prêt en ${i}s."
        break
    fi
    sleep 1
done

# =============================================================================
# 6. NGINX
# =============================================================================
step "6/7 — Nginx"
cat > "/etc/nginx/sites-available/umami" << NGINXEOF
server {
    listen 80;
    server_name ${UMAMI_DOMAIN};

    location / {
        proxy_pass http://127.0.0.1:${UMAMI_PORT};
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_cache_bypass \$http_upgrade;
    }

    location /_next/static/ {
        proxy_pass http://127.0.0.1:${UMAMI_PORT};
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
NGINXEOF

ln -sf /etc/nginx/sites-available/umami /etc/nginx/sites-enabled/umami
nginx -t && systemctl reload nginx

# =============================================================================
# 7. SSL
# =============================================================================
step "7/7 — SSL Let's Encrypt"
certbot --nginx -d "${UMAMI_DOMAIN}" --non-interactive --agree-tos --email "$LE_EMAIL" --redirect --quiet

# Password config via API
info "Changement mot de passe admin..."
sleep 3
ADMIN_TOKEN=$(curl -sf -X POST "http://127.0.0.1:${UMAMI_PORT}/api/auth/login" \
    -H "Content-Type: application/json" -d "{\"username\":\"admin\",\"password\":\"umami\"}" \
    | python3 -c "import sys,json; print(json.load(sys.stdin).get('token',''))" 2>/dev/null || echo "")

if [[ -n "$ADMIN_TOKEN" ]]; then
    ADMIN_USER_ID=$(curl -sf "http://127.0.0.1:${UMAMI_PORT}/api/me" -H "Authorization: Bearer ${ADMIN_TOKEN}" \
        | python3 -c "import sys,json; print(json.load(sys.stdin).get('id',''))" 2>/dev/null || echo "")
    if [[ -n "$ADMIN_USER_ID" ]]; then
        curl -sf -o /dev/null -X POST "http://127.0.0.1:${UMAMI_PORT}/api/users/${ADMIN_USER_ID}/password" \
            -H "Authorization: Bearer ${ADMIN_TOKEN}" -H "Content-Type: application/json" \
            -d "{\"currentPassword\":\"umami\",\"newPassword\":\"${UMAMI_ADMIN_PASSWORD}\"}" && log "Pass admin modifie."
    fi
fi

hr
echo -e "${BOLD}${GREEN}✅ Setup Umami terminé !${NC}"
echo "URL : https://${UMAMI_DOMAIN}"
echo "Pass: ${UMAMI_ADMIN_PASSWORD}"
echo "Credentials complets: cat ${CREDS_FILE}"
hr
