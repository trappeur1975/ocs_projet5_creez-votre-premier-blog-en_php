# ocs_projet5_creez-votre-premier-blog-en_php
ocs_projet5_creez-votre-premier-blog-en_php
1- Créez les diagrammes UML (diagrammes de cas d’utilisation / diagramme de classes /  diagrammes de séquence)
2- Créez le repository GitHub pour le projet.
3- Créez l’ensemble des issues sur le repository GitHub
4- estimer de l’ensemble de vos issues.
5- Entamez le développement de l’application et proposez des pull requests pour chacune des fonctionnalités/issues.
6- Relire son code à votre mentor (code proposé dans la ou les pull requests), et une fois validée(s) mergez la ou les pull requests dans la branche principale.
7- Valider la qualité du code via SymfonyInsight ou Codacy.


Ce code c'est inspiré fortement des tutorielles suivants :
    https://openclassrooms.com/fr/courses/4670706-adoptez-une-architecture-mvc-en-php
    https://grafikart.fr/formations/php
    https://openclassrooms.com/fr/courses/1665806-programmez-en-oriente-objet-en-php

----------------- info utile pour le developpement ----------------
1) php doc 
    https://docs.phpdoc.org/3.0/guide/references/phpdoc/tags/index.html
    https://docs.phpdoc.org/3.0/guide/guides/types.html

2) composer
    lien librairies pour composer : https://packagist.org/
    commande pour relancer l autoloader : composer dump-autoload
    commande pour supprimer un package composer remove vendor/package

----------------- librairie utiliser via composer ----------------
1) autoload
    un autoloader

2) AltoRouter
    un routeur

    https://packagist.org/packages/altorouter/altorouter

    pour la documentation de la librairie AltoRouter aller sur : http://altorouter.com/ ou https://packagist.org/packages/altorouter/altorouter

3) var-dumper (juste pour le developpement)

    https://packagist.org/packages/symfony/var-dumper

    pour la documentation : https://symfony.com/doc/current/components/var_dumper.html

    commande pour executer var-dumper : "dump($someVar);"

----------------- infos site ----------------
pour lancer le serveur php via la console avec pour dossier racine "public" executer la commande suivante:
    php -S localhost:8000 -t public

