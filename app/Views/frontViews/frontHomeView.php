<?php 
    $title = 'Front Home';
    ob_start(); 
?>
<!-- label alert-->
    <?php  if(isset($_GET['deleteUser'])): ?>
        <div class="alert alert-success">
            Votre compte user a bien été supprimé.
        </div>
    <?php elseif(isset($_GET['unauthorizedAccess'])): ?>
        <div class="alert alert-danger">
            votre statut ne vous autorise pas a acceder au contenu du site reserver a un certain statut.
        </div>
    <?php endif ?>

<!-- label error-->
<?php  if($error != null): ?> 
    <div class="alert alert-danger">
        <?php echo $error; ?>
    </div>
<?php endif ?>

<!-- start main content  -->       
    <h1>page Home du front</h1>
<!-- end main content  -->

<?php 
    $content = ob_get_clean(); 
    require('../app/Views/template.php'); 
?>