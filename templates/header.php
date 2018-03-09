<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title><?php echo $c->title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="<?php echo $c->uri_for('/js/devoir2.js'); ?>" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $c->uri_for('/css/devoir2.css'); ?>">
</head>
<body>

<div class='bgmenu'>
<?php
//
if ($r->session('loggedon') == 1) { 
   include_once($d->tpl_dir.'menu.php');
}
?>
</div>


