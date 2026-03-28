#!/bin/bash
# =============================================================================
# Postelio — Script de déploiement (version standalone pour référence)
# En production, ce fichier est à /opt/postelio-deploy/deploy.sh
# Il est généré automatiquement par setup.sh
# =============================================================================
set -euo pipefail

APP_DIR="/var/www/postelio"
LOG_DIR="/var/log/postelio"
BACKUP_DIR="/var/backups/postelio"
PHP="php8.3"
GITHUB_REPO="devkylian/postelio"
GITHUB_BRANCH="main"
DEPLOY_LOG="${LOG_DIR}/deploy.log"

exec >> "${DEPLOY_LOG}" 2>&1

log()  { echo "[DEPLOY $(date '+%Y-%m-%d %H:%M:%S')] $1"; }
fail() {
    log "ERREUR : $1"
    cd "$APP_DIR" && $PHP artisan up 2>/dev/null || true
    exit 1
}

log "====== DÉPLOIEMENT DÉMARRÉ ======"

cd "$APP_DIR" || fail "Impossible d'accéder à $APP_DIR"

# Refresh token
GITHUB_TOKEN="$(cat /etc/postelio-github-token)"
git remote set-url origin "https://${GITHUB_TOKEN}@github.com/${GITHUB_REPO}.git"

# --- Vérifier si nouveaux commits -------------------------------------------
git fetch origin "$GITHUB_BRANCH" --quiet
LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse "origin/${GITHUB_BRANCH}")

if [ "$LOCAL" = "$REMOTE" ]; then
    log "Déjà à jour — rien à déployer."
    exit 0
fi

PREV_COMMIT=$(git rev-parse --short HEAD)
git reset --hard "origin/${GITHUB_BRANCH}" --quiet
NEW_COMMIT=$(git rev-parse --short HEAD)
log "Mise à jour : ${PREV_COMMIT} → ${NEW_COMMIT}"

# --- Vérification extensions PHP requises ------------------------------------
REQUIRED_EXTS="bcmath mbstring xml curl gd zip intl tokenizer fileinfo"
MISSING_EXTS=""
for ext in $REQUIRED_EXTS; do
    if ! $PHP -m 2>/dev/null | grep -qi "^${ext}$"; then
        MISSING_EXTS="${MISSING_EXTS} php8.3-${ext}"
    fi
done
if [ -n "$MISSING_EXTS" ]; then
    fail "Extensions PHP manquantes :${MISSING_EXTS} — Installer avec : sudo apt-get install${MISSING_EXTS}"
fi

# --- PHP dependencies -------------------------------------------------------
log "composer install..."
if ! composer install --no-dev --optimize-autoloader --no-interaction --quiet 2>&1; then
    log "composer install échoué — tentative composer update..."
    composer update --no-dev --optimize-autoloader --no-interaction --quiet
fi

# --- Frontend (si JS/CSS/Blade modifié — Tailwind scanne aussi les vues) ----
FRONTEND_CHANGED=$(git diff "${PREV_COMMIT}" "${NEW_COMMIT}" --name-only 2>/dev/null \
    | grep -cE "^(resources/js|resources/css|resources/views|package\.json|vite\.config|tailwind\.config)" || true)
FRONTEND_CHANGED=${FRONTEND_CHANGED:-0}

if [ "$FRONTEND_CHANGED" -gt 0 ]; then
    log "Frontend modifié — rebuild npm..."
    npm ci --silent
    npm run build
else
    log "Pas de changements frontend."
fi

# --- Mode maintenance -------------------------------------------------------
log "Mode maintenance ON..."
$PHP artisan down --retry=60

# --- Sauvegarde BDD ---------------------------------------------------------
log "Sauvegarde base de données..."
mkdir -p "$BACKUP_DIR"
mysqldump --defaults-file="/home/deploy/.my.cnf" --single-transaction --no-tablespaces postelio \
    | gzip > "${BACKUP_DIR}/db_$(date +%Y%m%d_%H%M%S).sql.gz"
ls -t "${BACKUP_DIR}"/*.sql.gz 2>/dev/null | tail -n +15 | xargs -r rm -f

# --- Migrations -------------------------------------------------------------
log "Migrations..."
$PHP artisan migrate --force || fail "Migration échouée"

# --- Caches -----------------------------------------------------------------
log "Rebuild caches..."
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan event:cache
$PHP artisan storage:link 2>/dev/null || true

# --- Mode maintenance OFF ---------------------------------------------------
log "Mode maintenance OFF..."
$PHP artisan up

# --- Queue worker -----------------------------------------------------------
log "Redémarrage queue worker..."
sudo systemctl restart postelio-queue

log "====== DÉPLOIEMENT TERMINÉ ======"
