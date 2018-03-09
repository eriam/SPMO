<?php
/**
 * This class is a superclass of our controllers.
 * It provides utilies to be used in controllers
 * and it populates object's attributes.
 *
 * @author Eriam <eriam@mediavirtuel.com>
 */
include('lib/Model.php'); 

class Controller {
  
   public $title			= 'Accueil';
 
   public $uri_params   = array ();  

   public $template	   = null; 

   public $request	   = null;
   public $model	      = null;
   public $exception    = null; 

   function __construct($r, $matches = array()) {
      
      // We populate the class attributes from the request params
		foreach (get_class_vars(get_called_class()) as $key => $value){
			if ($r->param($key)) {
				$this->$key = $r->param($key);
			}
		}
      
      // Our uri_params are the results from the match of the 
      // uri on the regexp route
		$this->uri_params = $matches;
		$this->request    = $r;

      // We try to connect to the model only if we are in a 
      // subclass
      if (get_called_class() != "Controller") { 
         try {
            $model         = new Model($r);
            $this->model   = $model;
         }
         catch (Exception $e) {
            throw new Exception($e->getMessage()); 
         }
      }
   }

   /**
   * Display error and syslog it. 
   */
   public function error($error) {
      echo $error;
		syslog(LOG_ERR, $error);
      exit;
   }

   /**
   * Redirect the user via a 302. 
   *
   * @param  string  $key     The key under which the value will be saved 
   * @param  string  $value   The **value**
   *
   */
   public function redirect_to($uri) {
      header('Location: '.$this->uri_for($uri), true, 302);
      exit;
	}


   /**
   * Create the URI to be used as a link for a specific target. 
   */
   public function uri_for($target) {

		return $_SERVER['SCRIPT_NAME'].$target;

	}

}

?>
