<?php

use App\Entities\Form;

$title = 'Edit media';

ob_start(); 
?>
<!-- start the labels on the state of the edition or the creation of the media   -->
    <!-- to manage the display of the success or not of the EDITING of a media -->
        <?php  if(isset($_GET['success'])and($_GET['success'])==='true'): ?>
            <div class="alert alert-success">
                le media a bien été modifié.
            </div>
        <?php elseif(isset($_GET['success'])and($_GET['success'])==='false'): ?>
            <div class="alert alert-danger">
                le media n'a pu être modifié.
            </div>
    <!-- to manage the message when returning from the CREATION of a media with success   -->
        <?php elseif(isset($_GET['created'])and($_GET['created'])==='true'): ?>
            <div class="alert alert-success">
                le media a bien été créé.
            </div>
        <?php endif ?>
<!-- end the labels on the state of the edition or the creation of the mediaType   -->

<!-- start main content  -->
    <h1>Edit le media id: <?= $media->getId() ?></h1>

    <?php require('../app/Views/backViews/media/_form.php')?>
<!-- end main content  -->

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>