#!/bin/bash
# =============================================================================
# Postelio — Setup Umami Analytics (Ubuntu 22.04)
# Usage : sudo bash deploy/setup-umami.sh
# One command, clé en main — tous les mots de passe générés automatiquement
# =============================================================================
set -euo pipefail

# --- Couleurs (identiques à setup.sh) ----------------------------------------
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; BOLD='\033[1m'; NC='\033[0m'

log()  { echo -e "${GREEN}[✓]${NC} $1"; }
info() { echo -e "${BLUE}[→]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
fail() { echo -e "${RED}[✗] ERREUR : $1${NC}" && exit 1; }
hr()   { echo -e "${BOLD}────────────────────────────────────────────${NC}"; }

# --- Vérifications initiales --------------------------------------------------
[[ $EUID -ne 0 ]] && fail "Lancer en root : sudo bash deploy/setup-umami.sh"
[[ $(lsb_release -rs) != "22.04" ]] && warn "Testé sur Ubuntu 22.04 uniquement."

hr
echo -e "${BOLD}  Postelio — Setup Umami Analytics${NC}"
echo -e "  Tous les mots de passe sont générés automatiquement."
hr
echo ""

# --- Collecte des paramètres --------------------------------------------------
echo -e "${BOLD}Paramètres requis :${NC}"
echo ""

read -rp "  Sous-domaine Umami (ex: analytics.postelio.fr) : " UMAMI_DOMAIN
[[ -z "$UMAMI_DOMAIN" ]] && fail "Sous-domaine requis."

read -rp "  Email Let's Encrypt (pour le certificat SSL) : " LE_EMAIL
[[ -z "$LE_EMAIL" ]] && fail "Email requis."

echo ""
info "Génération des credentials..."

# =============================================================================
# GÉNÉRATION DES CREDENTIALS (aucun mot de passe à choisir)
# =============================================================================
UMAMI_DB_PASSWORD=$(openssl rand -base64 32 | tr -d '/+=\n' | cut -c1-36)
UMAMI_DB_USER="umami"
UMAMI_DB_NAME="umami"
UMAMI_APP_SECRET=$(openssl rand -hex 32)
# Mot de passe admin : 20 chars lisibles — openssl évite le SIGPIPE de tr+head
UMAMI_ADMIN_PASSWORD=$(openssl rand -base64 24 | tr -d '/+=\n' | cut -c1-20)

# Fichier de sauvegarde des credentials (root only)
CREDS_FILE="/root/umami-credentials.txt"

cat > "$CREDS_FILE" << EOF
# ============================================================
# Umami Analytics — Credentials
# Généré le $(date '+%d/%m/%Y à %H:%M:%S')
# CONSERVER CE FICHIER EN LIEU SÛR
# ============================================================

URL             : https://${UMAMI_DOMAIN}

Admin login     : admin
Admin password  : ${UMAMI_ADMIN_PASSWORD}

PostgreSQL DB   : ${UMAMI_DB_NAME}
PostgreSQL user : ${UMAMI_DB_USER}
PostgreSQL pass : ${UMAMI_DB_PASSWORD}
App secret      : ${UMAMI_APP_SECRET}

# Snippet à ajouter dans layouts/app.blade.php (après génération du site ID) :
# <script async src="https://${UMAMI_DOMAIN}/script.js" data-website-id="VOTRE-SITE-ID"></script>
EOF
chmod 600 "$CREDS_FILE"
log "Credentials sauvegardés dans $CREDS_FILE"

# --- Constantes ---------------------------------------------------------------
UMAMI_DIR="/opt/umami"
UMAMI_USER="umami"
UMAMI_PORT=3001
LOG_DIR="/var/log/umami"
NODE_VERSION="20"

echo ""

# =============================================================================
# 1. PAQUETS SYSTÈME
# =============================================================================
hr; info "1/8 — Paquets système"; hr

export DEBIAN_FRONTEND=noninteractive
apt-get update -qq

# Node.js 20 (peut déjà être présent via setup.sh)
if ! command -v node &>/dev/null || [[ $(node -v | grep -oP '\d+' | head -1) -lt 18 ]]; then
    info "Installation de Node.js ${NODE_VERSION}..."
    curl -fsSL "https://deb.nodesource.com/setup_${NODE_VERSION}.x" | bash - > /dev/null 2>&1
    apt-get install -y -qq nodejs
fi

# PostgreSQL
if ! command -v psql &>/dev/null; then
    info "Installation de PostgreSQL..."
    apt-get install -y -qq postgresql postgresql-contrib
fi

# Outils
apt-get install -y -qq git curl wget certbot python3-certbot-nginx > /dev/null 2>&1

log "Paquets installés. Node $(node -v) | psql $(psql --version | awk '{print $3}')"

# =============================================================================
# 2. POSTGRESQL — Base de données Umami
# =============================================================================
hr; info "2/8 — PostgreSQL"; hr

systemctl enable postgresql --quiet
systemctl start postgresql

# Création de la DB et de l'utilisateur via sudo -u postgres
sudo -u postgres psql -tc "SELECT 1 FROM pg_roles WHERE rolname='${UMAMI_DB_USER}'" \
    | grep -q 1 \
    || sudo -u postgres psql -c "CREATE USER ${UMAMI_DB_USER} WITH PASSWORD '${UMAMI_DB_PASSWORD}';"

sudo -u postgres psql -tc "SELECT 1 FROM pg_database WHERE datname='${UMAMI_DB_NAME}'" \
    | grep -q 1 \
    || sudo -u postgres psql -c "CREATE DATABASE ${UMAMI_DB_NAME} OWNER ${UMAMI_DB_USER} ENCODING 'UTF8';"

sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE ${UMAMI_DB_NAME} TO ${UMAMI_DB_USER};" > /dev/null

log "PostgreSQL — base '${UMAMI_DB_NAME}' créée."

# =============================================================================
# 3. UTILISATEUR SYSTÈME
# =============================================================================
hr; info "3/8 — Utilisateur système 'umami'"; hr

if ! id "$UMAMI_USER" &>/dev/null; then
    useradd -r -m -s /bin/bash -d "$UMAMI_DIR" "$UMAMI_USER"
    log "Utilisateur '${UMAMI_USER}' créé."
else
    log "Utilisateur '${UMAMI_USER}' déjà existant."
fi

mkdir -p "$LOG_DIR"
chown "$UMAMI_USER:$UMAMI_USER" "$LOG_DIR"

# =============================================================================
# 4. INSTALLATION D'UMAMI
# =============================================================================
hr; info "4/8 — Installation d'Umami (Next.js)"; hr

if [ -d "$UMAMI_DIR/.git" ]; then
    warn "Umami déjà cloné — mise à jour..."
    sudo -u "$UMAMI_USER" git -C "$UMAMI_DIR" pull --quiet
else
    info "Clonage du dépôt Umami..."
    git clone --depth 1 https://github.com/umami-software/umami.git "$UMAMI_DIR" --quiet
    chown -R "$UMAMI_USER:$UMAMI_USER" "$UMAMI_DIR"
fi

# --- Fichier de configuration .env.local -------------------------------------
cat > "${UMAMI_DIR}/.env" << ENVEOF
DATABASE_URL=postgresql://${UMAMI_DB_USER}:${UMAMI_DB_PASSWORD}@127.0.0.1:5432/${UMAMI_DB_NAME}
APP_SECRET=${UMAMI_APP_SECRET}
ENVEOF

chown "$UMAMI_USER:$UMAMI_USER" "${UMAMI_DIR}/.env"
chmod 640 "${UMAMI_DIR}/.env"

# --- Build -------------------------------------------------------------------
info "npm install..."
sudo -u "$UMAMI_USER" bash -c "cd $UMAMI_DIR && npm install --legacy-peer-deps --quiet" \
    >> "${LOG_DIR}/build.log" 2>&1 || fail "npm install a échoué. Voir ${LOG_DIR}/build.log"

info "npm run build (Next.js — peut prendre 2-3 minutes)..."
sudo -u "$UMAMI_USER" bash -c "cd $UMAMI_DIR && npm run build" \
    >> "${LOG_DIR}/build.log" 2>&1 || fail "Build échoué. Voir ${LOG_DIR}/build.log"

log "Umami compilé avec succès."

# =============================================================================
# 5. SERVICE SYSTEMD
# =============================================================================
hr; info "5/8 — Service systemd 'umami'"; hr

cat > /etc/systemd/system/umami.service << SVCEOF
[Unit]
Description=Umami Analytics
After=network.target postgresql.service
Wants=postgresql.service

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
# Hardening
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

# Attendre que le serveur soit prêt (max 60s)
info "Attente du démarrage d'Umami..."
for i in $(seq 1 60); do
    if curl -sf "http://127.0.0.1:${UMAMI_PORT}/api/heartbeat" > /dev/null 2>&1; then
        log "Umami démarré (${i}s)"
        break
    fi
    sleep 1
    if [ "$i" -eq 60 ]; then
        warn "Timeout — Umami met plus de 60s à démarrer. Vérifier : journalctl -u umami -n 50"
    fi
done

# =============================================================================
# 6. NGINX — Reverse proxy pour Umami
# =============================================================================
hr; info "6/8 — Nginx (reverse proxy)"; hr

cat > "/etc/nginx/sites-available/umami" << NGINXEOF
# Umami Analytics — HTTP (certbot upgarde vers HTTPS automatiquement)
server {
    listen 80;
    server_name ${UMAMI_DOMAIN};

    # Sécurité
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Proxy vers Umami (Next.js sur 127.0.0.1:${UMAMI_PORT})
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
        proxy_connect_timeout 10;
        client_max_body_size 10M;
    }

    # Next.js static assets (cache agressif)
    location /_next/static/ {
        proxy_pass http://127.0.0.1:${UMAMI_PORT};
        proxy_set_header Host \$host;
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Bloquer l'accès aux fichiers sensibles
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
hr; info "7/8 — SSL Let's Encrypt pour ${UMAMI_DOMAIN}"; hr

certbot --nginx \
    -d "${UMAMI_DOMAIN}" \
    --non-interactive \
    --agree-tos \
    --email "$LE_EMAIL" \
    --redirect \
    --quiet

log "Certificat SSL obtenu et Nginx rechargé."

# =============================================================================
# 8. LOGROTATE
# =============================================================================
hr; info "8/8 — Logrotate"; hr

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

log "Logrotate configuré."

# =============================================================================
# AJOUT DU MOT DE PASSE ADMIN DANS LE RÉSUMÉ FINAL
# (Umami crée le compte admin au 1er démarrage, on le configure via l'API)
# =============================================================================

# Changer le mot de passe admin par défaut via l'API Umami
# Umami démarre avec admin/umami — on change immédiatement
info "Configuration du mot de passe admin..."

# Attendre que l'API soit vraiment prête
sleep 5

# Obtenir le token JWT avec le login par défaut
ADMIN_TOKEN=$(curl -sf -X POST "http://127.0.0.1:${UMAMI_PORT}/api/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"username":"admin","password":"umami"}' \
    2>/dev/null | grep -oP '"token":"\K[^"]+' || echo "")

if [[ -n "$ADMIN_TOKEN" ]]; then
    # Récupérer le userId admin
    ADMIN_USER_ID=$(curl -sf "http://127.0.0.1:${UMAMI_PORT}/api/me" \
        -H "Authorization: Bearer ${ADMIN_TOKEN}" \
        2>/dev/null | grep -oP '"id":"\K[^"]+' | head -1 || echo "")

    if [[ -n "$ADMIN_USER_ID" ]]; then
        # Changer le mot de passe
        HTTP_STATUS=$(curl -sf -o /dev/null -w "%{http_code}" \
            -X POST "http://127.0.0.1:${UMAMI_PORT}/api/users/${ADMIN_USER_ID}/password" \
            -H "Authorization: Bearer ${ADMIN_TOKEN}" \
            -H "Content-Type: application/json" \
            -d "{\"currentPassword\":\"umami\",\"newPassword\":\"${UMAMI_ADMIN_PASSWORD}\"}" \
            2>/dev/null || echo "0")

        if [[ "$HTTP_STATUS" == "200" ]]; then
            log "Mot de passe admin changé avec succès."
        else
            warn "Changement de mot de passe via API impossible (code: ${HTTP_STATUS})."
            warn "→ Se connecter manuellement avec admin/umami puis changer le mot de passe."
            warn "→ Le mot de passe généré est dans $CREDS_FILE"
        fi
    fi
else
    warn "Impossible de se connecter à l'API Umami pour changer le mot de passe."
    warn "→ Connexion initiale : admin / umami — puis changer dans Profile > Password"
    warn "→ Utiliser le mot de passe généré : voir $CREDS_FILE"
fi

# =============================================================================
# RÉSUMÉ FINAL
# =============================================================================
hr
echo ""
echo -e "${BOLD}${GREEN}  ✅ Umami Analytics — Setup terminé !${NC}"
echo ""
hr
echo ""
echo -e "${BOLD}Accès :${NC}"
echo "  URL        : https://${UMAMI_DOMAIN}"
echo "  Login      : admin"
echo -e "  Password   : ${BOLD}${UMAMI_ADMIN_PASSWORD}${NC}"
echo ""
echo -e "${BOLD}Base de données PostgreSQL :${NC}"
echo "  DB         : ${UMAMI_DB_NAME}"
echo "  User       : ${UMAMI_DB_USER}"
echo "  Password   : ${UMAMI_DB_PASSWORD}"
echo ""
echo -e "${BOLD}Fichier credentials (root seulement) :${NC}"
echo "  cat $CREDS_FILE"
echo ""
echo -e "${BOLD}Service systemd :${NC}"
echo "  systemctl status umami"
echo "  journalctl -u umami -f"
echo "  tail -f ${LOG_DIR}/umami.log"
echo ""
hr
echo ""
echo -e "${BOLD}${YELLOW}  Étapes post-installation :${NC}"
echo ""
echo -e "  ${BOLD}1.${NC} Se connecter sur https://${UMAMI_DOMAIN}"
echo -e "  ${BOLD}2.${NC} Aller dans Settings → Websites → Add website"
echo -e "  ${BOLD}3.${NC} Ajouter : postelio.fr"
echo -e "  ${BOLD}4.${NC} Copier le tracking script généré"
echo -e "  ${BOLD}5.${NC} L'ajouter dans ${BOLD}resources/views/layouts/app.blade.php${NC}"
echo "     juste avant </head> :"
echo ""
echo -e "     ${BLUE}<script async src=\"https://${UMAMI_DOMAIN}/script.js\"${NC}"
echo -e "     ${BLUE}        data-website-id=\"VOTRE-SITE-ID\"></script>${NC}"
echo ""
echo -e "  ${BOLD}6.${NC} Déployer Postelio (push sur main) → tracking actif"
echo ""
hr
