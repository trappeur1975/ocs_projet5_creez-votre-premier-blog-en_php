<?php ob_start(); ?>
        
    <h1>page Home</h1>

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>