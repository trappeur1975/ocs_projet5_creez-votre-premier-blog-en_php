<?php 
$title = 'Backend Validate a user';

ob_start(); 

header('Location: /backend/adminUsers?validateUser=true');
$content = ob_get_clean(); 

require('../app/Views/template.php'); 
?>