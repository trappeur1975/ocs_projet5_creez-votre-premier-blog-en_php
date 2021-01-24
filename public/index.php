<?php //NOTRE ROUTEUR (indique au controller quel function lancer en fonction des paramétres (notamment ici "action") dans url de la page du site

require('../vendor/altorouter/altorouter/AltoRouter.php');
require('../App/controller/frontend.php');
require_once('../vendor/autoload.php');

use App\Controller\Backend;

$router = new AltoRouter();

// AltoRouter via des fonction anonyme
    $router->map('GET', '/', function () {  // pour la route http://localhost:8000/
        echo 'salut nicolas';
    });

    $router->map('GET', '/listposts', function (){  // pour la route http://localhost:8000/listposts
        listPosts();
    });

    $router->map('GET', '/post/[i:id]', function ($id){  // pour la route http://localhost:8000/post/1 ou http://localhost:8000/post/2 ou ....
        post($id);
    });
    // $router->map('GET', '/post/[i:id]/[i:id]', function ($id, $nom){  // pour generer plusieur parametre dans une fonction avec la route http://localhost:8000/post/1/salut
    //     post($id, $nom);
    // });

    $match = $router->match();
    if($match !== null){
        call_user_func_array($match['target'], $match['params']); //pour gerer les arguments appeller dans la closure
        // $match['target']($match['params']['id']);
        // $match['target']();
        // $match['target']($match['params']);
    }

// AltoRouter via using controller#action string
    $router->map('GET', '/listposts', 'Backend#listPosts');
    $router->map('GET', '/post/[i:id]', 'Backend#post');

    $match = $router->match();
    if ($match === false) {
        echo "// here you can handle 404 \n";
    } else {
        list($controller, $action) = explode('#', $match['target']);
        $nomController = 'App\\Controller\\'.$controller; //OBLIGER DE FAIRE COMME CELA POUR INTEGRER LE NAMESPACE CAR AVEC LE USE CELA NE FONCTIONNE PAS ??
        $myController = new $nomController(); 
        if (is_callable(array($myController, $action))) {
            call_user_func_array(array($myController, $action), array($match['params']));
        } else {
            echo 'Error: can not call ' . get_class($myController) . '#' . $action;
            // here your routes are wrong.
            // Throw an exception in debug, send a 500 error in production
        }
    }

// -----------------------------------

// try { 
//     if (isset($_GET['action'])) { // URL : http://localhost/ocs_projet5_creez-votre-premier-blog-en_php/?action=listPosts
//         if ($_GET['action'] == 'listPosts') {
//             listPosts();
//         } elseif ($_GET['action'] == 'post') { // URL : http://localhost/ocs_projet5_creez-votre-premier-blog-en_php/?action=post&id=1
//             if (isset($_GET['id']) && $_GET['id'] > 0) {
//                 post();
//             } else {
//                 // Erreur ! On arrête tout, on envoie une exception, donc au saute directement au catch
//                 throw new Exception('Aucun identifiant de billet envoyé');
//             }
//         }

//          // VOIR CI CE ELSE IF EST CORRECT POUR AJOUTER DES COMMENTAIRE A UN POST
//          elseif ($_GET['action'] == 'addComment') {
//              if (isset($_GET['id']) && $_GET['id'] > 0) {
//                  if (!empty($_POST['author']) && !empty($_POST['comment'])) {
//                      addComment($_GET['id'], $_POST['author'], $_POST['comment']);
//                  }
//                  else {
//                      // Autre exception
//                      throw new Exception('Tous les champs ne sont pas remplis !');
//                  }
//              }
//              else {
//                  // Autre exception
//                  throw new Exception('Aucun identifiant de billet envoyé');
//              }
//          }

//     } else {
//         //  listPosts();
//         echo "salut internaute";
//     }
// } catch (Exception $e) { // S'il y a eu une erreur, alors...
//     echo 'Erreur : ' . $e->getMessage();
// }