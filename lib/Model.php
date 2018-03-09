<?php
/**
 * This is a small class that connects to the database and 
 * uses subclasses to represent tables in the database as classes.
 *
 * @author Eriam <eriam@mediavirtuel.com>
 */
include('lib/Model/Achats.php'); 
include('lib/Model/Articles.php'); 
include('lib/Model/Clients.php'); 
include('lib/Model/TablePrivileges.php'); 
include('lib/Model/ColumnPrivileges.php'); 

class Model {

   private $dbuser   = '';
   private $dbpass   = '';
   private $dbhost   = 'localhost';
   private $dbname   = 'c216_devoir2';

   private $db       = null;

   public $acls      = array();

   function __construct($r) {

      if (($r->session('dbuser') != '' && $r->session('dbpass') != '') || 
            ($r->param('dbuser') != '' && $r->param('dbpass') != '')) {
      
         if ($r->param('dbuser') != '' && $r->param('dbpass') != '') {
            $r->save_to_session('dbuser', $r->param('dbuser'));
            $r->save_to_session('dbpass', $r->param('dbpass'));
         }

         $db = mysqli_connect($this->dbhost, $r->session('dbuser'), $r->session('dbpass'), $this->dbname);

         if (!$db) {
            throw new Exception("Mauvaise combinaison nom d'utilisateur et mot de passe");
         }
         else {
            // All ok we pick user's ACL then ..
            // Voici le code de la requête envoyée à la base pour déterminer le type d'utilisateur. 
            $this->db = $db;

            if ($r->session('loggedon') != 1) {
               $r->save_to_session('loggedon', 1);

               $insert_rights = array();
               $update_rights = array();

               $rs   = $this->get_resultset('TablePrivileges');

               $search_params = array();
               $search_params['privilege_type'] = 'INSERT';
               
               $res  = $rs->query_all($search_params);   

               while ($row = $res->fetch_assoc()) {
                  array_push($insert_rights, strtolower($row['TABLE_NAME']));
                  $r->save_to_session(strtolower('insert-'.$row['TABLE_NAME']), 1); 
               }

               $search_params = array();
               $search_params['privilege_type'] = 'UPDATE';

               $res  = $rs->query_all($search_params);   

               while ($row = $res->fetch_assoc()) {
                  array_push($update_rights, strtolower($row['TABLE_NAME']));
                  $r->save_to_session(strtolower('update-'.$row['TABLE_NAME']), 1); 
               }

               
               $r->save_to_session('insert_rights', $insert_rights); 
               $r->save_to_session('update_rights', $update_rights); 

            }


         }

      }
      else {
         throw new Exception('');
      }

   }


   /**
   *  Returns an instance of a resultset class
   */
   public function get_resultset($classname) {
      return new $classname($this->db);
   }

}

class LoginException extends Exception { }

?>
