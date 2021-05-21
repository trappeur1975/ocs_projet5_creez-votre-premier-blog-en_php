<?php 
$title = 'Backend Validate a comment';

ob_start(); 

header('Location: /backend/editCommentsPost/'.$comment->getPost_id().'?validateComment=true');
exit();

$content = ob_get_clean(); 

require('../app/Views/template.php'); 
?>