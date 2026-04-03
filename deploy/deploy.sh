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

rollback() {
    log "ERREUR FATALE DÉTECTÉE ! Annulation du déploiement..."
    if [ ! -z "${PREV_COMMIT:-}" ]; then
        log "Retour au commit précédent : ${PREV_COMMIT}"
        cd "$APP_DIR" && git reset --hard "$PREV_COMMIT" --quiet || true
        log "Nettoyage des caches..."
        cd "$APP_DIR" && $PHP artisan optimize:clear 2>/dev/null || true
    fi
    cd "$APP_DIR" && $PHP artisan up 2>/dev/null || true
}

fail() {
    log "ERREUR : $1"
    exit 1
}

# La trap catch n'importe quelle sortie non-0 (crash de bash due à `set -e`)
trap 'if [ $? -ne 0 ]; then rollback; fi' EXIT

log "====== DÉPLOIEMENT DÉMARRÉ ======"

cd "$APP_DIR" || fail "Impossible d'accéder à $APP_DIR"

# Refresh token
GITHUB_TOKEN="$(cat /etc/postelio-github-token)"
git remote set-url origin "https://${GITHUB_TOKEN}@github.com/${GITHUB_REPO}.git"

# --- Vérification Droits / Permissions ---------------------------------------
if [ ! -w "${APP_DIR}/.git" ]; then
    log "ERREUR : L'utilisateur $(whoami) n'a pas les droits d'écriture sur .git"
    fail "Permissions insuffisantes (échec .git writable)"
fi

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
    fail "Déploiement avorté : extensions manquantes."
fi

# --- PHP dependencies -------------------------------------------------------
log "composer install..."
if ! composer install --no-dev --optimize-autoloader --no-interaction --quiet 2>&1; then
    log "composer install échoué — tentative composer update..."
    composer update --no-dev --optimize-autoloader --no-interaction --quiet
fi

# --- Frontend (si JS/CSS/Blade modifié — Tailwind scanne aussi les vues) ----
FRONTEND_REGEX="(resources/|lang/|app/View/|app/Livewire/|package|vite\.config|tailwind\.config|postcss\.config|composer\.lock)"

log "Vérification des changements frontend..."
# On utilise une sous-shell et || true pour ne pas crash le script si grep ne trouve rien
FRONTEND_FILES=$(git diff "${PREV_COMMIT}" "${NEW_COMMIT}" --name-only 2>/dev/null | grep -E "$FRONTEND_REGEX" || true)
FRONTEND_CHANGED=$(echo "$FRONTEND_FILES" | grep -v '^$' | wc -l)

# On force aussi si le dossier build est manquant
if [ ! -d "public/build" ]; then
    log "Dossier public/build manquant — forçage build npm..."
    FRONTEND_CHANGED=1
fi

if [ "$FRONTEND_CHANGED" -gt 0 ]; then
    log "Changements frontend détectés (${FRONTEND_CHANGED} fichiers) — lancement build npm..."
    log "Fichiers concernés : $(echo "$FRONTEND_FILES" | tr '\n' ' ')"
    
    # Nettoyage manuel — sudo nécessaire si les fichiers appartiennent à www-data
    sudo rm -rf public/build
    
    log "npm install..."
    # On tente npm ci (plus sûr), sinon npm install (plus flexible)
    if ! npm ci --quiet 2>/dev/null; then
        npm install --quiet
    fi
    
    log "npm run build..."
    npm run build
    
    # Ajustement permissions après build
    chmod -R 775 public/build 2>/dev/null || true
    log "Build frontend terminé."
else
    log "Pas de changements frontend détectés — skip npm build."
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

# --- Storage link ------------------------------------------------------------
rm -f public/storage
$PHP artisan storage:link --quiet

# --- Mode maintenance OFF ---------------------------------------------------
log "Mode maintenance OFF..."
$PHP artisan up

# --- Queue worker -----------------------------------------------------------
log "Redémarrage queue worker..."
sudo systemctl restart postelio-queue

# --- Désactivation de l'annulation (Succès) -----------------------------------
trap - EXIT
log "====== DÉPLOIEMENT TERMINÉ ======"
