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
REQUIRED_EXTS="bcmath mbstring xml curl gd zip intl tokenizer fileinfo openssl filter"
MISSING_EXTS=""
for ext in $REQUIRED_EXTS; do
    if ! $PHP -r "exit(extension_loaded('$ext') ? 0 : 1);" 2>/dev/null; then
        MISSING_EXTS="${MISSING_EXTS} php8.3-${ext}"
    fi
done

if [ -n "$MISSING_EXTS" ]; then
    log "Extensions PHP manquantes : ${MISSING_EXTS}"
    log "Veuillez installer via : sudo apt-get install ${MISSING_EXTS} && sudo systemctl restart php8.3-fpm"
    # Note: On ne fail pas forcément ici si l'app peut tourner en mode dégradé, 
    # mais pour une prod Laravel il vaut mieux s'arrêter.
    fail "Déploiement avorté : extensions manquantes."
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

# On force aussi si le dossier build est manquant
if [ ! -d "public/build" ]; then
    log "Dossier public/build manquant — forçage build npm..."
    FRONTEND_CHANGED=1
fi

if [ "$FRONTEND_CHANGED" -gt 0 ]; then
    log "Frontend modifié (ou build manquant) — rebuild npm..."
    npm install --no-audit --no-fund --quiet
    npm run build --quiet
else
    log "Pas de changements frontend — skip npm build."
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
log "Rebuild caches (optimize)..."
$PHP artisan optimize
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
