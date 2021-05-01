<?php

use App\Entities\Form;

$title = 'Edit the comments of a post ';

ob_start(); 
?>

<!-- start the labels on the state of the comment of the post   -->
    <!-- to manage the display of the success or not of the comment of a post -->
    <?php  if(isset($_GET['deleteComment'])and($_GET['deleteComment'])==='true'): ?>
            <div class="alert alert-success">
                le commentaire a bien été supprimer.
            </div>
        <?php elseif(isset($_GET['deleteComment'])and($_GET['deleteComment'])==='false'): ?>
            <div class="alert alert-danger">
                le commentaire n'a pu être supprimer.
            </div>
    <?php endif ?>

<!-- start main content  -->
    <h1>Edit the comments of the post id: <?= $id ?></h1>
    <!-- <h1>Edit the comments of the post id: <?//= $user->getId() ?></h1> -->
    <table class= "table">
            <thead>
                <th>Id</th>
                <th>comment</th>
                <th>user_id</th>
                <th>post-id</th>
                <th>actions</th>
            </thead>
            <tbody>
                <?php foreach ($listCommentsForPost as $comment): ?>
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
                    <a href="<?= '/backend/editUser/'. $comment->getUser_id()?>">
                        #<?= $comment->getUser_id(); ?>
                    </td>
                    <td>
                        <a href="<?= '/post/'. $comment->getPost_id()?>">
                        #<?= $comment->getPost_id(); ?>
                    </td>
                    <td>
                        <a href="<?= '/backend/editUser/'. $comment->getId()?>" class="btn btn-primary">
                            Valider
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