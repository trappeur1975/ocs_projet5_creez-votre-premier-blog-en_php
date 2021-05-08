<?php 
    ob_start(); 
    // $countComment = 1;    
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
        <?php elseif(isset($_GET['deleteComment'])and($_GET['deleteComment'])==='true'): ?>
            <div class="alert alert-success">
                le commentaire a bien été supprimer.
            </div>    
        <?php elseif(isset($_GET['deleteComment'])and($_GET['deleteComment'])==='false'): ?>
            <div class="alert alert-danger">
                le commentaire n'a pu être supprimer.
            </div>
        <?php elseif(isset($_GET['successUploadComment'])and($_GET['successUploadComment'])==='true'): ?>
            <div class="alert alert-success">
                le commentaire a bien été modifier.
            </div>    
        <?php elseif(isset($_GET['successUploadComment'])and($_GET['successUploadComment'])==='false'): ?>
            <div class="alert alert-danger">
                le commentaire n'a pu être modifier.
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
        <!-- <img src="<?//='/'.$listMediasForPost[0]->getPath(); ?>" alt="<?//=$listMediasForPost[0]->getAlt(); ?>"/> -->
        <?php
            if(!empty($listMediasForPost)){
                echo '<img src=/'.$listMediasForPost[0]->getPath().' alt="'.$listMediasForPost[0]->getAlt().'">';
            }
        ?>
        
    <h2>content</h2>
        <?= formatHtml($post->getContent()); ?>
    <h2>DateCreate</h2>
        <?= htmlentities($post->getDateCreate()); ?>
    <h2>Datechange</h2>
        <?= htmlentities($post->getDatechange()); ?>
    <h2>auteur du Post :</h2>
        <p><?= formatHtml($userPost->getLastName().' '.$userPost->getFirstName()) ?></p>
    
    <?php //affiche du formulaire de commentaire seulement si on est connecter au site et un status abonner ou administrateur  
        if(isset($_SESSION['connection'])){
            if($userManager->getUserSatus($_SESSION['connection'])['status'] === 'administrateur' OR $userManager->getUserSatus($_SESSION['connection'])['status'] === 'abonner'){
                echo '<h3>laisser un nouveau commentaire : </h3>';
                require('../app/Views/frontViews/_formComment.php');
                echo '</br>';
                echo '</br>';
            }
        }  
    ?>
    
    <h3>les commentaires du post: </h3>
    
    <?php 
        foreach ($listCommentsForPost as $comment){   
            $userComment = $userManager->getUser($comment->getUser_id()); //pour recuperer le user du commentaire
    ?>
            <h5>commentaire de <?= $userComment->getLastName().' '.$userComment->getFirstName() ?></h5>

            

            <p><?= formatHtml($comment->getComment()); ?></p>


            <!-- <?php //$countComment++; ?> -->

            <?php
                if(isset($_SESSION['connection'])){
                    if($_SESSION['connection'] === $comment->getUser_id()){
            ?>
                        <form action="<?= '/deleteCommentPostFront/'. $comment->getId()?>" methode="POST"
                            onsubmit="return confirm('Souhaitez vous vraiment executer cette action?')">
                            <button type="submit" class="btn btn-danger">Supprimer votre commentaire</button>
                        </form>
                        <a href="<?= '/editCommentPostFront/'. $comment->getId()?>" class="btn btn-primary">
                            Modifier votre commentaire
                        </a>
            <?php
                    }
                }        
            ?>

    <?php } ?>

<?php 
$content = ob_get_clean();
require('../app/Views/template.php'); 
?>