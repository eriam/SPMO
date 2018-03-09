<?php
/**
 * This is the resultset for Achats
 *
 * @author Eriam <eriam@mediavirtuel.com>
 */
include_once('lib/Table.php'); 

class Achats extends Table {

   public $tablename   = 'achats';

   function __construct($db) {
		parent::__construct($db);

      $this->add_primary('id_achat'); 

      $this->add_attribute('quantite',    'INT');
      $this->add_attribute('date',        'DATE'); 

      // Virtual columns can emulate columns and translate virtual columns 
      // to object method
      $this->add_virtual_column('nom_client'); 
      $this->add_virtual_column('nom_article'); 
      $this->add_virtual_column('pu_article'); 
      $this->add_virtual_column('total'); 

      // Relations is usefull to create calculated joins on the tables
      $this->add_relation('id_client',    'clients',  'numero',      1);
      $this->add_relation('id_article',   'articles', 'reference',   1);

	}


   /**
   * Virtual columns that calculates the total for an achat
   */
   public function total($row) {
      return $this->pu_article($row) * $row['quantite']; 
   }
 
   /**
   * This is an helper that returns the related client's name
   */
   public function nom_client($row) {
      return $this->query_related($row['id_achat'], 'id_client')['nom']; 
   }
  
   /**
   * This is an helper that returns the related article's name
   */
   public function nom_article($row) {
      return $this->query_related($row['id_achat'], 'id_article')['nom'];  
   }
  
   /**
   * This is an helper that returns the related article's unit price
   */
   public function pu_article($row) {
      return $this->query_related($row['id_achat'], 'id_article')['prix']; 
   }
   

}

?>
