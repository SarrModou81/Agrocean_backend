# 🚀 Guide de Démarrage AGROCEAN Backend

## Prérequis
- **Laragon** installé et démarré sur votre machine Windows
- MySQL/MariaDB démarré dans Laragon
- PHP 8.1+ activé dans Laragon

## Étapes de Configuration

### 1. Démarrer Laragon
1. Ouvrir Laragon sur Windows
2. Cliquer sur **"Démarrer tout"** ou **"Start All"**
3. Vérifier que Apache et MySQL sont bien démarrés (icônes vertes)

### 2. Configuration de la Base de Données

#### Option A : Créer via HeidiSQL (inclus dans Laragon)
1. Dans Laragon, cliquer sur **"Base de données"** ou **"Database"**
2. Cela ouvre HeidiSQL
3. Créer une nouvelle base de données :
   - Nom : `agrocean_db`
   - Collation : `utf8mb4_unicode_ci`

#### Option B : Créer via ligne de commande
```bash
# Ouvrir le terminal Laragon et exécuter :
mysql -u root -p
# (mot de passe par défaut : vide, appuyer sur Entrée)

CREATE DATABASE agrocean_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

### 3. Configurer le fichier .env

Dans `C:\laragon\www\gestion-stock-agrocean\plus\Agrocean_backend\.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agrocean_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Installer les Dépendances et Migrer

Ouvrir le terminal Laragon dans le dossier backend :

```bash
# Se placer dans le dossier
cd C:\laragon\www\gestion-stock-agrocean\plus\Agrocean_backend

# Installer les dépendances (si ce n'est pas déjà fait)
composer install

# Générer la clé JWT
php artisan jwt:secret

# Exécuter les migrations et seeders
php artisan migrate:fresh --seed
```

### 5. Démarrer le Serveur Laravel

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Le serveur sera accessible sur : `http://localhost:8000`

### 6. Démarrer le Frontend Angular

Dans un nouveau terminal :

```bash
cd C:\laragon\www\gestion-stock-agrocean\plus\Agrocean_frontend

# Installer les dépendances (si ce n'est pas déjà fait)
npm install

# Démarrer le serveur de développement
ng serve
```

Le frontend sera accessible sur : `http://localhost:4200`

## 🔧 Commandes Utiles

### Backend Laravel
```bash
# Voir les routes disponibles
php artisan route:list

# Nettoyer le cache
php artisan cache:clear
php artisan config:clear

# Créer un nouvel utilisateur admin (via tinker)
php artisan tinker
>>> $user = App\Models\User::create(['nom' => 'Admin', 'prenom' => 'Super', 'email' => 'admin@agrocean.com', 'password' => bcrypt('password123'), 'role' => 'Administrateur', 'is_active' => true]);

# Vérifier l'utilisateur créé
>>> App\Models\User::all();
>>> exit
```

### Frontend Angular
```bash
# Compiler en mode production
ng build --configuration=production

# Vérifier les erreurs TypeScript
npx tsc --noEmit
```

## 📝 Comptes de Test

Après avoir exécuté `php artisan migrate:fresh --seed`, vous aurez :

### Utilisateur Administrateur
- **Email** : admin@agrocean.com
- **Mot de passe** : password

### Utilisateur Commercial
- **Email** : commercial@agrocean.com
- **Mot de passe** : password

### Utilisateur Gestionnaire de Stock
- **Email** : stock@agrocean.com
- **Mot de passe** : password

## ⚠️ Dépannage

### Erreur "Failed to connect to localhost port 8000"
- **Solution** : Le serveur Laravel n'est pas démarré. Exécutez `php artisan serve`

### Erreur "Connection refused (MySQL)"
- **Solution** : MySQL n'est pas démarré dans Laragon. Ouvrez Laragon et cliquez sur "Démarrer tout"

### Erreur "CORS policy"
- **Solution** : Vérifiez que `config/cors.php` contient `'allowed_origins' => ['*']` en mode développement

### Erreur "SQLSTATE[HY000] [1049] Unknown database"
- **Solution** : La base de données n'existe pas. Créez-la via HeidiSQL ou la ligne de commande

### Erreur de compilation Angular
- **Solution** : Supprimez `node_modules` et réinstallez :
  ```bash
  rm -rf node_modules
  npm install
  ```

## 📚 Architecture

```
Agrocean_backend/      # API Laravel
├── app/
│   ├── Http/
│   │   └── Controllers/  # Contrôleurs API
│   ├── Models/           # Modèles Eloquent
│   └── Services/         # Logique métier
├── database/
│   ├── migrations/       # Migrations de base de données
│   └── seeders/          # Données de test
└── routes/
    └── api.php           # Routes API

Agrocean_frontend/     # Application Angular
├── src/
│   ├── app/
│   │   ├── core/        # Services, guards, interceptors
│   │   ├── features/    # Modules fonctionnels
│   │   └── shared/      # Composants partagés
│   └── environments/    # Configuration
```

## 🎯 Prochaines Étapes

1. ✅ Démarrer Laragon
2. ✅ Créer la base de données
3. ✅ Configurer .env
4. ✅ Migrer la base de données
5. ✅ Démarrer Laravel (`php artisan serve`)
6. ✅ Démarrer Angular (`ng serve`)
7. ✅ Se connecter avec admin@agrocean.com / password
8. 🚀 Commencer à utiliser l'application !

## 💡 Conseils

- Gardez Laragon ouvert pendant le développement
- Utilisez deux terminaux : un pour Laravel, un pour Angular
- Les changements de code Angular sont automatiquement rechargés (hot reload)
- Pour Laravel, vous devrez redémarrer le serveur si vous modifiez .env ou des fichiers de configuration

## 📞 Support

Si vous rencontrez des problèmes :
1. Vérifiez les logs Laravel : `storage/logs/laravel.log`
2. Ouvrez la console du navigateur (F12) pour les erreurs frontend
3. Vérifiez que tous les services Laragon sont démarrés

Bon développement ! 🎉
