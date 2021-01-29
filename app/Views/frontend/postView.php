<?php //require('../vendor/altorouter/altorouter/AltoRouter.php');?>

<?php $title = 'The Blog'; ?>

<?php ob_start(); ?>
        
    <body>
        <h1>Test post nico</h1>
        <p>THE POST:</p>
        <h2>tilte</h2>
        <?= $post->getTitle(); ?>
        <h2>introduction</h2>
        <?= $post->getIntroduction(); ?>
        <h2>content</h2>
        <?= $post->getContent(); ?>
        <h2>DateCreate</h2>
        <?= $post->getDateCreate(); ?>
        <h2>Datechange</h2>
        <?= $post->getDatechange(); ?>
        <h2>User_id</h2>
        <?= $post->getUser_id(); ?>
        
        <!-- <a href="/listposts">listposts</a> -->
        <!-- ISSUE PROBLEME DE ROUTER -->
        <a href="<?php //echo($router->generate('listposts')) ?>">My listposts</a>
        <?php //$router->generate('listposts') ?>

 
    <p>FIN this POST</p>

<?php $content = ob_get_clean(); ?>

<?php require('template.php'); ?>