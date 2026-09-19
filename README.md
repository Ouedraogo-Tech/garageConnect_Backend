# GarageConnect — Backend (API Laravel)

Application de gestion de garage automobile — API REST sécurisée par rôle (Administrateur / Technicien).

Ce dépôt contient le back-end du projet. Le front-end React se trouve dans un dépôt séparé : [garageconnect_frontend](https://github.com/Ouedraogo-Tech/garageconnect_frontend)

## Technologies utilisées

- Laravel 13 (PHP 8.3)
- Laravel Sanctum (authentification par token)
- MySQL
- PHPUnit (tests automatisés)

## Fonctionnalités principales

- Authentification par rôle (Administrateur / Technicien)
- Gestion complète des véhicules, techniciens et réparations (CRUD + recherche)
- Gestion des comptes utilisateurs (réservée à l'administrateur)
- Contrôle d'accès fin : un technicien ne peut modifier que les réparations qui lui sont assignées

## Installation

1. Cloner ce dépôt
2. Installer les dépendances : `composer install`
3. Copier `.env.example` en `.env` et renseigner les identifiants de votre base de données
4. Générer la clé d'application : `php artisan key:generate`
5. Lancer les migrations : `php artisan migrate`
6. Démarrer le serveur : `php artisan serve`

L'API est alors accessible sur `http://127.0.0.1:8000`.

## Comptes de test

| Rôle | Email | Mot de passe |

| Administrateur | admin@garage.test | password123 |
| Technicien | technicien@garage.test | password123 |

## Tests

```bash
php artisan test
```

7 tests automatisés (autorisation par rôle, validation, réponses API).
