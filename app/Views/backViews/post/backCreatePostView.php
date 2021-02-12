<?php 
use App\Entities\Form;

$title = 'Create post';
ob_start(); 
?>

<?php  if(isset($_GET['created'])and($_GET['created'])==='false'): ?>
    <div class="alert alert-danger">
        le post n'a pu être créé.
    </div>
<?php endif ?>

<?php $form = new Form($post); ?>

<h1>Create a new Post</h1>

<?php require('../app/Views/backViews/post/_form.php')?>

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>