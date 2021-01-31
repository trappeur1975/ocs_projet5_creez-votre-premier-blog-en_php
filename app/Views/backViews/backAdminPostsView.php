

<?php 
$title = 'Administration des posts';
ob_start(); 
?>
        
    <h1>page Admin Post du backend</h1>



    <table class= "table">
        <thead>
            <th>Titre</th>
            <th>Actions</th>
        </thead>
        <tbody>
            <?php foreach ($listPosts as $post): ?>         
            <tr>
                <td>
                    <a href="<?= '/backend/editPost/'. $post->getId()?>"> <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                    <?= formatHtml($post->getTitle()) ?>
                    </a>
                </td>
                <td></td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>


<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>