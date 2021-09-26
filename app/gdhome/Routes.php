<?php
namespace gdhome;
use gdhome\controllers\PagesController as PagesController;
use gdhome\controllers\PostsController as PostsController;
class Routes {
    
    public function call($controller, $action){
        
      switch($controller) {
      case 'pages':
        $controller = new PagesController();
        break;
      case 'posts':
        // we need the model to query the database later in the controller
        require_once('models/post.php');
        $controller = new PostsController();
      break;
    }

    $controller->{ $action }();
    }
    
}
