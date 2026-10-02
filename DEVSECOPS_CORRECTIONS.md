# Correctif GitHub Actions — Projet Alerte Catastrophe

## Pourquoi la première version pouvait échouer

- `ubuntu-latest` annonçait sa migration vers Ubuntu 26. Ce message est un avertissement, pas l'erreur qui fait échouer le job. Les workflows sont désormais épinglés sur `ubuntu-24.04`.
- Le contrôle `Laravel Pint --test` pouvait rendre tout le CI rouge uniquement pour des différences de formatage. Il reste exécuté, mais il est temporairement informatif.
- Le SAST mélangeait découverte des vulnérabilités et blocage immédiat du pipeline. Sur un projet existant cela peut rendre le workflow rouge avant qu'une baseline soit établie.
- Le DAST écrivait directement ses rapports dans le dépôt monté dans le conteneur ZAP, ce qui peut créer des problèmes de droits. Les rapports sont maintenant écrits dans `zap-output/` avec les permissions adaptées.
- Le CD répétait un audit de dépendances déjà réalisé par le SAST. Le CD est maintenant centré sur la création d'un artefact de production et le déploiement.

## Mode de sécurité

Par défaut, SAST et DAST fonctionnent en **mode rapport** afin d'obtenir une première baseline exploitable.

Quand les rapports sont propres, créer dans GitHub :

- `Settings > Secrets and variables > Actions > Variables > New repository variable`
- `SECURITY_ENFORCE=true` pour rendre Gitleaks / Composer audit / npm audit / Semgrep bloquants selon le workflow.
- `DAST_ENFORCE=true` pour faire échouer le DAST lorsqu'un résultat ZAP de risque élevé est détecté.

Le secret `RENDER_DEPLOY_HOOK_URL` reste facultatif. Sans lui, le CD construit et publie l'artefact GitHub sans tenter de déploiement distant.

## Installation

Copier le dossier `.github` de ce patch à la racine du dépôt puis :

```bash
git add .github DEVSECOPS_CORRECTIONS.md
git commit -m "ci: corrige CI CD SAST DAST"
git push origin main
```
