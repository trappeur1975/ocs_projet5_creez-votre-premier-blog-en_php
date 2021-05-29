<?php 
    ob_start(); 
?>

<!-- start the labels on the state of the comment of the post   -->
    <!-- to manage the display of the success or not of the comment of a post -->
    <?php  if (isset($_GET['createdComment'])and($_GET['createdComment'])==='true') : ?>
            <div class="alert alert-success">
                le commentaire a bien été créé.
            </div>
        <?php elseif (isset($_GET['createdComment'])and($_GET['createdComment'])==='false') : ?>
            <div class="alert alert-danger">
                le commentaire n'a pu être créé.
            </div>
        <?php elseif (isset($_GET['deleteComment'])and($_GET['deleteComment'])==='true') : ?>
            <div class="alert alert-success">
                le commentaire a bien été supprimer.
            </div>    
        <?php elseif (isset($_GET['deleteComment'])and($_GET['deleteComment'])==='false') : ?>
            <div class="alert alert-danger">
                le commentaire n'a pu être supprimer.
            </div>
        <?php elseif (isset($_GET['successUploadComment'])and($_GET['successUploadComment'])==='true') : ?>
            <div class="alert alert-success">
                le commentaire a bien été modifier.
            </div>    
        <?php elseif (isset($_GET['successUploadComment'])and($_GET['successUploadComment'])==='false') : ?>
            <div class="alert alert-danger">
                le commentaire n'a pu être modifier.
            </div>
    <?php endif ?>

<h1><?= $title = formatHtml($post->getTitle()).' (post '. $id.')'; ?></h1>  <!-- we display here the title of the post but also to integrate this title in the browser tab by putting this title in the variable $ title  -->
    <h2>introduction</h2>
        <?= formatHtml($post->getIntroduction()); ?> 
        
            <?php
                if (!empty($listMediasImage)) { //media image
                    echo'<h3>image intro</h3>';
                    echo '<img src=/'.htmlentities($listMediasImage[0]->getPath()).' alt="'.$listMediasImage[0]->getAlt().'">';
                }
            ?>       
            
            <?php       
                if (!empty($listMediasVideo)) { //media video
                    echo'<h3>video intro</h3>';
                    echo '<iframe class="embed-responsive-item" src="'.$listMediasVideo[0]->getPath().'" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                }  
            ?>
        
    <h2>content</h2>
        <?= formatHtml($post->getContent()); ?>
    
    <?php // display of other images if they exist
        if (!empty($listMediasImage) and count($listMediasImage)>1) { //media image
            $compteur = 0;
            $indice = 0;

            echo'<h3>autres images</h3>';
                
            foreach ($listMediasImage as $mediaImage) {
                if ($compteur == 0) {
            ?>
                        <div class="row">
            <?php        
                    }
                        if ($indice !== 0) {
            ?>
                            <div class="col-12 col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                    <?php 
                                        
                                            echo '<img class="card-img-top" src=/'.htmlentities($mediaImage->getPath()).' alt="'.$mediaImage->getAlt().'">';
                                               
                                    ?>
                                    </div>
                                </div>
                            </div> 
            <?php       
                        }
                    $compteur++;
                    $indice++;

                    if ($compteur == 3) {
            ?>
                        </div>  <!-- fin row  -->
            <?php        
                        $compteur = 0;
                    }         
                } //fin foreach 
        }
    ?>

    <?php // display of other videos if they exist
        if (!empty($listMediasVideo) and count($listMediasVideo)>1) { //media video
            $compteur = 0;
            $indice = 0;

            echo'<h3>autres videos</h3>';
                
            foreach ($listMediasVideo as $mediaVideo) {
                if ($compteur == 0) {
            ?>
                        <div class="row">
            <?php        
                    }
                        if ($indice !== 0) {
            ?>
                            <div class="col-12 col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                    <?php 
                                        echo '<iframe class="embed-responsive-item" src="'.$mediaVideo->getPath().'" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                                    ?>
                                    </div>
                                </div>
                            </div> 
            <?php       
                        }
                    $compteur++;
                    $indice++;

                    if ($compteur == 3) {
            ?>
                        </div>  <!-- fin row  -->
            <?php        
                        $compteur = 0;
                    }         
                } //fin foreach 
        }
    ?>
    
    <h2>DateCreate</h2>
        <?= htmlentities($post->getDateCreate()); ?>
    <h2>Datechange</h2>
        <?= htmlentities($post->getDatechange()); ?>
    <h2>auteur du Post :</h2>
        <p><?= formatHtml($userPost->getLastName().' '.$userPost->getFirstName()) ?></p>
    
    <?php //displays the comment form only if you are connected to the site and a subscriber or administrator status 
        if (isset($_SESSION['connection'])) {
            if ($userManager->getUserSatus($_SESSION['connection'])['status'] === 'administrateur' OR $userManager->getUserSatus($_SESSION['connection'])['status'] === 'abonner') {
                echo '<h3>laisser un nouveau commentaire : </h3>';
                require'../app/Views/frontViews/_formComment.php';
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

            <?php
                if (isset($_SESSION['connection'])) {
                    if ($_SESSION['connection'] === $comment->getUser_id()) {
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
require'../app/Views/template.php'; 
?>