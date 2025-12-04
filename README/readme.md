# Quizzeo

Quizzeo est une application web de gestion de quiz avec plusieurs rôles :

- **Admin**
- **École (school)**
- **Entreprise (company)**
- **Utilisateur (user)**

---

## 1. Installation

1. Copier le projet dans le dossier web (par ex. `C:\xampp\htdocs\Projet_Web_Quizzeo`).
2. Importer le fichier SQL :

   - Ouvrir phpMyAdmin  
   - Créer une base de données (ex. `quizzeo`)  
   - Importer `Database/schema.sql`

3. Configurer la connexion à la BDD dans :

   - `config/database.php`

4. Vérifier le chemin de base :

   - Dans le code, `APP_BASE` vaut `/Projet_Web_Quizzeo`
   - L’URL d’accès locale sera donc :  
     `http://localhost/Projet_Web_Quizzeo/`

---

## 2. Navigation de base

- **Page d’accueil** : `/`
- **Connexion** : `/login`
- **Inscription** : `/register`
- **Déconnexion** : `/logout`

Après connexion, le bouton **“Tableau de bord”** t’envoie automatiquement vers le bon espace selon ton rôle.

---

## 3. Rôles et fonctionnalités

### a) Admin – `/admin`

- Voir la liste de tous les **utilisateurs**
  - Colonne *Statut* : ✅ Actif / ❌ Inactif  
  - Bouton **Activer / Désactiver** pour bloquer un compte
- Voir la liste de tous les **quiz**
  - Colonne *Actif* : ✅ Oui / ❌ Non  
  - Bouton **Activer / Désactiver** pour rendre un quiz disponible ou non

### b) École – `/school`

- Accéder au **tableau de bord de l’école**
  - Bouton **Créer un nouveau quiz** → `/school/quiz_create`
  - Liste des quiz de l’école avec :
    - **Modifier** → `/school/quiz_edit?id=...`
    - **Publier** (si statut = `draft`) → change le statut en `launched`
    - **Résultats** → `/school/quiz_result?id=...`

### c) Entreprise – `/company`

- Accéder au **tableau de bord entreprise**
  - Texte de bienvenue + créateur de quiz
  - Bouton **Créer un nouveau quiz** → `/company/survey_create`
  - Liste des quiz de l’entreprise avec :
    - **Modifier** → `/company/survey_edit?id=...`
    - **Publier** (si statut = `draft`) → `/company/quiz_launch?id=...`
- Suivre :
  - le statut du quiz (`draft`, `launched`, etc.)
  - le nombre de participants
  - les dates de création / mise à jour

### d) Utilisateur – `/user`

- Voir **“Mes quiz complétés”**
  - Nom du quiz
  - Nombre de tentatives
  - Dernier score
  - Lien **Voir l’historique** → `/user/attempt_history?quiz_id=...`

Les quiz sont accessibles via les liens publics générés (selon ta logique dans `PublicController`).

---

## 4. Structure générale

- `index.php` : routeur principal (gère les URLs)
- `Controller/` : logique métier (AdminController, SchoolController, CompanyController, etc.)
- `Model/` : requêtes vers la base (UserModel, QuizModel, ...)
- `View/` : fichiers de vues (HTML/PHP)
- `assets/css/style.css` : style global (navbar, dashboards, tables, etc.)

---

## 5. Compte de test

Adapter selon ce que tu as dans ta base, par exemple :

- **Admin** : `admin@example.com` / `motdepasse`
- **School** : `school@example.com` / `motdepasse`
- **Company** : `company@example.com` / `motdepasse`
- **User** : `user@example.com` / `motdepasse`

*(À compléter avec tes vrais comptes de démo.)*

---

Si quelque chose ne fonctionne pas (404 ou erreur PHP), vérifier en priorité :

- la route correspondante dans `index.php`
- le lien dans la vue (`href` / `action`)
- la fonction appelée dans le bon contrôleur.
