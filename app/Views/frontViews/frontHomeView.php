<?php ob_start(); ?>
        
    <h1>page Home du front</h1>

<?php 
$content = ob_get_clean(); 
require('../app/Views/template.php'); 
?>