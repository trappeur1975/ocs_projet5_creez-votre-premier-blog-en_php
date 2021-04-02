<form action="" method="post">
    <?= $form->input('path', 'path') ?>
    <?= $form->input('alt', 'alt') ?>
    <?= $form->input('type', 'type') ?>
    <?= $form->select('type', 'type', $list) ?>

    <button class="btn btn-primary">
        <?php if($media->getId() !==null):?>
            Modifier
        <?php else:?>
            créer
        <?php endif?>
    </button>
    <a href="/backend/adminMedias" class="btn btn-primary">Administration des medias</a>
</form>