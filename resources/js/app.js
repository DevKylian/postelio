import './bootstrap';
import { registerSW } from './pwa/sw-register';
import { setupOfflineInterception } from './pwa/offline-ui';

registerSW();
setupOfflineInterception();
