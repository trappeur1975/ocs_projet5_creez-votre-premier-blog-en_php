<?php //this file is our router based on the altoRouter library which uses anonymous functions to execute the controller functions according to the routes (url)

require_once('../vendor/autoload.php');
require('../vendor/altorouter/altorouter/AltoRouter.php');
require('../app/Controllers/frontend.php');

$router = new AltoRouter();

// routing
try { 
    

    $router->map('GET', '/', function () {  // for the road  http://localhost:8000/
        echo 'salut nicolas';
    });

    $router->map('GET', '/listposts', function (){  // for the road  http://localhost:8000/listposts
        listPosts();
    }, 'listpots');

    $router->map('GET', '/post/[i:id]', function ($id){  // for the road http://localhost:8000/post/1 ou http://localhost:8000/post/2 ou ....
        post($id);
    }, 'blog');

    // $router->map('GET', '/post/[i:id]/[i:id]', function ($id, $nom){  // pour generer plusieur parametre dans une fonction avec la route http://localhost:8000/post/1/salut
    //     post($id, $nom);
    // });

    $match = $router->match(); // we check if a route exists in relation to url to call 
    if($match !== false){
        call_user_func_array($match['target'], $match['params']); // to manage the arguments called in the closure 
        // $match['target']($match['params']['id']);
        // $match['target']();
        // $match['target']($match['params']);
    } else { //if no route matches we call a 404 page (here "erros.php") 
        require_once('../app/Views/errors.php'); // pourquoi le chemin absolu (/app/View/errors.php)ne fonctionne pas
    }
} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
}