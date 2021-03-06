<?php
/**
 * Resultset mapping for information_schema.TABLE_PRIVILEGES
 *
 * @author Eriam <eriam@mediavirtuel.com>
 */
include_once('lib/Table.php'); 

class TablePrivileges extends Table {

   public $tablename   = 'information_schema.TABLE_PRIVILEGES';

   function __construct($db) {
		parent::__construct($db);

      $this->add_primary('GRANTEE'); 

      $this->add_attribute('GRANTEE',        'VARCHAR(100)');
      $this->add_attribute('TABLE_CATALOG',  'VARCHAR(100)');
      $this->add_attribute('TABLE_SCHEMA',   'VARCHAR(100)');
      $this->add_attribute('TABLE_NAME',     'VARCHAR(100)');
      $this->add_attribute('PRIVILEGE_TYPE', 'VARCHAR(100)');

	}



}

?>

