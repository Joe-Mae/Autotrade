# Déploiement AUTOTRADE

## 1. Pré-requis serveur

- PHP 8.2+ (8.4 recommandé) avec : `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`, `curl`, `zip`
- MySQL 8 / MariaDB
- Composer 2
- **Racine web pointant sur `public/`** (jamais sur la racine du projet)
- HTTPS (Let's Encrypt ou certificat du client)

## 2. Mise en ligne

```bash
# 1. Transférer le code (sans vendor/, .env, node_modules/)
# 2. Dépendances de production
composer install --no-dev --optimize-autoloader

# 3. Créer le .env à partir de .env.example, puis renseigner :
#    APP_ENV=production
#    APP_DEBUG=false
#    APP_URL=https://le-domaine-du-client
#    DB_* , MAIL_* , ADMIN_EMAIL , ADMIN_PASSWORD

# 4. Nouvelle clé (installation neuve)
php artisan key:generate

# 5. Base de données
php artisan migrate --force

# 6. Création du compte administrateur (une seule fois)
php artisan db:seed --class=AdminSeeder --force

# 7. Changer immédiatement le mot de passe initial
php artisan admin:password

# 8. Caches de production
php artisan optimize
```

Droits d'écriture nécessaires : `storage/`, `bootstrap/cache/`, `public/uploads/`.

## 3. Les pièges à éviter

### APP_KEY et base de données
Si vous importez la base **locale** sur le serveur, copiez aussi la **même `APP_KEY`**.
Sinon les valeurs chiffrées (mot de passe SMTP) deviennent illisibles : il faudra
ressaisir le mot de passe SMTP dans Administration → Paramètres.
Pour une installation neuve, `key:generate` est la bonne solution.

### Racine web
Si Apache/Nginx pointe sur la racine du projet au lieu de `public/`, le fichier `.env`
devient téléchargeable. C'est la erreur de déploiement la plus fréquente.

### Nginx et le dossier uploads
Le fichier `public/uploads/.htaccess` n'est lu que par Apache. Sous Nginx, ajouter :

```nginx
location ~* ^/uploads/.*\.(php|phtml|phar|php[0-9])$ {
    deny all;
}
```

La validation des images côté Laravel reste la protection principale.

### Reverse proxy / Cloudflare
`bootstrap/app.php` ne fait confiance qu'à `127.0.0.1` et `::1`. Si le serveur est
derrière Cloudflare ou un Nginx proxy sur une autre machine, ajouter son IP :

```php
$middleware->trustProxies(at: ['127.0.0.1', '::1', 'IP_DU_PROXY']);
```

Sans cela : cookies non `Secure`, URLs générées en `http://`.

### Cache de configuration
Après toute modification du `.env` en production :

```bash
php artisan optimize:clear
php artisan optimize
```

Les commandes `admin:password` / `admin:email` lisent `config/admin.php` :
elles continuent de fonctionner avec le cache actif.

## 4. Vérifications après déploiement

- Page d'accueil, catalogue et page véhicule s'affichent en HTTPS.
- Formulaire de contact : un envoi réel arrive bien (puis vérifier les indésirables).
- `/admin` redirige vers `/admin/login` sans être connecté.
- En-têtes présents :

```bash
curl -I https://le-domaine-du-client/
```

Doivent apparaître : `Content-Security-Policy`, `X-Frame-Options`,
`X-Content-Type-Options`, `Referrer-Policy`, `Strict-Transport-Security`.
`X-Powered-By` doit être absent.

- Sauvegarder la base et le `.env` après l'installation.

## 5. Changer l'e-mail ou le mot de passe en production

Depuis un terminal SSH (ou cPanel → Terminal), dans le dossier du projet :

```bash
php artisan admin:password
php artisan admin:email "nouvel@email.fr"
```

Options utiles :

```bash
php artisan admin:password "MotDePasseSolide2026!"   # sans invite
php artisan admin:email "nouvel@email.fr" --current=ancien@email.fr
```

Pensez à mettre à jour `ADMIN_EMAIL` dans le `.env` (facultatif).

## 6. Rappels de sécurité

- `APP_DEBUG=false` et `APP_ENV=production` en permanence.
- Ne jamais exécuter `db:seed` sur une base déjà en service (le seeder ne fait rien
  si un compte existe, mais autant éviter).
- Le mot de passe administrateur doit rester **unique à ce site**.
- Laravel 11 est en fin de support : planifier la montée en 12/13 pour corriger
  les avis `composer audit` (dont la faille CRLF de la règle `email`).
