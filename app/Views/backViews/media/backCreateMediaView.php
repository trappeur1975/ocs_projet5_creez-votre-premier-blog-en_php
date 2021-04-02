<?php 
use App\Entities\Form;

$title = 'Create mediaType';
ob_start(); 
?>
<!-- label if the creation of the user was not successful -->
    <?php  if(isset($_GET['created'])and($_GET['created'])==='false'): ?>
        <div class="alert alert-danger">
            le user n'a pu être créé.
        </div>
    <?php endif ?>

<!-- start main content  -->
    <h1>Create a new mediaType</h1>

    <?php require('../app/Views/backViews/mediaType/_form.php')?>
<!-- end main content  -->

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>