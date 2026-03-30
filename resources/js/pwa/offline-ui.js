/**
 * Postelio Offline UI Helpers
 * Intercepts form submissions and redirects when offline.
 */
import { addToQueue } from './offline-queue.js';

export function setupOfflineInterception() {
  // We can use a custom event or intercept standard Livewire submissions
  // but a cleaner way is to use Alpine.js directly in the views.
  
  window.addEventListener('pwa:offline-submit', async (event) => {
    const { url, data, redirectTo } = event.detail;
    
    try {
      await addToQueue({
        type: 'generate-post',
        url: url,
        data: data
      });
      
      // Notify user
      window.dispatchEvent(new CustomEvent('pwa:queued', {
        detail: { message: 'Votre post a été mis en attente. Il sera généré dès le retour de votre connexion.' }
      }));
      
      // Redirect after a short delay
      if (redirectTo) {
        setTimeout(() => {
          window.location.href = redirectTo;
        }, 2000);
      }
    } catch (error) {
      console.error('Failed to queue offline request:', error);
    }
  });
}
