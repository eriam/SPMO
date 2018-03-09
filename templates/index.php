
<div class="container">

<h2>
Bienvenu(e) dans notre application de suivi des ventes.
</h2>

<p>
Vous avez les droits de consultation
<?php if (sizeof($r->session('insert_rights')) > 0 or sizeof($r->session('update_rights')) > 0) { ?>
 ainsi que des droits
<?php if (sizeof($r->session('insert_rights')) > 0) { ?>
 en écriture sur <?php echo join(', ',$r->session('insert_rights')); ?>
<?php } ?>
<?php if (sizeof($r->session('update_rights')) > 0) { ?>
 et en mise a jour sur <?php echo join(', ',$r->session('update_rights')); ?>
<?php } ?>
<?php } ?>.
</p>

</div>

