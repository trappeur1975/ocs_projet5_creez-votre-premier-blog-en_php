<?php //require('../vendor/altorouter/altorouter/AltoRouter.php');?>



<?php ob_start(); ?>
        
    <body>
        <h1>Test post nico</h1>
        <p>THE POST:</p>
        <h2>tilte</h2>
        <?= $title = formatHtml($post->getTitle()); ?> <!-- we display here the title of the post but also to integrate this title in the browser tab by putting this title in the variable $ title  -->
        <h2>introduction</h2>
        <?= formatHtml($post->getIntroduction()); ?> 
        <h2>content</h2>
        <?= formatHtml($post->getContent()); ?>
        <h2>DateCreate</h2>
        <?= htmlentities($post->getDateCreate()); ?>
        <h2>Datechange</h2>
        <?= htmlentities($post->getDatechange()); ?>
        <h2>User_id</h2>
        <?= htmlentities($post->getUser_id()); ?>
        
        <!-- <a href="/listposts">listposts</a> -->
        <a href="<?php //echo($router->generate('listposts')) ?>">My listposts</a>
        <?php //$router->generate('listposts') ?>

 
    <p>FIN this POST</p>

<?php $content = ob_get_clean(); ?>

<?php require('template.php'); ?>