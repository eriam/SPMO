<?php
/**
 * This is the resultset for Clients
 *
 * @author Eriam <eriam@mediavirtuel.com>
 */
include_once('lib/Table.php'); 

class Clients extends Table {

   public $tablename = 'clients';

   function __construct($db) {
		parent::__construct($db);

      $this->add_primary('numero'); 

      $this->add_attribute('nom',         'VARCHAR(100)');
      $this->add_attribute('prenom',      'VARCHAR(100)'); 
      $this->add_attribute('adresse',     'VARCHAR(200)');
      $this->add_attribute('codepostal',  'INT'); 
      $this->add_attribute('ville',       'VARCHAR(100)'); 
      $this->add_attribute('pays',        'VARCHAR(50)'); 
      $this->add_attribute('telephone',   'VARCHAR(50)');

	}


   /**
   * This method is a generic method to retieve a record's text_id
   * this is used for user interfaces
   */
   public function text_id($row) {
      return $row['nom']." ".$row['prenom']; 
   }
  

}

?>
