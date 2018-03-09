<?php
/**
 * This is the parent class for all table classes. It provides definition methods
 * and data manipulation methods.
 *
 * @author Eriam <eriam@mediavirtuel.com>
 */

class Table {

   public $db                 = null;  
   
   public $table_primary      = null;  
   public $table_attributes   = array(); 
   public $table_relations    = array();
   public $table_vcolumns     = array();

   function __construct($db) {
	
      $this->db = $db;

   }


   /**
   * Sets the table primary key. 
   *
   * @param  string  $attribute_name   The primary key name
   *
   */
   public function add_primary($attribute_name) {
  
      $this->primary = $attribute_name; 

   }

   /**
   * Returns a text identifier for a tulpe. 
   *
   * @param  array  $row   A row record
   *
   */
   public function text_id($row) {
      return $this->primary; 
   }
  
   /**
   * Registers an attribute (a table column ..)
   *
   * @param  string  $attribute_name   Attribute name
   * @param  string  $attribute_type   Attribute type
   *
   */
   public function add_attribute($attribute_name, $attribute_type) {
  
      $this->table_attributes[$attribute_name] = $attribute_type; 

   }
 
   /**
   * Registers a virtual column which is a class method that behaves just like
   * a column table and make calculations easy.
   *
   * @param  string  $name   The name under which we register the virtual column, 
   *                         the class must provide a method with the same name
   *
   */
   public function add_virtual_column($name) {
  
      $this->table_vcolumns[] = $name; 

   }
  
  
   /**
   * Registers a relation between the current class and a remote class. This will be translated
   * into a join between corresponding tables.
   *
   * @param  string  $src_attribute_name  The name of the local join column
   * @param  string  $dst_table_name      The name of the table to relate to
   * @param  string  $dst_attribute_name  The name of the foreign join column
   *
   */
   public function add_relation($src_attribute_name, $dst_table_name, $dst_attribute_name, $required) {
 
      $this->table_relations[$src_attribute_name] = array(
         $dst_table_name,
         $dst_attribute_name,
         $required
      ); 

   }
 
 
  
   /**
   * Returns the columns .
   *
   */
   public function columns() {
      return array_merge($this->table_attributes, $this->table_relations);
   }
  
  
   /**
   * Registers a relation between the current class and a remote class. This will be translated
   * into a join between corresponding tables.
   *
   */
   public function virtual_columns() {
      return $this->table_vcolumns;
   }
  
   
  
   /**
   * This method is a helper to retrieve the real and virtual column names alltogether 
   */
   public function column_names() {
      return array_merge(array_keys($this->table_attributes), array_keys($this->table_relations));
   }
   
   /**
   * This method create a join between a table and it's related table referenced by
   * its $src_attribute_name and returns the related record from the db.
   */
   public function query_related($id, $src_attribute_name) {

      $relation = $this->table_relations[$src_attribute_name];

      $query = 
         'SELECT '.$relation[0].".* ".
         ' FROM '.$this->tablename.
         ' INNER JOIN '.$relation[0].
         ' ON '.$this->tablename.'.'.$src_attribute_name.' = '.$relation[0].'.'.$relation[1].
         ' WHERE '.$this->primary.' = '.$id;

      $res = $this->db->query($query);

      if ($res) {
         
         return $res->fetch_assoc();

      }
      else {
         throw new Exception($this->db->error);
      }
 
   }
 
    
   /**
   * This method queries the db and returns the record for the specified $id.
   */
   public function query_element($id) {

      $query = 
         'SELECT '.$this->primary.", ".
         implode(", ", $this->column_names()).
         ' FROM '.$this->tablename.
         ' WHERE '.$this->primary.' = '.$id;

      $res = $this->db->query($query);

      if ($res) {
         
         return $res->fetch_assoc();

      }
      else {
         throw new Exception($this->db->error);
      }
 
   }
 
 
   /**
   * This method updates a record in the db for the specified $id. It is secured by mysqli_real_escape_string.
   */
   public function update_element($id, $r) {

      $query   = 'UPDATE '.$this->tablename.' SET ';
      $updates = array();

      foreach ($this->table_attributes as $name => $type) {
         if ($r->param($name) != '') {
            array_push($updates, mysqli_real_escape_string($this->db, $name).' = \''.mysqli_real_escape_string($this->db, $r->param($name)).'\'');
         }
      }

      foreach ($this->table_relations as $name => $relation) {
         if ($r->param($name) != '') {
            array_push($updates, mysqli_real_escape_string($this->db, $name).' = '.mysqli_real_escape_string($this->db, $r->param($name)));
         }
      }

      if ($updates) {
         $query = 
            $query.implode(", ", $updates).
            ' WHERE '.$this->primary.' = '.$id;

         $res = $this->db->query($query);

         if ($res) {
            
            return $this->db->affected_rows;

         }
         else {
            throw new Exception($this->db->error);
         }
      }
      else {
         return;
      }

   }
 
 
  
 
   /**
   * This method returns all rows from a table.
   */
   public function query_all($search_params = array(), $order_params = '') {
 
      $query = 
         'SELECT '.$this->primary.", ".
         implode(", ", $this->column_names()).
         ' FROM '.$this->tablename.
         ' WHERE 1 ';


      foreach ($search_params as $name => $value) {

         $query .= "\nAND ".mysqli_real_escape_string($this->db, $name)." = '".mysqli_real_escape_string($this->db, $value)."'";

      }

      if ($order_params != '') {

         $query .= "\nORDER BY ".mysqli_real_escape_string($this->db, $order_params)."";

      }

      $res = $this->db->query($query);

      if ($res) {
      
         return $res;

      }
      else {
         throw new Exception($this->db->error);
      }

 
   }
 
  
 
   /**
   * This method creates a record in the database from the fields in the request. 
   * Secured by mysqli_real_escape_string.
   */
   public function create($r) {
  
      $query = "INSERT INTO ".$this->tablename;

      $names   = array();
      $values  = array();

      // We populate the class attributes from the request params
		foreach ($this->table_attributes as $name => $type){
			if ($r->param($name)) {
				array_push($names,   $name);
				array_push($values,  mysqli_real_escape_string($this->db, $r->param($name)));
			}
		}

      // We populate the class relations from the request params
		foreach ($this->table_relations as $name => $relation){
			if ($r->param($name)) {
				array_push($names,   $name);
				array_push($values,  mysqli_real_escape_string($this->db, $r->param($name)));
			}
		}

      $query = $query.' ('.implode(", ", $names).') VALUES ';

      $query = $query." ('".implode("', '", $values)."');";

      $res = $this->db->query($query);

      if ($res) {
      
         $res = $this->db->query("SELECT LAST_INSERT_ID() AS id;");

         $res->data_seek(0);

         return $res->fetch_assoc()['id'];

      }
      else {
         throw new Exception($this->db->error);
      }


   }
 



}

?>
