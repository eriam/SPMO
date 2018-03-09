<?php
/**
 * This is the resultset for Articles
 *
 * @author Eriam <eriam@mediavirtuel.com>
 */
include_once('lib/Table.php'); 

class Articles extends Table {

   public $tablename = 'articles';

   function __construct($db) {
		parent::__construct($db);

      $this->add_primary('reference'); 

      $this->add_attribute('nom',            'VARCHAR(50)');
      $this->add_attribute('description',    'VARCHAR(200)'); 
      $this->add_attribute('prix',           'FLOAT');
      $this->add_attribute('stock_actuel',   'INT');

	}


   /**
   * This method is a generic method to retieve a record's text_id
   * this is used for user interfaces
   */
   public function text_id($row) {
      return $row['nom'];  
   }
  

}

?>
