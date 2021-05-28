<?php
$title = 'Font Liste des posts';
ob_start(); 
?>
    <h1>Les Posts du Blog</h1>

            <!-- <div class="row"> -->
            <?php
                $compteur = 0;
                
                foreach ($listPosts as $post) {
                    if ($compteur == 0) {
            ?>
                        <div class="row">
            <?php        
                    }
            ?>
                            <div class="col-12 col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-text">  
                                            <h2><?= formatHtml($post->getTitle()) ?></h2>
                                            <?= $post->getDateChange()?>
                                        </p>
                                        <p class="card-text text-justify">  
                                            <?= $post->getIntroduction()?>
                                        </p>
                                        <a href="<?= '/post/'. $post->getId()?>" class="btn btn-primary">Voir le Post</a>
                                    </div>
                                </div>
                            </div> 
            <?php       
                    $compteur++;

                    if ($compteur == 3) {
            ?>
                        </div>  <!-- fin row  -->
            <?php        
                        $compteur = 0;
                    }         
                } //fin foreach 
            ?>

<?php 
$content = ob_get_clean();
require'../app/Views/template.php'; 
?>