
<div class="container">

<?php
// Just view an element ..

$rs      = $m->get_resultset(ucfirst($c->uri_params[0]).'s');

$data    = $rs->query_element($c->uri_params[1]);
 
// We populate the class attributes from the request params
foreach ($rs->table_attributes as $key => $value){
   echo "$key = ".$data[$key].'<br>';
} 
?>

</div>


