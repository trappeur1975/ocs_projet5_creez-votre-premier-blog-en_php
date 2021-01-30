<?php
$title = 'The Blog';

ob_start(); 
?>

<body>
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

require('template.php'); 
?>