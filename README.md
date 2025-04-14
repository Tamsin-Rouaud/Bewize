# TaskLinker

TaskLinker est une application Symfony qui permet de gérer des projets et les tâches associées, en respectant une logique de rôles et de droits d'accès pour les utilisateurs. Elle est conçue dans un cadre d'exercice de formation et met en pratique les principes de l'architecture MVC, la sécurité via les Voters, et la gestion des utilisateurs avec authentification.

## 🚀 Fonctionnalités principales

- Authentification sécurisée (login/logout)
- Deux rôles d’utilisateur :
  - 👨‍💼 **Chef de projet** : peut créer, modifier, supprimer des projets et des tâches
  - 👷 **Collaborateur** : peut voir ses projets, modifier le statut de ses propres tâches
- Affectation d'employés à un projet
- Affichage des tâches par statut
- Affectation d’une tâche à un employé
- Filtres d’accès via Voter pour sécuriser les actions
- UI claire avec visualisation des initiales des collaborateurs

## 🔒 Gestion des droits d’accès

L’application utilise des **Voters Symfony** pour définir précisément les droits :
- `ProjetVoter`
  - `VOIR_PROJET` : un collaborateur peut voir un projet s’il y est affecté
  - `MODIFIER_PROJET` : réservé aux chefs de projet
- `TacheVoter`
  - `VOIR_TACHE` : tous les utilisateurs peuvent consulter une tâche
  - `MODIFIER_STATUT` : uniquement l’employé affecté
  - `MODIFIER_TACHE` : uniquement le chef de projet
  - Suppression de tâche : uniquement le chef de projet


## 🧪 Installation et utilisation

1. **Cloner le projet :**

```bash
git clone https://github.com/votre-utilisateur/bewize.git
cd tasklinker

2. **Installer les dépendances :**

bash
Copier
Modifier
composer install

3. **Configurer la base de données :**

-> Créer un fichier .env.local :
        DATABASE_URL="mysql://root:@127.0.0.1:3306/bewize"

-> Puis dans le terminal :
    symfony console doctrine:database:create
    symfony console doctrine:migrations:migrate
    symfony console doctrine:fixtures:load

4. **Lancer le serveur :
    symfony server:start

5. **Accéder à l’application :**
    http://127.0.0.1:8000

