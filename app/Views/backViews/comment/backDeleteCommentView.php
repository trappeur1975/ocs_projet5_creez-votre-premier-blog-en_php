<?php 
$title = 'Delete a comment';

ob_start(); 

// dd($comment);

header('Location: /backend/editCommentsPost/'.$comment->getPost_id().'?deleteComment=true');
// header('Location: /backend/editCommentsPost?deleteComment=true');
$content = ob_get_clean(); 

require('../app/Views/template.php'); 
?>