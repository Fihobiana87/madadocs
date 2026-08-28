# MadaDocs

MadaDocs est une application web légère pour créer des documents administratifs et professionnels malgaches (CV, lettres, demandes, factures) en quelques minutes, sans complexité inutile. Conçue pour tourner sur un hébergement mutualisé gratuit (InfinityFree) : PHP + MySQL uniquement, aucune dépendance lourde.

## Stack technique

- PHP 8.1+ (testé avec PHP 8.5), MVC maison (aucun framework lourd)
- MySQL / MariaDB via PDO (SQLite supporté en local pour le développement)
- Dompdf (vendorisé, pas besoin de Composer sur l'hébergement)
- HTML5, CSS custom (aucun framework CSS), JavaScript vanilla
- IA optionnelle via l'API Groq (gratuite), remplaçable par un autre fournisseur

## Structure du projet

```
/app
    /Controllers    Contrôleurs HTTP
    /Core           Noyau (routeur, DB, session, CSRF, auth, rendu PDF, client IA...)
    /Models         Accès aux données (PDO, requêtes préparées)
    /views          Vues PHP (layouts, pages, vues PDF)
    /helpers        Fonctions globales (env, config, e(), csrf_field()...)
/public             Racine web (à publier telle quelle sur l'hébergement)
    /assets         CSS / JS / images
    index.php       Front controller
    .htaccess       Réécriture d'URL + blocage de fichiers sensibles
/config             Configuration (app, database, ai)
/storage            Logs et PDF générés (jamais exposé publiquement)
/vendor             Dompdf et dépendances (vendorisées, à committer)
database.sql        Schéma + données de départ (catégories et modèles de documents)
routes.php          Table de routes
```

## Installation locale (développement)

1. `composer install` si vous ajoutez des dépendances (Dompdf est déjà vendorisé dans le dépôt).
2. Copiez `.env.example` en `.env` et complétez les valeurs. Pour tester sans MySQL, utilisez :
   ```
   DB_DRIVER=sqlite
   DB_SQLITE_PATH=chemin/vers/storage/madadocs.sqlite
   ```
   puis importez `database.sql` adapté (voir script de dev) ou une base MySQL locale avec `database.sql` tel quel.
3. Générez une clé d'application unique : `php -r "echo bin2hex(random_bytes(32));"` → `APP_KEY` dans `.env`.
4. Lancez le serveur de développement PHP **en pointant sur `public/`** :
   ```
   php -S localhost:8000 -t public public/router.php
   ```
5. Ouvrez `http://localhost:8000`.
6. Rendez-vous sur `/admin/installation` pour créer le tout premier compte administrateur (cette page se désactive automatiquement dès qu'un admin existe).

## Déploiement sur InfinityFree

InfinityFree ne fournit pas d'accès SSH/Composer et son répertoire web public (`htdocs`) est le seul dossier accessible depuis le navigateur. Pour garder `app/`, `config/`, `storage/` et `vendor/` **hors d'atteinte du public**, on les place à la racine du compte (à côté de `htdocs`), et seul le contenu de `public/` va dans `htdocs`.

1. **Créer la base de données** dans le panneau InfinityFree (section MySQL Databases), noter l'hôte, le nom, l'utilisateur et le mot de passe fournis.
2. **Importer `database.sql`** via phpMyAdmin (bouton Importer).
3. **Uploader les fichiers par FTP** :
   - Le contenu du dossier `public/` → directement dans `htdocs/`
   - Les dossiers `app/`, `config/`, `storage/`, `vendor/`, ainsi que `routes.php`, `composer.json`, `composer.lock`, `.env` (créé à partir de `.env.example`) → dans le dossier **parent** de `htdocs` (à la racine de votre compte, au même niveau que `htdocs`).
4. **Adapter `public/index.php` si nécessaire** : les chemins utilisent `dirname(__DIR__)` pour remonter d'un niveau vers `app/`, `config/`, `vendor/`. Cela fonctionne à condition que `public/` et les autres dossiers restent à la même profondeur relative que dans le dépôt (ce qui est le cas avec la disposition ci-dessus : `htdocs` = `public/`, et son parent contient `app/`, `config/`, etc.).
5. **Configurer `.env`** sur le serveur avec les identifiants MySQL réels, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://votredomaine.infinityfreeapp.com`, et une `APP_KEY` unique générée pour la production (ne réutilisez jamais celle du développement).
6. **Vérifier les permissions** : `storage/logs` et `storage/generated` doivent être accessibles en écriture par PHP (755 en général suffit sur InfinityFree).
7. Ouvrez votre domaine puis `/admin/installation` pour créer le compte administrateur.

### Pourquoi cette disposition ?

Un hébergeur mutualisé sans SSH ne permet pas de définir un « document root » personnalisé autrement qu'en pointant `htdocs` sur le bon dossier. En ne mettant que `public/` dans `htdocs`, le code applicatif, la configuration (avec les identifiants de base de données) et les logs ne sont jamais servables directement par le navigateur, même en cas d'erreur de configuration Apache.

## Configuration de l'assistant IA

MadaDocs utilise [Groq](https://console.groq.com) (offre gratuite, API compatible OpenAI) pour l'assistant de rédaction. Le fournisseur est isolé dans `app/Core/AiClient.php` et remplaçable :

1. Créez un compte gratuit sur console.groq.com et générez une clé API.
2. Dans `.env` :
   ```
   AI_PROVIDER=groq
   AI_API_KEY=votre_cle
   AI_MODEL=openai/gpt-oss-20b
   ```
3. Si `AI_PROVIDER=none` ou que la clé est absente/invalide, l'assistant affiche un message clair et le reste du site fonctionne normalement — l'IA n'est jamais une dépendance bloquante.
4. Pour changer de fournisseur, ajoutez une méthode `callVotreFournisseur()` dans `AiClient::complete()` sans toucher au reste de l'application.

Un anti-abus simple (limite de requêtes par session et par heure) est appliqué côté serveur dans `AiClient::isRateLimited()`.

## Sécurité

- Mots de passe hashés avec `password_hash()` (bcrypt), jamais stockés en clair.
- Toutes les requêtes SQL utilisent des requêtes préparées PDO.
- Protection CSRF sur tous les formulaires qui modifient des données.
- Sortie systématiquement échappée (`e()` / `htmlspecialchars`) pour prévenir le XSS ; Content-Security-Policy stricte (pas de script inline).
- Sessions : cookies `HttpOnly`, `SameSite=Lax`, régénération de l'identifiant à la connexion.
- Panneau admin protégé par rôle, création du premier compte admin via une page qui se désactive automatiquement.
- Téléchargements de documents anonymes protégés par un jeton signé (HMAC), pour empêcher l'énumération d'identifiants.
- Erreurs PHP jamais affichées en production (`APP_DEBUG=false`), page 500 générique côté utilisateur, détails journalisés dans `storage/logs/`.
- `.htaccess` bloque l'accès direct aux fichiers `.env`, `.log`, `.sql`, `.md` si jamais ils se retrouvaient dans `htdocs`.

## Dépannage

| Symptôme | Piste |
|---|---|
| Page blanche / erreur 500 | Vérifiez `storage/logs/php-errors.log` et `storage/logs/app-*.log` |
| « database_unavailable » | Vérifiez les identifiants MySQL dans `.env` et que la base a été importée |
| PDF non généré | Vérifiez que `storage/generated/` est accessible en écriture |
| Assistant IA indisponible | Vérifiez `AI_PROVIDER` et `AI_API_KEY` dans `.env` ; consultez les logs pour l'erreur exacte (réseau, quota, réponse invalide) |
| Page admin inaccessible | Le compte doit avoir le rôle `admin` ; utilisez `/admin/installation` s'il n'existe encore aucun administrateur |
