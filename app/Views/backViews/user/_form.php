<form action="" method="post" enctype="multipart/form-data">
    <?= $formUser->input('firstName', 'firstName','firstName' ) ?>
    <?= $formUser->input('lastName', 'lastName', 'lastName') ?>
    <?= $formUser->input('email', 'email', 'email') ?>
    <?= $formUserType->selectSimple('id', 'userType_id', 'statut', $listUserTypesSelect) ?> <!--select des userType -->
    <?= $formUser->input('slogan', 'slogan', 'slogan') ?>

    <!-- affichage du logo du user (faudra verifier que le tableau de logo existe bien-->
    <?php  
         if ($formUser->getEdit() === true and !empty($listLogos)){//dans le cas ou l'on edit un user et que celui ci possede un logo
            echo $formMediaLogoUser->inputImage($logoUser, 'logo de l user', 'logoUser', 'logo actuel de l user');   //pour l'affichage du logo
        }
    ?>

    <?= $formMediaUploadLogo->inputFile('logo', 'mediaUploadLogo', 'rajouter un logo (uploader un fichier image max 500ko) a cette user') ?>    <!-- pour la creation du upload du logo -->
    <?= $formMediaUploadLogo->input('alt', 'altFileMediaLogo', 'texte alternatif pour le logo uploader') ?>    <!-- pour la creation du input du text alt du media logo -->
    
    <?= $formSocialNetwork->input('url', 'socialNetwork','ajouter un socialNetwork' ) ?>

    <?php 
        if ($formUser->getEdit() === true and !empty($listSocialNetworksForUser)){ //pour gerer le cas si on edit un user et que celui ci possede au moins un socialnetwork(on affiche le select) ou si on cree un user ou que celui-ci ne possede pas au moins un socialNetwork (on n affiche pas le select)
            echo $formSocialNetworkSelect->selectMultiple('id', 'socialNetworksUser','supprimer un/des socialNetwork', $listSocialNetworksForUserForSelect, $listSocialNetworksForUserForSelect); //pour la creation du select des medias
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