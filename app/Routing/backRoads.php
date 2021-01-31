<?php //here are defined the routes of the website backend part. the routing is defined by the router of the AltoRouteur library which uses a mapping between url requested by the Internet user and an anonymous function which calls the functions defined in the files of the "Controllers" folder. 

$router->map('GET', '/backend', function (){  // for the road  http://localhost:8000/backend
    backend();
});

// $router->map('GET', '/post/[i:id]/[i:id]', function ($id, $nom){  // pour generer plusieur parametre dans une fonction avec la route http://localhost:8000/post/1/salut
//     post($id, $nom);
// });