<?php //NOTRE ROUTEUR (indique au controller quel function lancer en fonction des paramétres (notamment ici "action") dans url de la page du site
require('./App/controller/frontend.php');
// require_once('vendor/autoload.php');

try { // On essaie de faire des choses
    if (isset($_GET['action'])) { // URL : http://localhost/ocs_projet5_creez-votre-premier-blog-en_php/?action=listPosts
        if ($_GET['action'] == 'listPosts') {
            listPosts();
        } elseif ($_GET['action'] == 'post') { // URL : http://localhost/ocs_projet5_creez-votre-premier-blog-en_php/?action=post&id=1
            if (isset($_GET['id']) && $_GET['id'] > 0) {
                post();
            } else {
                // Erreur ! On arrête tout, on envoie une exception, donc au saute directement au catch
                throw new Exception('Aucun identifiant de billet envoyé');
            }
        }
        //  // VOIR CI CE ELSE IF EST CORRECT POUR AJOUTER DES COMMENTAIRE A UN POST
        //  elseif ($_GET['action'] == 'addComment') {
        //      if (isset($_GET['id']) && $_GET['id'] > 0) {
        //          if (!empty($_POST['author']) && !empty($_POST['comment'])) {
        //              addComment($_GET['id'], $_POST['author'], $_POST['comment']);
        //          }
        //          else {
        //              // Autre exception
        //              throw new Exception('Tous les champs ne sont pas remplis !');
        //          }
        //      }
        //      else {
        //          // Autre exception
        //          throw new Exception('Aucun identifiant de billet envoyé');
        //      }
        //  }

    } else {
        //  listPosts();
        echo "salut internaute";
    }
} catch (Exception $e) { // S'il y a eu une erreur, alors...
    echo 'Erreur : ' . $e->getMessage();
}
