<?php //here are defined the routes of the website front part. the routing is defined by the router of the AltoRouteur library which uses a mapping between url requested by the Internet user and an anonymous function which calls the functions defined in the files of the "Controllers" folder. 

// SITE
    $router->map('POST|GET', '/', function () {  // for the road  http://localhost:8000/
        frontHome();
    });

    $router->map('GET', '/listposts', function (){  // for the road  http://localhost:8000/listposts
        listPosts();
    }, 'listpots');

    $router->map('POST|GET', '/post/[i:id]', function ($id){  // for the road http://localhost:8000/post/1 ou http://localhost:8000/post/2 ou ....
        post($id);
    }, 'blog');

//COMMENT
    //function to edit a comment in front
    $router->map('POST|GET', '/editCommentPostFront/[i:id]', function ($id){  // for the road http://localhost:8000/editCommentPostFront/1 ou http://localhost:8000/editCommentPostFront/2 ou ....
        editCommentPostFront($id);
    });

    //function to delete a comment of post in frontView
    $router->map('POST|GET', '/deleteCommentPostFront/[i:id]', function ($id){  // for the road http://localhost:8000/deleteCommentPostFront/1 ou http://localhost:8000/deleteCommentPostFront/2 ou ....
        deleteCommentPostFront($id);
    });

// USER
    //function userFrontDashboard (id is the id of the user who wishes to access his dashboard)
    $router->map('POST|GET', '/userFrontDashboard/[i:id]', function ($id){  // for the road http://localhost:8000/userFrontDashboard/1 ou http://localhost:8000/userFrontDashboard/2 ou ....
        userFrontDashboard($id);
    });

    //function to create a user in front
    $router->map('POST|GET', '/createUserFront', function (){  // for the road http://localhost:8000/createUserFront
        createUserFront();
    });

    //function to delete a user in front
    $router->map('POST|GET', '/deleteUserFront/[i:id]', function ($id){  // for the road http://localhost:8000/deleteUserFront/1 ou http://localhost:8000/deleteUserFront/2 ou ....
        deleteUserFront($id);
    });