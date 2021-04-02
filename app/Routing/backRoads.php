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
// MEDIA
    //function to administer media 
    $router->map('GET', '/backend/adminMedias', function (){  // for the road  http://localhost:8000/backend/adminMedias
        adminMedias();
    });

    // ------------------en cours----------------------------------------
    //function to edit a media
    $router->map('POST|GET', '/backend/editMedia/[i:id]', function ($id){  // for the road http://localhost:8000/backend/editMedia/1 ou http://localhost:8000/backend/editMedia/2 ou ....
        editMedia($id);
    });

    //function to create a media
    $router->map('POST|GET', '/backend/createMedia', function (){  // for the road http://localhost:8000/backend/createMedia
            createMedia();
    });

// ------------------PAS FAIT----------------------------------------
    //function to delete a media
    $router->map('POST|GET', '/backend/deleteMedia/[i:id]', function ($id){  // for the road http://localhost:8000/backend/deleteMedia/1 ou http://localhost:8000/backend/deleteMedia/2 ou ....
        deleteMedia($id);
    });

// MEDIATYPE
    //function to administer mediaType 
    $router->map('GET', '/backend/adminMediaTypes', function (){  // for the road  http://localhost:8000/backend/adminMediaTypes
        adminMediaTypes();
    });

    //function to edit a mediaType
    $router->map('POST|GET', '/backend/editMediaType/[i:id]', function ($id){  // for the road http://localhost:8000/backend/editMediaType/1 ou http://localhost:8000/backend/editMediaType/2 ou ....
        editMediaType($id);
    });

    //function to create a mediaType
    $router->map('POST|GET', '/backend/createMediaType', function (){  // for the road http://localhost:8000/backend/createMediaType
            createMediaType();
    });

    //function to delete a mediaType
    $router->map('POST|GET', '/backend/deleteMediaType/[i:id]', function ($id){  // for the road http://localhost:8000/backend/deleteMediaType/1 ou http://localhost:8000/backend/deleteMediaType/2 ou ....
        deleteMediaType($id);
    });