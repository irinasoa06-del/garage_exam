# Guide de Configuration Firebase (Complet)

Ce guide explique comment configurer Firebase pour tout le projet (Backend Laravel + Frontend Vue + Mobile).

## 1. Création du Projet Firebase

1.  Rendez-vous sur la [Console Firebase](https://console.firebase.google.com/).
2.  Connectez-vous avec votre compte Google.
3.  Cliquez sur **"Créer un projet"**.
4.  Nommez votre projet (ex: `garage-reparations`).
5.  Désactivez "Google Analytics" (inutile pour ce test) et validez.

## 2. Activation de la Base de Données (Firestore)

1.  Dans le menu de gauche, allez dans **"Création"** > **"Firestore Database"**.
2.  Cliquez sur **"Créer une base de données"**.
3.  **Mode de sécurité** : Choisissez **"Mode test"** (IMPORTANT pour le développement, sinon vous aurez des erreurs de permission).
4.  **Emplacement** : Choisissez `eur3` (Europe) ou laisser par défaut (`us-central1`).
5.  Cliquez sur **"Activer"**.

## 3. Configuration du Backend (Laravel API)

Le Backend a besoin d'un accès "Admin" pour écrire dans la base de données.

1.  Dans la console Firebase, cliquez sur la **roue dentée** (Paramètres Projet) > **"Paramètres du projet"**.
2.  Allez dans l'onglet **"Comptes de service"**.
3.  Cliquez sur **"Générer une nouvelle clé privée"**.
4.  Un fichier `.json` va se télécharger.
5.  **Renommez** ce fichier en `firebase_credentials.json`.
6.  **Déplacez** ce fichier à la racine du dossier Backend (`examen_garage/laravel-web/`).
    *   *Chemin final attendu dans Docker : `/var/www/html/firebase_credentials.json`*
7.  Ouvrez le fichier `.env` du Backend (`laravel-web/.env`) et mettez à jour :
    ```ini
    FIREBASE_PROJECT_ID=votre-id-de-projet-firebase (ex: garage-reparations)
    FIREBASE_CREDENTIALS=/var/www/html/firebase_credentials.json
    ```
    *(Note : Si vous avez utilisé le script dummy, remplacez les valeurs fictives).*

8.  **Redémarrez le conteneur Laravel** pour prendre en compte le changement :
    ```powershell
    docker compose -f laravel-web/docker-compose-image.yml restart laravel
    ```

## 4. Configuration du Frontend (Web & Mobile)

Le Frontend et le Mobile partagent la même configuration "Client".

1.  Dans les **"Paramètres du projet"** Firebase (onglet Général).
2.  Descendez à **"Vos applications"**.
3.  Cliquez sur l'icône **Web** (`</>`).
4.  Donnez un nom (ex: `Garage Web`).
5.  Copiez l'objet `firebaseConfig` qui apparaît.
6.  Ouvrez le fichier `garage-web-front/vue-project/src/services/firebase.js`.
7.  Remplacez le bloc `firebaseConfig` par le vôtre :

    ```javascript
    const firebaseConfig = {
      apiKey: "AIzaSyD...",
      authDomain: "garage-reparations.firebaseapp.com",
      projectId: "garage-reparations",
      storageBucket: "garage-reparations.appspot.com",
      messagingSenderId: "...",
      appId: "..."
    };
    ```

## 5. Vérification

1.  Lancez le Frontend (`npm run dev`).
2.  Lancez l'API (`docker compose up`).
3.  Créez une réparation via l'API ou l'interface Admin.
4.  Vérifiez dans la **Console Firebase > Firestore Database** : une collection `reparations` (ou `interventions`) doit apparaître avec vos données !

---
*En cas de problème "Permission Denied" sur le frontend : Vérifiez que vous êtes bien en "Mode Test" dans l'onglet "Règles" de Firestore.*
