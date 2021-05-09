<?php //here are defined the routes of the website backend part. the routing is defined by the router of the AltoRouteur library which uses a mapping between url requested by the Internet user and an anonymous function which calls the functions defined in the files of the "Controllers" folder.

$router->map('GET', '/backend', function (){  // for the road  http://localhost:8000/backend
    backHome();
});

// CONNECTION / DECONNECTION AU SITE
    //function to connect to the site 
    $router->map('POST|GET', '/backend/connection', function (){  // for the road  http://localhost:8000/backend/connection
        connection();
    });

    //function to disconnect to the site 
    $router->map('POST|GET', '/backend/disconnection', function (){  // for the road  http://localhost:8000/backend/disconnection
        disconnection();
    });

// POST
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

// USER
    //function to administer users 
    $router->map('GET', '/backend/adminUsers', function (){  // for the road  http://localhost:8000/backend/adminUsers
        adminUsers();
    });

    //function to administer users waite validate
    $router->map('GET', '/backend/adminUsersWaiteValidate', function (){  // for the road  http://localhost:8000/backend/adminUsersWaiteValidate
        adminUsersWaiteValidate();
    });

    //function to edit a user
    $router->map('POST|GET', '/backend/editUser/[i:id]', function ($id){  // for the road http://localhost:8000/backend/editUser/1 ou http://localhost:8000/backend/editUser/2 ou ....
        editUser($id);
    });

    //function to create a user
    $router->map('POST|GET', '/backend/createUser', function (){  // for the road http://localhost:8000/backend/createUser
            createUser();
    });

    //function to delete a user
    $router->map('POST|GET', '/backend/deleteUser/[i:id]', function ($id){  // for the road http://localhost:8000/backend/deleteUser/1 ou http://localhost:8000/backend/deleteUser/2 ou ....
        deleteUser($id);
    });

    //function to validate a user
    $router->map('POST|GET', '/backend/validateUser/[i:id]', function ($id){  // for the road http://localhost:8000/backend/validateUser/1 ou http://localhost:8000/backend/validateUser/2 ou ....
        validateUser($id);
    });

// COMMENT
    //function to administer comments waite validate
    $router->map('GET', '/backend/adminCommentsWaiteValidate', function (){  // for the road  http://localhost:8000/backend/adminCommentsWaiteValidate
        adminCommentsWaiteValidate();
    });

    // function to display all the comments of a post
    $router->map('GET', '/backend/editCommentsPost/[i:id]', function ($id){  // for the road http://localhost:8000/backend/editCommentsPost/1 ou http://localhost:8000/backend/editCommentsPost/2 ou ....
        editCommentsPost($id);
    });

    //function to delete a comment
    $router->map('POST|GET', '/backend/deleteComment/[i:id]', function ($id){  // for the road http://localhost:8000/backend/deleteComment/1 ou http://localhost:8000/backend/deleteComment/2 ou ....
        deleteComment($id);
    });

    //function to validate a post
    $router->map('POST|GET', '/backend/validateComment/[i:id]', function ($id){  // for the road http://localhost:8000/backend/validateComment/1 ou http://localhost:8000/backend/validateComment/2 ou ....
        validateComment($id);
    });