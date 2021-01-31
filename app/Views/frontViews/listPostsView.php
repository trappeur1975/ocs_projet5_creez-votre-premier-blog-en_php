<?php
$title = 'liste des posts';
ob_start(); 
?>
    <h1>Test listpost nico</h1>
        <p>affichage des post:</p>

        <?php
        // methode grafikart
        foreach ($listPosts as $post) {
            echo htmlentities($post->getTitle()) . '</br>'; // ici on affiche que les titres
        }
        ?>

        <p>this finish3</p>

<?php 
$content = ob_get_clean();
require('../app/Views/template.php'); 
?>