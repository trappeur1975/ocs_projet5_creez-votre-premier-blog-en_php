<?php 
$title = 'Backend Delete a post';

ob_start(); 

header('Location: /backend/adminPosts?delete=true'); //ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route 
exit();

$content = ob_get_clean();

require('../app/Views/template.php'); 
?>