#!/usr/bin/env python3
"""
Postelio — Webhook receiver pour GitHub CD
Écoute sur 127.0.0.1:9876, proxié par Nginx depuis /deploy-webhook

Variables d'environnement :
  WEBHOOK_SECRET  — Secret configuré dans GitHub
  DEPLOY_SCRIPT   — Chemin vers deploy.sh
  WEBHOOK_PORT    — Port d'écoute (défaut: 9876)
"""
import hashlib
import hmac
import http.server
import json
import logging
import os
import subprocess
import threading

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
    """Lance deploy.sh en arrière-plan et log le résultat."""
    log.info("Déploiement lancé…")
    result = subprocess.run(
        ["bash", DEPLOY_SCRIPT],
        capture_output=True,
        text=True,
    )
    if result.returncode == 0:
        log.info("Déploiement terminé avec succès.")
    else:
        log.error(
            "Déploiement en échec (code %d) :\n%s",
            result.returncode,
            result.stderr[-2000:],  # Limiter la taille du log
        )


class WebhookHandler(http.server.BaseHTTPRequestHandler):

    def do_POST(self):
        if self.path != "/webhook":
            self._reply(404, "Not Found")
            return

        # Lecture du body
        length = int(self.headers.get("Content-Length", 0))
        body   = self.rfile.read(length)

        # Vérification signature HMAC-SHA256
        received = self.headers.get("X-Hub-Signature-256", "")
        expected = "sha256=" + hmac.new(WEBHOOK_SECRET, body, hashlib.sha256).hexdigest()

        if not hmac.compare_digest(received, expected):
            log.warning("Signature invalide — requête rejetée (IP: %s)", self.client_address[0])
            self._reply(401, "Unauthorized")
            return

        # Parse payload
        try:
            payload = json.loads(body)
        except json.JSONDecodeError:
            self._reply(400, "Invalid JSON")
            return

        ref = payload.get("ref", "")

        # Ignorer les pushs hors main
        if ref != "refs/heads/main":
            log.info("Branche ignorée : %s", ref)
            self._reply(200, f"Ignored ({ref})")
            return

        pusher  = payload.get("pusher", {}).get("name", "inconnu")
        commits = payload.get("commits", [])
        nb      = len(commits)
        msg     = commits[-1].get("message", "").split("\n")[0] if commits else "?"

        log.info(
            "Push de %s — %d commit(s) — dernier : %s",
            pusher, nb, msg[:80]
        )

        # Répondre immédiatement à GitHub (évite un timeout)
        self._reply(200, "Deploying…")

        # Déploiement en arrière-plan
        threading.Thread(target=run_deploy, daemon=True).start()

    def do_GET(self):
        """Health check simple."""
        if self.path == "/health":
            self._reply(200, "OK")
        else:
            self._reply(404, "Not Found")

    def _reply(self, code: int, message: str):
        body = message.encode()
        self.send_response(code)
        self.send_header("Content-Type", "text/plain; charset=utf-8")
        self.send_header("Content-Length", str(len(body)))
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
