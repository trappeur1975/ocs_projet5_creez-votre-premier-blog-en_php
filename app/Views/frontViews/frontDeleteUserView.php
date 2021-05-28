<?php 
$title = 'Front Delete a user';

ob_start(); 

header('Location: /?deleteUser=true');
return http_response_code(302);

$content = ob_get_clean(); 

require'../app/Views/template.php'; 
?>