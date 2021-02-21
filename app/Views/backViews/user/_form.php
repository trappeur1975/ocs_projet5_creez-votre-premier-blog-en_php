<form action="" method="post">
    <?= $form->input('firstName', 'firstName') ?>
    <?= $form->input('lastName', 'lastName') ?>
    <?= $form->input('email', 'email') ?>
    <?= $form->input('picture', 'picture') ?>
    <?= $form->input('logo', 'logo') ?>
    <?= $form->input('slogan', 'slogan') ?>
    <?= $form->input('socialNetworks', 'socialNetworks') ?>  <!-- DOIT ETRE UN ARRAY -->
    <?= $form->input('login', 'login') ?>
    <?= $form->input('password', 'password') ?>
    <?= $form->input('validate', 'validate') ?> <!-- DOIT ETRE UN DATETIME -->

    <button class="btn btn-primary">
        <?php if($user->getId() !==null):?>
            Modifier
        <?php else:?>
            créer
        <?php endif?>
    </button>
    <a href="/backend/adminPosts" class="btn btn-primary">Administration des post</a>
</form>