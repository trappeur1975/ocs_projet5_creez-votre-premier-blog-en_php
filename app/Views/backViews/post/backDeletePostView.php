<?php 
$title = 'Backend Delete a post';

ob_start(); 

header('Location: /backend/adminPosts?delete=true');
return http_response_code(302);

$content = ob_get_clean();

require'../app/Views/template.php'; 
?>