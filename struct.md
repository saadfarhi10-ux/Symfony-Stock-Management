Les entites :
    🔹 Produit
            id
            reference
            nom
            prix
            stock
            seuilAlerte
            catégorie (ManyToOne)                  
    🔹 Catégorie
            id
            nom
            description
            relation OneToMany avec Produit
    🔹 MouvementStock
            id
            type (entrée / sortie)
            quantite
            date
            produit (ManyToOne)


1)    symfony new --webapp gestion_magasin
            CREE
                gestion_magasin/
                ├── composer.json
                ├── symfony.lock
                ├── .env
                ├── bin/console
                ├── public/index.php
                ├── src/
                │   ├── Controller/
                │   ├── Entity/
                │   ├── Kernel.php
                │   └── Repository/
                ├── templates/
                ├── config/
                ├── migrations/
                ├── var/
                └── vendor/


2)    php bin/console make:user
                une entité User
                avec :
                email → identifiant
                password → mot de passe hashé
                roles → droits d’accès
                    CREE
                        src/Entity/User.php
                        src/Repository/UserRepository.php


3)    php bin/console make:security
                un formulaire de connexion

                une route /login

                une route /logout

                un AppAuthenticator 

                    CREE
                        src/Security/AppAuthenticator.php
                        src/Controller/SecurityController.php
                        templates/security/login.html.twig
                    MODIFIER
                        config/packages/security.yaml

4)    php bin/console make:migration

                    CREE
                        migrations/Version20260107205628.php
                    ceci est la creation du fichier de migration ou la traduction de ce que j ai en php -> sql 

      php bin/console doctrine:migrations:migrate

                    CREE 
                        dans sqlmyadmin on peut mtn voir notre table user cree 

5)    php bin/console make:entity Categorie

        Categorie
        ---------
        id            : integer (clé primaire, auto-incrémentée)
        nom           : string(100), NOT NULL
        description   : text, NULLABLE

                    CREE
                        src/Entity/Categorie.php

        php bin/console make:migration
        php bin/console doctrine:migrations:migrate

        la meme chose on relie le l'entite Categorie avec une table sql 


6)    php bin/console make:entity Produit

        Produit
        -------
        id            : integer (clé primaire, auto-incrémentée)
        reference     : string(50), NOT NULL
        nom           : string(150), NOT NULL
        prix          : float, NOT NULL
        stock         : integer, NOT NULL
        seuilAlerte   : integer, NOT NULL
        categorie     : relation ManyToOne vers Catégorie NOT NULL

                    CREE
                        src/Entity/Produit.php
                    
        php bin/console make:migration
        php bin/console doctrine:migrations:migrate

        la meme chose on relie le l'entite Categorie avec une table sql 

7)    php bin/console make:entity MouvementStock

        MouvementStock
        --------------
        id            : integer (clé primaire, auto-incrémentée)
        type          : string(20), NOT NULL
        quantite      : integer, NOT NULL
        date          : datetime, NOT NULL
        produit       : relation ManyToOne vers Produit NOT NULL

- Make:crud est la commande qui transforme ton modèle (entités + BD) en application visible et utilisable.

8)   php bin/console make:crud Categorie  

                    CREE
                    make:crud Categorie
                    │
                    ├── src/Controller/CategorieController.php
                    ├── src/Form/CategorieType.php
                    └── templates/categorie/
                        ├── index.html.twig
                        ├── new.html.twig
                        ├── edit.html.twig
                        ├── show.html.twig
                        ├── _form.html.twig
                        └── _delete_form.html.twig 

9)    php bin/console make:crud Produit

                    CREE
                    ├── src/Controller/ProduitController.php
                    ├── src/Form/ProduitType.php
                    └── templates/produit/
                        ├── index.html.twig
                        ├── new.html.twig
                        ├── edit.html.twig
                        ├── show.html.twig
                        ├── _form.html.twig
                        └── _delete_form.html.twig

10)    php bin/console make:crud MouvementStock

                    CREE
                    ├── src/Controller/MouvementStockController.php
                    ├── src/Form/MouvementStockType.php
                    └── templates/mouvement_stock/
                        ├── index.html.twig
                        ├── new.html.twig
                        ├── edit.html.twig
                        ├── show.html.twig
                        ├── _form.html.twig
                        └── _delete_form.html.twig


    



