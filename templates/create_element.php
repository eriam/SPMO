
<?php
/*
   This view is a bit complex, it is used in both create and update
*/
if (
   $c->uri_params[0] != 'client' &&
   $c->uri_params[0] != 'achat' &&
   $c->uri_params[0] != 'article'
) {
   throw new Exception('Erreur de droits.');
   exit;
}
?>

<div class="container" id='container'>


<?php 

   if ($c->uri_params[1] != '') {
       echo "<h2>Modification de ".$c->uri_params[0]."</h2>\n";
   }
   else {
       echo "<h2>Ajout de ".$c->uri_params[0]."</h2>\n";
   }

?>



<script type="text/javascript">
   window.onload = function(e){ 

<?php 

   $target = $c->uri_for('/'.$c->uri_params[0]); 

   $default_values = array();

   if ($c->uri_params[1] != '') {
      $target .= '/'.$c->uri_params[1];

      $default_values = $m->get_resultset(ucfirst($c->uri_params[0]).'s')->query_element($c->uri_params[1]);

   }

?>


      f = new Form('createForm', 'post', '<?php echo $target; ?>');

<?php
//

$missing_relations = array();

$rs      = $m->get_resultset(ucfirst($c->uri_params[0]).'s');

foreach ($rs->table_attributes as $name => $type){

   $default_value = $default_values[$name];

   $default_value = ereg_replace( "\n",'&#10;', $default_value);
   $default_value = ereg_replace( "\r",'&#13;', $default_value);

   echo "f.addField('$name', '$type', '".$default_value."');\n";
}

foreach ($rs->table_relations as $name => $relation){
   
   echo "var rel_".$name." = f.addRelation('".$name."', '".$relation[1]."');\n";

   $rel_rs  = $m->get_resultset(ucfirst($relation[0]));
   $res     = $rel_rs->query_all();
   $row_cnt = $res->num_rows;
  
   if ($row_cnt == 0 && $relation[2] == 1) {
      array_push($missing_relations, $relation[0]);
   }

   while ($row = $res->fetch_assoc()) {
      echo "rel_".$name.".addElement('".$rel_rs->text_id($row)."', '".$row[$relation[1]]."'";
      
      if ($row[$relation[1]] == $default_values[$name]) {
         echo ", 'true'";
      }
      
      echo ");\n";
   }  


}
  

if (sizeof($missing_relations) > 0) {
   echo "}
   </script>
   Avant d'enregistrer un ".$c->uri_params[0]." vous devez créer:<ul>";
   echo join('<li>', $missing_relations);
   echo "</ul>";

}
else {
   echo "f.appendToDOM('Enregistrer', 'Effacer');
   }
</script>";
}

?>



</div>



