<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?= $title?? 'The Blog' ?></title> <!-- displays "the Blog" by default if $ title has not been defined before   -->
        <!--  THE PATH IS MANAGED IN RELATION TO THE "index.php" FILE on the other hand, you must indicate the absolute path to access the "style.css" file as below, not in relative terms like "<link href =" css / style.css " rel = "stylesheet" /> "because otherwise for the route" http: // localhost: 8000 / post / 1 "it will look for the file" style.css "in" GET /post/css/style.css "(info exit from the visual studio code terminal console) which generates a 404- error -->
        <link href="/css/style.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    </head>
    
    <body>
        <!-- ---------------- HEADER ------------------ -->
        <div class="row">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark"> 
                <a href="/" class="navbar-brand" id="blog">Blog Nico</a>
                
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div id="navbarContent" class="collapse navbar-collapse justify-content-center">
                    
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="/" class="nav-link">Home</a>
                        </li>
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
                        
                        <!-- display of certain menu depending on whether user is connected or not to the site  -->
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
                </div>
            </nav>
        </div>

        <!-- ---------------- BODY ------------------ -->
        <div class="container mt-4">
            <div > <!-- to display flash messages -->
                <?php           
                    getFalshErrors();          
                ?>
            </div> 

            <?= $content ?> <!-- to display the main content of the pages -->
        </div>
        
        <!-- ---------------- FOOTER------------------ -->

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
                    <div class="col-lg-6 col-md-12 mb-4 mb-md-0 " id="finalWord">   <!--Grid column-->
                        <h5 class="text-uppercase">Mot de fin</h5>

                        <p>
                        C'est avec un grand plaisir que j ai réalisé ce blog dans le cadre de ma formation Openclassrooms qui m'as permis de progresser dans le developpement de site web sous php.
                        Ce sera encore plus de plaisir que j'aurais à travailler sur d'autres projets professionnels de ce type donc n'hésiter pas a me contacter.
                        </p>
                    </div>  <!--Grid column-->
                
                    <div class="col-lg-6 col-md-12 mb-4 mb-md-0">   <!--Grid column-->
                        <h5 class="text-uppercase">Administration du Site</h5>

                            <!-- display of certain menu depending on whether user is connected or not to the site  -->
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

        <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
	    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    </body>
</html>