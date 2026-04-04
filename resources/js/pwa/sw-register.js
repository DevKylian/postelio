/**
 * Postelio Service Worker Registration
 * Handles registration and online/offline synchronization.
 */
import { getQueue, removeFromQueue, updateQueueItemStatus } from './offline-queue.js';

export async function registerSW() {
  if ('serviceWorker' in navigator) {
    try {
      const registration = await navigator.serviceWorker.register('/sw.js');
      console.log('[PWA] SW registered:', registration.scope);
    } catch (error) {
      console.warn('[PWA] SW registration failed:', error);
    }
  }

  // Handle online event: process queued posts
  window.addEventListener('online', async () => {
    console.log('[PWA] Back online, processing queue...');
    document.dispatchEvent(new CustomEvent('pwa:online'));
    await processQueue();
  });

  window.addEventListener('offline', () => {
    console.log('[PWA] Gone offline');
    document.dispatchEvent(new CustomEvent('pwa:offline'));
  });

  // Expose the manual sync trigger globally so UI components can call it
  window.PostelioSync = { processQueue };
}

/**
 * Fetch a fresh CSRF token from the server.
 * Returns null if the server is unreachable (we're still offline).
 */
async function getFreshCsrfToken() {
  try {
    const res = await fetch('/csrf-token', {
      method: 'GET',
      headers: { 'Accept': 'application/json' },
      cache: 'no-store',
    });
    if (!res.ok) return null;
    const data = await res.json();
    return data.token ?? null;
  } catch {
    // Fallback: try the meta tag in the current document
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : null;
  }
}

/**
 * Process the offline queue when coming back online.
 * Can also be called manually.
 */
export async function processQueue() {
  const queue = await getQueue();
  if (queue.length === 0) return;

  console.log(`[PWA] Processing ${queue.length} queued request(s)...`);

  // Get a fresh CSRF token before submitting
  const csrfToken = await getFreshCsrfToken();
  if (!csrfToken) {
    console.warn('[PWA] Could not obtain CSRF token — aborting sync.');
    return;
  }

  // Update meta tag so Livewire also benefits
  const meta = document.querySelector('meta[name="csrf-token"]');
  if (meta) meta.content = csrfToken;

  let syncedCount = 0;
  let failedCount = 0;

  for (const item of queue) {
    if (item.status !== 'pending') continue;

    try {
      await updateQueueItemStatus(item.id, 'syncing');

      if (item.type === 'generate-post') {
        await syncPostRequest(item, csrfToken);
      }

      await removeFromQueue(item.id);
      syncedCount++;
      console.log(`[PWA] Synced queue item #${item.id}`);
    } catch (error) {
      await updateQueueItemStatus(item.id, 'pending'); // reset for retry
      failedCount++;
      console.error(`[PWA] Failed to sync queue item #${item.id}:`, error);
    }
  }

  // Notify the UI of the sync result
  window.dispatchEvent(new CustomEvent('pwa:sync-complete', {
    detail: { synced: syncedCount, failed: failedCount }
  }));

  // Also refresh the pending count badge
  window.dispatchEvent(new CustomEvent('pwa:queued'));
}

/**
 * Sync a single queued post request with the server.
 * Reconstructs FormData from the stored data (Blobs are preserved by IndexedDB).
 */
async function syncPostRequest(item, csrfToken) {
  const formData = new FormData();
  const { url, data } = item;

  for (const [key, value] of Object.entries(data)) {
    if (value === null || value === undefined) continue;

    if (key === 'extraImages' && Array.isArray(value)) {
      // Each element is a Blob/File stored in IndexedDB
      value.forEach((blob, i) => {
        if (blob instanceof Blob) {
          const fileName = blob.name || `image-${i}.jpg`;
          const file = blob instanceof File ? blob : new File([blob], fileName, { type: blob.type });
          formData.append('extraImages[]', file);
        }
      });
    } else if (key === 'platforms' && Array.isArray(value)) {
      value.forEach(p => formData.append('platforms[]', p));
    } else if (value instanceof Blob) {
      const fileName = value.name || (key === 'audio' ? 'audio.webm' : `${key}.jpg`);
      const file = value instanceof File ? value : new File([value], fileName, { type: value.type });
      formData.append(key, file);
    } else if (typeof value === 'boolean') {
      formData.append(key, value ? '1' : '0');
    } else {
      formData.append(key, String(value));
    }
  }

  const response = await fetch(url, {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json',
    },
  });

  if (!response.ok) {
    const body = await response.text();
    throw new Error(`Server ${response.status}: ${body.slice(0, 200)}`);
  }

  return response.json();
}
