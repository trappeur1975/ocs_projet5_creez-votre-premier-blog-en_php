

<?php 
$title = 'Administration des posts';
ob_start(); 
?>
        
    <h1>page Admin Post du backend</h1>

    <?php  if(isset($_GET['delete'])): ?>
        <div class="alert alert-success">
            le post a bien été supprimé.
        </div>
    <?php endif ?>

    <table class= "table">
        <thead>
            <th>Id</th>
            <th>Titre</th>
            <th>Actions</th>
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
                    <a href="<?= '/backend/deletePost/'. $post->getId()?>" class="btn btn-danger" onclick="return confirm('Souhaitez vous vraiment axecuter cette action?')"> <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                        Supprimer
                    </a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>


<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>