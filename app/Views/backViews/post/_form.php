<form action="" method="post">
    <!-- CREATION DES CHAMPS PROPRE A LA CLASSE POST -->
        <?= $formPost->input('title', 'title', 'titre') ?>
        <?= $formPost->textarea('introduction', 'introduction') ?>
        <?= $formPost->textarea('content', 'contenu') ?>
        <?= $formPost->input('dateCreate', 'dateCreate', 'date de creation') ?>
        <?= $formPost->input('dateChange', 'dateChange', 'date de modification') ?>
    
    <!-- CREATION DES CHAMPS  SELECT -->
        <?= $formUser->select('id', 'user', 'auteur', $listSelectUsers) ?>   <!-- pour la creation du select des users -->
        <?= $formMedia->select('id', 'path','medias', $listSelectMediasForUser, $listSelectMediasForPost, 'multiple') ?><!--  pour la creation du select des medias -->
        <!--<?//= $formMedia->select('id', 'media', $listSelectMediasForUser, $listSelectMediasForPost, 'multiple') ?>  pour la creation du select des medias -->

    <button class="btn btn-primary">
        <?php if($post->getId() !==null):?>
            Modifier
        <?php else:?>
            créer
        <?php endif?>
    </button>
    <a href="/backend/adminPosts" class="btn btn-primary">Administration des post</a>
</form>