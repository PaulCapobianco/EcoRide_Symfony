# EcoRide — Guide d’installation locale

Ce projet est une application Symfony 7.3 (PHP 8.2+) avec AssetMapper/Stimulus. Voici la marche à suivre pour la faire tourner en local dans l’état actuel.

## Structure du dépôt
Le paquet fourni pour l’ECF contient :
- `EcoRide/` : code Symfony complet (toutes les commandes ci-dessous se lancent depuis ce dossier).
- `docs/` : documentation, diagrammes et dump SQL (`dump_ecoride.sql`).
- `README.md` : ce fichier (à la racine, à côté des deux dossiers précédents).

## Prérequis
- PHP **≥ 8.2** avec les extensions courantes (ctype, iconv).
- Composer.
- Base de données MySQL/MariaDB accessible (adapter `DATABASE_URL`).
- Outil e-mail de dev recommandé : Mailpit/Mailhog (à renseigner dans `MAILER_DSN`).
- Symfony CLI (facultatif mais pratique) ou `php -S`.

## Installation
```bash
git clone git@github.com:PaulCapobianco/EcoRide_Symfony.git EcoRide-Paul-Capobianco
cd EcoRide-Paul-Capobianco/EcoRide    # adapter si vous avez cloné dans un autre dossier

# Copier la config locale
# Copier le modèle d'environnement et remplir vos identifiants locaux
cp .env.example .env
# Éditer .env/.env.local : DATABASE_URL, MAILER_DSN, etc.

# Dépendances PHP
composer install

# Pas de dépendances Node requises (asset-mapper)
```

## Base de données
```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
```

## Assets (CSS/JS)
Le projet utilise AssetMapper + ImportMap (Chart.js est importé en local, pas via CDN). Après installation ou modification des assets :
```bash
php bin/console importmap:install   # récupère les modules JS référencés
php bin/console asset-map:compile   # génère les fichiers servis dans /public
```
Si vous ne touchez pas aux assets, la compilation existante suffit.

## Lancer l’application
Avec Symfony CLI :
```bash
# Pour lancer temporairement en mode prod (sans barre debug) :
APP_ENV=prod APP_DEBUG=0 symfony server:start
# puis symfony server:stop pour arrêter avant de revenir en dev
```
Ou en mode interactif :
```bash
symfony server:start
```
L’application est alors accessible sur `http://127.0.0.1:8000`.

> **Validation HTML** : certaines erreurs remontées par les validateurs proviennent uniquement de la barre de debug Symfony. Pour un HTML « propre », démarrez le serveur en prod (`APP_ENV=prod APP_DEBUG=0 symfony server:start`) avant d’exécuter la validation.

## Variables utiles
- `DATABASE_URL` : connexion MySQL/MariaDB.
- `MAILER_DSN` : ex. `smtp://localhost:1025` (Mailpit).
- `APP_ENV` : `dev` par défaut en local.

### Mailpit (e-mails de dev)
Dans un autre terminal :
```bash
mailpit
```
L’interface est accessible par défaut sur `http://127.0.0.1:8025`.

### Import du dump SQL
Le dossier `../docs/` (un niveau au-dessus de `EcoRide/`) contient `dump_ecoride.sql` (structure + données).
```bash
# Adapter les identifiants MySQL à votre environnement.
# L’option -p vous demandera le mot de passe (ou utilisez -p'motdepasse').
mysql -u <user> -p <base> < ../docs/dump_ecoride.sql
```

Vous devriez maintenant disposer d’un environnement local identique à l’état actuel du site.***

## Arborescence (principales)

Racine du dépôt :
```
.
├─ EcoRide/                # application Symfony
├─ docs/                   # documentation & dump SQL
└─ README.md
```

Dossier `EcoRide/` :
```
.
├─ assets/                 # JS (Stimulus), CSS, images
│  ├─ controllers/         # Contrôleurs Stimulus
│  ├─ js/                  # Entrée main.js, modules
│  ├─ styles/              # Feuilles de style
├─ bin/                    # Exécutables Symfony/console
├─ config/                 # Config Symfony
├─ migrations/             # Migrations Doctrine
├─ public/                 # Racine web (index.php, uploads, images)
├─ src/                    # Code PHP (controllers, services, entities…)
│  └─ Controller/          # Contrôleurs (Admin, Covoiturage, Profile…)
├─ templates/              # Vues Twig
├─ translations/           # Traductions (si besoin)
├─ var/                    # Cache/logs
├─ vendor/                 # Dépendances Composer
├─ composer.json / lock    # Dépendances PHP
└─ importmap.php           # Config importmap/asset-mapper
```
