<?php

use App\Entities\Form;

$title = 'Edit post';
ob_start(); 
?>

<?php  if(isset($_GET['success'])and($_GET['success'])==='true'): ?>
    <div class="alert alert-success">
        le post a bien été modifié.
    </div>
<?php elseif(isset($_GET['success'])and($_GET['success'])==='false'): ?>
    <div class="alert alert-danger">
        le post n'a pu être modifié.
    </div>
<?php endif ?>

<?php $form = new Form($post); ?>

<h1>Edit le post id: <?= $post->getId() ?></h1>
 <form action="" method="post">
    <?= $form->input('title', 'titre') ?>
    <?= $form->textarea('introduction', 'introduction') ?>
    <?= $form->input('dateCreate', 'date de creation') ?>
    
    <button class="btn btn-primary">Modifier</button>
 </form>

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>