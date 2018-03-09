<?php 
/**
* This application is structured around a dispatcher, controllers 
* static files and templates.
*
* All this script does is getting these pieces together. The 
* pieces are in the following places:
*
*    - dispatcher: in lib/
*    - models: in lib/Model.php and lib/Model/
*    - controllers: in lib/App/
*    - templates: in templates/
*    - static files: in public/
*
* @author Eriam <eriam@mediavirtuel.com>
*/
ini_set('display_errors',           0);
ini_set('display_startup_errors',   0);
error_reporting(E_ALL);

include('lib/Dispatcher.php'); 

// Our 
$d = new Dispatcher('App');

// We register our routes to the dispatcher, our application
// will respond on theses URI only.

// Index, login and logout pages
$d->register_static_route('GET',    '/',			         'Devoir2', 'index');
$d->register_static_route('POST',   '/',			         'Devoir2', 'index');
$d->register_static_route('GET',    '/logout',			   'Devoir2', 'logout');

// Semantic URI scheme
$d->register_regexp_route('GET',    '/(articles|achats|clients|tests)',	'Devoir2', 'view_elements');
$d->register_regexp_route('GET',    '/(article|achat|client)/(\d+)',	   'Devoir2', 'view_element');
$d->register_regexp_route('POST',   '/(article|achat|client)/(\d+)',	   'Devoir2', 'update_element');
$d->register_regexp_route('GET',    '/(article|achat|client)',	         'Devoir2', 'create_element');
$d->register_regexp_route('POST',   '/(article|achat|client)',          'Devoir2', 'create_element');

// And we deal with a request, first checking if we have a static 
// file request and if not passing the request to the controller
// and then to the view (MVC without model).
$d->to_view(
	$d->to_controller(
		$d->to_static(
			new Request($_REQUEST)
		)
	)
);

?>
