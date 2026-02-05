# Cahier de Charges - Système de Gestion de Garage Automobile

## 1. Présentation du Projet

### 1.1 Contexte
Développement d'un système complet de gestion de garage automobile permettant aux clients de suivre leurs véhicules et réparations, aux mécaniciens de gérer les interventions, et aux administrateurs de superviser l'ensemble des opérations.

### 1.2 Objectifs
- Digitaliser la gestion des véhicules et des réparations
- Offrir une interface mobile pour les clients
- Fournir un tableau de bord web pour l'administration
- Automatiser les notifications via Firebase
- Centraliser les données dans une base PostgreSQL

### 1.3 Périmètre
Le projet comprend trois composants principaux :
1. **Application mobile** (Ionic/Angular) - Interface client
2. **Application web** (Vue.js) - Interface administrateur
3. **API Backend** (Laravel 10) - Logique métier et base de données

---

## 2. Architecture Technique

### 2.1 Stack Technologique

#### Backend
- **Framework** : Laravel 10
- **Langage** : PHP 8.1+
- **Base de données** : PostgreSQL
- **Authentification** : Laravel Sanctum (tokens JWT)
- **Notifications** : Firebase Cloud Messaging via `kreait/laravel-firebase`
- **Conteneurisation** : Docker (nginx + PHP-FPM + PostgreSQL)

#### Application Mobile
- **Framework** : Ionic 7
- **Framework JS** : Angular 15
- **Langage** : TypeScript 4.8
- **Capacitor** : Pour builds natifs Android/iOS
- **Tests** : Jasmine/Karma

#### Application Web
- **Framework** : Vue.js 3
- **Build Tool** : Vite 7
- **Langage** : JavaScript (ES6+)
- **Router** : Vue Router 5
- **HTTP Client** : Axios
- **Tests** : Vitest

### 2.2 Diagramme d'Architecture

```mermaid
graph TB
    subgraph "Clients"
        Mobile[Application Mobile<br/>Ionic/Angular]
        Web[Application Web<br/>Vue.js]
    end
    
    subgraph "Backend"
        API[API REST Laravel<br/>Port 8000]
        DB[(PostgreSQL<br/>Database)]
        Firebase[Firebase<br/>Notifications]
    end
    
    Mobile -->|REST API| API
    Web -->|REST API| API
    API -->|SQL| DB
    API -->|Push Notifications| Firebase
    Firebase -->|FCM| Mobile
    
    style Mobile fill:#4CAF50
    style Web fill:#2196F3
    style API fill:#FF9800
    style DB fill:#9C27B0
    style Firebase fill:#FFC107
