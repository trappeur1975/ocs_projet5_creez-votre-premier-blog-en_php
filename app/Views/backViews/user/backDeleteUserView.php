<?php 
$title = 'Backend Delete a user';

ob_start(); 

header('Location: /backend/adminUsers?delete=1'); //ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route 
return http_response_code(302);

$content = ob_get_clean(); 

require('../app/Views/template.php'); 
?>