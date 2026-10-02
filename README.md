# Alerte Catastrophe

Application Laravel de **signalement, validation et suivi des catastrophes**, avec un site public distinct du back-office de gestion.

## Fonctionnalités principales

- site public moderne : accueil, fonctionnement du service, alertes publiques et détails d’une alerte ;
- inscription, connexion, déconnexion et réinitialisation du mot de passe ;
- rôles `citizen`, `moderator` et `admin` ;
- signalements géolocalisés volontairement simples, sans niveau de gravité à choisir ;
- prise de photo directement depuis le téléphone et import depuis la galerie, jusqu’à 6 photos ;
- workflow métier : `pending → validated/rejected`, puis `validated → resolved` ;
- correction par le citoyen d’un signalement rejeté avant une nouvelle validation ;
- gestion des catégories et types de catastrophe ;
- gestion des utilisateurs, rôles et activation/désactivation des comptes ;
- protection du dernier administrateur actif ;
- journal d’audit des actions sensibles ;
- interface responsive pour téléphone, tablette et ordinateur.

## Prérequis

Le projet utilise Laravel 13 et nécessite notamment :

- PHP 8.3 ou plus récent ;
- extensions PHP `mbstring`, `dom`, `xml`, `fileinfo`, `openssl` ;
- `pdo_mysql` pour MySQL ou `pdo_sqlite` pour SQLite ;
- Composer si les dépendances doivent être réinstallées ou mises à jour ;
- Node.js/npm uniquement si vous souhaitez reconstruire les sources frontend. Les fichiers CSS/JS prêts à servir sont déjà dans `public/`.

L’archive complète conserve `vendor/`, `storage/`, `bootstrap/cache/`, `.env` et les autres fichiers de la version fournie. **Ne supprimez pas les répertoires `storage/framework/*`** : Laravel y stocke notamment les vues Blade compilées, les caches et, selon la configuration, les sessions.

## Installation / démarrage

Après extraction, ouvrez un terminal dans la racine du projet.

Si `vendor/` est déjà présent, l’application peut utiliser les dépendances incluses. Si Composer est disponible, il reste conseillé de vérifier les dépendances :

```bash
composer install
```

Le projet contient déjà un `.env` issu de la version fournie. **Ne l’écrasez pas automatiquement.** Vérifiez surtout la configuration de la base :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=catastrophe_db
DB_USERNAME=root
DB_PASSWORD=VOTRE_MOT_DE_PASSE_MYSQL
```

Si vous partez d’une installation sans `.env`, utilisez alors :

```bash
cp .env.example .env
php artisan key:generate
```

Puis :

```bash
php artisan optimize:clear
php artisan migrate --seed
php artisan app:create-admin
php artisan storage:link
php artisan serve
```

La commande `app:create-admin` demande le mot de passe de façon interactive et ne l’écrit pas dans le code source.

## Politique de mot de passe

À la demande du projet, la règle applicative est centralisée à **6 caractères minimum** et 128 maximum pour :

- l’inscription ;
- la réinitialisation du mot de passe ;
- le changement de mot de passe dans le profil ;
- la création interactive d’un administrateur ;
- le mot de passe administrateur éventuellement fourni au seeder.

La limite de 6 caractères est une exigence fonctionnelle de ce projet. Pour une mise en production, un mot de passe sensiblement plus long reste fortement recommandé, en particulier pour les modérateurs et administrateurs.

## Authentification et autorisation

L’authentification repose sur la session Laravel. Après connexion, la session est régénérée. Les tentatives de connexion sont limitées et un compte désactivé est refusé puis déconnecté s’il avait encore une session active.

Les autorisations sensibles sont vérifiées **côté serveur**, via les middleware de rôle et `AlertPolicy`. Le simple masquage d’un bouton dans Blade n’est jamais considéré comme une protection suffisante.

## Espace citoyen et espace de gestion

Un utilisateur `citizen` ne reçoit plus le dashboard administratif après sa connexion. Il est dirigé vers `/mon-espace`, une page citoyenne avec **navbar**, actions simples, derniers signalements et conseils. Les routes de création, consultation, modification et profil utilisent elles aussi la mise en page citoyenne sans sidebar.

Le dashboard `/tableau-de-bord` est réservé aux rôles `moderator` et `admin`.

## Workflow des alertes

Le cycle autorisé est :

```text
En attente (pending)
  ├──> Validée (validated) ───> Résolue (resolved)
  └──> Rejetée (rejected)
          └──> correction par le citoyen ───> En attente
```

Une alerte `resolved` ne peut plus être modérée. Une alerte `rejected` doit être modifiée par son auteur pour repasser automatiquement à `pending`.

Un nouveau signalement ne peut utiliser qu’un type de catastrophe actif appartenant à une catégorie active. Un type inactif existant peut rester associé à son ancienne catégorie inactive, mais il ne peut pas être réactivé tant que cette catégorie reste inactive.

## Photos : caméra et galerie

Le formulaire propose deux entrées distinctes :

- **Prendre une photo** : utilise un champ fichier HTML avec `capture="environment"` afin de demander la caméra arrière sur les téléphones compatibles ;
- **Choisir dans la galerie** : permet de sélectionner plusieurs images déjà présentes sur l’appareil.

Le backend fusionne les deux sources et vérifie une limite globale de **6 photos**. Les fichiers sont contrôlés côté serveur par les règles de fichier Laravel : JPG/JPEG, PNG ou WebP, 4 Mo maximum par image. Elles sont stockées sur le disque Laravel `public` sous `storage/app/public`.

L’attribut HTML `capture` dépend du navigateur et du système mobile. La galerie reste donc toujours disponible comme solution de repli.

Pour les rendre accessibles depuis le navigateur :

```bash
php artisan storage:link
```

Si PHP refuse plusieurs photos avant même que Laravel ne les valide, vérifiez aussi `upload_max_filesize` et `post_max_size` dans `php.ini`. La limite PHP doit être supérieure à la taille totale du formulaire.

## Erreur « Please provide a valid cache path »

La version actuelle conserve les répertoires nécessaires et fournit aussi un `config/view.php` robuste qui utilise directement `storage_path('framework/views')`.

Si une copie manuelle du projet a tout de même supprimé les dossiers vides, recréez-les :

### PowerShell

```powershell
@(
  "storage\framework\views",
  "storage\framework\cache\data",
  "storage\framework\sessions",
  "storage\logs",
  "bootstrap\cache"
) | ForEach-Object { New-Item -ItemType Directory -Force -Path $_ | Out-Null }

php artisan optimize:clear
```

## Erreur MySQL « Access denied »

Ce message ne vient pas de l’authentification de l’application. Il indique que MySQL refuse les identifiants configurés dans `.env`. Vérifiez `DB_USERNAME`, `DB_PASSWORD`, le nom de la base et que MySQL est démarré, puis :

```bash
php artisan config:clear
php artisan migrate
```


## Affichage des erreurs en production

Pendant le développement, `APP_DEBUG=true` permet de voir la pile d'erreur complète. Sur un serveur accessible aux utilisateurs, utilisez `APP_DEBUG=false` afin que Laravel affiche les pages d'erreur 403/404/500 prévues par le projet au lieu d'exposer des détails techniques.

```env
APP_ENV=production
APP_DEBUG=false
```

## Sécurité intégrée

Le projet inclut notamment : protection CSRF Laravel, mots de passe hashés, régénération de session, contrôle des rôles côté serveur, Policy sur les alertes, validation par `FormRequest`, limitation des tentatives, en-têtes HTTP de sécurité, Content Security Policy, journal d’audit et protection contre l’auto-rétrogradation / auto-désactivation du dernier administrateur actif.

En production HTTPS, activez également un cookie de session sécurisé dans l’environnement de production et configurez un véritable service SMTP pour la réinitialisation de mot de passe.

## Tests

Les tests métier sont dans `tests/Feature/BusinessRulesTest.php`.

```bash
php artisan test
```

Ils couvrent notamment le mot de passe à 6 caractères, l’interdiction de signaler un type inactif, les transitions de statut et la protection du dernier administrateur.

## Sources et documentation de référence

Les choix techniques ont été alignés en priorité sur les références officielles et reconnues :

- Laravel 13 — Validation et règles de mot de passe : https://laravel.com/docs/13.x/validation
- Laravel 13 — Authentification : https://laravel.com/docs/13.x/authentication
- Laravel 13 — Autorisation / Policies : https://laravel.com/docs/13.x/authorization
- Laravel 13 — Réinitialisation des mots de passe : https://laravel.com/docs/13.x/passwords
- Laravel 13 — Stockage de fichiers : https://laravel.com/docs/13.x/filesystem
- Laravel 13 — Validation des fichiers : https://laravel.com/docs/13.x/validation
- MDN — attribut HTML `capture` pour la caméra mobile : https://developer.mozilla.org/fr/docs/Web/HTML/Reference/Attributes/capture
- Font Awesome — utilisation sur le Web : https://docs.fontawesome.com/web
- Laravel 13 — Structure des répertoires : https://laravel.com/docs/13.x/structure
- Laravel 13 — Rate limiting : https://laravel.com/docs/13.x/rate-limiting
- OWASP — Authentication Cheat Sheet : https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html

## À propos de `castrop.sql`

Le fichier SQL historique reste conservé comme référence. Pour l’application Laravel actuelle, **les migrations de `database/migrations/` sont la source de vérité** afin d’éviter d’entretenir deux schémas concurrents.

## Mise à jour depuis une version avec niveaux de gravité

Le projet contient une migration de compatibilité qui retire les anciennes colonnes `effective_severity` et `default_severity` si elles existent déjà. Après remplacement du code sur une base existante, exécutez :

```bash
php artisan optimize:clear
php artisan migrate
```

Sur une nouvelle base, les migrations de création ne génèrent plus ces colonnes. Le fichier historique `resources/views/components/severity.blade.php` est conservé comme stub sans rendu afin de préserver l'arborescence de la version fournie.
