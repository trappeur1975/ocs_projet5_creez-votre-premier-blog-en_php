<?php

use App\Entities\Form;

$title = 'Edit post';

ob_start(); 
?>
<!-- to manage the display of the success or not of the EDITING of a post -->
    <?php  if(isset($_GET['success'])and($_GET['success'])==='true'): ?>
        <div class="alert alert-success">
            le post a bien été modifié.
        </div>
    <?php elseif(isset($_GET['success'])and($_GET['success'])==='false'): ?>
        <div class="alert alert-danger">
            le post n'a pu être modifié.
        </div>
<!-- to manage the message when returning from the CREATION of a post with success   -->
    <?php elseif(isset($_GET['created'])and($_GET['created'])==='true'): ?>
        <div class="alert alert-success">
            le post a bien été créé.
        </div>
    <?php endif ?>

<?php $form = new Form($post); ?>

<h1>Edit le post id: <?= $post->getId() ?></h1>

<?php require('../app/Views/backViews/post/_form.php')?>

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>