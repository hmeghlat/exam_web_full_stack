# API Hamsters & Utilisateurs (Symfony 7.3)

## 🎯 Description
Une API REST construite avec **Symfony 7.3** permettant :
- L'inscription, l'authentification et la gestion des utilisateurs via **JWT** (LexikJWTAuthenticationBundle).
- La gestion de hamsters associés aux utilisateurs 



## 📦 Dépendances clés
- Symfony Framework 7.3
- Doctrine ORM / Migrations / Fixtures
- LexikJWTAuthenticationBundle (authentification stateless)
- Faker (fixtures)
- Validator / Serializer / PropertyAccess / PropertyInfo

Version PHP requise: **>= 8.2**



## 🚀 Installation (Windows PowerShell)
```powershell
# 1. Cloner
git clone https://github.com/hmeghlat/exam_web_full_stack.git 

# 2. Installer dépendances
composer install

# 3. Copier .env.local (si nécessaire) et configurer la base
# Dans .env ou .env.local : DATABASE_URL="mysql://user:pass@127.0.0.1:3306/exam?charset=utf8mb4"

# 4. Générer (si absent) les clés JWT
# Les clés existent dans config/jwt/, sinon:
openssl genrsa -out config/jwt/private.pem 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

# 5. Définir les variables dans .env.local
# JWT_SECRET_KEY="%kernel.project_dir%/config/jwt/private.pem"
# JWT_PUBLIC_KEY="%kernel.project_dir%/config/jwt/public.pem"
# JWT_PASSPHRASE="change_me"

# 6. Créer la base et appliquer les migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 7. (Optionnel) Charger des fixtures si ajoutées
php bin/console doctrine:fixtures:load 

# 8. Lancer le serveur de développement
symfony server:start 
```
Accès API: `http://127.0.0.1:8000`

## 🔑 Variables d'environnement essentielles
```

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=change_me
APP_ENV=dev

Creer un fichier .env.local est mettre ces deux ligne :
APP_SECRET=ChangerCetteValeur
DATABASE_URL=mysql://user:pass@127.0.0.1:3306/exam?charset=utf8mb4

```


