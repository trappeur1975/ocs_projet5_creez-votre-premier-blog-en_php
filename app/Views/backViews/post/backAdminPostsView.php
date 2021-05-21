<?php 
$title = 'Backend Post administration';
ob_start(); 
?>
        
<!-- label if the post was successfully deleted  -->
    <?php  if(isset($_GET['delete'])): ?>
        <div class="alert alert-success">
            le post a bien été supprimé.
        </div>
    <?php endif ?>

<!-- start main content  -->
    <h1>Backend Post Administration</h1>
        <table class= "table">
            <thead>
                <th>Id</th>
                <th>Title</th>
                <th>
                    <a href="/backend/createPost" class="btn btn-primary">Nouveau post</a>
                    <a href="/backend/adminCommentsWaiteValidate" class="btn btn-warning">Valider les commentaires en attente</a>
                </th>
            </thead>
            <tbody>
                <?php foreach ($listPosts as $post): ?>         
                <tr>
                    <td>
                        #<?= $post->getId(); ?>
                    </td>

                    <td>
                        <a href="<?= '/backend/editPost/'. $post->getId()?>"> <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                        <?= formatHtml($post->getTitle()) ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= '/backend/editPost/'. $post->getId()?>" class="btn btn-primary"> <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                            Editer
                        </a>
                        <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                            <!-- si on utilise la methode "get -->
                                <!-- <a href="<?= '/backend/deletePost/'. $post->getId()?>" class="btn btn-danger" onclick="return confirm('Souhaitez vous vraiment axecuter cette action?')">
                                    Supprimer
                                </a> -->
                            <!-- si on utilise la methode "post" -->
                                <form action="<?= '/backend/deletePost/'. $post->getId()?>" methode="POST"
                                    onsubmit="return confirm('Souhaitez vous vraiment executer cette action?')">
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                </form>
                        <a href="<?= '/backend/editCommentsPost/'. $post->getId()?>" class="btn btn-success"> <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                            Voir les commentaires
                        </a>
                        <a href="<?= '/post/'. $post->getId()?>" class="btn btn-secondary"> <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                            Voir le post
                        </a>
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