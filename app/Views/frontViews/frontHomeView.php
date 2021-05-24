<?php 
    $title = 'Blog Nico';
    ob_start(); 
?>
<!-- label alert-->
    <?php  if(isset($_GET['deleteUser'])): ?>
        <div class="alert alert-success">
            Votre compte user a bien été supprimé.
        </div>
    <?php elseif(isset($_GET['unauthorizedAccess'])): ?>
        <div class="alert alert-danger">
            votre statut ne vous autorise pas a acceder au contenu du site reserver a un certain statut.
        </div>
    <?php elseif(isset($_GET['SendEmail'])and($_GET['SendEmail'])==='true'): ?>
        <div class="alert alert-success">
            Votre message a bien été envoyé.
        </div>
    <?php elseif(isset($_GET['SendEmail'])and($_GET['SendEmail'])==='false'): ?>
        <div class="alert alert-danger">
            Votre message n'a pas pu être envoyer.
        </div>
    <?php endif ?>

<!-- start main content  -->       
    <div class="row">
        <h1><?= $title ?></h1>
        <p>Bienvenu sur mon Blog qui vous permet d'acceder a différents articles sur le monde du developpement</p>
    </div>
    <div class="row">
        <div class="col-5">
            <img src="<?= $logoUser->getPath() ?> " alt="<?= $logoUser->getAlt() ?>" class="rounded-circle rounded img-fluid" id="logoAdmin">
        </div>    
        <div class="col-7">
            <h2>
                <?= formatHtml($user->getFirstName()); ?>
                <?= formatHtml($user->getLastName()); ?>
            </h2>
            <h3>Mon slogan</h3>
            <p>  
                "<?= formatHtml($user->getSlogan()); ?>"
            </p>
            <h3>Pour en savoir plus sur moi</h3>
            <p>
                <a href="./document/cv_tchenio_nicolas.pdf">télecharger mon Cv</a>
            </p>
        </div>
    </div>
    <div class="row">  
        <div> 
            <h1>Pour me contacter</h1>  
            <form action="" method="post">
                <fieldset>
                    <div class="form-group">
                        <label for="name">Entrez votre prénom/nom</label>
                        <input type="text" class="form-control" name ="name" id="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Entrez votre mail</label>
                        <input type="email" class="form-control" name ="email" id="email" placeholder="nicolasTchenio@hotmail.com" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea class="form-control" name ="message" id="message" rows="3" required></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Envoyer</button>
                </fieldset>
            </form>
        </div>
    </div>

<!-- end main content  -->

<?php 
    $content = ob_get_clean(); 
    require('../app/Views/template.php'); 
?>