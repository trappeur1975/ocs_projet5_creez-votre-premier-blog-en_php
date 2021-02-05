<?php 
$title = 'Delete a post';

ob_start(); 

// header('Location: /backend/adminPosts'); //ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route 
header('Location: /backend/adminPosts?delete=1'); //ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route 

$content = ob_get_clean(); 

require('../app/Views/template.php'); 
?>