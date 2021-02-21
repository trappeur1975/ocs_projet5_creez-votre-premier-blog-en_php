<form action="" method="post">
    <?= $form->input('type', 'type') ?>

    <button class="btn btn-primary">
        <?php if($mediaType->getId() !==null):?>
            Modifier
        <?php else:?>
            créer
        <?php endif?>
    </button>
    <a href="/backend/adminMediaTypes" class="btn btn-primary">Administration des mediaTypes</a>
</form>