# Plateforme de Gestion d'Articles de Presse

Système de gestion éditoriale pour rédactions avec workflow de validation.

## Technologies
- Laravel 12
- Alpine.JS
- Spatie Laravel Permission
- Tailwind CSS

## Fonctionnalités
- Authentification avec rôles (Rédacteur/Éditeur)
- CRUD complet des articles
- Workflow de validation (Brouillon → En attente → Validé/Rejeté)
- Filtres dynamiques (catégorie, auteur, statut, date)
- Page publique des articles validés

## Installation
\`\`\`bash

git clone https://github.com/Abdessamad404/press-management-app.git

cd press-management-app

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate --seed

npm run dev

php artisan serve

\`\`\`

## Comptes de test
- Éditeur: editor@test.com / password
- Rédacteur: writer@test.com / password"
