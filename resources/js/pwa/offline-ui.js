/**
 * Postelio Offline UI Helpers
 * Intercepts post generation submissions when offline and queues them locally.
 */
import { addToQueue } from './offline-queue.js';

export function setupOfflineInterception() {
  window.addEventListener('pwa:offline-submit', async (event) => {
    const { url, data, redirectTo } = event.detail;

    try {
      await addToQueue({
        type: 'generate-post',
        url: url,
        data: data,
      });

      // Update the pending count badge everywhere
      window.dispatchEvent(new CustomEvent('pwa:queued'));

      // Notify the user with the app's toast system
      window.dispatchEvent(new CustomEvent('notify', {
        detail: {
          type: 'success',
          message: 'Post mis en file d\'attente. Il sera généré dès le retour de votre connexion.',
        }
      }));

      // Navigate away after a short delay so the user sees the confirmation
      if (redirectTo) {
        setTimeout(() => {
          window.location.href = redirectTo;
        }, 1800);
      }
    } catch (error) {
      console.error('[PWA] Failed to queue offline request:', error);

      window.dispatchEvent(new CustomEvent('notify', {
        detail: {
          type: 'error',
          message: 'Impossible de mettre le post en file d\'attente. Vérifiez l\'espace disponible.',
        }
      }));
    }
  });
}
