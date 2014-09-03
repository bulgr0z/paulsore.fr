<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 */
?>

  </div><!-- #main -->

  <footer id="footer" class="site-footer" role="contentinfo">

    <form id="contact" action="" method="post">
      <div class="bloc bloc-contact current">

        <div id="contact-loading">
          <h1>Livraison en cours...</h1>
          <div class="wrapper">
            <span class="spinner"></span>
          </div>
        </div>

        <div class="col-lg-11 col-lg-offset-1">

          <h1 class="white">Contact</h1>
          <h2>Et si on en parlait ? Envoyez-moi un message <br/>ou passez moi un coup de téléphone !</h2>

          <div class=row>
            <div class="col-lg-5">
              <p class="warning">tous les champs sont obligatoires</p>
              <textarea name="client_message" id="message" rows="8" cols="40" placeholder="Votre message"></textarea>
              <input type="email" id="email" name="client_mail" value="" placeholder="Votre adresse email">
              <input type="text" id="name" name="client_mail" value="" placeholder="Votre nom">
              <input type="submit" id="submit" class="bulbtn black small" name="submit" value="envoyer">
            </div>
            <div class="col-lg-7">
              <p class="legend">TELEPHONE</p>
              <h2 class="contact-item">06 23 88 44 14</h2>
              <p class="legend">EMAIL</p>
              <h2 class="contact-item">paul.sore@gmail.com</h2>
              <p class="legend">SKYPE</p>
              <h2 class="contact-item">paul.sore</h2>
            </div>
          </div>

          <div id="madein">Made with <span class="icon flaticon-fresh6"></span> in Bordeaux</div>

        </div>

      </div>

      <div class="bloc bloc-success">
        <h1>Merci !</h1>
        <h2>Votre message est bien arrivé !<br/> Je prendrai contact avec vous dans les plus brefs délais</h2>
      </div>

      <div class="bloc bloc-error">
        <h1>Oops ! Une erreur est survenue</h1>
        <h2>Merci de vérifier les champs du formulaire de contact<br/><a href="#" id="retour-contact">Retour</a></h2>
      </div>

    </form>

  </footer><!-- #colophon -->
  </div><!-- #page -->

  <?php wp_footer(); ?>
  </body>
  </html>
