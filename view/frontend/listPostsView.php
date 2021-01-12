<?php $title = 'The Blog'; ?>

<?php ob_start(); ?>

<body>
    <h1>Test mvc</h1>
    <p>affichage des post:</p>

    <?php //boucle d'affichage des donnnees => ici les titres des posts
    while ($data = $listPosts->fetch()) {
        echo htmlspecialchars($data['title']) . '</br>';
    }
    $listPosts->closeCursor();
    ?>

    <p>this finish3</p>

    <?php //teste hydratation

    // attention ne pas mettre de $ devant les key de ce tableau
    $donnees = array(
        'id' => 56,
        'title' => 'mon premier post',
        'introduction' => 'mon introduction',
        'content' => 'mon contenu super class',
        'dateCreate' => 'janvier2020',
        'datechange' => 'janvier2021',
        'user_id' => 20
    );

    // $myPost = new Post($donnees);
    // var_dump($myPost);
    ?>

    <?php $content = ob_get_clean(); ?>

    <?php require('template.php'); ?>