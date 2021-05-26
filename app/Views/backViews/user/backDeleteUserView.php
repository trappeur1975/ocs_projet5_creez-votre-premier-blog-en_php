<?php 
$title = 'Backend Delete a user';

ob_start(); 

header('Location: /backend/adminUsers?delete=1');
return http_response_code(302);

$content = ob_get_clean(); 

require('../app/Views/template.php'); 
?>