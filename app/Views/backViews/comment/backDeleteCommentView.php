<?php 
$title = 'Backend Delete a comment';

ob_start(); 

header('Location: /backend/editCommentsPost/'.$comment->getPost_id().'?deleteComment=true');
exit();

$content = ob_get_clean(); 

require('../app/Views/template.php'); 
?>