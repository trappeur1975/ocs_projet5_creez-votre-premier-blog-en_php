<?php

use App\Entities\Form;

$title = 'Backend Edit user';

ob_start(); 
?>
<!-- start the labels on the state of the edition or the creation of the user   -->
    <!-- to manage the display of the success or not of the EDITING of a user -->
        <?php  if(isset($_GET['success'])and($_GET['success'])==='true'): ?>
            <div class="alert alert-success">
                le user a bien été modifié.
            </div>
        <?php elseif(isset($_GET['success'])and($_GET['success'])==='false'): ?>
            <div class="alert alert-danger">
                le user n'a pu être modifié.
            </div>
    <!-- to manage the message when returning from the CREATION of a user with success   -->
        <?php elseif(isset($_GET['created'])and($_GET['created'])==='true'): ?>
            <div class="alert alert-success">
                le user a bien été créé.
            </div>
        <?php endif ?>
<!-- end the labels on the state of the edition or the creation of the user   -->

 <!--  handle connection error messages  -->
 <!-- <?php  if($errorMessage != null): ?> 
        <div class="alert alert-danger">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif ?> -->

<!-- start main content  -->
    <h1>Backend Edit the User id: <?= $user->getId() ?></h1>

    <?php require('../app/Views/backViews/user/_form.php')?>
<!-- end main content  -->

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>