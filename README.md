# AUTOTRADE — Laravel (vente de voitures)

Application **Laravel 11** + **Blade** + **Bootstrap 5** + **JS** pour la vente de véhicules d'exception.

> Ce projet est un site de **vente uniquement** : la fonctionnalité de location a été retirée.

## Installation

### 1. Créer la base MySQL (phpMyAdmin)

1. Ouvre **phpMyAdmin** (souvent http://localhost/phpmyadmin)
2. Onglet **Bases de données** → nom : `autotrade` → Collation : `utf8mb4_unicode_ci` → **Créer**

### 2. Configurer Laravel

Le fichier `.env` est déjà réglé pour MySQL :

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=autotrade
DB_USERNAME=root
DB_PASSWORD=
```

Adapte `DB_USERNAME` / `DB_PASSWORD` si ton MySQL a un mot de passe (WAMP/Laragon parfois vide, MAMP parfois `root`).

### 3. Installer et migrer

```bash
cd autotrade-laravel
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Ouvrez http://127.0.0.1:8000

Tu verras ensuite les tables dans phpMyAdmin : `users`, `vehicles`, `purchase_requests`, `contact_messages`, etc.

## Connexion administrateur

| Champ | Valeur |
|-------|--------|
| URL | http://127.0.0.1:8000/admin/login |
| E-mail | `admin@autotrade.fr` |
| Mot de passe | `admin123` |

Si le compte n'existe pas encore (projet déjà migré) :

```bash
php artisan db:seed --class=AdminSeeder
```

ou tout rejouer :

```bash
php artisan migrate:fresh --seed
```

## Fonctionnalités admin (après connexion)

- **Catalogue** : ajouter / modifier / supprimer un véhicule, basculer disponible ↔ vendu
- **Achats** : changer le statut des demandes d'achat (en_attente, en_cours, signee, refusee)
- **Messages** : marquer comme lus
- **Déconnexion**

Les routes `/admin/*` (sauf login) sont protégées par le middleware `auth`.

## Pages publiques

| Page | Route |
|------|-------|
| Accueil | `/` |
| Catalogue | `/catalogue` |
| Vente | `/vente` |
| Détail | `/vehicule/{slug}` |
| Demande d'achat | `/demande-achat/{slug}` |
| Contact | `/contact` |

## Stack

- Laravel 11, **MySQL** (phpMyAdmin)
- Bootstrap 5 + CSS custom (thème sombre / volt)
- Auth session Laravel (pas de Breeze obligatoire)
