<?php 
$title = 'Administration des medias';
ob_start(); 
?>
        

<!-- label if the user was successfully deleted  -->
    <?php  if(isset($_GET['delete'])): ?>
        <div class="alert alert-success">
            le media a bien été supprimé.
        </div>
    <?php endif ?>

<!-- start main content  -->
    <h1>page Admin Medias du backend</h1>
        <table class= "table">
            <thead>
                <th>Id</th>
                <th>Path</th>
                <th>
                    <a href="/backend/createMedia" class="btn btn-primary">nouveau media</a>
                <th>

                </th>
            </thead>
            <tbody>
                <?php foreach ($listMedias as $media): ?>         
                <tr>
                    <td>
                        #<?= $media->getId() ?>
                    </td>

                    <td>
                        <a href="<?= '/backend/editMedia/'. $media->getId()?>"> <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                        <?= formatHtml($media->getPath()) ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= '/backend/editMedia/'. $media->getId()?>" class="btn btn-primary"> <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                            Editer
                        </a>
                        <!-- ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route -->
                            <!-- si on utilise la methode "post" -->
                                <form action="<?= '/backend/deleteMedia/'. $media->getId()?>" methode="POST"
                                    onsubmit="return confirm('Souhaitez vous vraiment executer cette action?')">
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                </form>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
<!-- end main content  -->

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>