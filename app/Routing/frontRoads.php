<?php //here are defined the routes of the website front part. the routing is defined by the router of the AltoRouteur library which uses a mapping between url requested by the Internet user and an anonymous function which calls the functions defined in the files of the "Controllers" folder. 
$router->map('GET', '/', function () {  // for the road  http://localhost:8000/
    home();
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