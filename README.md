# Paheko SaaS Laravel

Plateforme SaaS pour déploiement automatique d'instances Paheko avec souscription Stripe.

## Fonctionnalités
- Formulaire de souscription (sous-domaine, email, nom association)
- Vérification AJAX disponibilité sous-domaine
- Paiement Stripe Checkout
- Webhook Stripe (déploiement, suspension, réactivation)
- Table `instances` (BDD)
- Vue Blade moderne (Tailwind CDN)

## Installation
1. Copier `.env.example` en `.env` et configurer la BDD et Stripe
2. `composer install`
3. `php artisan migrate`
4. Lancer le serveur : `php artisan serve`
5. Accéder à `/subscribe`

## À compléter
- Intégration Stripe Checkout (Cashier ou SDK)
- Déploiement automatique Paheko
- Notification email
- Sécurité et logs
