
# Readme ClassNotes


### 0) Introduction

_ClassNotes_ est une application écrite en **PHP 7 Symfony 5.1** qui donne la possibilité de gérer une classe en gérant des données élèves, des données de notes et des moyennes.


### I) Installation

Le dépôt GITHUB du projet _ClassNotes_ se trouve sous cette URL :
[https://github.com/maximedehan/ClassNotes](https://github.com/maximedehan/ClassNotes)

 - Cloner le projet à partir de :
[https://github.com/maximedehan/ClassNotes.git](https://github.com/maximedehan/ClassNotes.git)

 - Ouvrir le projet sous PHPStorm de préférence.

 - Créer une base de donnée SQLite ou MySQL indépendante ou non de PHPStorm. En Symfony 5, la configuration de la base de données se fait à la racine du projet, dans le fichier .env

 Une fois votre SGBD installée, pour créer une base de données à partir des entités Doctrine du projet, lancer cette commande à la racine du projet :

**php bin/console doctrine:database:create**

- PHPStorm dispose d&#39;un serveur interne pour lancer le projet en local, il suffit de lancer cette commande à la racine :

**php -S localhost:8080 -t public**


### II) L&#39;interface de ClassNotes

Une fois la commande lancée, à l&#39;adresse **localhost:8080** ,vous devriez avoir accès à un écran d&#39;accueil. Mais aucune donnée n&#39;est présente en BDD pour pouvoir tester l&#39;API encore. **Il faudra donc dans un premier temps en insérer manuellement ou via les routes.**
 Les écrans comprennent 4 sections distinctes :

 - _ **Accueil** _avec le lien vers les routes générées par APIPlateform.

- _ **Gestion des élèves** _qui permet de visualiser les élèves saisis, avec la possibilité d&#39;en supprimer, d&#39;en mettre à jour et d&#39;en ajouter.

- _ **Gestion des notes** _ qui permet de visualiser les notes saisies pour chaque élève, avec la possibilité d&#39;en supprimer, d&#39;en mettre à jour et d&#39;en ajouter.

 - _ **Calcul des moyennes** _qui permet de visualiser la moyenne générale de la classe et la moyenne générale pour chaque élève.


### **III) Les routes de ClassNotes**

L&#39;écran d&#39;accueil indique un lien vers les routes mises en place automatiquement par APIPlateform.
 Dans ce projet, je me suis attaché à ne pas utiliser APIPlateform pour créer ma propre API, pour créer mon propre CRUD. Les 8 routes principales utilisées dans le projet sont donc :

| **Entité** | **Méthode** | **Route** | **Commentaires** |
| --- | --- | --- | --- |
| Student | GET | /api/student | Visualiser les étudiants |
| Student | POST | /api/student/add | Ajouter un étudiant |
| Student | POST | /api/student/update/{id} | Mettre à jour un étudiant |
| Student | DELETE | /api/student/delete/{id} | Supprimer un étudiant |
| Mark | GET | /api/mark | Visualiser les notes |
| Mark | POST | /api/mark/add | Ajouter une note |
| Mark | POST | /api/mark/update/{id} | Mettre à jour une note |
| Mark | DELETE | /api/mark/delete/{id} | Supprimer une note |

Une autre route permet de visualiser les moyennes par élève et la moyenne générale de la classe. Il n&#39;a pas été créé d&#39;entité Average, le controller utilise les entités Student et Mark pour calculer les moyennes :

|
 | GET | /api/average | Visualiser les moyennes par élève et pour la classe |
| --- | --- | --- | --- |


### **IV) Tester le code**

_**A) Tester l&#39;interface**_

Pour tester lancer l&#39;interface via le serveur interne à PHPStorm :

**php -S localhost:8080 -t public**

_**B) Tester les routes**_

Pour tester les routes en dehors de l&#39;interface elle-même, il est possible d&#39;utiliser _Postman_.
 Par exemple pour tester la méthode DELETE pour supprimer un élève :
 Mettre l&#39;URL suivante avec la méthode DELETE : [https://localhost:8080/api/student/delete/](https://localhost:8080/api/student/delete/){id}

 Dans l&#39;onglet « Body », mettre la donnée au format JSON suivante :

{
 id : 10
} 

De même, pour tester les routes générées par APIPlateform. La solution offre la possibilité de tester ses propres requêtes directement par son interface.

_**C) Les Tests Unitaires**_

Pour tester tous les tests unitaires, se mettre à la racine du projet, et lancer la commande suivante :

php bin/phpunit

Pour tester un test unitaire en particulier, se mettre à la racine du projet, et lancer la commande suivante :

php bin/phpunit test/Controller/AverageControllerTest.php

ou

php bin/phpunit test/Service/MarkHelperTest.php


### **V) Améliorations possibles**

_**A) Sur les entités Doctrine et la BDD**_

On aurait pu créer deux autres classes pour améliorer l&#39;application, par exemple :
 une entité Classe (avec un ID, un string nom\_matière, un integer coef), en relation 1-N avec Student.
 une entité Matière (avec un ID, un string nom\_matière, un integer coef) , en relation 1-N avec Mark.

_**B) Sur les Tests unitaires**_

Les tests auraient pu couvrir tous les controllers et les services et nos jeux de tests auraient mérités une meilleure refactorisation.

_**C) Sur l&#39;utilisation des exceptions et des messages d&#39;erreurs**_

Il aurait été intéressant d&#39;utiliser un peu plus les exceptions et de mieux gérer l&#39;aspect graphique des messages d&#39;erreurs dans le cas où une exception est levée par l&#39;utilisateur lors du remplissage du formulaire.

_**D) Sur l&#39;optimisation du code**_

On aurait pu refactoriser encore davantage certaines parties du code récurrentes via des services ...
 Tout en améliorant la qualité du code au regard de la norme PSR.

_**E) Sur les routes**_

Pour les routes de mises à jour de nos entités, il aurait été mieux d&#39;utiliser la méthode PATCH.

_**F) Sur l&#39;API en général**_

On aurait pu utiliser les routes proposées par APIPlateform pour simplifier la gestion des entités.
 Créer une application Symfony _ClassNotes_ pour l&#39;interface et une application à part en Symfony _ClassNotes_ API (pour la gestion des entités par les routes).
