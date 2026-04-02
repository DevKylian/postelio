# Technical Stack — Postelio

## 🧱 Architecture
Postelio est une application **Laravel** monolithique moderne, utilisant l'écosystème TALL (Tailwind, Alpine, Laravel, Livewire) pour une grande interactivité sans la complexité d'un SPA séparé.

## 💻 Backend
- **PHP 8.x**
- **Laravel** : Framework principal (Routing, MVC, Eloquent, Queues, Mail, etc.).
- **Livewire** : Composants dynamiques (Générateur de post, Dashboard, etc.).

## 🎨 Frontend
- **Tailwind CSS** : Styling utilitaire, personnalisation via `tailwind.config.js`.
- **Alpine.js** : Interactivité légère côté client (animations, modales, transitions).
- **Vite** : Bundleur d'assets.
- **Lucide Icons** : Pack d'icônes SVG propre et moderne.

## 🤖 Intelligence Artificielle
- **OpenAI GPT-4o** : Intégré via `openai-php/laravel` pour l'analyse des vocaux et la rédaction de contenus (JSON responses).
- **Logiciel de Post-traitement** : Transformation du JSON AI en champs éditables dans Livewire.

## 🖼️ Génération d'Images (Rendu)
- **Browsershot (Spatie)** : Utilise Headless Chrome pour transformer du HTML/CSS en images PNG.
- **Vues Blade dédiées** : `resources/views/render/postelio-instagram.blade.php` sert de canevas graphique.
- **Intervention Image** : Utilisé pour certains traitements d'image complémentaires si besoin.

## 📦 Dépendances Clés
- `laravel/framework` : Cœur de l'application.
- `livewire/livewire` : Composants interactifs.
- `spatie/laravel-browsershot` : Rendu PNG.
- `openai-php/laravel` : Client OpenAI.
- `laravel/breeze` (probablement) : Système d'authentification.

## 🏗️ Structure du Projet (Points Clés)
- `app/Livewire/Admin/InstagramPostGenerator.php` : Cœur fonctionnel du générateur.
- `resources/views/welcome.blade.php` : Landing page vitrine (très riche en design).
- `resources/views/components/post-templates/` : Composants graphiques des templates Instagram.
- `tailwind.config.js` : Définition des tokens de couleur (`midnight`, `copper`, `chalk`).
