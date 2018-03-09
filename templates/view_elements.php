
<?php
// This view displays all elements from a table.
if (
   $c->uri_params[0] != 'clients' &&
   $c->uri_params[0] != 'achats' &&
   $c->uri_params[0] != 'articles'
) {
   throw new Exception('Erreur de droits.');
   exit;
}
?>


<div class="container">

<h2>
Affichage de la liste des <?php echo $c->uri_params[0]; ?>
</h2>

<table class='table table-bordered table-hover table-sm'>
<thead>
<tr>
<th>#</th>

<?php
// Display table headers

   $rs   = $m->get_resultset(ucfirst($c->uri_params[0]));
   $res  = $rs->query_all(array(), $r->getp('sort'));

   foreach ($rs->columns() as $name => $type) {
      if (!is_array($type)) {
         // Sort the column ..
         echo "<th><a href='".$c->uri_for('/'.$c->uri_params[0].'?sort='.$name)."' title='Trier'>".$name."</a></th>\n";
      }
   }

   foreach ($rs->virtual_columns() as $name => $type) {
      echo "<th>".$type."</th>\n";
   }
?>
<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php
// Display table values


while ($row = $res->fetch_assoc()) {

   echo "<tr>\n";
   
   echo "<td>".$row[$rs->primary]."</td>\n";

   foreach ($rs->columns() as $name => $type) {
      if (!is_array($type)) {
         echo "<td>".$row[$name]."</td>\n";
      }
   }
  
   foreach ($rs->virtual_columns() as $name => $type) {
      echo "<td>".$rs->$type($row)."</td>\n";
   }
    

?>
<td>

<?php if ($r->session('update-'.$c->uri_params[0]) == 1) { ?>
<form method='post' action="<?php echo $c->uri_for('/'.substr($c->uri_params[0],0,-1).'/'.$row[$rs->primary]); ?>">
<button type="submit" class="btn btn-default btn-sm">
Editer
</button>
</form>
<?php } ?>

</td>
<?php
//

   echo "</tr>\n";
  
}  

   

?>
</tbody>
</table>

<?php if ($r->session('insert-'.$c->uri_params[0]) == 1) { ?>
<form method='get' action="<?php echo $c->uri_for('/'.substr($c->uri_params[0],0,-1)); ?>">
<button type="submit" class="btn btn-default btn-sm">
Ajouter un <?php echo substr($c->uri_params[0],0,-1); ?>
</button>
</form>
<?php } ?>


</div>


