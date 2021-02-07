<?php 
use App\Entities\Form;

$title = 'Create post';
ob_start(); 
?>

<?php  if(isset($_GET['success'])and($_GET['success'])==='true'): ?>
    <div class="alert alert-success">
        le post a bien été enregistré.
    </div>
<?php elseif(isset($_GET['success'])and($_GET['success'])==='false'): ?>
    <div class="alert alert-danger">
        le post n'a pu être enregistré.
    </div>
<?php endif ?>

<?php $form = new Form($post); ?>

<h1>Create a new Post</h1>
 <form action="" method="post">
    <?= $form->input('title', 'titre') ?>
    <?= $form->textarea('introduction', 'introduction') ?>
    <?= $form->textarea('content', 'content') ?>
    <?= $form->input('dateCreate', 'date de creation') ?>
    <?= $form->input('dateChange', 'date de changement') ?>
    <?= $form->input('user_id', 'user_id') ?>
    
    <button class="btn btn-primary">Enregistrer</button>
 </form>


<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>