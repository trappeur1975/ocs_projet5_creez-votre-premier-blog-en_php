<?php 
$title = 'Connexion';
ob_start();
?>
    <h1>page de connexion au backend du site</h1>
    
    <!-- to manage the case where users (not identified) on the site wish to access content where they must be logged in (see the "check ()" function of the "Auth" class  -->
     <?php  if(isset($_GET['badConnection'])): ?> 
        <div class="alert alert-danger">
            vous n'étes identifié sur le site
        </div>
    <?php endif ?>

    <!--  handle connection error messages  -->
    <?php  if($error != null): ?> 
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif ?>
   
    <h2>Se connecter</h2>

    <form action="<?= '/backend/connection' ?>" method="post"> <!-- here we indicate the action of the form in order not to leave the alert "not identified" permanently on the connection page. this allows to reload the url of the page without "? badConnection = true"   -->
        <?= $form->input('login', 'login') ?>
        <?= $form->input('password', 'password') ?>
        <button type= "submit" class="btn btn-primary">Se connecter</button>
    </form>

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>