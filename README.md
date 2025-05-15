# Espace Travail API

## Présentation

Espace Travail API est une application backend développée en PHP avec le framework Symfony. 
Elle fournit une API RESTful robuste pour la gestion des espaces de travail. 
Ce projet s’inscrit dans le cadre du BTS SIO SLAM, épreuve E6.

## Fonctionnalités principales

- Authentification et gestion des utilisateurs (JWT)
- Gestion des rôles et des permissions
- Création, modification et suppression pour les entités suivantes :
  - Utilisateurs
  - Espaces de travail et ses sous-espaces : bureaux, salles de réunion et espace collaboratif.
  - Equipements
  - Statuts
  - TypeBureau
  - TypeAmbiance
  - Reservations equipements et espaces
- API conforme aux standards REST (API Platform)
- Documentation automatique de l’API (OpenAPI/Swagger)
- Sécurité avancée (JWT, CORS, validation)

## Technologies utilisées

- **PHP 8.3**
- **Symfony 7**
- **API Platform**
- **Doctrine ORM**
- **JWT Authentication (LexikJWTAuthenticationBundle)**
- **MySQL**
- **Docker & Docker Compose**
- **Composer**
- **PHPUnit** (tests unitaires et couverture)
- **Monolog** (logs)
- **NelmioCorsBundle** (CORS)

## Prérequis

- Docker & Docker Compose
- Git
- Un éditeur de code (VSCode, PHPStorm, etc.)
- Postman ou un autre outil pour tester l’API (facultatif)
- Connaissance de Symfony et PHP

## Installation

1. **Cloner le dépôt**
   ```bash
   git clone <url-du-repo>
   cd espace_travail_api
   ```

2. **Configurer les variables d’environnement**
   Copiez le fichier `.env` en `.env.local` et adaptez les valeurs si besoin.

3. **Lancer les conteneurs Docker**
   ```bash
   docker compose up -d --build
   ```

4. **Installer les dépendances PHP**
   ```bash
   docker compose exec php composer install
   ```
4.1 **Accèder au phpmyadmin et page wev**
   Accédez à phpMyAdmin via l’URL suivante :  
   ```
  http://localhost:8080/index.php
   ```
   Identifiants par défaut :
   - **Utilisateur** : root
   - **Mot de passe** : root

   Accédez à l’application via l’URL suivante :  
      ```
      https://www.api-reservation.localhost/api
      ```

5. **Créer la base de données et exécuter les migrations**
   ```bash
   docker compose exec php php bin/console doctrine:database:create --if-not-exists
   docker compose exec php php bin/console doctrine:migrations:migrate
   ```

6. **Charger les fixtures(données de dev)**
   ```bash
   docker compose exec php php bin/console doctrine:fixtures:load --env=dev
   ```

7. Pour environnement de test
   ```bash
   docker compose exec php php bin/console doctrine:database:create --if-not-exists --env=test
   docker compose exec php php bin/console doctrine:migrations:migrate --env=test
   docker compose exec php php bin/console doctrine:fixtures:load --env=test
   ```
   
8. Pour environnement de production
   ```bash
   docker compose exec php php bin/console doctrine:database:create --if-not-exists --env=prod
   docker compose exec php php bin/console doctrine:migrations:migrate --env=prod
   ```
   Les fixtures ne sont pas chargées en production par sécurité

## Lancement des tests

Pour exécuter les tests unitaires et générer la couverture de code :
```bash
docker compose exec -T php mkdir -p var/coverage
XDEBUG_MODE=coverage docker compose exec -T php php bin/phpunit --coverage-clover var/coverage/clover.xml
```

## Documentation de l’API

Une documentation interactive est disponible à l’adresse :
```
https://www.api-reservation.localhost/api
```
(Accessible une fois les conteneurs démarrés)

## Bonnes pratiques & qualité

- Respect des conventions PSR et des standards Symfony
- Utilisation de l’outil PHPStan pour l’analyse statique
- Linting automatique via GitHub Actions
- Couverture de code mesurée avec PHPUnit et Xdebug
- Sécurité des endpoints via JWT et gestion fine des droits

## Structure du projet

- `src/` : Code source principal (entités, services, etc)
- `config/` : Configuration Symfony et bundles
- `migrations/` : Fichiers de migration de base de données
- `tests/` : Tests unitaires et fonctionnels
- `.docker/` : Configuration Docker
- `.github/` : Actions GitHub pour CI et linting
- `.env` : Variables d’environnement
- `docker-compose.yml` : Configuration Docker Compose 

## Contribution

Les contributions sont les bienvenues !  
Merci de respecter les conventions de code et de soumettre vos Pull Requests sur une branche dédiée.

## Auteur

Baptiste Digonnet
BTS SIO SLAM – Épreuve E6

## Licence

Ce projet est sous licence MIT.  
Voir le fichier [LICENSE](LICENSE) pour plus d’informations.

`