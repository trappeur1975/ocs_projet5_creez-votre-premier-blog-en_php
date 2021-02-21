<?php

use App\Entities\Form;

$title = 'Edit mediaType';

ob_start(); 
?>
<!-- start the labels on the state of the edition or the creation of the mediaType   -->
    <!-- to manage the display of the success or not of the EDITING of a mediaType -->
        <?php  if(isset($_GET['success'])and($_GET['success'])==='true'): ?>
            <div class="alert alert-success">
                le mediaType a bien été modifié.
            </div>
        <?php elseif(isset($_GET['success'])and($_GET['success'])==='false'): ?>
            <div class="alert alert-danger">
                le mediaType n'a pu être modifié.
            </div>
    <!-- to manage the message when returning from the CREATION of a user with success   -->
        <?php elseif(isset($_GET['created'])and($_GET['created'])==='true'): ?>
            <div class="alert alert-success">
                le mediaType a bien été créé.
            </div>
        <?php endif ?>
<!-- end the labels on the state of the edition or the creation of the mediaType   -->

<!-- start main content  -->
    <h1>Edit le mediaType id: <?= $mediaType->getId() ?></h1>

    <?php require('../app/Views/backViews/mediaType/_form.php')?>
<!-- end main content  -->

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>