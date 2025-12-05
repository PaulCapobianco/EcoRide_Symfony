# Documentation technique — EcoRide

## 1. Réflexions technologiques initiales
- **Framework** : Symfony 7.3 (robustesse, sécurité, écosystème Doctrine/Mailer/UX Turbo).
- **Langages** : PHP 8.2+, Twig pour les vues, JS léger avec Stimulus/Turbo + AssetMapper.
- **Base de données** : MySQL 8 (Doctrine ORM, migrations).
- **Front** : Bootstrap 5 + Bootstrap Icons ; Chart.js chargé en local via importmap (stats admin).
- **Fichiers médias** : Uploads dans `public/uploads`.
- **E-mails** : Symfony Mailer (configurable via `MAILER_DSN`, Mailpit en dev) pour vérification de compte, reset mot de passe, notifications trajets.
- **Sécurité** : Auth Symfony (guards modernes), rôles `ROLE_USER`, `ROLE_EMPLOYE`, `ROLE_ADMIN`, vérification e-mail.

## 2. Environnement de travail
- OS : Ubuntu (dev principal) / compatible Linux, macOS, WSL.
- PHP 8.2+, Composer.
- MySQL 8 local.
- Symfony CLI (serveur local, commandes pratiques).
- Mailpit/Mailhog pour les e-mails de dev.
- Navigateur avec DevTools ; VS Code (extensions Symfony/Twig/PHP Intelephense).

### Variables principales (.env.local)
- `DATABASE_URL="mysql://admin:*studi@aksis*@127.0.0.1:3306/ecoride?serverVersion=8.0&charset=utf8mb4"`
- `MAILER_DSN="smtp://localhost:1025"` (ex. Mailpit)
- `APP_ENV=dev`

## 3. Architecture (rapide)
- `src/Controller` : Covoiturage (recherche, détail, lifecycle), Profile, Admin, Auth, Static.
- `src/Entity` : Utilisateur, Covoiturage, Voiture, Participation, Avis, Parametre, Configuration, Role.
- `src/Service` : RideManager, ParticipationManager, RideNotificationService, ProfileHelper, EmailVerificationService, PasswordResetService (génère les tokens + e-mails de reset).
- `src/Repository` : Requêtes personnalisées (filtres covoiturage, notes…).
- `templates/` : Vues Twig (home, covoiturages, profil, admin, emails).
- `assets/` : JS (Stimulus controllers), CSS (layout, pages), importmap.
- `public/` : index.php, assets compilés, uploads.

## 4. Déploiement / Mise en place
*(Les étapes d’installation détaillées sont décrites dans `README.md` à la racine du dépôt. Extrait : clone → `composer install` → migrations/import du dump → `importmap:install` + `asset-map:compile` → `symfony serve`.)*

5) **E-mails**
   - Dev : Mailpit (`MAILER_DSN=smtp://localhost:1025`) utilisé pour tous les tests.
   - Prod : DSN SMTP réel (ex. Sendgrid, Mailgun).
   - Flux implémentés : vérification de compte, changement d’e-mail, réinitialisation de mot de passe (token 2 h), notifications trajets (annulation, feedback).

## 5. Notes complémentaires
- **Rôles & parcours** : US1–US13 couverts (recherche, filtres, détail, participation, avis, lifecycle trajets, espaces employé/admin, stats). Ajout du flux utilisateur mot de passe oublié + changement de mot de passe depuis le profil.
- **Chart.js** : import local via importmap, utilisé dans le dashboard admin.
- **Responsive** : header/footer et vues clés ajustés pour tablettes/mobiles (nav wrap, footer en colonne).
- **Crédits** : débit à la participation, crédit chauffeur après validation avis par un employé ; annulation rembourse le passager.
- **Données SQL** : un dump complet `docs/dump_ecoride.sql` est fourni (schéma + données de démo). Générer des dumps séparés si requis (schema/data).
