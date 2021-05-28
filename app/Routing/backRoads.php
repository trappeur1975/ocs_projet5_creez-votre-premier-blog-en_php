<?php
/**
 * here are defined the routes of the website backend part. the routing is defined by the router of the AltoRouteur library which uses a mapping between url requested by the Internet user and an anonymous function which calls the functions defined in the files of the "Controllers" folder.
 */


// CONNECTION / DECONNECTION AU SITE
    //function to connect to the site => for the road  http://localhost:8000/backend/connection
    $router->map('POST|GET', '/backend/connection', function (){
        connection();
    });

    //function to disconnect to the site => for the road  http://localhost:8000/backend/disconnection
    $router->map('POST|GET', '/backend/disconnection', function (){
        disconnection();
    });

// POST
    //function to administer posts => for the road  http://localhost:8000/backend/adminPosts
    $router->map('GET', '/backend/adminPosts', function (){
        adminPosts();
    });

    //function to edit a post => for the road http://localhost:8000/backend/editPost/1 ou http://localhost:8000/backend/editPost/2 ou ....
    $router->map('POST|GET', '/backend/editPost/[i:id]', function ($id){
        editPost($id);
    });

    //function to create a post => for the road http://localhost:8000/backend/createPost
    $router->map('POST|GET', '/backend/createPost', function (){
            createPost();
    });

    //function to delete a post => for the road http://localhost:8000/backend/deletePost/1 ou http://localhost:8000/backend/deletePost/2 ou ....
    $router->map('POST|GET', '/backend/deletePost/[i:id]', function ($id){
        deletePost($id);
    });

// USER
    //function to administer users  => for the road  http://localhost:8000/backend/adminUsers
    $router->map('GET', '/backend/adminUsers', function (){
        adminUsers();
    });

    //function to administer users waite validate => for the road  http://localhost:8000/backend/adminUsersWaiteValidate
    $router->map('GET', '/backend/adminUsersWaiteValidate', function (){
        adminUsersWaiteValidate();
    });

    //function to edit a user => for the road http://localhost:8000/backend/editUser/1 ou http://localhost:8000/backend/editUser/2 ou ....
    $router->map('POST|GET', '/backend/editUser/[i:id]', function ($id){
        editUser($id);
    });

    //function to create a user => for the road http://localhost:8000/backend/createUser
    $router->map('POST|GET', '/backend/createUser', function (){
            createUser();
    });

    //function to delete a user => for the road http://localhost:8000/backend/deleteUser/1 ou http://localhost:8000/backend/deleteUser/2 ou ....
    $router->map('POST|GET', '/backend/deleteUser/[i:id]', function ($id){
        deleteUser($id);
    });

    //function to validate a user => for the road http://localhost:8000/backend/validateUser/1 ou http://localhost:8000/backend/validateUser/2 ou ....
    $router->map('POST|GET', '/backend/validateUser/[i:id]', function ($id){
        validateUser($id);
    });

// COMMENT
    //function to administer comments waite validate => for the road  http://localhost:8000/backend/adminCommentsWaiteValidate
    $router->map('GET', '/backend/adminCommentsWaiteValidate', function (){
        adminCommentsWaiteValidate();
    });

    // function to display all the comments of a post => for the road http://localhost:8000/backend/editCommentsPost/1 ou http://localhost:8000/backend/editCommentsPost/2 ou ....
    $router->map('GET', '/backend/editCommentsPost/[i:id]', function ($id){
        editCommentsPost($id);
    });

    //function to delete a comment => for the road http://localhost:8000/backend/deleteComment/1 ou http://localhost:8000/backend/deleteComment/2 ou ....
    $router->map('POST|GET', '/backend/deleteComment/[i:id]', function ($id){
        deleteComment($id);
    });

    //function to validate a post => for the road http://localhost:8000/backend/validateComment/1 ou http://localhost:8000/backend/validateComment/2 ou ....
    $router->map('POST|GET', '/backend/validateComment/[i:id]', function ($id){
        validateComment($id);
    });