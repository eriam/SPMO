<?php
/**
 * 
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

      $this->add_virtual_column('nom_client'); 
      $this->add_virtual_column('nom_article'); 
      $this->add_virtual_column('pu_article'); 
      $this->add_virtual_column('total'); 

      $this->add_relation('id_client',    'clients',  'numero');
      $this->add_relation('id_article',   'articles', 'reference');

	}


   /**
   * 
   */
   public function total($row) {
      return $this->pu_article($row) * $row['quantite']; 
   }
 
   /**
   * 
   */
   public function nom_client($row) {
      return $this->query_related($row['id_achat'], 'id_client')['nom']; 
   }
  
   /**
   * 
   */
   public function nom_article($row) {
      return $this->query_related($row['id_achat'], 'id_article')['nom'];  
   }
  
   /**
   * 
   */
   public function pu_article($row) {
      return $this->query_related($row['id_achat'], 'id_article')['prix']; 
   }
   

}

?>

