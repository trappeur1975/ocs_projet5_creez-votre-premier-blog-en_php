<?php 
use App\Entities\Form;

$title = 'Backend Create post';
ob_start(); 
?>
<!-- label if the creation of the post was not successful -->
    <?php  if (isset($_GET['created'])and($_GET['created'])==='false') : ?>
        <div class="alert alert-danger">
            le post n'a pu être créé.
        </div>
    <?php endif ?>

<!-- start main content  -->
    <h1>Backend Create a new Post</h1>

    <?php require'../app/Views/backViews/post/_form.php'?>
<!-- end main content  -->

<?php 
$content = ob_get_clean(); 
require'../app/Views/template.php'; 
?>