<?php

use App\Entities\Form;

$title = 'Comments waite a validate ';

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
        <?php  elseif(isset($_GET['validateComment'])and($_GET['validateComment'])==='true'): ?>
            <div class="alert alert-success">
                le commentaire a bien été validé.
            </div>
        <?php elseif(isset($_GET['validateComment'])and($_GET['validateComment'])==='false'): ?>
            <div class="alert alert-danger">
                le commentaire n'a pu être validé.
            </div>     
    <?php endif ?>

<!-- start main content  -->
    <h1>Backend Comments awaiting validation</h1>
    <table class= "table">
            <thead>
                <th>Id</th>
                <th>Comment</th>
                <th>User_id</th>
                <th>Post-id</th>
                <th>Validate</th>
                <th>
                    <a href="/backend/adminPosts" class="btn btn-secondary">Administration des posts</a>
                </th>
            </thead>
            <tbody>
                <?php foreach ($listCommentsWaiteValidate as $comment): ?>
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

                    <td  <?=($comment->getValidate()) !== null ? 'class = "validate"' : 'class = "noValidate"';?> >
                        <?php
                            if($comment->getValidate() !== null){
                                echo $comment->getValidate();
                            } else {
                                echo "en attente de validation";
                            }
                        ?>
                    </td>


                    <td>
                        <a href="<?= '/backend/validateComment/'. $comment->getId()?>" class="btn btn-info">
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