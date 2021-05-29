<?php //here are defined the routes of the website front part. the routing is defined by the router of the AltoRouteur library which uses a mapping between url requested by the Internet user and an anonymous function which calls the functions defined in the files of the "Controllers" folder. 

// SITE
    //function to go the website => for the road  http://localhost:8000/
    $router->map('POST|GET', '/', function () {
        frontHome();
    });

    // function to display the list of posts => for the road  http://localhost:8000/listposts
    $router->map('GET', '/listposts', function (){
        listPosts();
    }, 'listpots');

    // function to display a post  => for the road http://localhost:8000/post/1 ou http://localhost:8000/post/2 ou ....
    $router->map('POST|GET', '/post/[i:id]', function ($id){
        post($id);
    }, 'blog');

//COMMENT
    //function to edit a comment in front => for the road http://localhost:8000/editCommentPostFront/1 ou http://localhost:8000/editCommentPostFront/2 ou ....
    $router->map('POST|GET', '/editCommentPostFront/[i:id]', function ($id){
        editCommentPostFront($id);
    });

    //function to delete a comment of post in frontView => for the road http://localhost:8000/deleteCommentPostFront/1 ou http://localhost:8000/deleteCommentPostFront/2 ou ....
    $router->map('POST|GET', '/deleteCommentPostFront/[i:id]', function ($id){
        deleteCommentPostFront($id);
    });

// USER
    //function userFrontDashboard (id is the id of the user who wishes to access his dashboard) => for the road http://localhost:8000/userFrontDashboard/1 ou http://localhost:8000/userFrontDashboard/2 ou ...
    $router->map('POST|GET', '/userFrontDashboard/[i:id]', function ($id){
        userFrontDashboard($id);
    });

    //function to create a user in front => for the road http://localhost:8000/createUserFront
    $router->map('POST|GET', '/createUserFront', function (){
        createUserFront();
    });

    //function to delete a user in front => for the road http://localhost:8000/deleteUserFront/1 ou http://localhost:8000/deleteUserFront/2 ou ...
    $router->map('POST|GET', '/deleteUserFront/[i:id]', function ($id){
        deleteUserFront($id);
    });