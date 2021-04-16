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
        <?= $formUser->selectSimple('id', 'user', 'auteur', $listSelectUsers) ?>   <!-- pour la creation du select des users -->
        <?php 
            if ($formUser->getEdit() === true){ //pour gerer le cas si on edit un post (on affiche le select) ou si on cree un post (on n affiche pas le select car on integrera un champs pour integrer des media au post que l on cree)
                echo $formMediasSelect->selectMultiple('id', 'path','medias', $listSelectMediasForUser, $listSelectMediasForPost); //pour la creation du select des medias
            }
        ?>

    <!-- CREATION DES CHAMP POUR LE UPLOAD DE MEDIA (fichier) -->
        <?= $formMediaUpload->inputFile('media', 'mediaUpload', 'rajouter un media a ce post') ?>    <!-- pour la creation du upload du media -->
        <?= $formMediaType->selectSimple('id', 'mediaType', 'Type du media uploader', $listMediaTypes) ?>   <!-- pour la creation du select des mediaTypes -->
        <?= $formMediaUpload->input('alt', 'alt', 'texte alternatif du media uploader') ?>    <!-- pour la creation du input du text alt du media -->

    <button class="btn btn-primary">
        <?php if($post->getId() !==null):?>
            Modifier
        <?php else:?>
            créer
        <?php endif?>
    </button>
    <a href="/backend/adminPosts" class="btn btn-primary">Administration des post</a>
</form>