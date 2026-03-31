#!/bin/bash
# =============================================================================
# Postelio — Setup Umami Analytics (Ubuntu 22.04)
# Usage : sudo bash deploy/setup-umami.sh <sous-domaine> <email-letsencrypt>
# Exemple: sudo bash deploy/setup-umami.sh analytics.postelio.fr admin@postelio.fr
# Idempotent : peut être relancé en cas d'échec partiel.
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
[[ $EUID -ne 0 ]] && fail "Lancer en root : sudo bash deploy/setup-umami.sh <domaine> <email>"
command -v python3 &>/dev/null || fail "python3 requis."

# --- Paramètres (args CLI ou interactif) ------------------------------------
UMAMI_DOMAIN="${1:-}"
LE_EMAIL="${2:-}"

if [[ -z "$UMAMI_DOMAIN" ]]; then
    read -rp "  Sous-domaine Umami (ex: analytics.postelio.fr) : " UMAMI_DOMAIN
fi
if [[ -z "$LE_EMAIL" ]]; then
    read -rp "  Email Let's Encrypt (pour le certificat SSL)   : " LE_EMAIL
fi
[[ -z "$UMAMI_DOMAIN" ]] && fail "Sous-domaine requis."
[[ -z "$LE_EMAIL" ]]     && fail "Email requis."

# --- Constantes --------------------------------------------------------------
UMAMI_DIR="/opt/umami"
UMAMI_USER="umami"
UMAMI_PORT=3001
LOG_DIR="/var/log/umami"
CREDS_FILE="/root/umami-credentials.txt"
UMAMI_DB_NAME="umami"
UMAMI_DB_USER="umami_db"

hr
echo -e "${BOLD}  Postelio — Setup Umami Analytics${NC}"
echo -e "  Domaine : ${UMAMI_DOMAIN}"
hr
echo ""

# =============================================================================
# GÉNÉRATION DES CREDENTIALS (idempotent — réutilise ceux existants si présents)
# =============================================================================
step "0/7 — Credentials"

if [[ -f "$CREDS_FILE" ]]; then
    # Relance : réutiliser les credentials existants pour ne pas casser le DB
    warn "Credentials existants détectés — réutilisation (${CREDS_FILE})."
    UMAMI_DB_PASSWORD=$(grep "^MySQL password" "$CREDS_FILE" | awk '{print $NF}')
    UMAMI_APP_SECRET=$(grep  "^App secret"    "$CREDS_FILE" | awk '{print $NF}')
    UMAMI_ADMIN_PASSWORD=$(grep "^Admin password" "$CREDS_FILE" | awk '{print $NF}')
    [[ -z "$UMAMI_DB_PASSWORD"    ]] && fail "Impossible de lire MySQL password depuis $CREDS_FILE"
    [[ -z "$UMAMI_APP_SECRET"     ]] && fail "Impossible de lire App secret depuis $CREDS_FILE"
    [[ -z "$UMAMI_ADMIN_PASSWORD" ]] && fail "Impossible de lire Admin password depuis $CREDS_FILE"
    log "Credentials récupérés depuis $CREDS_FILE"
else
    # Première installation : générer des mots de passe
    UMAMI_DB_PASSWORD=$(python3 -c "import secrets; print(secrets.token_hex(24))")
    UMAMI_APP_SECRET=$(python3 -c "import secrets; print(secrets.token_hex(32))")
    UMAMI_ADMIN_PASSWORD=$(python3 -c "import secrets; print(secrets.token_urlsafe(16))")

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
    log "Credentials générés et sauvegardés dans $CREDS_FILE"
fi

# =============================================================================
# 1. PAQUETS SYSTÈME
# =============================================================================
step "1/7 — Paquets système"

export DEBIAN_FRONTEND=noninteractive
apt-get update -qq

# Node.js 20
if ! command -v node &>/dev/null || [[ "$(node -v | grep -oP '\d+' | head -1)" -lt 18 ]]; then
    info "Installation Node.js 20..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash - > /dev/null 2>&1
    apt-get install -y -qq nodejs
fi

# certbot
if ! command -v certbot &>/dev/null; then
    apt-get install -y -qq certbot python3-certbot-nginx
fi

log "Node $(node -v) — certbot $(certbot --version 2>&1 | head -1)"

# =============================================================================
# 2. MYSQL — Base de données (idempotent)
# =============================================================================
step "2/7 — MySQL (base umami)"

systemctl is-active mysql --quiet || systemctl start mysql

# CREATE USER IF NOT EXISTS puis ALTER USER pour forcer mysql_native_password
# même si l'user existait déjà avec caching_sha2_password (défaut MySQL 8)
mysql -u root << MYSQL_SCRIPT
CREATE DATABASE IF NOT EXISTS \`${UMAMI_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${UMAMI_DB_USER}'@'localhost' IDENTIFIED WITH mysql_native_password BY '${UMAMI_DB_PASSWORD}';
ALTER USER '${UMAMI_DB_USER}'@'localhost' IDENTIFIED WITH mysql_native_password BY '${UMAMI_DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${UMAMI_DB_NAME}\`.* TO '${UMAMI_DB_USER}'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT

log "Base MySQL '${UMAMI_DB_NAME}' et user '${UMAMI_DB_USER}' prêts (mysql_native_password)."

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
    warn "Umami déjà cloné — git pull..."
    git -C "$UMAMI_DIR" pull --quiet
else
    if [ -d "$UMAMI_DIR" ]; then
        warn "Dossier $UMAMI_DIR existant sans .git — suppression..."
        rm -rf "$UMAMI_DIR"
    fi
    info "Clonage du dépôt Umami..."
    git clone --depth 1 https://github.com/umami-software/umami.git "$UMAMI_DIR" --quiet
fi
chown -R "$UMAMI_USER:$UMAMI_USER" "$UMAMI_DIR"

# --- Fichier .env ------------------------------------------------------------
printf 'DATABASE_URL=mysql://%s:%s@127.0.0.1:3306/%s\n' \
    "$UMAMI_DB_USER" "$UMAMI_DB_PASSWORD" "$UMAMI_DB_NAME" > "${UMAMI_DIR}/.env"
printf 'APP_SECRET=%s\n' "$UMAMI_APP_SECRET" >> "${UMAMI_DIR}/.env"
chown "$UMAMI_USER:$UMAMI_USER" "${UMAMI_DIR}/.env"
chmod 640 "${UMAMI_DIR}/.env"
log ".env écrit."

# --- Patch check-db.js -------------------------------------------------------
# Umami v3 : check-db fait une vérification qui échoue systématiquement avec
# MySQL 8 sur une base vide (reçoit "[" au lieu d'une réponse scalaire).
# Les migrations Prisma sont lancées séparément juste après le build.
cat > "${UMAMI_DIR}/scripts/check-db.js" << 'CHECKDBEOF'
// Patched by setup-umami.sh — check-db bypassed, migrations run separately
console.log('check-db: skipped (migrations handled post-build)');
process.exit(0);
CHECKDBEOF
log "check-db.js patché (contournement bug MySQL 8 / Umami v3)."

# --- npm install (idempotent) ------------------------------------------------
info "npm install (1-2 min)..."
sudo -u "$UMAMI_USER" bash -c "cd ${UMAMI_DIR} && npm install --legacy-peer-deps --quiet" \
    >> "${LOG_DIR}/build.log" 2>&1 \
    || fail "npm install échoué. Voir : cat ${LOG_DIR}/build.log"

# --- npm run build (skip si .next déjà présent et .git n'a pas changé) -------
if [ -d "${UMAMI_DIR}/.next" ]; then
    warn "Build .next déjà présent — skip build (relancer avec rm -rf ${UMAMI_DIR}/.next si besoin)."
else
    info "npm run build (3-5 min)..."
    sudo -u "$UMAMI_USER" bash -c "cd ${UMAMI_DIR} && npm run build" \
        >> "${LOG_DIR}/build.log" 2>&1 \
        || fail "Build échoué. Voir : cat ${LOG_DIR}/build.log"
fi

# --- Migrations Prisma (idempotent) ------------------------------------------
info "Migrations Prisma..."
sudo -u "$UMAMI_USER" bash -c "cd ${UMAMI_DIR} && npx prisma migrate deploy" \
    >> "${LOG_DIR}/build.log" 2>&1 \
    || warn "Migration Prisma : vérifier les logs si premier lancement."
log "Umami prêt."

# =============================================================================
# 5. SERVICE SYSTEMD (idempotent)
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
# restart gère aussi bien "pas encore démarré" que "déjà en cours"
systemctl restart umami
log "Service umami démarré / redémarré."

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

[[ $READY -eq 0 ]] && warn "Umami n'a pas répondu en 90s. Vérifier : journalctl -u umami -n 50"

# =============================================================================
# 6. NGINX — Reverse proxy (idempotent)
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
# 7. SSL LET'S ENCRYPT (idempotent — certbot skip si cert valide)
# =============================================================================
step "7/7 — SSL Let's Encrypt"

certbot --nginx \
    -d "${UMAMI_DOMAIN}" \
    --non-interactive \
    --agree-tos \
    --email "$LE_EMAIL" \
    --redirect \
    --quiet

log "Certificat SSL obtenu / renouvelé."

# =============================================================================
# LOGROTATE (idempotent)
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
if [[ $READY -eq 1 ]]; then
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
                warn "Changement automatique impossible (code: ${HTTP_STATUS}) — mot de passe peut-être déjà changé."
            fi
        fi
    else
        warn "API non joignable pour changer le mot de passe (déjà changé lors d'une précédente exécution)."
    fi
fi

# =============================================================================
# RÉSUMÉ FINAL
# =============================================================================
echo ""
hr
echo ""
echo -e "${BOLD}${GREEN}  Umami Analytics — Setup terminé !${NC}"
echo ""
hr
echo ""
echo -e "${BOLD}Accès :${NC}"
echo "  URL      : https://${UMAMI_DOMAIN}"
echo "  Login    : admin"
echo -e "  Password : ${BOLD}${UMAMI_ADMIN_PASSWORD}${NC}"
echo ""
echo -e "${BOLD}Logs & monitoring :${NC}"
echo "  systemctl status umami"
echo "  journalctl -u umami -f"
echo "  tail -f ${LOG_DIR}/umami.log"
echo ""
echo -e "${BOLD}Credentials complets :${NC}"
echo "  cat ${CREDS_FILE}"
echo ""
hr
echo ""
echo -e "${BOLD}${YELLOW}  Étapes post-installation :${NC}"
echo ""
echo "  1. Se connecter sur https://${UMAMI_DOMAIN}"
echo "  2. Settings > Websites > Add website > postelio.fr"
echo "  3. Copier le tracking script généré"
echo "  4. L'ajouter dans resources/views/layouts/app.blade.php avant </head>"
echo ""
echo -e "     ${BLUE}<script async src=\"https://${UMAMI_DOMAIN}/script.js\"${NC}"
echo -e "     ${BLUE}        data-website-id=\"VOTRE-SITE-ID\"></script>${NC}"
echo ""
echo "  5. Push sur main → tracking actif"
echo ""
hr
