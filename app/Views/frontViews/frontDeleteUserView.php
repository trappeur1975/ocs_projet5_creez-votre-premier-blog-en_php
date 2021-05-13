<?php 
$title = 'Front Delete a user';

ob_start(); 

header('Location: /?deleteUser=true');

// disconnection();

$content = ob_get_clean(); 

require('../app/Views/template.php'); 
?>