<?php
use App\Entities\Auth;
use App\Entities\Form;
use App\Entities\Comment;
use App\Models\PostManager;
use App\Models\UserManager;
use App\Models\MediaManager;
use App\Models\CommentManager;

/**
 * function use for road http://localhost:8000
 * will display the view frontHomeView.php  
 */
function frontHome()
{
    require('../app/Views/frontViews/frontHomeView.php');
}

/**
 * function use for road http://localhost:8000/listposts
 * will display the view frontListPostsView.php  
 */
function listPosts()
{
    $postManager = new PostManager();
    /**
     * will be used in "listPostsView.php" in the foreach loop 
     * 
     * @ Post[] 
     * */
    $listPosts = $postManager->getListPosts();
    require('../app/Views/frontViews/frontListPostsView.php');
}

/**
 * function use for road http://localhost:8000/post/1 ou http://localhost:8000/post/2 ou ....
 * will display the view frontPostView.php  
 */
function post($id)
{
    session_start();
    
    // post
    $postManager = new PostManager(); // Création de l'objet manger de post
    $post = $postManager->getPost($id);

    // user
    $userManager = new UserManager();
    $userPost = $userManager->getUser($post->getUser_id());
    
    // media
    $mediaManager= new MediaManager();
    $listMediasForPost = $mediaManager->getListMediasForPost($id);

    // comment
    $commentManager = new CommentManager();
    $listCommentsForPost = $commentManager->listCommentsNotNullForPost($id);
    // $listCommentsForPost = $commentManager->getListCommentsForPost($id);
   
    // pour creer un nouveau commentaire pour un post
    $comment = new Comment();
    $formComment = new Form($comment);

    // traitement server et affichage des retours d'infos 
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a creation of a post) has been made
        
        $errors = [];
        //modification pour gerer l enregistrement dans la base de donnee via le Postmanager
        
        // $dateCreate = DateTime::createFromFormat('Y-m-d H:i:s',new Datetime()); // pour que la date String soit en Datetime
        // $dateCreate = new Datetime();
        
        if(empty($errors)){
            Auth::check(['administrateur','abonner']);    
        // if(empty($errors) AND isset($_SESSION['connection'])){
        //     if($userManager->getUserSatus($_SESSION['connection'])['status'] === 'administrateur' OR $userManager->getUserSatus($_SESSION['connection'])['status'] === 'abonner'){
                $dateTime = new Datetime();
                $date = $dateTime->format('Y-m-d H:i:s');
                $validate = null;
                if($userManager->getUserSatus($_SESSION['connection'])['status'] === 'administrateur'){ //pour valider automatiquement le commentaire si le commentateur a un status "administrateur"
                    $validate = $date;
                }

                // enregistrement en bdd du comment    
                $comment
                    ->setComment($_POST['comment'])
                    ->setDateCompletion($date)
                    ->setValidate($validate)
                    ->setUser_id($_SESSION['connection']) // --------------POUR LE TESTE J AI MIS USER 3 MAIS IL FAUDRA RECUPERER LE ID DE L USER CONNECTER-----------
                    ->setPost_id($post->getId())
                    ;
                
                $commentManager->addComment($comment);// add the comment to the database and get the last id of the comments in the database via the return of the function
                
                header('Location: /post/'.$id.'?createdComment=true');
            // }else{

            //     header('Location: /post/'.$id.'?createdComment=false');
            // }

        }else{
            // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
            header('Location: /post/'.$id.'?createdComment=false');
        }
    }
    require('../app/Views/frontViews/frontPostView.php');
}

/**
 * function use for road http://localhost:8000/deleteCommentPostFront/1 ou http://localhost:8000/deleteCommentPostFront/2 ou ....
 * 
 */
function deleteCommentPostFront($id)
{
    Auth::check(['administrateur','abonner']);
    
    // on supprime le commentaire
    $commentManager = new CommentManager();
    $comment = $commentManager->getComment($id);
    
    if($comment->getUser_id() === $_SESSION['connection']){ //on verifier que le commentaire que le user souhaite modifier lui appartient bien
        $commentManager->deleteComment($id);
        // $comment = $commentManager->deleteComment($id);

        require('../app/Views/frontViews/frontDeleteCommentPostView.php');
    }else {
        throw new Exception('impossible de supprimmer le commentaire :'.$comment->getId().'par le user :'.$_SESSION['connection']);
    }
}

/**
 * function use for road http://localhost:8000/editCommentPostFront/1 ou http://localhost:8000/editCommentPostFront/2 ou ....
 * 
 */
function editCommentPostFront($id)
{
    Auth::check(['administrateur','abonner']);
    
    // on edit le commentaire (a travers son formulaire)
    $commentManager = new CommentManager();
    $comment = $commentManager->getComment($id);

    if($comment->getUser_id() === $_SESSION['connection']){ //on verifier que le commentaire que le user souhaite modifier lui appartient bien

        $formComment = new Form($comment, true);

        if($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification of a comment) has been made
            //for data validation
                $errors = [];

                // if(empty($_POST['title'])){
                //     $errors['title'][] = 'Le champs titre ne peut être vide';
                // }
                // if(mb_strlen($_POST['title'])<=3){
                //     $errors['title'][] = 'Le champs titre doit contenir plus de 3 caractere';
                // }

                if(empty($errors)){
                    // enregistrement des modifications du commentaire
                    if (!empty($_POST['comment'])){
                        $comment->setComment($_POST['comment']);
                                            
                        $commentManager->updateComment($comment);
                    }
                        
                    header('Location: /post/'.$comment->getPost_id().'?successUploadComment=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /post/'.$comment->getPost_id().'?successUploadComment=false');
                }
        }
        require('../app/Views/frontViews/frontEditCommentPostView.php');
    }else {
        throw new Exception('impossible de modifier le commentaire :'.$comment->getId().'par le user :'.$_SESSION['connection']);
    }

}


