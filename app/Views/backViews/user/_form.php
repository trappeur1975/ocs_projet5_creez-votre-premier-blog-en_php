<form action="" method="post">
    <?= $formUser->input('firstName', 'firstName','firstName' ) ?>
    <?= $formUser->input('lastName', 'lastName', 'lastName') ?>
    <?= $formUser->input('email', 'email', 'email') ?>
    <?= $formUserType->selectSimple('id', 'userType_id', 'statut', $listSelectUserTypes) ?> <!--select des userType -->
    <?= $formUser->input('slogan', 'slogan', 'slogan') ?>
    <?= $formUser->input('login', 'login', 'login') ?>
    <?= $formUser->input('password', 'password', 'password') ?>
    <?= $formUser->input('validate', 'validate', 'validate') ?> <!-- DOIT ETRE UN DATETIME -->

    <button class="btn btn-primary">
        <?php if($user->getId() !==null):?>
            Modifier
        <?php else:?>
            créer
        <?php endif?>
    </button>

    <a href="/backend/adminPosts" class="btn btn-primary">Administration des post</a>
</form>