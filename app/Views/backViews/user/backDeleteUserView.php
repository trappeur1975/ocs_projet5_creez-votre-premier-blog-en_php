<?php 
$title = 'Delete a user';

ob_start(); 

header('Location: /backend/adminUsers?delete=1'); //ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route 

$content = ob_get_clean(); 

require('../app/Views/template.php'); 
?>