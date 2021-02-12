<form action="" method="post">
    <?= $form->input('title', 'titre') ?>
    <?= $form->textarea('introduction', 'introduction') ?>
    <?= $form->textarea('content', 'content') ?>
    <?= $form->input('dateCreate', 'date de creation') ?>
    <?= $form->input('dateChange', 'date de changement') ?>
    <?= $form->input('user_id', 'user_id') ?>

    <button class="btn btn-primary">
        <?php if($post->getId() !==null):?>
            Modifier
        <?php else:?>
            créer
        <?php endif?>
    </button>
    <a href="/backend/adminPosts" class="btn btn-primary">Administration des post</a>
</form>