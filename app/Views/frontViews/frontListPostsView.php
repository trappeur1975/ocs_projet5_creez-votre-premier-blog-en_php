<?php
$title = 'Liste des posts';
ob_start(); 
?>
    <h1>Test listpost nico</h1>
        <p>affichage des post:</p>

        <?php
        // methode grafikart
        foreach ($listPosts as $post) {
        ?>
            <!-- echo formatHtml($post->getTitle()) . '</br>'; // ici on affiche que les titres -->
            <a href="<?= '/post/'. $post->getId()?>">
                <?= formatHtml($post->getTitle()) ?>
            </a>
            </br>
        <?php   
        }
        ?>

        <p>this finish3</p>

<?php 
$content = ob_get_clean();
require('../app/Views/template.php'); 
?>