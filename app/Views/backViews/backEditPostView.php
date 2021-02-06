<?php 
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

<h1>Edit le post id: <?= $post->getId() ?></h1>
 <form action="" method="post">
    <div class="form-group">
        <label for="title">Titre</label>
        <input type="text" class="form-control" name="title" value="<?= formatHtml($post->getTitle()); ?>">
    </div>
    
    <button class="btn btn-primary">Modifier</button>
 </form>

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>