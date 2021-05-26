<form action="" method="post" enctype="multipart/form-data">
    <?= $formUser->input('firstName', 'firstName','firstName' ) ?>
    <?= $formUser->input('lastName', 'lastName', 'lastName') ?>
    <?= $formUser->input('email', 'email', 'email') ?>
    <?= $formUser->input('slogan', 'slogan', 'slogan') ?>

    <!-- affichage du logo du user (verifie que le tableau de logo existe bien-->
    <?php  
         if ($formUser->getEdit() === true and !empty($listLogos)){// in the event that we edit a user and that this one has a logo 
            echo $formMediaLogoUser->inputImage($logoUser, 'logo de l user', 'logoUser', 'logo actuel de l user');   // for logo display 
        }
    ?>

    <?= $formMediaUploadLogo->inputFile('logo', 'mediaUploadLogo', 'rajouter un logo (uploader un fichier image max 500ko) a cette user') ?>    <!-- for the creation of the logo upload  -->
    <?= $formMediaUploadLogo->input('alt', 'altFileMediaLogo', 'texte alternatif pour le logo uploader') ?>    <!-- for the creation of the input of the alt text of the media logo  -->
    
    <?= $formSocialNetwork->input('url', 'socialNetwork','ajouter un socialNetwork' ) ?>

    <?php 
        if ($formUser->getEdit() === true and !empty($listSocialNetworksForUser)){ // to manage the case if we edit a user and this one has at least one socialnetwork (we display the select) or if we create a user or if this one does not have at least one socialNetwork (we do not display the select) 
            echo $formSocialNetworkSelect->selectMultiple('id', 'socialNetworksUser','supprimer un/des socialNetwork', $listSocialNetworksForUserForSelect, $listSocialNetworksForUserForSelect); // for the creation of the media select 
        }
    ?>

    <?= $formUser->input('login', 'login', 'login') ?> 
    <?= $formUser->input('password', 'password', 'password') ?>
  
    <button class="btn btn-primary">
        <?php if($user->getId() !==null):?>
            Modifier
        <?php else:?>
            créer
        <?php endif?>
    </button>

   <a href="/" class="btn btn-secondary">Annuler</a>
</form>