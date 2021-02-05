<?php 
$title = 'Create post';
ob_start(); 
?>

<h1>Create a new Post</h1>


<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>