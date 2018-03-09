<?php
/**
* This class our main dispatcher. It provides
* a method to register a path and associate
* a class and method to each registered path.
* It parses each request and dispatch it either 
* to a static file, or to a controller and a view. 
*
* @author Eriam <eriam@mediavirtuel.com>
*/
include('lib/Controller.php'); 
include('lib/Request.php'); 

class Dispatcher {

   private $dispatched_to  = '';

   private $static_routes  = array ();  
   private $regexp_routes  = array ();

   private $tpl_dir  = './templates/';
   private $stc_dir  = './public/';
   private $app_name = '';

   function __construct($app_name) {

      $this->app_name  = $app_name;

      // html cleanup
      ob_start(array($this,'minify'));
     
      // syslog
		openlog($this->app_name, LOG_PERROR | LOG_PID, LOG_USER);

   }

   /**
   * Register a static path route to a class and method
   *
   * @param  string $http_method     HTTP method to listen to (ex: GET, POST)
   * @param  string $http_path       HTTP path to listen to (ex: "/")
   * @param  string $classname       Name of the controller class to forward the request to (ex: 
   * @param  string $classmethod     Name of the class method to forward the request to.
   *
   */
   public function register_static_route($http_method, $http_path, $classname, $classmethod) {
      $this->register_route($http_method, $http_path, $classname, $classmethod, 'static_routes');
   }

   /**
   * Register a regexp path route to a class and method
   *
   * @param  string $http_method     HTTP method to listen to (ex: GET, POST)
   * @param  string $http_path       Contains the regexp against which the request path will be tested.
   * @param  string $classname       Name of the controller class to forward the request to (ex: 
   * @param  string $classmethod     Name of the class method to forward the request to.
   *
   */
   public function register_regexp_route($http_method, $http_path, $classname, $classmethod) {
      $this->register_route($http_method, $http_path, $classname, $classmethod, 'regexp_routes');
   }

   /**
   * Does the effective registering of the route
   *
   * @param  string $http_method     HTTP method to listen to (ex: GET, POST)
   * @param  string $http_path       HTTP path to listen to (ex: "/")
   * @param  string $classname       Name of the controller class to forward the request to (ex: 
   * @param  string $classmethod     Name of the class method to forward the request to.
   */
   protected function register_route($http_method, $http_path, $classname, $classmethod, $destination) {

      if (!isset($this->{$destination}[$http_method][$http_path])) {
         
         include_once('lib/'.$this->app_name.'/'.$classname.'.php'); 
         
         syslog(LOG_INFO, "Register $destination $http_method for $http_path");

         $this->{$destination}[$http_method][$http_path] = array(
            $classname, 
            $classmethod
         );

      } 

   }

   /**
   * Dispatch a request to a registered controller class. 
   *
   * @param  Request       $r             The client request class object
   *
   * @return Controller    $controller    The controller object to be used during the dispatch flow.
   */
   public function to_controller($r) {

      // Check for static routes and after for regexp routes.

      if (isset($this->static_routes[$r->http_method][$r->http_path])) {
         $route      = $this->static_routes[$r->http_method][$r->http_path];

         $classname	= $route[0];
         $method     = $route[1];
         $controller = null;
         
         $this->dispatched_to = $method;

         try {
            
            $controller = new $classname($r);

            try {
               $controller->$method($r);
            }
            catch (Exception $e) {
               $this->send_error($r, $e);
            }

         } 
         catch (Exception $e) {
            $controller = new Controller($r);
            $controller->template   = 'login';
            if ($e->getMessage() != '') { $controller->exception  = $e; }
         }

			return $controller;

      }
      else {

         foreach ($this->regexp_routes[$r->http_method] as $regexp => $route) {

            // The regexp route allows for capture in the path

            $regexp = preg_replace('|/|', '\/', $regexp);
            $regexp = '/'.$regexp.'/';
            
            $controller = null;

            if (preg_match($regexp, $r->http_path, $matches)) {
               
               array_shift($matches);

               $classname	= $route[0];
               $method     = $route[1];

               $this->dispatched_to = $method;

               try {

                  $controller = new $classname($r, $matches);

                  try {
                     $controller->$method($r);
                  }
                  catch (Exception $e) {
                     $this->send_error($r, $e);
                  }

               } 
               catch (Exception $e) {
                  $controller = new Controller($r, $matches);
                  $controller->template   = 'login';
                  if ($e->getMessage() != '') { $controller->exception  = $e; }
               }

               return $controller;

            }

         }

      }

      echo("No route defined for ".$r->http_method." ".$r->http_path."");
   }


   /**
   * Dispatch a controller object to a view. 
   *
   * @param  Controller    $controller    The controller object to be used during the dispatch flow.
   *
   */
   public function to_view($c) {

      try {

         $r = $c->request;
         $m = $c->model;
         $d = $this;

         // First if the controller has a specific template we use
         // it otherwise we use the view that has the name of the 
         // dispatched method.
         if (file_exists($this->tpl_dir.$c->template.'.php')) {
            include_once($this->tpl_dir.'header.php');
            include_once($this->tpl_dir.$c->template.'.php');
            include_once($this->tpl_dir.'footer.php');
         }
         else if ($this->dispatched_to != '' && file_exists($this->tpl_dir.$this->dispatched_to.'.php')) {
            include_once($this->tpl_dir.'header.php');
            include_once($this->tpl_dir.$this->dispatched_to.'.php'); 
            include_once($this->tpl_dir.'footer.php');
         }
         else {
            if (isset($c)) {
               $c->error('No addressable view ('.$c->template.' or '.$this->dispatched_to.')');
            }
         }

      }
      catch (Exception $e) {
         $this->send_error($r, $e);
      }
      
   } 


   /**
   * Check if a static file is requested and output it in that case. 
   *
   * @param  Request       $r             The client request class object
   *
   */
   public function to_static($r) {
	
		if ($r->http_method == 'GET') {
	
			$filename = $this->stc_dir.$r->http_path;

			if (is_file($filename)) {
            
            ob_end_flush();

            $fi         = new finfo(FILEINFO_MIME);
            $mimetype   = $fi->buffer(file_get_contents($filename));
            $extension  = pathinfo($filename,PATHINFO_EXTENSION);

            switch($extension){
               case 'css': 
                  $mimetype = 'text/css';
                  break;
               case 'js':
                  $mimetype = 'application/javascript';
                  break;
               default:
               break;
            }

            header('Content-Type: '										.$mimetype);
				header('Content-Disposition: attachment; filename='.basename($filename));
				header('Content-Length: '									.filesize($filename));
			
				readfile($filename);
				
				exit;
			}
		}

		return $r;
	}

   /**
   * Utility. 
   */
   protected function warning($warning) {
      echo $warning;
   }


   /**
   * Uses a default error template. 
   */
   protected function send_error($r, $e) {
      ob_get_clean();
      $controller = new Controller($r);
      $controller->template   = 'error';
      if ($e->getMessage() != '') { $controller->exception  = $e; }
      $this->to_view($controller);
      exit;
   }
   /**
   * Clean HTML output. 
   */
   protected function minify($buffer) {
      
      $buffer = preg_replace("/\n/",   "",   $buffer);
      $buffer = preg_replace("/\t/",   "",   $buffer);
      $buffer = preg_replace("/   /",  " ",  $buffer);
      $buffer = preg_replace("/  /",   " ",  $buffer);

      return $buffer;
   }

}

?>
