<form action="" method="post" enctype="multipart/form-data">
    <?= $formUser->input('firstName', 'firstName','firstName' ) ?>
    <?= $formUser->input('lastName', 'lastName', 'lastName') ?>
    <?= $formUser->input('email', 'email', 'email') ?>
    <?= $formUserType->selectSimple('id', 'userType_id', 'statut', $listSelectUserTypes) ?> <!--select des userType -->
    <?= $formUser->input('slogan', 'slogan', 'slogan') ?>

    <?= $formMediaUploadLogo->inputFile('logo', 'mediaUploadLogo', 'rajouter un logo (uploader un fichier image max 500ko) a cette user') ?>    <!-- pour la creation du upload du logo -->
    <?= $formMediaUploadLogo->input('alt', 'altFileMediaLogo', 'texte alternatif pour le logo uploader') ?>    <!-- pour la creation du input du text alt du media logo -->
    
    <?= $formSocialNetwork->input('url', 'socialNetwork','ajouter un socialNetwork' ) ?>
    <?php 
        if ($formUser->getEdit() === true){ //pour gerer le cas si on edit un user (on affiche le select) ou si on cree un user (on n affiche pas le select)
            echo $formSocialNetworkSelect->selectMultiple('id', 'socialNetworksUser','supprimer un/des socialNetwork', $listSocialNetworksForUser, $listSocialNetworksForUser); //pour la creation du select des medias
        }
    ?>

    <?= $formUser->input('login', 'login', 'login') ?> 
    <?= $formUser->input('password', 'password', 'password') ?>
    <?= $formUser->input('validate', 'validate', 'validate') ?> <!--  DOIT ETRE UN DATETIME -->

    <button class="btn btn-primary">
        <?php if($user->getId() !==null):?>
            Modifier
        <?php else:?>
            créer
        <?php endif?>
    </button>

    <a href="/backend/adminUsers" class="btn btn-primary">Administration des users</a>
</form>