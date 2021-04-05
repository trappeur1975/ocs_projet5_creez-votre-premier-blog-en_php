<?php //this file is our router based on the altoRouter library.

$router = new AltoRouter();

// routing
try { 
    
    require('frontRoads.php');
    require('backRoads.php');
    
    $match = $router->match(); // we check if a route exists in relation to url to call 
    if($match !== false){
        call_user_func_array($match['target'], $match['params']); // to manage the arguments called in the closure 
        // $match['target']($match['params']['id']);
        // $match['target']();
        // $match['target']($match['params']);
    } else { //if no route matches we call a 404 page (here "erros.php") 
        require_once('../app/Views/errors.php');
    }
} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
}