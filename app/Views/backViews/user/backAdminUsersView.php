<?php 
$title = 'Backend User Administration';
ob_start(); 
?>

<!-- label alert-->
    <?php  if(isset($_GET['delete'])): ?>
        <div class="alert alert-success">
            le user a bien été supprimé.
        </div>
        <?php  elseif(isset($_GET['validateUser'])and($_GET['validateUser'])==='true'): ?>
            <div class="alert alert-success">
                le user a bien été validé.
            </div>
        <?php elseif(isset($_GET['validateUser'])and($_GET['validateUser'])==='false'): ?>
            <div class="alert alert-danger">
                le user n'a pu être validé.
            </div>
    <?php endif ?>

<!-- start main content  -->
    <h1>Backend User Administration</h1>
        <table class= "table">
            <thead>
                <th>Id</th>
                <th>User</th>
                <th>Validate</th>
                <th>
                    <a href="/backend/createUser" class="btn btn-primary">Nouveau user</a>
                    <a href="/backend/adminUsersWaiteValidate" class="btn btn-warning">Valider les users en attente</a>
                </th>
            </thead>
            <tbody>
                <?php foreach ($listUsers as $user): ?>         
                <tr>
                    <td>
                        #<?= $user->getId(); ?>
                    </td>
                    <td>
                        <a href="<?= '/backend/editUser/'. $user->getId()?>"> <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                        <?= formatHtml($user->getLastName()) ?>
                        </a>
                    </td>
                    <td <?=($user->getValidate()) !== null ? 'class = "validate"' : 'class = "noValidate"';?> >
                        <?php
                            if($user->getValidate() !== null){
                                echo $user->getValidate();
                            } else {
                                echo "en attente de validation";
                            }
                        ?>
                    </td>
                    <td>
                        <a href="<?= '/backend/editUser/'. $user->getId()?>" class="btn btn-primary"> <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                            Editer
                        </a>
                        <a href="<?= '/backend/validateUser/'. $user->getId()?>" class="btn btn-info">
                            Valider
                        </a>
                        <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                            <!-- si on utilise la methode "post" -->
                                <form action="<?= '/backend/deleteUser/'. $user->getId()?>" methode="POST"
                                    onsubmit="return confirm('Souhaitez vous vraiment executer cette action?')">
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                </form>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
<!-- end main content  -->

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>