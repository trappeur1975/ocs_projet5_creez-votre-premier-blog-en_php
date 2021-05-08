<form action="" method="post" ">
    <?= $formComment->textarea('comment', 'commentaire') ?>
    <button class="btn btn-primary">
        <?php 
            if ($formComment->getEdit() === true ){
                echo 'modifier le commentaire';
            } else {
                echo 'envoyer le commentaire';
            }
        ?>
    </button>
</form>