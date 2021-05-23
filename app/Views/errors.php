<?php 
    $title = 'Error page';
    ob_start(); 
?>


<?php
http_response_code(400);
?>

<h1>Page error 404</h1>

<h2>la ressource que vous demandez n'existe pas</h2>


<?php 
    $content = ob_get_clean(); 
    require('../app/Views/template.php'); 
?>