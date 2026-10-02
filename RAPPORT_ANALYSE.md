# Rapport de seconde analyse et corrections

## Base utilisée

Cette seconde passe est réalisée **à partir de la version complète fournie par l’utilisateur**. Aucun fichier d’origine n’a été supprimé. Le composant historique `resources/views/components/severity.blade.php` est conservé uniquement comme stub de compatibilité et n’affiche plus aucun niveau de gravité. Les répertoires nécessaires à Laravel, dont `vendor/`, `storage/framework/`, `storage/logs/`, `bootstrap/cache/` et `.env`, sont conservés.

## Corrections Laravel et logique métier

- politique de mot de passe centralisée à 6 caractères minimum et 128 maximum ;
- même règle appliquée à l’inscription, au profil, à la réinitialisation, à `app:create-admin` et au seeder administrateur ;
- réponse générique de demande de réinitialisation afin de ne pas révéler si une adresse email existe ;
- workflow d’alerte strict : `pending → validated/rejected`, puis `validated → resolved` ;
- une alerte rejetée doit être modifiée par son auteur pour repasser à `pending` ;
- une alerte résolue n’accepte plus de nouvelle décision de modération ;
- validation des types de catastrophe disponibles : type actif + catégorie active pour un nouveau signalement ;
- prise en charge cohérente d’un ancien type inactif appartenant à une catégorie inactive sans permettre sa réactivation tant que la catégorie reste inactive ;
- validation serveur des photos et limite totale de six fichiers ;
- service dédié au stockage/suppression des photos avec nettoyage des fichiers écrits lorsqu’une erreur survient pendant l’enregistrement ;
- contrôle explicite d’un échec de stockage d’une photo ;
- transactions pour les modifications sensibles et la protection du dernier administrateur actif ;
- journal d’audit enrichi avec anciens/nouveaux statuts lors de la modération ;
- protection contre l’auto-désactivation ou l’auto-rétrogradation d’un administrateur ;
- limitation des routes sensibles (connexion, inscription, envoi de lien de réinitialisation, création d’alerte).

## Correction du cache Blade

L’erreur `Please provide a valid cache path` rencontrée dans une archive précédente provenait de la disparition du dossier `storage/framework/views`.

La version actuelle :

1. conserve les dossiers `storage/framework/views`, `storage/framework/cache`, `storage/framework/sessions` et leurs `.gitignore` ;
2. ajoute `config/view.php` avec un chemin compilé basé sur `storage_path('framework/views')`, ce qui évite la valeur `false` de `realpath()` lorsque le dossier doit être recréé ;
3. documente les commandes de récupération dans le README.

## UI/UX

Le site public et l’administration sont maintenant deux expériences visuelles différentes.

### Site visiteur

- en-tête public et navigation simples ;
- page d’accueil de type site d’information, et non dashboard ;
- message principal et appels à l’action visibles immédiatement ;
- explication en trois étapes du processus de signalement ;
- alertes publiques récentes ;
- page de recherche/filtrage des alertes ;
- page de détail publique sans exposer l’identité du citoyen ;
- rappel de sécurité ;
- adaptation mobile avec navigation compacte.
- historique citoyen affiché en cartes lisibles sur mobile plutôt qu’en tableau administratif.

### Espace connecté / administration

- vraie structure de back-office avec sidebar et topbar ;
- navigation selon le rôle ;
- tableaux lisibles et responsives ;
- formulaires structurés ;
- états vides et messages d’erreur explicites ;
- interfaces dédiées aux catégories, catastrophes, utilisateurs et journal d’audit ;
- formulaire de modération qui ne propose que les transitions réellement autorisées par le backend.

## Sécurité

- CSRF Laravel sur les formulaires ;
- Policy Laravel pour les opérations sur les alertes ;
- middleware de rôles pour modération et administration ;
- session régénérée après authentification ;
- comptes désactivés refusés puis déconnectés ;
- limitation des tentatives de connexion ;
- validation par FormRequest / Rule objets ;
- en-têtes `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, `Cross-Origin-Opener-Policy` ;
- Content Security Policy compatible avec les ressources locales actuelles ;
- HSTS en production lorsque la requête utilise HTTPS ;
- mot de passe de l’administrateur non codé en dur dans l’application.

La règle de 6 caractères correspond à la demande fonctionnelle. Pour la production, une longueur supérieure reste recommandée, particulièrement pour les comptes privilégiés.

## Vérifications automatiques effectuées

- syntaxe PHP sur `app/`, `bootstrap/`, `config/`, `database/`, `routes/` et `tests/` : OK ;
- chargement des routes Laravel : 37 routes ;
- actions contrôleurs des routes : présentes ;
- compilation syntaxique directe de 28 templates Blade : OK ;
- syntaxe JavaScript : OK ;
- parsing CSS : aucune erreur de syntaxe détectée ;
- sources CSS/JS et versions servies dans `public/` synchronisées ;
- comparaison avec la version fournie : tous les fichiers d’origine sont conservés ; l’ancien composant de gravité reste uniquement comme stub de compatibilité sans rendu visuel.
- contrôle d’intégrité de l’arborescence : 9 121 fichiers d’origine retrouvés, 0 fichier manquant, 7 fichiers ajoutés pour l’espace citoyen, les pages d’erreur et la migration de compatibilité.

### Limite de l’environnement d’analyse

Le PHP du conteneur d’analyse ne dispose pas des extensions `mbstring`, `dom/xml` et des drivers PDO SQLite/MySQL nécessaires pour exécuter la suite PHPUnit et les migrations avec une base réelle. Les tests ont été ajoutés mais leur exécution complète doit être faite sur l’environnement PHP du projet avec ces extensions.

## Références utilisées

La mise en œuvre suit en priorité la documentation Laravel 13 pour la validation, les Policies, les fichiers, la structure des répertoires et le rate limiting, ainsi que les recommandations OWASP pour l’authentification. Les liens exacts sont regroupés dans `README.md`.


# Passe UI/UX et simplification du 2 octobre 2026

## Changements fonctionnels demandés

- suppression complète des niveaux de gravité dans les formulaires, les filtres, les modèles, la modération, le site public et les vues administratives ;
- migration de compatibilité pour retirer les anciennes colonnes de gravité sur une base déjà migrée ;
- ajout d’une entrée **caméra arrière** (`capture="environment"`) et d’une entrée **galerie** séparée ;
- validation serveur commune aux deux sources et limite globale de six photos ;
- prévisualisation des nouvelles photos avant envoi ;
- espace `citizen` séparé du back-office : navbar, page d’accueil citoyenne, navigation mobile et aucune sidebar ;
- le dashboard est désormais réservé aux modérateurs et administrateurs ;
- page détail d’alerte rendue plus défensive face à des relations manquantes et simplifiée ;
- pages d’erreur 403, 404 et 500 ajoutées pour la production ;
- UI modernisée avec typographie plus lisible, contrastes plus doux, composants mobiles plus grands et icônes Font Awesome ;
- navigation mobile citoyenne optimisée avec barre d’accès rapide ;
- images de listes chargées avec `loading="lazy"` pour limiter le coût réseau.

## Références techniques

Les choix sont alignés sur la documentation Laravel 13 pour la validation et le stockage des fichiers, MDN pour l’attribut `capture` et la documentation officielle Font Awesome pour les icônes Web.
