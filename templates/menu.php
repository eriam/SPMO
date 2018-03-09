
<nav>
   <div class="header">
      <a href="<?php echo $c->uri_for('/'); ?>">Bienvenu chez nous SA</a>
   </div>

   <div class="menu-tables">
      <ul>
         <li class="<?php if ($c->uri_params[0] == 'clients') { echo "active"; } ?>"><a href="<?php echo $c->uri_for('/clients'); ?>">Clients</a></li>
         <li class="<?php if ($c->uri_params[0] == 'articles') { echo "active"; } ?>"><a href="<?php echo $c->uri_for('/articles'); ?>">Articles</a></li>
         <li class="<?php if ($c->uri_params[0] == 'achats') { echo "active"; } ?>"><a href="<?php echo $c->uri_for('/achats'); ?>">Achats</a></li>
      </ul>
   </div>

   <div class="menu-user">
      <ul>
         <li><a href="<?php echo $c->uri_for('/'); ?>">En ligne: <?php echo $r->session('dbuser'); ?></a></li>
         <li><a title='Logout' href='<?php echo $c->uri_for('/logout'); ?>'>Se deconnecter</a></li>
      </ul>
   </div>

</nav>

