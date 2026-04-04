import './bootstrap';
import { registerSW } from './pwa/sw-register';
import { setupOfflineInterception } from './pwa/offline-ui';
import { addToQueue, getQueue, removeFromQueue, getQueueCount, clearQueue, updateQueueItemStatus } from './pwa/offline-queue';

// Expose queue API globally so Livewire/Alpine components can use it
// without needing to dynamically import the bundled module
window.PostelioQueue = {
    addToQueue,
    getQueue,
    removeFromQueue,
    getQueueCount,
    clearQueue,
    updateQueueItemStatus,
};

registerSW();
setupOfflineInterception();
