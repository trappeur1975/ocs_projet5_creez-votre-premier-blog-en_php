<?php //this file is the entry to our website. it just calls our router 

require_once('../vendor/autoload.php');
require_once('../vendor/altorouter/altorouter/AltoRouter.php');
require_once('../app/Controllers/frontController.php');
require_once('../app/Controllers/backController.php');

// calls from our route
require_once('../app/Routing/router.php');