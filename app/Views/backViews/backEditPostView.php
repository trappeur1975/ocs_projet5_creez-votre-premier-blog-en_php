<?php 
$title = 'Edit post';
$success = false;
ob_start(); 
?>

<?php  if(isset($_GET['success'])): ?>
    <div class="alert alert-success">
        le post a bien été modifié.
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