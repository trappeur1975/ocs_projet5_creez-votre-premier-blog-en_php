<?php 
$title = 'Administration des users';
ob_start(); 
?>
        

<!-- label if the user was successfully deleted  -->
    <?php  if(isset($_GET['delete'])): ?>
        <div class="alert alert-success">
            le user a bien été supprimé.
        </div>
    <?php endif ?>

<!-- start main content  -->
    <h1>page Admin User du backend</h1>
        <table class= "table">
            <thead>
                <th>Id</th>
                <th>Titre</th>
                <th>
                    <a href="/backend/createUser" class="btn btn-primary">nouveau user</a>
                <th>

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
                        <?= formatHtml($user->getFirstName()) ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= '/backend/editUser/'. $user->getId()?>" class="btn btn-primary"> <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                            Editer
                        </a>
                        <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                            <!-- si on utilise la methode "get -->
                                <!-- <a href="<?= '/backend/deleteUser/'. $user->getId()?>" class="btn btn-danger" onclick="return confirm('Souhaitez vous vraiment axecuter cette action?')">
                                    Supprimer
                                </a> -->
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