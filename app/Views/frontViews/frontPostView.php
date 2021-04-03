<?php ob_start(); ?>
        
<h1>Test post nico</h1>
    <p>THE POST:</p>
    <h2>tilte</h2>
        <?= $title = formatHtml($post->getTitle()); ?> <!-- we display here the title of the post but also to integrate this title in the browser tab by putting this title in the variable $ title  -->
    <h2>image</h2>
        <!-- <img src="/media/post_top_javascript_img1.png" alt="Photo de montagne" /> -->
        <img src="<?='/'.$listMediasForPost[0]->getPath(); ?>" alt="<?=$listMediasForPost[0]->getAlt(); ?>" />
        <h3>type image : <?=$listMediasForPost[0]->getMediaType(); ?></h3>

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

<?php 
$content = ob_get_clean();
require('../app/Views/template.php'); 
?>