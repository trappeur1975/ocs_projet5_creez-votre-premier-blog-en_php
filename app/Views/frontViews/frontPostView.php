<?php 
    ob_start(); 
    $countComment = 1;    
?>

<!-- start the labels on the state of the comment of the post   -->
    <!-- to manage the display of the success or not of the comment of a post -->
    <?php  if(isset($_GET['createdComment'])and($_GET['createdComment'])==='true'): ?>
            <div class="alert alert-success">
                le commentaire a bien été créé.
            </div>
        <?php elseif(isset($_GET['createdComment'])and($_GET['createdComment'])==='false'): ?>
            <div class="alert alert-danger">
                le commentaire n'a pu être créé.
            </div>
    <?php endif ?>

<h1><?= $title = formatHtml($post->getTitle()).' (post '. $id.')'; ?></h1>  <!-- we display here the title of the post but also to integrate this title in the browser tab by putting this title in the variable $ title  -->       
<!-- <h1>THE POST: <?= $id ?></h1> -->
    <!-- <h2>tilte</h2>
        <?= $title = formatHtml($post->getTitle()); ?> we display here the title of the post but also to integrate this title in the browser tab by putting this title in the variable $ title  -->
    <h2>introduction</h2>
        <?= formatHtml($post->getIntroduction()); ?> 
    <h2>image</h2>
        <!-- <img src="/media/post_top_javascript_img1.png" alt="Photo de montagne" /> -->
        <img src="<?='/'.$listMediasForPost[0]->getPath(); ?>" alt="<?=$listMediasForPost[0]->getAlt(); ?>" />
    <h2>content</h2>
        <?= formatHtml($post->getContent()); ?>
    <h2>DateCreate</h2>
        <?= htmlentities($post->getDateCreate()); ?>
    <h2>Datechange</h2>
        <?= htmlentities($post->getDatechange()); ?>
    <h2>auteur du commentaire :</h2>
        <p><?= formatHtml($userPost->getLastName().' '.$userPost->getFirstName()) ?></p>
    <h2>les commentaires : </h2>
    
    <?php 
        foreach ($listCommentsForPost as $comment){   
            $userComment = $userManager->getUser($comment->getUser_id()); //pour recuperer le user du commentaire
    ?>
        <h3>commentaire de <?= $userComment->getLastName().' '.$userComment->getFirstName() ?></h3>
        <!-- <h3>commentaire : <?= $countComment; ?></h3> -->
        <p><?= formatHtml($comment->getComment()); ?></p>
        <?php $countComment++; ?>
    <?php } ?>

    <?php require('../app/Views/frontViews/_form.php')?>

    <!-- <a href="/listposts">listposts</a> -->
    <!-- <a href="<?php //echo($router->generate('listposts')) ?>">My listposts</a> -->
    <?php //$router->generate('listposts') ?>

<?php 
$content = ob_get_clean();
require('../app/Views/template.php'); 
?>