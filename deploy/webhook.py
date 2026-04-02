#!/usr/bin/env python3
"""
Postelio — Webhook receiver pour GitHub CD
Écho sur 127.0.0.1:WEBHOOK_PORT, Nginx proxy /deploy-webhook → ici
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
    log.info("Déploiement lancé via script : %s", DEPLOY_SCRIPT)
    try:
        result = subprocess.run(
            ["bash", DEPLOY_SCRIPT],
            capture_output=True, text=True
        )
        if result.returncode == 0:
            log.info("Déploiement terminé avec succès.")
        else:
            log.error("Déploiement en échec (code %d) :\n%s", result.returncode, result.stderr)
    except Exception as e:
        log.error("Erreur système lors du lancement du déploiement : %s", str(e))


class WebhookHandler(http.server.BaseHTTPRequestHandler):

    def do_POST(self):
        if self.path != "/webhook":
            self._reply(404, "Not Found")
            return

        length = int(self.headers.get("Content-Length", 0))
        body   = self.rfile.read(length)

        # Vérification signature HMAC-SHA256
        received = self.headers.get("X-Hub-Signature-256", "")
        if not received:
            log.warning("Requête sans signature rejetée.")
            self._reply(401, "Unauthorized")
            return

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
        log.info("Push reçu de %s (%d commit(s)) — lancement du déploiement…", pusher, commits)

        # Répondre immédiatement, déployer en arrière-plan
        self._reply(200, "Deploying…")
        threading.Thread(target=run_deploy, daemon=True).start()

    def _reply(self, code, message):
        body = message.encode()
        self.end_headers()
        self.wfile.write(body)

    def log_message(self, *_):
        pass  # Logs gérés par logging standard


if __name__ == "__main__":
    if not WEBHOOK_SECRET:
        log.warning("WEBHOOK_SECRET non défini — toutes les requêtes seront acceptées !")

    server = http.server.HTTPServer(("127.0.0.1", PORT), WebhookHandler)
    log.info("Webhook server démarré sur 127.0.0.1:%d", PORT)

    try:
        server.serve_forever()
    except KeyboardInterrupt:
        log.info("Arrêt du webhook server.")
        server.shutdown()
