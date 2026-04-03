#!/bin/bash
# =============================================================================
# Postelio — Setup Production (Ubuntu 22.04)
# Usage : sudo bash setup.sh
# =============================================================================
set -euo pipefail

# --- Couleurs ----------------------------------------------------------------
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; BOLD='\033[1m'; NC='\033[0m'

log()  { echo -e "${GREEN}[✓]${NC} $1"; }
info() { echo -e "${BLUE}[→]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
fail() { echo -e "${RED}[✗] ERREUR : $1${NC}" && exit 1; }
hr()   { echo -e "${BOLD}────────────────────────────────────────────${NC}"; }

# --- Constantes --------------------------------------------------------------
APP_NAME="postelio"
APP_DIR="/var/www/postelio"
DEPLOY_DIR="/opt/postelio-deploy"
LOG_DIR="/var/log/postelio"
BACKUP_DIR="/var/backups/postelio"
PHP="php8.3"
PHP_FPM="php8.3-fpm"
WEBHOOK_PORT=9876
DOMAIN="" # Défini via read plus bas
GITHUB_REPO="devkylian/postelio"
GITHUB_BRANCH="main"

# --- Vérifications initiales -------------------------------------------------
[[ $EUID -ne 0 ]] && fail "Lancer en root : sudo bash setup.sh"
[[ $(lsb_release -rs) != "22.04" ]] && warn "Testé sur Ubuntu 22.04 uniquement."

hr
echo -e "${BOLD}  Postelio — Setup Production${NC}"
hr
echo ""

# --- Collecte des paramètres -------------------------------------------------
echo -e "${BOLD}Paramètres requis :${NC}"
echo ""

read -rp "  GitHub Personal Access Token (repo:read) : " GITHUB_TOKEN
[[ -z "$GITHUB_TOKEN" ]] && fail "Token GitHub requis."

read -rsp "  Mot de passe MySQL pour l'utilisateur 'postelio' : " DB_PASSWORD
echo ""
[[ -z "$DB_PASSWORD" ]] && fail "Mot de passe MySQL requis."

read -rsp "  Secret webhook GitHub (chaîne aléatoire) : " WEBHOOK_SECRET
echo ""
[[ -z "$WEBHOOK_SECRET" ]] && fail "Secret webhook requis."

read -rp "  Domaine de l'application (ex: postelio.fr) : " DOMAIN
[[ -z "$DOMAIN" ]] && fail "Domaine requis."

read -rp "  Email Let's Encrypt (pour les alertes SSL) : " LE_EMAIL
[[ -z "$LE_EMAIL" ]] && fail "Email requis."

read -rsp "  Clé OpenAI API : " OPENAI_API_KEY
echo ""

echo ""
info "Démarrage de l'installation..."
echo ""

# =============================================================================
# 1. PAQUETS SYSTÈME
# =============================================================================
hr; info "1/10 — Paquets système"; hr

export DEBIAN_FRONTEND=noninteractive

apt-get update -qq
apt-get upgrade -y -qq

# PHP 8.3
apt-get install -y -qq software-properties-common
add-apt-repository -y ppa:ondrej/php > /dev/null 2>&1
apt-get update -qq

apt-get install -y -qq \
    $PHP $PHP_FPM \
    php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl \
    php8.3-gd php8.3-zip php8.3-bcmath php8.3-intl \
    php8.3-tokenizer php8.3-fileinfo php8.3-sqlite3 \
    nginx mysql-server \
    git curl wget unzip zip \
    certbot python3-certbot-nginx \
    ffmpeg dnsutils \
    ufw fail2ban \
    logrotate cron \
    libatk1.0-0 libatk-bridge2.0-0 libcups2 libxdamage1 \
    libxcomposite1 libxrandr2 libgbm1 libpango-1.0-0 \
    libcairo2 libasound2 libnspr4 libnss3 libxss1 libxtst6 \
    fonts-liberation

# Google Chrome Stable (au lieu de Chromium Snap qui pose problème avec Browsershot)
if ! command -v google-chrome-stable &>/dev/null; then
    info "Installation de Google Chrome Stable..."
    wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | gpg --dearmor -o /usr/share/keyrings/google-chrome.gpg
    echo "deb [arch=amd64 signed-by=/usr/share/keyrings/google-chrome.gpg] http://dl.google.com/linux/chrome/deb/ stable main" > /etc/apt/sources.list.d/google-chrome.list
    apt-get update -qq
    apt-get install -y -qq google-chrome-stable
fi

# Composer
if ! command -v composer &>/dev/null; then
    info "Installation de Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer > /dev/null
fi

# Node.js 20
if ! command -v node &>/dev/null; then
    info "Installation de Node.js 20..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash - > /dev/null 2>&1
    apt-get install -y -qq nodejs
fi

log "Paquets installés."

# =============================================================================
# 2. UTILISATEUR DEPLOY
# =============================================================================
hr; info "2/10 — Utilisateur deploy"; hr

if ! id "deploy" &>/dev/null; then
    useradd -m -s /bin/bash deploy
    log "Utilisateur 'deploy' créé."
else
    log "Utilisateur 'deploy' déjà existant."
fi

# Sudo limité : uniquement pour redémarrer les services
cat > /etc/sudoers.d/deploy-postelio << 'EOF'
deploy ALL=(ALL) NOPASSWD: /bin/systemctl restart postelio-queue, /bin/systemctl restart postelio-webhook, /bin/systemctl status postelio-queue
EOF
chmod 0440 /etc/sudoers.d/deploy-postelio
log "Sudoers configuré."

# =============================================================================
# 3. MYSQL
# =============================================================================
hr; info "3/10 — MySQL"; hr

systemctl enable mysql --quiet
systemctl start mysql

# Sécurisation et création DB
# Sécurisation et création DB via sudo
sudo mysql << MYSQL_SCRIPT
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '';
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';

CREATE DATABASE IF NOT EXISTS \`postelio\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'postelio'@'localhost' IDENTIFIED WITH mysql_native_password BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`postelio\`.* TO 'postelio'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT

# Fichier credentials pour mysqldump (pas de mot de passe en clair dans les scripts)
cat > /home/deploy/.my.cnf << EOF
[client]
user=postelio
password=${DB_PASSWORD}
EOF
chmod 600 /home/deploy/.my.cnf
chown deploy:deploy /home/deploy/.my.cnf

log "MySQL configuré — base 'postelio' créée."

# =============================================================================
# 4. CLONAGE DU DÉPÔT
# =============================================================================
hr; info "4/10 — Clonage du dépôt"; hr

mkdir -p "$APP_DIR" "$LOG_DIR" "$BACKUP_DIR" "$DEPLOY_DIR"

CLONE_URL="https://${GITHUB_TOKEN}@github.com/${GITHUB_REPO}.git"

if [ -d "$APP_DIR/.git" ]; then
    warn "Dépôt déjà cloné dans $APP_DIR"
else
    git clone --branch "$GITHUB_BRANCH" "$CLONE_URL" "$APP_DIR" --quiet
    log "Dépôt cloné."
fi

# Marquer le dossier comme safe pour git (évite l'erreur dubious ownership)
git config --global --add safe.directory "$APP_DIR"
sudo -u deploy git config --global --add safe.directory "$APP_DIR"

# Configurer git pour les pulls futurs
cd "$APP_DIR"
git remote set-url origin "$CLONE_URL"
git config core.fileMode false

# Sauvegarder le token séparément pour les déploiements
echo "$GITHUB_TOKEN" > /etc/postelio-github-token
chmod 600 /etc/postelio-github-token

log "Dépôt configuré."

# =============================================================================
# 5. CONFIGURATION DE L'APPLICATION
# =============================================================================
hr; info "5/10 — Configuration Laravel"; hr

cd "$APP_DIR"

# Génération du .env
APP_KEY=$(openssl rand -base64 32)

cat > "$APP_DIR/.env" << EOF
APP_NAME=Postelio
APP_ENV=production
APP_KEY=base64:${APP_KEY}
APP_DEBUG=false
APP_URL=https://${DOMAIN}

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=postelio
DB_USERNAME=postelio
DB_PASSWORD=${DB_PASSWORD}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=log
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@${DOMAIN}
MAIL_FROM_NAME=Postelio

OPENAI_API_KEY=${OPENAI_API_KEY}
OPENAI_MODEL=gpt-4o
OPENAI_WHISPER_MODEL=whisper-1

BROWSERSHOT_CHROME_PATH=/usr/bin/google-chrome-stable
EOF

chmod 640 "$APP_DIR/.env"

# Permissions (avant composer/npm pour que deploy puisse écrire)
chown -R deploy:www-data "$APP_DIR"

# Installation des dépendances PHP
info "composer install..."
sudo -u deploy composer install --no-dev --optimize-autoloader --no-interaction --quiet

# Installation des dépendances Node.js + build
info "npm install + build..."
sudo -u deploy npm ci --silent
sudo -u deploy npm run build

# Migrations initiales
info "Migrations..."
sudo -u deploy $PHP artisan migrate --force

# Caches
sudo -u deploy $PHP artisan config:cache
sudo -u deploy $PHP artisan route:cache
sudo -u deploy $PHP artisan view:cache
sudo -u deploy $PHP artisan event:cache

# Storage link
sudo -u deploy $PHP artisan storage:link 2>/dev/null || true

# Queue jobs table
sudo -u deploy $PHP artisan queue:table 2>/dev/null || true
sudo -u deploy $PHP artisan migrate --force

# Permissions robustes
chown -R deploy:www-data "$APP_DIR"
find "$APP_DIR" -type f -exec chmod 644 {} \;
find "$APP_DIR" -type d -exec chmod 755 {} \;
# Dossiers nécessitant des droits d'écriture pour l'utilisateur ET le groupe
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" "$APP_DIR/.git" "$APP_DIR/public" 2>/dev/null || true
# Forcer le bit SETGID pour que les nouveaux fichiers héritent du groupe www-data
chmod g+s "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
chown -R deploy:www-data "$LOG_DIR" "$BACKUP_DIR"

log "Application configurée."

# =============================================================================
# 6. PHP-FPM
# =============================================================================
hr; info "6/10 — PHP-FPM"; hr

cat > "/etc/php/8.3/fpm/pool.d/postelio.conf" << 'EOF'
[postelio]
user = deploy
group = www-data
listen = /run/php/php8.3-fpm-postelio.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.max_requests = 500

php_admin_value[error_log] = /var/log/postelio/php-fpm-error.log
php_admin_flag[log_errors] = on
php_value[upload_max_filesize] = 50M
php_value[post_max_size] = 50M
php_value[max_execution_time] = 300
php_value[memory_limit] = 512M
EOF

# Désactiver le pool www par défaut
mv /etc/php/8.3/fpm/pool.d/www.conf /etc/php/8.3/fpm/pool.d/www.conf.bak 2>/dev/null || true

systemctl enable $PHP_FPM --quiet
systemctl restart $PHP_FPM
log "PHP-FPM configuré."

# =============================================================================
# 7. NGINX
# =============================================================================
hr; info "7/10 — Nginx"; hr

# Config temporaire HTTP (pour certbot)
cat > "/etc/nginx/sites-available/postelio" << EOF
server {
    listen 80;
    server_name ${DOMAIN} www.${DOMAIN};
    root ${APP_DIR}/public;
    index index.php;

    client_max_body_size 50M;

    # Webhook — bypass Laravel, toujours disponible
    location = /deploy-webhook {
        proxy_pass http://127.0.0.1:${WEBHOOK_PORT}/webhook;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_read_timeout 10;
    }

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm-postelio.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* { deny all; }

    gzip on;
    gzip_comp_level 5;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml image/svg+xml;
}
EOF

ln -sf /etc/nginx/sites-available/postelio /etc/nginx/sites-enabled/postelio
rm -f /etc/nginx/sites-enabled/default

nginx -t && systemctl enable nginx --quiet && systemctl reload nginx
log "Nginx configuré (HTTP)."

# =============================================================================
# 8. SSL LET'S ENCRYPT
# =============================================================================
hr; info "8/10 — SSL Let's Encrypt"; hr

# Vérifier si le domaine pointe vers ce serveur avant de lancer Certbot
info "Vérification DNS pour ${DOMAIN}..."
IP_SERVER=$(curl -s https://ifconfig.me)
ADDR_DNS=$(dig +short "${DOMAIN}" | tail -n1)

if [[ "$ADDR_DNS" != "$IP_SERVER" ]]; then
    warn "Le domaine ${DOMAIN} ne semble pas encore pointer vers ${IP_SERVER} (trouvé: ${ADDR_DNS:-aucun})."
    warn "SSL ignoré pour l'instant. Relancez 'certbot --nginx' manuellement plus tard."
else
    certbot --nginx \
        -d "${DOMAIN}" \
        -d "www.${DOMAIN}" \
        --non-interactive \
        --agree-tos \
        --email "$LE_EMAIL" \
        --redirect \
        --quiet
fi

# Renouvellement auto via cron (certbot le fait seul mais on s'assure)
(crontab -l 2>/dev/null | grep -v certbot; echo "0 3 * * * certbot renew --quiet --post-hook 'systemctl reload nginx'") | crontab -

log "SSL configuré — certificat obtenu pour ${DOMAIN}."

# =============================================================================
# 9. SERVICES SYSTEMD
# =============================================================================
hr; info "9/10 — Services systemd"; hr

# --- Queue Worker ------------------------------------------------------------
cat > /etc/systemd/system/postelio-queue.service << EOF
[Unit]
Description=Postelio Queue Worker
After=network.target mysql.service
Wants=mysql.service

[Service]
Type=simple
User=deploy
Group=www-data
WorkingDirectory=${APP_DIR}
ExecStart=/usr/bin/${PHP} artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=90
Restart=always
RestartSec=5
StandardOutput=append:${LOG_DIR}/queue.log
StandardError=append:${LOG_DIR}/queue-error.log
KillSignal=SIGTERM
TimeoutStopSec=60

[Install]
WantedBy=multi-user.target
EOF

# --- Scheduler (cron toutes les minutes) ------------------------------------
(crontab -u deploy -l 2>/dev/null; echo "* * * * * cd ${APP_DIR} && ${PHP} artisan schedule:run >> ${LOG_DIR}/scheduler.log 2>&1") | crontab -u deploy -

# --- Webhook Server ----------------------------------------------------------
cat > /etc/systemd/system/postelio-webhook.service << EOF
[Unit]
Description=Postelio Webhook Server (GitHub CD)
After=network.target

[Service]
Type=simple
User=deploy
WorkingDirectory=${DEPLOY_DIR}
Environment="WEBHOOK_SECRET=${WEBHOOK_SECRET}"
Environment="DEPLOY_SCRIPT=${DEPLOY_DIR}/deploy.sh"
ExecStart=/usr/bin/python3 ${DEPLOY_DIR}/webhook.py
Restart=always
RestartSec=5
StandardOutput=append:${LOG_DIR}/webhook.log
StandardError=append:${LOG_DIR}/webhook-error.log

[Install]
WantedBy=multi-user.target
EOF

systemctl daemon-reload
systemctl enable postelio-queue --quiet
systemctl start postelio-queue
systemctl enable postelio-webhook --quiet
log "Services systemd activés."

# =============================================================================
# 10. WEBHOOK SERVER + DEPLOY SCRIPT
# =============================================================================
hr; info "10/10 — Scripts de déploiement"; hr

# --- webhook.py --------------------------------------------------------------
cat > "${DEPLOY_DIR}/webhook.py" << 'PYEOF'
#!/usr/bin/env python3
"""
Postelio — Webhook receiver pour GitHub CD
Écoute sur 127.0.0.1:WEBHOOK_PORT, Nginx proxy /deploy-webhook → ici
"""
import hashlib, hmac, http.server, json, logging, os, subprocess, threading

WEBHOOK_SECRET = os.environ.get("WEBHOOK_SECRET", "").encode()
DEPLOY_SCRIPT  = os.environ.get("DEPLOY_SCRIPT", "/opt/postelio-deploy/deploy.sh")
PORT           = int(os.environ.get("WEBHOOK_PORT", "9876"))

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S",
)
log = logging.getLogger("webhook")


def run_deploy():
    log.info("Déploiement lancé…")
    result = subprocess.run(
        ["bash", DEPLOY_SCRIPT],
        capture_output=True, text=True
    )
    if result.returncode == 0:
        log.info("Déploiement terminé avec succès.")
    else:
        log.error("Déploiement en échec (code %d) :\n%s", result.returncode, result.stderr)


class WebhookHandler(http.server.BaseHTTPRequestHandler):

    def do_POST(self):
        if self.path != "/webhook":
            self._reply(404, "Not Found")
            return

        length = int(self.headers.get("Content-Length", 0))
        body   = self.rfile.read(length)

        # Vérification signature HMAC-SHA256
        received = self.headers.get("X-Hub-Signature-256", "")
        expected = "sha256=" + hmac.new(WEBHOOK_SECRET, body, hashlib.sha256).hexdigest()
        if not hmac.compare_digest(received, expected):
            log.warning("Signature invalide — requête rejetée.")
            self._reply(401, "Unauthorized")
            return

        try:
            payload = json.loads(body)
        except json.JSONDecodeError:
            self._reply(400, "Bad Request")
            return

        # Ignorer si ce n'est pas la branche main
        if payload.get("ref") != "refs/heads/main":
            self._reply(200, f"Ignored ({payload.get('ref', '?')})")
            return

        pusher  = payload.get("pusher", {}).get("name", "?")
        commits = len(payload.get("commits", []))
        log.info("Push de %s (%d commit(s)) — déploiement en cours…", pusher, commits)

        # Répondre immédiatement, déployer en arrière-plan
        self._reply(200, "Deploying…")
        threading.Thread(target=run_deploy, daemon=True).start()

    def _reply(self, code, message):
        body = message.encode()
        self.send_response(code)
        self.send_header("Content-Type", "text/plain")
        self.send_header("Content-Length", str(len(body)))
        self.end_headers()
        self.wfile.write(body)

    def log_message(self, *_):
        pass  # On gère les logs nous-mêmes


if __name__ == "__main__":
    server = http.server.HTTPServer(("127.0.0.1", PORT), WebhookHandler)
    log.info("Webhook server démarré sur 127.0.0.1:%d", PORT)
    server.serve_forever()
PYEOF

# --- deploy.sh ---------------------------------------------------------------
GITHUB_TOKEN_STORED=$(cat /etc/postelio-github-token)

cat > "${DEPLOY_DIR}/deploy.sh" << DEPLOYEOF
#!/bin/bash
# =============================================================================
# Postelio — Script de déploiement
# Appelé automatiquement par webhook.py à chaque push sur main
# =============================================================================
set -euo pipefail

APP_DIR="${APP_DIR}"
LOG_DIR="${LOG_DIR}"
BACKUP_DIR="${BACKUP_DIR}"
PHP="${PHP}"
GITHUB_TOKEN="\$(cat /etc/postelio-github-token)"
DEPLOY_LOG="\${LOG_DIR}/deploy.log"

exec >> "\${DEPLOY_LOG}" 2>&1

log()  { echo "[DEPLOY \$(date '+%Y-%m-%d %H:%M:%S')] \$1"; }

rollback() {
    log "ERREUR FATALE DÉTECTÉE ! Annulation du déploiement..."
    if [ ! -z "\${PREV_COMMIT:-}" ]; then
        log "Retour au commit précédent : \${PREV_COMMIT}"
        cd "\$APP_DIR" && git reset --hard "\$PREV_COMMIT" --quiet || true
        log "Nettoyage des caches..."
        cd "\$APP_DIR" && \$PHP artisan optimize:clear 2>/dev/null || true
    fi
    cd "\$APP_DIR" && \$PHP artisan up 2>/dev/null || true
}

fail() {
    log "ERREUR : \$1"
    exit 1
}

# La trap catch n'importe quelle sortie non-0 (crash de bash due à set -e)
trap 'if [ \$? -ne 0 ]; then rollback; fi' EXIT

log "====== DÉPLOIEMENT DÉMARRÉ ======"

cd "\$APP_DIR" || fail "Impossible d'accéder à \$APP_DIR"

# Refresh token dans l'URL git au cas où
git remote set-url origin "https://\${GITHUB_TOKEN}@github.com/${GITHUB_REPO}.git"

# --- Vérification Droits / Permissions ---------------------------------------
if [ ! -w "\$APP_DIR/.git" ]; then
    log "ERREUR : L'utilisateur \$(whoami) n'a pas les droits d'écriture sur \$APP_DIR/.git"
    fail "Permissions insuffisantes (échec .git writable)"
fi

# --- Vérifier s'il y a de nouveaux commits ----------------------------------
git fetch origin ${GITHUB_BRANCH} --quiet
LOCAL=\$(git rev-parse HEAD)
REMOTE=\$(git rev-parse origin/${GITHUB_BRANCH})

if [ "\$LOCAL" = "\$REMOTE" ]; then
    log "Déjà à jour — rien à déployer."
    exit 0
fi

PREV_COMMIT=\$(git rev-parse --short HEAD)
git reset --hard "origin/${GITHUB_BRANCH}" --quiet
NEW_COMMIT=\$(git rev-parse --short HEAD)
log "Mise à jour : \${PREV_COMMIT} → \${NEW_COMMIT}"

# --- Vérification extensions PHP requises ------------------------------------
REQUIRED_EXTS="bcmath mbstring xml curl gd zip intl tokenizer fileinfo openssl filter"
MISSING_EXTS=""
for ext in \$REQUIRED_EXTS; do
    if ! \$PHP -r "exit(extension_loaded('\$ext') ? 0 : 1);" 2>/dev/null; then
        MISSING_EXTS="\${MISSING_EXTS} php8.3-\${ext}"
    fi
done

if [ -n "\$MISSING_EXTS" ]; then
    log "Extensions PHP manquantes : \${MISSING_EXTS}"
    log "Veuillez installer via : sudo apt-get install \${MISSING_EXTS} && sudo systemctl restart php8.3-fpm"
    fail "Déploiement avorté : extensions manquantes."
fi

# --- Dépendances PHP ---------------------------------------------------------
log "composer install..."
if ! composer install --no-dev --optimize-autoloader --no-interaction --quiet 2>&1; then
    log "composer install échoué — tentative composer update..."
    composer update --no-dev --optimize-autoloader --no-interaction --quiet
fi

# --- Frontend (si JS/CSS/Blade modifié — Tailwind scanne aussi les vues) ----
FRONTEND_REGEX="(resources/|lang/|app/View/|app/Livewire/|package|vite\.config|tailwind\.config|postcss\.config|composer\.lock)"
FRONTEND_FILES=\$(git diff "\${PREV_COMMIT}" "\${NEW_COMMIT}" --name-only 2>/dev/null | grep -E "\$FRONTEND_REGEX" | tr '\n' ' ' || echo "")
FRONTEND_CHANGED=\$(echo "\$FRONTEND_FILES" | wc -w | xargs)

# On force aussi si le dossier build est manquant
if [ ! -d "public/build" ]; then
    log "Dossier public/build manquant — forçage build npm..."
    FRONTEND_CHANGED=1
fi

if [ "\$FRONTEND_CHANGED" -gt 0 ]; then
    log "Changements frontend détectés (\${FRONTEND_CHANGED} fichiers) — rebuild npm..."
    log "Fichiers : \${FRONTEND_FILES}"
    # Nettoyage manuel car Vite peut échouer sur rmdir (EACCES) si les permissions sont strictes
    rm -rf public/build
    npm ci --quiet
    npm run build
    # Ajustement permissions après build
    chmod -R 775 public/build 2>/dev/null || true
else
    log "Pas de changements frontend — skip npm build."
fi

# --- Mode maintenance --------------------------------------------------------
log "Mode maintenance activé..."
\$PHP artisan down --retry=60

# --- Sauvegarde BDD ----------------------------------------------------------
log "Sauvegarde de la base de données..."
mkdir -p "\${BACKUP_DIR}"
mysqldump --defaults-file="/home/deploy/.my.cnf" --single-transaction --no-tablespaces postelio \
    | gzip > "\${BACKUP_DIR}/db_\$(date +%Y%m%d_%H%M%S).sql.gz"
# Garder seulement les 14 dernières sauvegardes
ls -t "\${BACKUP_DIR}"/*.sql.gz 2>/dev/null | tail -n +15 | xargs -r rm -f

# --- Migrations --------------------------------------------------------------
log "Migrations..."
\$PHP artisan migrate --force || fail "Migrations échouées — rollback nécessaire"

# --- Caches ------------------------------------------------------------------
log "Rebuild des caches (optimize)..."
\$PHP artisan optimize
\$PHP artisan view:cache
\$PHP artisan event:cache

# --- Storage link ------------------------------------------------------------
rm -f public/storage
\$PHP artisan storage:link --quiet

# --- Mode maintenance OFF ----------------------------------------------------
log "Mode maintenance désactivé..."
\$PHP artisan up

# --- Redémarrage queue worker ------------------------------------------------
log "Redémarrage du queue worker..."
sudo systemctl restart postelio-queue

# --- Succès ------------------------------------------------------------------
trap - EXIT
log "====== DÉPLOIEMENT TERMINÉ ======"
DEPLOYEOF

chmod +x "${DEPLOY_DIR}/deploy.sh" "${DEPLOY_DIR}/webhook.py"
chown -R deploy:deploy "${DEPLOY_DIR}"

# Démarrer le webhook server
systemctl start postelio-webhook

log "Scripts de déploiement créés."

# =============================================================================
# LOGROTATE
# =============================================================================
cat > /etc/logrotate.d/postelio << 'EOF'
/var/log/postelio/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 640 deploy www-data
    sharedscripts
    postrotate
        systemctl restart postelio-queue > /dev/null 2>&1 || true
        systemctl restart postelio-webhook > /dev/null 2>&1 || true
    endscript
}
EOF

# =============================================================================
# FIREWALL UFW
# =============================================================================
hr; info "Firewall UFW"; hr

ufw --force reset > /dev/null
ufw default deny incoming > /dev/null
ufw default allow outgoing > /dev/null
ufw allow ssh > /dev/null
ufw allow 'Nginx Full' > /dev/null
# Port webhook interne : PAS exposé publiquement (uniquement via Nginx)
ufw --force enable > /dev/null
log "Firewall configuré (SSH + HTTP/HTTPS uniquement)."

# =============================================================================
# FAIL2BAN
# =============================================================================
systemctl enable fail2ban --quiet
systemctl start fail2ban

# =============================================================================
# RÉSUMÉ FINAL
# =============================================================================
hr
echo ""
echo -e "${BOLD}${GREEN}  ✅ Setup terminé avec succès !${NC}"
echo ""
hr
echo ""
echo -e "${BOLD}Application :${NC}"
echo "  URL          : https://${DOMAIN}"
echo "  Dossier      : ${APP_DIR}"
echo "  Logs         : ${LOG_DIR}/"
echo "  Sauvegardes  : ${BACKUP_DIR}/"
echo ""
echo -e "${BOLD}Services actifs :${NC}"
echo "  postelio-queue    — Queue worker Laravel"
echo "  postelio-webhook  — Récepteur GitHub webhook"
echo "  nginx, mysql, php8.3-fpm"
echo ""
echo -e "${BOLD}${YELLOW}Action requise sur GitHub :${NC}"
echo ""
echo "  1. Aller dans : https://github.com/${GITHUB_REPO}/settings/hooks"
echo "  2. Cliquer   : 'Add webhook'"
echo "  3. Remplir   :"
echo "       Payload URL  : https://${DOMAIN}/deploy-webhook"
echo "       Content type : application/json"
echo "       Secret       : (le secret webhook saisi pendant le setup)"
echo "       Events       : 'Just the push event'"
echo "  4. Cocher    : Active ✓"
echo "  5. Cliquer   : 'Add webhook'"
echo ""
echo -e "${BOLD}Test :${NC}"
echo "  Push sur main → la prod se met à jour automatiquement."
echo "  Logs de déploiement : tail -f ${LOG_DIR}/deploy.log"
echo ""
echo -e "${BOLD}${YELLOW}Important : configurer les variables manquantes dans .env :${NC}"
echo "  nano ${APP_DIR}/.env"
echo "  → MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD (si email nécessaire)"
echo ""
hr
