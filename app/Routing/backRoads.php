<?php //here are defined the routes of the website backend part. the routing is defined by the router of the AltoRouteur library which uses a mapping between url requested by the Internet user and an anonymous function which calls the functions defined in the files of the "Controllers" folder.
$router->map('GET', '/backend', function (){  // for the road  http://localhost:8000/backend
    backHome();
});

//function to administer posts 
$router->map('GET', '/backend/adminPosts', function (){  // for the road  http://localhost:8000/backend/adminPosts
    adminPosts();
});

//function to edit a post
$router->map('POST|GET', '/backend/editPost/[i:id]', function ($id){  // for the road http://localhost:8000/backend/editPost/1 ou http://localhost:8000/backend/editPost/2 ou ....
    editPost($id);
});


//function to create a post
$router->map('POST|GET', '/backend/createPost', function (){  // for the road http://localhost:8000/backend/createPost
        createPost();
});

//function to delete a post
$router->map('POST|GET', '/backend/deletePost/[i:id]', function ($id){  // for the road http://localhost:8000/backend/deletePost/1 ou http://localhost:8000/backend/deletePost/2 ou ....
    deletePost($id);
});