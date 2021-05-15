<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?= $title?? 'The Blog' ?></title> <!-- affiche "the Blog" par defaut si $title n a pas éte definit auparavant  -->
        <!--  LE CHEMIN EST GERER PAR RAPPORT AU FICHIER "index.php" par contre il faut indiquer le chemin absolu pour acceder au fichier "style.css" comme ci dessous en non en relatif comme  "<link href="css/style.css" rel="stylesheet" />" car sinon pour la route "http://localhost:8000/post/1" il va chercher le fichier "style.css" dans "GET /post/css/style.css" (info sortir de la console terminal de visual studio code) ce qui genere une erreur 4040-->
        <link href="/css/style.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    </head>
    
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary"> 
            <a href="/" class="navbar-brand">Mon site</a>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="/listposts" class="nav-link">les articles</a>
                </li>
                <?php   
                    if(isset($_SESSION['connection']) AND $userLogged->getUserType_id() == 2){ //si on en logger et que l on est un user de status "administrateur"
                ?>
                    <li class="nav-item">
                        <a href="/backend/adminPosts" class="nav-link">Articles</a>
                    </li>
                    <li class="nav-item">
                        <a href="/backend/adminUsers" class="nav-link">Users</a>
                        <!-- <a href="<?= '/backend/adminUsers'?>"class="nav-link">Users</a> -->
                    </li>
                <?php
                    }
                ?>
                
                <!-- affichage de ceratin menu selon si user est connecter ou non au site -->
                <?php
                    if(!isset($_SESSION['connection'])){
                ?>
                    <li class="nav-item">
                        <form action="/backend/connection" method="post"> <!-- for security to prevent me being forcibly connected by sending me this link -->
                            <button type ="submit" class="nav-link" style="background:transparent; border:none;" >Se connecter</button>
                        </form>
                    </li>
                    <li class="nav-item">
                        <a href="/createUserFront" class="nav-link">Creer un compte</a>
                     </li>
                <?php
                    } else {   
                ?>
                    <li class="nav-item">
                        <form action="/backend/disconnection" method="post"> <!-- for security to prevent me being forcibly disconnected by sending me this link -->
                            <button type ="submit" class="nav-link" style="background:transparent; border:none;" >Se deconnecter</button>
                        </form>
                    </li>
                <?php      
                    }
                ?>

                <?php
                    if(isset($_SESSION['connection']) AND $userLogged->getUserType_id() == 1){ //si on en logger et que l on est un user de status "abonner"

                ?>
                    <li class="nav-item">
                        <a href="/userFrontDashboard/<?=$_SESSION['connection']?>" class="nav-link">Mon dashboard</a>
                    </li>
                <?php      
                    }
                ?>

            </ul>
        </nav>
        
        <div class="container mt-4">
            <?= $content ?>
        </div>
        <footer class="bg-light py-4 footer">
            <div class="container">
                <p>my footer</p>
            </div>
        </footer>
    </body>
</html>