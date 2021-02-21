<?php 
$title = 'Delete a mediaType';

ob_start(); 

header('Location: /backend/adminMediaTypes?delete=1'); //ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route 

$content = ob_get_clean(); 

require('../app/Views/template.php'); 
?>