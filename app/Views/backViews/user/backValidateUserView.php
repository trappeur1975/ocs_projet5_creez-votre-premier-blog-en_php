<?php 
$title = 'Backend Validate a user';

ob_start(); 

header('Location: /backend/adminUsers?validateUser=true');
return http_response_code(302);

$content = ob_get_clean(); 

require('../app/Views/template.php'); 
?>