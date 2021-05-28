<?php 

$title = 'Front Delete a comment frontPostView';

ob_start(); 

header('Location: /post/'.$comment->getPost_id().'?deleteComment=true');
$content = ob_get_clean(); 

require'../app/Views/template.php'; 
?>