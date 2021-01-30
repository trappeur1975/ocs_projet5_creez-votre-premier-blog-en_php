<?php //require('../vendor/altorouter/altorouter/AltoRouter.php');?>

<?php $title = 'The Blog'; ?>

<?php ob_start(); ?>
        
    <body>
        <h1>Test post nico</h1>
        <p>THE POST:</p>
        <h2>tilte</h2>
        <?= nl2br(htmlentities($post->getTitle())); ?>
        <h2>introduction</h2>
        <?= nl2br(htmlentities($post->getIntroduction())); ?>  <!-- note perso mettre se formattage dans la class post a cette fonction -->
        <h2>content</h2>
        <?= nl2br(htmlentities($post->getContent())); ?> <!-- note perso mettre se formattage dans la class post a cette fonction -->
        <h2>DateCreate</h2>
        <?= htmlentities($post->getDateCreate()); ?>
        <h2>Datechange</h2>
        <?= htmlentities($post->getDatechange()); ?>
        <h2>User_id</h2>
        <?= htmlentities($post->getUser_id()); ?> -->
        <!-- <?php dump($post); ?> -->
        
        <!-- <a href="/listposts">listposts</a> -->
        <a href="<?php //echo($router->generate('listposts')) ?>">My listposts</a>
        <?php //$router->generate('listposts') ?>

 
    <p>FIN this POST</p>

<?php $content = ob_get_clean(); ?>

<?php require('template.php'); ?>