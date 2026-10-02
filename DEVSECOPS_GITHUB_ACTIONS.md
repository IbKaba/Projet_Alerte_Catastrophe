# GitHub Actions DevSecOps — Alerte Catastrophe

Cette configuration est adaptée au projet Laravel 13 / PHP 8.3 avec Vite.

## Workflows

- `.github/workflows/ci.yml` : validation Composer, installation, syntaxe PHP, Laravel Pint, migration MySQL de contrôle, tests Laravel/PHPUnit et build Vite.
- `.github/workflows/sast.yml` : Gitleaks, Semgrep, `composer audit`, `npm audit` et Trivy.
- `.github/workflows/dast.yml` : démarre une copie isolée de Laravel avec SQLite puis lance OWASP ZAP Baseline contre `http://127.0.0.1:8000`.
- `.github/workflows/cd.yml` : après un CI réussi sur `main`, construit une archive de production. Si le secret `RENDER_DEPLOY_HOOK_URL` existe, le workflow déclenche aussi le déploiement Render.

## Branches

Les workflows CI et SAST tournent sur `main` et `develop`, ainsi que sur les pull requests vers ces branches. DAST tourne sur `main`, manuellement et chaque dimanche à 02:00 UTC. SAST tourne également chaque lundi à 04:00 UTC.

## Secret GitHub pour le CD Render

Dans le dépôt GitHub :

`Settings > Secrets and variables > Actions > New repository secret`

Créer :

`RENDER_DEPLOY_HOOK_URL`

Sa valeur doit être le Deploy Hook du service Render. Ne jamais mettre cette URL directement dans le fichier YAML.

Si ce secret n'est pas défini, le workflow CD reste utile : il construit et conserve l'artefact de production sans déclencher de déploiement distant.

## Protection de la branche main

Dans GitHub, activer une règle de protection sur `main` et demander au minimum les checks suivants avant fusion :

- `Laravel / PHP`
- `Frontend / Vite`
- `Gitleaks - secrets`
- `Semgrep - source code`
- `Dependency audit`
- `Trivy - vulnerabilities / secrets / misconfiguration`

Le DAST peut être rendu obligatoire plus tard, une fois les éventuels faux positifs ZAP traités.

## Notes importantes

- Le CI utilise MySQL 8.4 uniquement pour vérifier que les migrations passent dans un moteur proche de la production. Les tests Laravel continuent d'utiliser la configuration SQLite en mémoire définie dans `phpunit.xml`.
- Le DAST utilise sa propre base `database/dast.sqlite` et ne touche pas à une base de production.
- Le scan ZAP Baseline est passif. Le workflow échoue seulement si le rapport JSON contient au moins une alerte de risque élevé.
- Le workflow SAST n'utilise pas CodeQL afin d'éviter de dépendre de GitHub Code Security sur un dépôt privé.
- Si le dépôt appartient à une organisation et que Gitleaks Action demande une licence, remplacez cette étape par l'image Docker Gitleaks ou configurez `GITLEAKS_LICENSE` conformément à la documentation Gitleaks.

## Premier lancement

1. Copier `.github/` et `DEVSECOPS_GITHUB_ACTIONS.md` à la racine du dépôt.
2. `git add .github DEVSECOPS_GITHUB_ACTIONS.md`
3. `git commit -m "ci: ajout pipeline CI CD SAST DAST"`
4. `git push origin main`
5. Ouvrir l'onglet **Actions** du dépôt GitHub.

## Si le frontend n'a pas encore de package-lock.json

Le workflow fonctionne quand même avec `npm install`. Pour rendre les builds encore plus reproductibles, il est recommandé d'exécuter localement `npm install` puis de versionner `package-lock.json`.
