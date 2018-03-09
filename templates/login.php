
<div class="container">

   <form class='form' method='post' action='<?php echo $c->uri_for('/'); ?>'>

      <h2>Veuillez vous connecter</h2>

      <div class="form-group">
         <label for="inputdbuser">Entrez votre nom d'utilisateur</label>
         <input type="text" id="inputdbuser" class="form-control" name="dbuser"  placeholder="Nom d'utilisateur"/>
      </div>

      <div class="form-group">
         <label for="inputdbpass">Entrez votre mot de passe</label>
         <input type="password" id="inputdbpass" class="form-control" name="dbpass"  placeholder="Mot de passe"/>
      </div>

      <?php
      //
      if (isset($c->exception)) { 
      ?>
      <div class="alert">
      <?php
         echo $c->exception->getMessage();
      ?>
      </div>
      <?php
      }
      ?>


      <button class="btn btn-lg btn-primary btn-block"  type="submit">Se connecter</button> 

   </form> 

</div>

