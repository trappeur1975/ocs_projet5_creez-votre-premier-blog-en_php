<?php 
$title = 'Backend Delete a comment';

ob_start(); 

header('Location: /backend/editCommentsPost/'.$comment->getPost_id().'?deleteComment=true');
return http_response_code(302);

$content = ob_get_clean(); 

require'../app/Views/template.php'; 
?>