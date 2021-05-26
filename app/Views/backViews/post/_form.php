<form action="" method="post" enctype="multipart/form-data">
    <!-- CREATION OF FIELDS SPECIFIC TO THE POST CLASS  -->
        <?= $formPost->input('title', 'title', 'titre') ?>
        <?= $formPost->textarea('introduction', 'introduction') ?>
        <?= $formPost->textarea('content', 'contenu') ?>
        <?= $formPost->input('dateCreate', 'dateCreate', 'date de creation') ?>
        <?php
            if ($formPost->getEdit() === true){ // to handle the case if we edit a post (we display the select) or if we create a post (we do not display the select because we will integrate a field to integrate media into the post we are creating) 
                echo $formPost->input('dateChange', 'dateChange', 'date de modification');
            }
        ?>

    <!-- CREATION OF SELECT FIELDS  -->
        <?= $formUser->selectSimple('id', 'user', 'auteur', $listUsersSelect) ?>   <!-- for the creation of the users' select  -->
        <?php 
            if ($formUser->getEdit() === true and !empty($listMediasForUserForType)){ // to manage the case if we edit a post and the user of this post has at least one media image (we display the select) or if we create a post (we do not display the select because we will integrate a field to integrate media to the post we create) 
                echo $formMediasImageSelect->selectMultiple('id', 'path','medias', $listMediasForUserSelect, $listMediasForPostSelect); // for the creation of the media select 
            }
        ?>

    <!-- CREATION OF FIELDS FOR THE MEDIA UPLOAD (image)  -->
        <?= $formMediaUploadImage->inputFile('media', 'mediaUploadImage', 'rajouter une image (uploader un fichier max 500ko) a ce post') ?>    <!-- for the creation of the media upload  -->
        <?= $formMediaUploadImage->input('alt', 'altFileMediaImage', 'texte alternatif du media IMAGE uploader') ?>    <!-- for the creation of the input of the alt text of the media image  -->
    <!-- CREATION OF FIELDS FOR THE MEDIA UPLOAD (video)  -->
        <?= $formMediaUploadVideo->input('path', 'mediaUploadVideo', 'rajouter une video (lien url youtube ou vimeo uniquement) a ce post') ?>
        <?= $formMediaUploadVideo->input('alt', 'altFileMediaVideo', 'texte alternatif du media VIDEO uploader') ?>    <!-- for the creation of the input of the alt text of the media video -->

    <button class="btn btn-primary">
        <?php if($post->getId() !==null):?>
            Modifier
        <?php else:?>
            créer
        <?php endif?>
    </button>
    <a href="/backend/adminPosts" class="btn btn-secondary">Administration des posts</a>
</form>