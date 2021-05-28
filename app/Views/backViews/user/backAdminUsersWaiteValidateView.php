<?php 
$title = 'Backend Users waite a valid';
ob_start(); 
?>

<!-- label alert-->
    <?php  if (isset($_GET['delete'])) : ?>
        <div class="alert alert-success">
            le user a bien été supprimé.
        </div>
        <?php  elseif (isset($_GET['validateUser'])and($_GET['validateUser'])==='true') : ?>
            <div class="alert alert-success">
                le user a bien été validé.
            </div>
        <?php elseif (isset($_GET['validateUser'])and($_GET['validateUser'])==='false') : ?>
            <div class="alert alert-danger">
                le user n'a pu être validé.
            </div>
    <?php endif ?>

<!-- start main content  -->
    <h1>Backend Users awaiting validation</h1>
        <table class= "table">
            <thead>
                <th>Id</th>
                <th>User</th>
                <th>Validate</th>
                <th>
                    <a href="/backend/createUser" class="btn btn-primary">Nouveau user</a>
                    <a href="/backend/adminUsers" class="btn btn-secondary">Administration des users</a>
                </th>
            </thead>
            <tbody>
                <?php foreach ($listUsersWaiteValidate as $user): ?>         
                <tr>
                    <td>
                        #<?= $user->getId(); ?>
                    </td>
                    <td>
                        <a href="<?= '/backend/editUser/'. $user->getId()?>">
                        <?= formatHtml($user->getLastName()) ?>
                        </a>
                    </td>
                    <td <?=($user->getValidate()) !== null ? 'class = "validate"' : 'class = "noValidate"';?> >
                        <?php
                            if ($user->getValidate() !== null) {
                                echo $user->getValidate();
                            } else {
                                echo "en attente de validation";
                            }
                        ?>
                    </td>
                    <td>
                        <a href="<?= '/backend/editUser/'. $user->getId()?>" class="btn btn-primary">
                            Editer
                        </a>
                        <a href="<?= '/backend/validateUser/'. $user->getId()?>" class="btn btn-info">
                            Valider
                        </a>
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
require'../app/Views/template.php'; 
?>