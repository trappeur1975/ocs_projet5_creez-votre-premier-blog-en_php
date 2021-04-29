<form action="" method="post" enctype="multipart/form-data">
    <!-- CREATION DES CHAMPS PROPRE A LA CLASSE POST -->
        <?= $formPost->input('title', 'title', 'titre') ?>
        <?= $formPost->textarea('introduction', 'introduction') ?>
        <?= $formPost->textarea('content', 'contenu') ?>
        <?= $formPost->input('dateCreate', 'dateCreate', 'date de creation') ?>
        <?php
            if ($formPost->getEdit() === true){ //pour gerer le cas si on edit un post (on affiche le select) ou si on cree un post (on n affiche pas le select car on integrera un champs pour integrer des media au post que l on cree)
                echo $formPost->input('dateChange', 'dateChange', 'date de modification');
            }
        ?>

    <!-- CREATION DES CHAMPS  SELECT -->
        <?= $formUser->selectSimple('id', 'user', 'auteur', $listUsersSelect) ?>   <!-- pour la creation du select des users -->
        <?php 
            if ($formUser->getEdit() === true){ //pour gerer le cas si on edit un post (on affiche le select) ou si on cree un post (on n affiche pas le select car on integrera un champs pour integrer des media au post que l on cree)
                echo $formMediasSelectImage->selectMultiple('id', 'path','medias', $listMediasForUserSelect, $listMediasForPostSelect); //pour la creation du select des medias
            }
        ?>

    <!-- CREATION DES CHAMP POUR LE UPLOAD DE MEDIA (image) -->
        <?= $formMediaUploadImage->inputFile('media', 'mediaUploadImage', 'rajouter une image (uploader un fichier max 500ko) a ce post') ?>    <!-- pour la creation du upload du media -->
        <?= $formMediaUploadImage->input('alt', 'altFileMediaImage', 'texte alternatif du media IMAGE uploader') ?>    <!-- pour la creation du input du text alt du media image -->
    <!-- CREATION DES CHAMP POUR LE UPLOAD DE MEDIA (video) -->
        <?= $formMediaUploadVideo->input('path', 'mediaUploadVideo', 'rajouter une video (lien url youtube ou vimeo uniquement) a ce post') ?>
        <?= $formMediaUploadVideo->input('alt', 'altFileMediaVideo', 'texte alternatif du media VIDEO uploader') ?>    <!-- pour la creation du input du text alt du media video -->

    <button class="btn btn-primary">
        <?php if($post->getId() !==null):?>
            Modifier
        <?php else:?>
            créer
        <?php endif?>
    </button>
    <a href="/backend/adminPosts" class="btn btn-primary">Administration des post</a>
</form>