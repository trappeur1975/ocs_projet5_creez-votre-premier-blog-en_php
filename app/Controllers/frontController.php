<?php
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
    $listCommentsForPost = $commentManager->getListCommentsForPost($id);
   
    $comment = new Comment();
    $formComment = new Form($comment);

    // traitement server et affichage des retours d'infos 
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a creation of a post) has been made
        
        $errors = [];
        //modification pour gerer l enregistrement dans la base de donnee via le Postmanager
        
        // $dateCreate = DateTime::createFromFormat('Y-m-d H:i:s',new Datetime()); // pour que la date String soit en Datetime
        // $dateCreate = new Datetime();
        
        if(empty($errors)){
            
            $dateTime = new Datetime();
            $date = $dateTime->format('Y-m-d H:i:s');  
            // $dateCreate = DateTime::createFromFormat('Y-m-d H:i:s',new Datetime()); // pour que la date String soit en Datetime
            
            // enregistrement en bdd du comment    
            $comment
                ->setComment($_POST['comment'])
                ->setDateCompletion($date)
                ->setValidate(NULL)
                ->setUser_id(3) // --------------POUR LE TESTE J AI MIS USER 3 MAIS IL FAUDRA RECUPERER LE ID DE L USER CONNECTER-----------
                ->setPost_id($post->getId())
                ;
            
            $commentManager->addComment($comment);// add the comment to the database and get the last id of the comments in the database via the return of the function
            
            header('Location: /post/'.$id.'?createdComment=true');
        }else{
            // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
            header('Location: /post/'.$id.'?createdComment=false');
        }
    }
    require('../app/Views/frontViews/frontPostView.php');
}