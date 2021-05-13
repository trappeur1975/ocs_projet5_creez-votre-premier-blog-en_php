<?php

use App\Entities\Form;

$title = 'Front user dashboard';

ob_start(); 
?>
    <!-- start the labels on the state of the edition of the user   -->
    <!-- to manage the display of the success or not of the EDITING of a user -->
    <?php  if(isset($_GET['successEditUser'])and($_GET['successEditUser'])==='true'): ?>
            <div class="alert alert-success">
                le user a bien été modifié.
            </div>
        <?php elseif(isset($_GET['successEditUser'])and($_GET['successEditUser'])==='false'): ?>
            <div class="alert alert-danger">
                le user n'a pu être modifié.
            </div>
        <?php endif ?>
<!-- end the labels on the state of the edition of the user   -->
    
    <!-- start main content  -->
        <h1>Front user dashboard with id  <?= $id ?></h1>
        <h2>Suppression de mon compte user</h2>
                <form action="<?= '/deleteUserFront/'. $user->getId()?>" methode="POST"
                    onsubmit="return confirm('Souhaitez vous vraiment executer cette action?')">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            
        <h2>Mes infos (modification)</h2>
            <?php require('../app/Views/frontViews/_formUserFront.php')?>

        <h2>Mes commentaires</h2>


        <table class= "table">
            <thead>
                <th>Id</th>
                <th>comment</th>
                <th>post-id</th>
                <th>validate</th>
                <th>actions</th>
            </thead>
            <tbody>
                <?php foreach ($listCommentsForUser as $comment): ?>
                <tr>
                    <td>
                        #<?= $comment->getId(); ?>
                    </td>
                    <td>
                        <a href="<?= '/post/'. $comment->getPost_id()?>">
                        <?= formatHtml($comment->getComment()); ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= '/post/'. $comment->getPost_id()?>">
                        #<?= $comment->getPost_id(); ?>
                    </td>

                    <td <?=($comment->getValidate()) !== null ? 'class = "validate"' : 'class = "noValidate"';?> >
                        <?php
                            if($comment->getValidate() !== null){
                                echo $comment->getValidate();
                            } else {
                                echo "en attente de validation";
                            }
                        ?>
                    </td>
                    <td>
                        <a href="<?= '/editCommentPostFront/'. $comment->getId()?>" class="btn btn-warning">
                            Modifier
                        </a>
                        <!-- si on utilise la methode "post" -->
                        <form action="<?= '/backend/deleteComment/'. $comment->getId()?>" methode="POST"
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