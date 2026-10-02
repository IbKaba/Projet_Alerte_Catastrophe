# Correctifs — alertes publiques, photos et responsive

## Problèmes corrigés

1. **Les alertes citoyennes n'apparaissaient pas sur la page publique**
   - La portée `publiclyVisible()` ne retournait que les statuts `validated` et `resolved`.
   - Les alertes `pending` sont désormais visibles immédiatement sur le site public avec le badge **En attente**.
   - Les alertes rejetées restent privées.
   - Le filtre public accepte maintenant `En attente`, `Validée` et `Résolue`.

2. **Les photos ne s'affichaient pas malgré `php artisan storage:link`**
   - Les vues dépendaient directement de `/storage/...`.
   - Une route Laravel dédiée `photos-alertes/{photo}` diffuse maintenant les images depuis le disque `public` avec contrôle d'accès.
   - Cela rend l'affichage des photos indépendant du lien symbolique `public/storage`.
   - Les photos des alertes rejetées ne sont accessibles qu'au propriétaire ou au personnel autorisé.

3. **Déplacement horizontal des pages**
   - Les conteneurs principaux ont maintenant des limites de largeur et `min-width: 0` sur les éléments flex/grid sensibles.
   - Le défilement horizontal global est bloqué.
   - Les tableaux administratifs conservent leur propre défilement horizontal local sur petit écran.
   - Les menus mobiles, cartes, textes longs et en-têtes sont contraints à la largeur de l'écran.

## Vérifications effectuées

- Syntaxe PHP des fichiers `app`, `routes`, `config` et `database` : OK.
- `php artisan route:list --except-vendor` : 38 routes chargées, dont la nouvelle route photo.
- 28 vues Blade compilées et leur PHP généré vérifié syntaxiquement via le compilateur Blade.
- JavaScript `public/js/app.js` : syntaxe OK.
- Test direct du contrôleur photo sur une image réelle du projet : HTTP 200, `image/png`, taille correcte.

## Note environnement

La commande Artisan `view:cache` ne peut pas être utilisée dans l'environnement de vérification fourni ici car l'extension PHP DOM n'y est pas installée. La compilation Blade a donc été vérifiée directement via le compilateur Laravel. Ce point ne concerne pas le code du projet.
