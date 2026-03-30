/**
 * Postelio Service Worker Registration
 * Handles registration and online/offline synchronization.
 */
import { getQueue, removeFromQueue } from './offline-queue.js';

export async function registerSW() {
  if ('serviceWorker' in navigator) {
    try {
      const registration = await navigator.serviceWorker.register('/sw.js');
      console.log('SW registered:', registration);
    } catch (error) {
      console.warn('SW registration failed:', error);
    }
  }

  // Handle online event to process queue
  window.addEventListener('online', async () => {
    console.log('Back online, checking queue...');
    document.dispatchEvent(new CustomEvent('pwa:online'));
    await processQueue();
  });

  window.addEventListener('offline', () => {
    console.log('Gone offline');
    document.dispatchEvent(new CustomEvent('pwa:offline'));
  });
}

/**
 * Process the offline queue when coming back online.
 */
async function processQueue() {
  const queue = await getQueue();
  if (queue.length === 0) return;

  console.log(`Processing ${queue.length} queued requests...`);
  
  for (const item of queue) {
    try {
      // Logic for each request type
      if (item.type === 'generate-post') {
        await syncPostRequest(item);
      }
      
      // Successfully synced, remove from queue
      await removeFromQueue(item.id);
      console.log(`Successfully synced queued request: ${item.id}`);
    } catch (error) {
      console.error(`Failed to sync queued request: ${item.id}`, error);
      // We keep it in the queue for the next sync attempt (retry)
    }
  }
}

/**
 * Sync a post request with the server.
 * This depends on how the server expects the data.
 */
async function syncPostRequest(item) {
  const formData = new FormData();
  
  // Reconstruct formData from stored data
  // Blobs/Files are reconstructed automatically if stored correctly in IndexedDB
  for (const [key, value] of Object.entries(item.data)) {
    if (Array.isArray(value)) {
      value.forEach(v => formData.append(`${key}[]`, v));
    } else {
      formData.append(key, value);
    }
  }

  const response = await fetch(item.url, {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    }
  });

  if (!response.ok) {
    throw new Error(`Server responded with ${response.status}`);
  }

  const result = await response.json();
  return result;
}
