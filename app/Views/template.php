<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?= $title?? 'The Blog' ?></title> <!-- affiche "the Blog" par defaut si $title n a pas éte definit auparavant  -->
        <!--  LE CHEMIN EST GERER PAR RAPPORT AU FICHIER "index.php" par contre il faut indiquer le chemin absolu pour acceder au fichier "style.css" comme ci dessous en non en relatif comme  "<link href="css/style.css" rel="stylesheet" />" car sinon pour la route "http://localhost:8000/post/1" il va chercher le fichier "style.css" dans "GET /post/css/style.css" (info sortir de la console terminal de visual studio code) ce qui genere une erreur 4040-->
        <link href="/css/style.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    </head>
    
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary"> 
            <a href="/" class="navbar-brand">Blog Nico</a>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="/listposts" class="nav-link">Les articles</a>
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

                <?php
                    if(isset($_SESSION['connection']) AND $userLogged->getUserType_id() == 1 AND !is_null($userLogged->getValidate())){ //si on en logger et que l on est un user de status "abonner"

                ?>
                    <li class="nav-item">
                        <a href="/userFrontDashboard/<?=$_SESSION['connection']?>" class="nav-link">Mon dashboard</a>
                    </li>
                <?php      
                    }
                ?>
                
                <!-- affichage de certain menu selon si user est connecter ou non au site -->
                <?php
                    if(!isset($_SESSION['connection']) OR is_null($userLogged->getValidate())){
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
            </ul>
        </nav>
        
        <!-- ----------------BODY OF SITE------------------ -->
        <div class="container mt-4">
            <div > <!-- pour afficher les messages flash-->
                <?php           
                    getFalshErrors();          
                ?>
            </div> 

            <?= $content ?> <!-- pour afficher le contenu principal des pages-->
        </div>
        <!-- ----------------BODY OF SITE------------------ -->

        <footer class="text-center text-dark" style="background-color: #f1f1f1;">
            
            <div class="container pt-4">    <!-- Grid container -->
                <section class="mb-4">  <!-- Section: Social media -->
                <!-- Linkedin -->
                <a
                    class="btn btn-link btn-floating btn-lg text-dark m-1"
                    href="https://www.linkedin.com/in/nicolas-tchenio/"
                    role="button"
                    data-mdb-ripple-color="dark"
                    ><i class="bi bi-linkedin"></i
                ></a>
                <!-- Github -->
                <a
                    class="btn btn-link btn-floating btn-lg text-dark m-1"
                    href="https://github.com/trappeur1975/ocs_projet5_creez-votre-premier-blog-en_php"
                    role="button"
                    data-mdb-ripple-color="dark"
                    ><i class="bi bi-github"></i
                ></a>
                <!-- Facebook -->
                <a
                    class="btn btn-link btn-floating btn-lg text-dark m-1"
                    href="https://www.facebook.com/nicolas.tchenio"
                    role="button"
                    data-mdb-ripple-color="dark"
                    ><i class="bi bi-facebook"></i
                ></a>
                </section>  <!-- Section: Social media -->   
            </div>  <!-- Grid container -->
            
            
            <div class="container p-4"> <!-- Grid container -->       
                <div class="row">   <!--Grid row-->
                    <div class="col-lg-6 col-md-12 mb-4 mb-md-0">   <!--Grid column-->
                        <h5 class="text-uppercase">Mot de fin</h5>

                        <p>
                        C'est avec un grand plaisir que j ai réalisé ce blog dans le cadre de ma formation Openclassrooms qui m'as permis de progresser dans le developpement de site web sous php.
                        Ce sera encore plus de plaisir que j'aurais à travailler sur d'autres projets professionnels de ce type donc n'hésiter pas a me contacter.
                        </p>
                    </div>  <!--Grid column-->
                
                    <div class="col-lg-6 col-md-12 mb-4 mb-md-0">   <!--Grid column-->
                        <h5 class="text-uppercase">Administration du Site</h5>

                            <!-- affichage de certain menu selon si user est connecter ou non au site -->
                            <?php
                                if(!isset($_SESSION['connection']) OR is_null($userLogged->getValidate())){
                            ?>
                                    <form action="/backend/connection" method="post"> <!-- for security to prevent me being forcibly connected by sending me this link -->
                                        <button type ="submit" class="btn btn-primary btn-lg" >Se connecter</button>
                                    </form>
                        
                                    <button type ="submit" class="btn btn-success btn-sm" id="compte" >
                                        <a href="/createUserFront" class="nav-link" id="compte">Creer un compte
                                    </a></button>
                            <?php
                                } else {   
                            ?>
                                <form action="/backend/disconnection" method="post"> <!-- for security to prevent me being forcibly disconnected by sending me this link -->
                                    <button type ="submit" class="btn btn-danger btn-lg" >Se deconnecter</button>
                                </form>
                            <?php      
                                }
                            ?>
                    </div>  <!--Grid column-->
                </div>  <!--Grid row-->
            </div>  <!-- Grid container -->
            
            <div class="text-center text-dark p-3" style="background-color: rgba(0, 0, 0, 0.2);">   <!-- Copyright -->
                © 2021 Copyright:
                <a class="text-dark" href="https://www.linkedin.com/in/nicolas-tchenio/">Nicolas tchenio</a>
            </div>  <!-- Copyright -->
            
        </footer>

    </body>
</html>