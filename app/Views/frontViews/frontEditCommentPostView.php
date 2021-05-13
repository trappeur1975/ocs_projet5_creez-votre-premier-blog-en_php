<?php

use App\Entities\Form;

$title = 'Front Edit comment front';

ob_start(); 
?>

<!-- start main content  -->
    <h1>Front Edit the Comment id: <?= $id ?></h1>

    <?php require('../app/Views/frontViews/_formComment.php'); ?>
    <a href="<?= '/post/'.$comment->getPost_id()?>" class="btn btn-success">
        Annuler
    </a>

<!-- end main content  -->

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>