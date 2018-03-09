<?php
/**
 * This class holds the client request. It offers
 * a few helpers around http variables and utilities.
 *
 * @author Eriam <eriam@mediavirtuel.com>
 */

class Request {

   public $http_method  = '';
   public $http_path    = '';

   function __construct() {

		session_start();

      $this->http_method  = $_SERVER['REQUEST_METHOD'];
	
		if (!is_null($_SERVER['PATH_INFO'])) {
			$this->http_path = $_SERVER['PATH_INFO'];
		}
		else {
			$this->http_path = '/';
		}

		if ($this->http_path == '') {
			$this->http_path = '/';
		}

   }


   /**
   * Grab a POST param value by name. 
   *
   * @param  string  $name    The name of the POST param 
   *
   * @return string           The value or null if it's there is no such param.
   */
   public function param($name) {
		if (isset($_POST[$name])) {
			return $_POST[$name];
		}
		else {
			return null;
		}
	}

   /**
   * Grab a GET param value by name. 
   *
   * @param  string  $name    The name of the GET param 
   *
   * @return string           The value or null if it's there is no such param.
   */
   public function getp($name) {
		if (isset($_GET[$name])) {
			return $_GET[$name];
		}
		else {
			return null;
		}
	}

   /**
   * Save a key and associated value in the current user's session. 
   *
   * @param  string  $key     The key under which the value will be saved 
   * @param  string  $value   The **value**
   *
   */
   public function save_to_session($key, $value) {
		$_SESSION[$key] = $value;
	}


   /**
   * Unset session's key and associated value. 
   *
   * @param  string  $key     The key for which we request deletion 
   *
   */
   public function delete_from_session($key) {
		unset($_SESSION[$key]);
	}


   /**
   * Reinitialize a session and deletes all its associated keys. 
   *
   * @param  string  $key     The key for which we request deletion 
   *
   */
   public function reinit_session() {
		session_unset();
	}



   /**
   * Returns session's key value. 
   *
   * @param  string  $key     The key for which we want to retrieve the value 
   *
   * @return string           The value or null if it's not set.
   */
   public function session($key) {
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}
		else {
			return null;
		}
	}

}

?>
