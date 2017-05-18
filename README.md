# Service de gestion des fiches de PROJ1.

## Description du projet.

Ce projet est un *service de gestion des fiches* pour le cours de PROJ1.

Il est écrit en **PHP, HTML, CSS et Javascript**, est organisé selon le **modèle MVC**, et repose sur plusieurs projets libres détaillés plus bas, dont le framework PHP *Laravel* et le framework CSS *Material Design Lite*.

Les sources du projet, dans `./sources`, sont organisés de la façon suivante :

- Le dossier `app` contient l'ensemble des classes qui déterminent le comportement de l'application.
	- Le dossier `app/Http/Controllers` contient tous les *Controllers* de l'application, i.e. les classes qui prennent en charge les requêtes HTTP envoyées à l'application, et renvoient les réponses correspondantes.
	- Le dossier `app/Http/Middleware` contient tous les *Middlewares* de l'application, i.e. les classes qui interceptent les requêtes HTTP avant qu'elles ne parviennent aux Controllers, notamment pour vérifier l'authentification des utilisateurs et leurs privilèges.
	- Le dossier `app/Http/Entities` contient toutes les *Entities* de l'application, i.e. les entités manipulables par l'application. Par exemple, le fichier `app/Http/Entities/Fiche.php` contient la classe `Fiche` qui correspond à une fiche de PROJ1.
	- Le dossier `app/Http/Mail` contient toutes les définitions des mails que l'application peut envoyer.
- Le dossier `database` contient notamment toutes les *Migrations* de l'application, qui sont grosso modo les schémas des tables de la base de données.
- Le dossier `public` contient toutes les sources CSS et Javascript de l'application.
- Le dossier `resources/views` contient toutes les *Views* de l'application, qui sont grosso modo des fichiers HTML complémentés par un petit langage de template.
- Le fichier `routes/web.php` déclare toutes les *Routes* de l'application, i.e. les liens entre URLs, Middleware et Controllers. Les autres types de routes *(`console` et `api`)* ne sont pas utilisées.


## Crédits et licenses.

Ce projet repose sur :

- Le framework web [Laravel](https://laravel.com/), sous license MIT.
- L'ORM [Doctrine](http://www.doctrine-project.org/), sous license MIT.
- Le wrapper de Doctrine pour Laravel, [LaravelDoctrine](http://www.laraveldoctrine.org/), sous license MIT.
- L'utilitaire [PHPArchive](https://github.com/splitbrain/php-archive), sous license MIT.
- Le framework CSS [Material Design Lite](https://github.com/google/material-design-lite), sous license Apache.
- Le framework Javascript [jQuery](https://github.com/jquery/jquery), sous license MIT.

Je distribue ce projet sous license MIT, comme indiqué dans `LICENSE`.


## Procédure d'installation.

### 1. Création de la base de données

On commence par créer la base de données, en important le fichier `./database.sql`.


### 2. Transfert des fichiers et installation des dépendances

Supposons que l'on souhaite stocker le service dans le dossier /var/www/fiches.

1. Copier le contenu de `./sources` vers `/var/www/fiches`.
2. Installer le gestionnaire de packets Composer, à l'aide de la commande `wget https://raw.githubusercontent.com/composer/getcomposer.org/1b137f8bf6db3e79a38a5bc45324414a6b1f9df2/web/installer -O - -q | php -- --quiet`.
3. Installer les dépendences du projet. Pour cela, `cd /var/www/fiches && composer install`.
4. Corriger les droits des différents dossiers. En particulier, l'utilisateur apache doit avoir les droits de lecture et d'écriture sur les dossiers `/var/www/fiches/storage` et `/var/www/fiches/bootstrap/cache`, ainsi que sur tout leur contenu récursivement.
5. Adapter la configuration de l'application pour l'environnement de destination. Concrètement, il faut créer dans `/var/www/fiches` un fichier `.env`, qui contient les champs suivants :

```
APP_ENV=local
APP_KEY=[LA CLE D'APPLICATION, A GENERER AVEC `php artisan key:generate`]
APP_DEBUG=true
APP_LOG_LEVEL=debug
APP_URL=[L'URL FINALE DE L'APPLICATION, PAR EXEMPLE `http://fiches.ens-lyon.fr`]

DB_CONNECTION=mysql
DB_HOST=[L'HOTE DE LA BASE DE DONNEES, TYPIQUEMENT `127.0.0.1`]
DB_PORT=[LE PORT DE LA BASE DE DONNEES, TYPIQUEMENT `3306`]
DB_DATABASE=[LE NOM DE LA BASE DE DONNEES]
DB_USERNAME=[L'UTILISATEUR DE LA BASE DE DONNEES]
DB_PASSWORD=[LE MOT DE PASSE POUR CET UTILISATEUR]

BROADCAST_DRIVER=log
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

REDIS_HOST=null
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_DRIVER=[LE DRIVER DE MAIL A UTILISER, `mail` SI LA FONCTION MAIL() DE PHP EST BIEN CONFIGUREE, `sendmail` SINON]

PUSHER_APP_ID=null
PUSHER_KEY=null
PUSHER_SECRET=null

```

### 3. Lien avec le dépot Git

Supposons que le service de gestion des fiches soit stocké dans le dossier /var/www/fiches, et que le dépot Git contenant les fiches soit dans /var/git/fiches.

Pour relier le dépot Git au service, il faut **créer le lien symbolique** `/var/www/fiches/git -> /var/git/fiches`.


### 4. Fin de l'installation

L'installation devrait être terminée. Il suffit de se connecter avec l'utilisateur `admin@localhost/enslyon` présent par défaut en base de données, en n'oubliant pas de changer son mot de passe ensuite !

