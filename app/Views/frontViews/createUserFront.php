<?php 
use App\Entities\Form;

$title = 'Front Create user';
ob_start(); 
?>
<!-- label if the creation of the user was not successful -->
    <?php  if (isset($_GET['createdUser'])and($_GET['createdUser'])==='true') : ?>
        <div class="alert alert-success">
            le user a pu être créé et est en attente de validation par l'administrateur du site.
        </div>
        <?php  elseif (isset($_GET['createdUser'])and($_GET['createdUser'])==='false') : ?>
        <div class="alert alert-danger">
            le user n'a pu être créé.
        </div>
    <?php endif ?>

<!-- start main content  -->
    <h1>Front Create a new user in front</h1>

    <?php require'../app/Views/frontViews/_formUserFront.php'?>
<!-- end main content  -->

<?php 
$content = ob_get_clean(); 
require'../app/Views/template.php'; 
?>