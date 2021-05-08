<?php //here are defined the routes of the website front part. the routing is defined by the router of the AltoRouteur library which uses a mapping between url requested by the Internet user and an anonymous function which calls the functions defined in the files of the "Controllers" folder. 
$router->map('GET', '/', function () {  // for the road  http://localhost:8000/
    frontHome();
});

$router->map('GET', '/listposts', function (){  // for the road  http://localhost:8000/listposts
    listPosts();
}, 'listpots');

$router->map('POST|GET', '/post/[i:id]', function ($id){  // for the road http://localhost:8000/post/1 ou http://localhost:8000/post/2 ou ....
    post($id);
}, 'blog');

//function to delete a comment of post in frontView
$router->map('POST|GET', '/deleteCommentPostFront/[i:id]', function ($id){  // for the road http://localhost:8000/deleteCommentPostFront/1 ou http://localhost:8000/deleteCommentPostFront/2 ou ....
    deleteCommentPostFront($id);
});


//function to edit a comment
$router->map('POST|GET', '/editCommentPostFront/[i:id]', function ($id){  // for the road http://localhost:8000/editCommentPostFront/1 ou http://localhost:8000/editCommentPostFront/2 ou ....
    editCommentPostFront($id);
});