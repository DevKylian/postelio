#!/bin/bash
# =============================================================================
# Postelio — Setup Umami Analytics (Ubuntu 22.04)
# Usage : sudo bash deploy/setup-umami.sh
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
command -v python3 &>/dev/null || fail "python3 requis (déjà présent normalement)."

hr
echo -e "${BOLD}  Postelio — Setup Umami Analytics${NC}"
echo -e "  Tous les mots de passe générés automatiquement."
hr
echo ""

# --- Collecte des paramètres -------------------------------------------------
echo -e "${BOLD}Paramètres requis :${NC}"
echo ""
read -rp "  Sous-domaine Umami (ex: analytics.postelio.fr) : " UMAMI_DOMAIN
[[ -z "$UMAMI_DOMAIN" ]] && fail "Sous-domaine requis."
read -rp "  Email Let's Encrypt (pour le certificat SSL)   : " LE_EMAIL
[[ -z "$LE_EMAIL" ]] && fail "Email requis."
echo ""

# --- Constantes --------------------------------------------------------------
UMAMI_DIR="/opt/umami"
UMAMI_USER="umami"
UMAMI_PORT=3001
LOG_DIR="/var/log/umami"
CREDS_FILE="/root/umami-credentials.txt"
UMAMI_DB_NAME="umami"
UMAMI_DB_USER="umami_db"

# =============================================================================
# GÉNÉRATION DES CREDENTIALS
# python3 secrets : uniquement hex/urlsafe → aucun $ ni caractère spécial
# qui pourrait exploser dans les heredocs sous pipefail
# =============================================================================
step "0/7 — Génération des credentials"

UMAMI_DB_PASSWORD=$(python3 -c "import secrets; print(secrets.token_hex(24))")
UMAMI_APP_SECRET=$(python3 -c "import secrets; print(secrets.token_hex(32))")
UMAMI_ADMIN_PASSWORD=$(python3 -c "import secrets; print(secrets.token_urlsafe(16))")

log "Passwords générés (hex/urlsafe — safe pour heredocs et .env)."

# Sauvegarde credentials via printf (pas de heredoc avec variables)
{
    printf '# ============================================================\n'
    printf '# Umami Analytics — Credentials\n'
    printf '# Genere le %s\n' "$(date '+%d/%m/%Y a %H:%M:%S')"
    printf '# CONSERVER CE FICHIER EN LIEU SUR\n'
    printf '# ============================================================\n\n'
    printf 'URL              : https://%s\n\n' "$UMAMI_DOMAIN"
    printf 'Admin login      : admin\n'
    printf 'Admin password   : %s\n\n' "$UMAMI_ADMIN_PASSWORD"
    printf 'MySQL DB         : %s\n' "$UMAMI_DB_NAME"
    printf 'MySQL user       : %s\n' "$UMAMI_DB_USER"
    printf 'MySQL password   : %s\n' "$UMAMI_DB_PASSWORD"
    printf 'App secret       : %s\n\n' "$UMAMI_APP_SECRET"
    printf '# Snippet tracking (ajouter dans layouts/app.blade.php avant </head>) :\n'
    printf '# <script async src="https://%s/script.js" data-website-id="SITE-ID"></script>\n' "$UMAMI_DOMAIN"
} > "$CREDS_FILE"
chmod 600 "$CREDS_FILE"
log "Credentials sauvegardés dans $CREDS_FILE"

# =============================================================================
# 1. PAQUETS SYSTÈME
# =============================================================================
step "1/7 — Paquets système"

export DEBIAN_FRONTEND=noninteractive
apt-get update -qq

# Node.js 20 (peut déjà être présent via setup.sh principal)
if ! command -v node &>/dev/null || [[ "$(node -v | grep -oP '\d+' | head -1)" -lt 18 ]]; then
    info "Installation Node.js 20..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash - > /dev/null 2>&1
    apt-get install -y -qq nodejs
fi

# certbot (peut déjà être présent)
if ! command -v certbot &>/dev/null; then
    apt-get install -y -qq certbot python3-certbot-nginx
fi

log "Node $(node -v) — certbot $(certbot --version 2>&1 | head -1)"

# =============================================================================
# 2. MYSQL — Base de données Umami (réutilise MySQL existant)
# =============================================================================
step "2/7 — MySQL (base umami)"

systemctl is-active mysql --quiet || systemctl start mysql

# Connexion root sans mot de passe (configuré ainsi par setup.sh principal)
mysql -u root << MYSQL_SCRIPT
CREATE DATABASE IF NOT EXISTS \`${UMAMI_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${UMAMI_DB_USER}'@'localhost' IDENTIFIED BY '${UMAMI_DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${UMAMI_DB_NAME}\`.* TO '${UMAMI_DB_USER}'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT

log "Base MySQL '${UMAMI_DB_NAME}' et user '${UMAMI_DB_USER}' prêts."

# =============================================================================
# 3. UTILISATEUR SYSTÈME
# =============================================================================
step "3/7 — Utilisateur système 'umami'"

if ! id "$UMAMI_USER" &>/dev/null; then
    useradd -r -m -s /bin/bash -d "$UMAMI_DIR" "$UMAMI_USER"
    log "Utilisateur '${UMAMI_USER}' créé."
else
    log "Utilisateur '${UMAMI_USER}' déjà existant."
fi

mkdir -p "$LOG_DIR"
chown "$UMAMI_USER:$UMAMI_USER" "$LOG_DIR"

# =============================================================================
# 4. INSTALLATION D'UMAMI (Next.js)
# =============================================================================
step "4/7 — Installation Umami"

if [ -d "$UMAMI_DIR/.git" ]; then
    warn "Umami déjà cloné — mise à jour..."
    sudo -u "$UMAMI_USER" git -C "$UMAMI_DIR" pull --quiet
else
    info "Clonage du dépôt Umami..."
    git clone --depth 1 https://github.com/umami-software/umami.git "$UMAMI_DIR" --quiet
    chown -R "$UMAMI_USER:$UMAMI_USER" "$UMAMI_DIR"
fi

# --- Fichier .env (printf — pas de heredoc avec variables sensibles) ---------
printf 'DATABASE_URL=mysql://%s:%s@127.0.0.1:3306/%s\n' \
    "$UMAMI_DB_USER" "$UMAMI_DB_PASSWORD" "$UMAMI_DB_NAME" > "${UMAMI_DIR}/.env"
printf 'APP_SECRET=%s\n' "$UMAMI_APP_SECRET" >> "${UMAMI_DIR}/.env"

chown "$UMAMI_USER:$UMAMI_USER" "${UMAMI_DIR}/.env"
chmod 640 "${UMAMI_DIR}/.env"
log ".env écrit."

# --- npm install -------------------------------------------------------------
info "npm install (peut prendre 1-2 min)..."
sudo -u "$UMAMI_USER" bash -c "cd $UMAMI_DIR && npm install --legacy-peer-deps --quiet" \
    >> "${LOG_DIR}/build.log" 2>&1 \
    || fail "npm install échoué. Voir : cat ${LOG_DIR}/build.log"

# --- npm run build -----------------------------------------------------------
info "npm run build (Next.js — peut prendre 3-5 min)..."
sudo -u "$UMAMI_USER" bash -c "cd $UMAMI_DIR && npm run build" \
    >> "${LOG_DIR}/build.log" 2>&1 \
    || fail "Build échoué. Voir : cat ${LOG_DIR}/build.log"

log "Umami compilé."

# =============================================================================
# 5. SERVICE SYSTEMD
# =============================================================================
step "5/7 — Service systemd 'umami'"

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
NoNewPrivileges=true
PrivateTmp=true
ProtectSystem=strict
ReadWritePaths=${UMAMI_DIR} ${LOG_DIR}

[Install]
WantedBy=multi-user.target
SVCEOF

systemctl daemon-reload
systemctl enable umami --quiet
systemctl start umami

# Attendre que le serveur soit prêt (max 90s)
info "Attente du démarrage d'Umami (max 90s)..."
READY=0
for i in $(seq 1 90); do
    if curl -sf "http://127.0.0.1:${UMAMI_PORT}/api/heartbeat" > /dev/null 2>&1; then
        READY=1
        log "Umami répond après ${i}s."
        break
    fi
    sleep 1
done

if [[ $READY -eq 0 ]]; then
    warn "Umami n'a pas répondu en 90s. Vérifier : journalctl -u umami -n 50"
fi

# =============================================================================
# 6. NGINX — Reverse proxy
# =============================================================================
step "6/7 — Nginx"

cat > "/etc/nginx/sites-available/umami" << NGINXEOF
server {
    listen 80;
    server_name ${UMAMI_DOMAIN};

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        proxy_pass         http://127.0.0.1:${UMAMI_PORT};
        proxy_http_version 1.1;
        proxy_set_header   Upgrade \$http_upgrade;
        proxy_set_header   Connection 'upgrade';
        proxy_set_header   Host \$host;
        proxy_set_header   X-Real-IP \$remote_addr;
        proxy_set_header   X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto \$scheme;
        proxy_cache_bypass \$http_upgrade;
        proxy_read_timeout 300;
        client_max_body_size 10M;
    }

    location /_next/static/ {
        proxy_pass http://127.0.0.1:${UMAMI_PORT};
        proxy_set_header Host \$host;
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    location ~ /\.(env|git) { deny all; return 404; }

    access_log /var/log/nginx/umami-access.log;
    error_log  /var/log/nginx/umami-error.log;
}
NGINXEOF

ln -sf /etc/nginx/sites-available/umami /etc/nginx/sites-enabled/umami
nginx -t && systemctl reload nginx
log "Nginx configuré pour ${UMAMI_DOMAIN}."

# =============================================================================
# 7. SSL LET'S ENCRYPT
# =============================================================================
step "7/7 — SSL Let's Encrypt"

certbot --nginx \
    -d "${UMAMI_DOMAIN}" \
    --non-interactive \
    --agree-tos \
    --email "$LE_EMAIL" \
    --redirect \
    --quiet

log "Certificat SSL obtenu."

# =============================================================================
# LOGROTATE
# =============================================================================
cat > /etc/logrotate.d/umami << 'LOGREOF'
/var/log/umami/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 640 umami umami
    sharedscripts
    postrotate
        systemctl reload umami > /dev/null 2>&1 || true
    endscript
}
LOGREOF

# =============================================================================
# CHANGEMENT MOT DE PASSE ADMIN (admin/umami → mot de passe généré)
# =============================================================================
info "Changement du mot de passe admin par défaut..."
sleep 3

ADMIN_TOKEN=$(curl -sf -X POST "http://127.0.0.1:${UMAMI_PORT}/api/auth/login" \
    -H "Content-Type: application/json" \
    -d "{\"username\":\"admin\",\"password\":\"umami\"}" \
    2>/dev/null | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('token',''))" \
    2>/dev/null || echo "")

if [[ -n "$ADMIN_TOKEN" ]]; then
    ADMIN_USER_ID=$(curl -sf "http://127.0.0.1:${UMAMI_PORT}/api/me" \
        -H "Authorization: Bearer ${ADMIN_TOKEN}" \
        2>/dev/null | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('id',''))" \
        2>/dev/null || echo "")

    if [[ -n "$ADMIN_USER_ID" ]]; then
        HTTP_STATUS=$(curl -sf -o /dev/null -w "%{http_code}" \
            -X POST "http://127.0.0.1:${UMAMI_PORT}/api/users/${ADMIN_USER_ID}/password" \
            -H "Authorization: Bearer ${ADMIN_TOKEN}" \
            -H "Content-Type: application/json" \
            -d "{\"currentPassword\":\"umami\",\"newPassword\":\"${UMAMI_ADMIN_PASSWORD}\"}" \
            2>/dev/null || echo "0")

        if [[ "$HTTP_STATUS" == "200" ]]; then
            log "Mot de passe admin changé."
        else
            warn "Changement de mot de passe automatique impossible (code: ${HTTP_STATUS})."
            warn "-> Connexion initiale : admin / umami"
            warn "-> Puis changer manuellement avec le mot de passe dans : $CREDS_FILE"
        fi
    fi
else
    warn "API non joignable pour changer le mot de passe."
    warn "-> Se connecter avec admin / umami et changer manuellement."
fi

# =============================================================================
# RÉSUMÉ FINAL
# =============================================================================
echo ""
hr
echo ""
echo -e "${BOLD}${GREEN}  Umami Analytics — Setup termine !${NC}"
echo ""
hr
echo ""
echo -e "${BOLD}Acces :${NC}"
echo "  URL           : https://${UMAMI_DOMAIN}"
echo "  Login         : admin"
echo -e "  Password      : ${BOLD}${UMAMI_ADMIN_PASSWORD}${NC}"
echo ""
echo -e "${BOLD}Services :${NC}"
echo "  systemctl status umami"
echo "  journalctl -u umami -f"
echo "  tail -f ${LOG_DIR}/umami.log"
echo ""
echo -e "${BOLD}Credentials complets :${NC}"
echo "  cat ${CREDS_FILE}"
echo ""
hr
echo ""
echo -e "${BOLD}${YELLOW}  Etapes post-installation :${NC}"
echo ""
echo "  1. Se connecter sur https://${UMAMI_DOMAIN}"
echo "  2. Settings > Websites > Add website > postelio.fr"
echo "  3. Copier le tracking script genere"
echo "  4. L'ajouter dans resources/views/layouts/app.blade.php avant </head>"
echo ""
echo -e "     ${BLUE}<script async src=\"https://${UMAMI_DOMAIN}/script.js\"${NC}"
echo -e "     ${BLUE}        data-website-id=\"VOTRE-SITE-ID\"></script>${NC}"
echo ""
echo "  5. Push sur main -> tracking actif"
echo ""
hr
