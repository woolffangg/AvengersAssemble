# Architecture MVC avec couche DAO - ChatWeb

## Vue d'ensemble
Cette application respecte maintenant une architecture MVC (Model-View-Controller) propre avec une couche DAO (Data Access Object) pour séparer clairement les responsabilités.

## Structure des couches

### 1. Couche DAO (Data Access Object)
**Localisation :** `/chatweb/dao/`
**Responsabilité :** Accès aux données et interactions avec la base de données

#### Fichiers DAO :
- **`UserDAO.php`** : Gestion des utilisateurs
  - CRUD complet (Create, Read, Update, Delete)
  - Authentification et vérifications d'existence
  - Gestion des rôles

- **`SalonDAO.php`** : Gestion des salons
  - CRUD des salons
  - Requêtes avec informations du propriétaire
  - Gestion de la visibilité

- **`MessageDAO.php`** : Gestion des messages
  - Optimisé pour le chat en temps réel
  - Support SSE (Server-Sent Events)
  - Requêtes avec jointures utilisateur

- **`MembreDAO.php`** : Gestion des relations membre-salon
  - Ajout/suppression de membres
  - Vérification d'appartenance
  - Requêtes sur les relations

### 2. Couche Model (Modèles métier)
**Localisation :** `/chatweb/model/`
**Responsabilité :** Logique métier et règles de gestion

#### Modèles :
- **`User.php`** : 
  - Entité utilisateur avec getters/setters
  - Méthodes métier : `isAdmin()`, `canModerateSalon()`
  - Délégation vers UserDAO pour l'accès aux données

- **`Salon.php`** :
  - Entité salon avec logique d'accès
  - Méthodes métier : `canUserAccess()`, `canUserManage()`
  - Gestion des membres via MembreDAO

- **`Message.php`** :
  - Entité message avec validation
  - Méthodes métier : `canUserPost()`, `isValid()`, `getSafeMessage()`
  - Délégation vers MessageDAO

### 3. Couche Service
**Localisation :** `/chatweb/service/`
**Responsabilité :** Orchestration et logique applicative complexe

#### Services :
- **`UserService.php`** : 
  - Gestion de l'authentification
  - Inscription avec validations
  - Utilise User et UserDAO

- **`SalonService.php`** :
  - Gestion des salons et membres
  - Logique d'accès et permissions
  - Utilise Salon, SalonDAO et MembreDAO

### 4. Couche Controller
**Localisation :** `/chatweb/controller/`
**Responsabilité :** Traitement des requêtes HTTP et coordination

#### Contrôleurs :
- **`UserController.php`** : Actions utilisateur (login, register, logout)
- **`SalonController.php`** : Actions salon (kick, visibility, quit)
- **`MessageController.php`** : Actions message (add, stream SSE)

### 5. Couche View
**Localisation :** `/chatweb/view/`
**Responsabilité :** Présentation et interface utilisateur

## Avantages de cette architecture

### 1. Séparation des responsabilités
- **DAO** : Uniquement l'accès aux données
- **Model** : Uniquement la logique métier
- **Service** : Orchestration et logique applicative
- **Controller** : Traitement des requêtes HTTP
- **View** : Présentation

### 2. Maintenabilité
- Code plus lisible et organisé
- Modifications facilitées
- Tests unitaires possibles sur chaque couche

### 3. Réutilisabilité
- DAOs réutilisables par différents services
- Modèles indépendants de la couche d'accès
- Services réutilisables par différents contrôleurs

### 4. Évolutivité
- Facile d'ajouter de nouvelles fonctionnalités
- Changement de base de données simplifié (modification des DAOs uniquement)
- Support de nouveaux formats de données

## Flux de données

```
[HTTP Request] 
    ↓
[Controller] → [Service] → [Model] → [DAO] → [Database]
    ↓              ↓         ↓        ↓
[View] ←――――――――――――――――――――――――――――――――――――――
```

## Migration effectuée

### Avant (Mélange des responsabilités)
- Modèles avec accès direct à la base
- Logique métier dans les contrôleurs
- Requêtes SQL dispersées

### Après (Architecture propre)
- DAO pour l'accès aux données
- Modèles avec logique métier pure
- Services pour l'orchestration
- Contrôleurs légers et focalisés

## Compatibilité SSE
La nouvelle architecture maintient la compatibilité avec le système SSE (Server-Sent Events) :
- `MessageDAO::findNewMessages()` optimisé pour SSE
- `MessageController::streamMessages()` utilise les nouveaux DAOs
- Pas de régression fonctionnelle

## Tests recommandés
1. **Connexion/Inscription** : Vérifier UserService et UserDAO
2. **Création de salon** : Tester SalonService et SalonDAO
3. **Envoi de messages** : Valider MessageDAO et SSE
4. **Gestion des membres** : Contrôler MembreDAO

Cette architecture respecte les principes SOLID et les bonnes pratiques du développement PHP moderne.
