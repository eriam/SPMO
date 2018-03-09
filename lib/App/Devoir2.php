<?php
/**
 * 
 *
 * @author Eriam <eriam@mediavirtuel.com>
 */

class Devoir2 extends Controller {

   public $title				= 'Accueil';

   function __construct($r, $matches = array()) {
		parent::__construct($r, $matches);
	}

   /**
   * Simple placeholder to display the index page
   *
   * @param  Request    $r    The request object.
   */
   public function index($r) {
		// 
   }


   /**
   * Simple placeholder to display the helloworld page
   *
   * @param  Request    $r    The request object.
   */
   public function helloworld($r) {
		//
		$this->template	= 'helloworld';

   }

   /**
   * Log the user out ..
   *
   * @param  Request    $r    The request object.
   */
   public function logout($r) {
		//
      $r->reinit_session();
      $this->redirect_to('/');
   }



   /**
   * This controller is use to display a list of elements
   * this is just a placeholder as much of the stuff is
   * happening in the model and in the view
   *
   * @param  Request    $r    The request object.
   */
   public function view_elements($r) {
		//

      

   }

   /**
   * This controller is use to display an element
   * this is just a placeholder as much of the stuff is
   * happening in the model and in the view
   *
   * @param  Request    $r    The request object.
   */
   public function view_element($r) {
		//

   }


   /**
   * This controller is use to create an element it either reacts
   * to a POST request in case the user is submitting data for
   * and element to be created or it simply displays the form (this is 
   * happening in the view)
   *
   * @param  Request    $r    The request object.
   */
   public function create_element($r) {
		//

      if ($r->http_method == 'POST') {
         
         $m    = $this->model;
         $rs   = $m->get_resultset(ucfirst($this->uri_params[0].'s'));
         $id   = $rs->create($r);

         $this->redirect_to('/'.$this->uri_params[0].'/'.$id);

      }

   }


   /**
   * This controller is a placeholder and the logic is the same 
   * as in the create_element the difference is that the view will
   * take the element's values to be updated to fill the form
   *
   * @param  Request    $r    The request object.
   */
   public function update_element($r) {
		//
		$this->template	= 'create_element';

      if ($r->http_method == 'POST') {
  
         $m    = $this->model;
         $rs   = $m->get_resultset(ucfirst($this->uri_params[0].'s'));
         $id   = $rs->update_element($this->uri_params[1], $r);

      }

   }

}

?>
