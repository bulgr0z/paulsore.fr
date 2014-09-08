<?php

// Hooks ajax
//
// Pour un usage sur le front, penser a déclarer `ajaxurl` avec par exemple
//   var ajaxurl = <?php echo admin_url( 'admin-ajax.php' );
// dans le header


add_action('wp_ajax_sendmail', 'bulgroz_sendmail');
add_action('wp_ajax_nopriv_sendmail', 'bulgroz_sendmail');

function bulgroz_sendmail() {

  // valider le mail
  $is_valid = bulgroz_filter_email($_POST);
  $is_sent = false;
  $isspam = bulgroz_email_checkspam($_POST, $_SERVER);

  if ($is_valid && !$isspam) {

    $headers = array(
      "From: ". $_POST['name'] ." <". $_POST['email'] .">"
    );

    $is_sent = wp_mail(get_bloginfo('admin_email'), 'Contact paulsore.fr', $_POST['message'], implode("\r\n", $headers));
  }
  pre('sent ?');
  pre($is_sent);
  echo json_encode(array(
    'result' => $is_sent
  ));

  die();
}

// Basic validation
function bulgroz_filter_email($post) {

  $is_valid = true;
  // Ajouter des validations ici ( $post[key] => FILTER )
  $validation = array(
    'email' => FILTER_VALIDATE_EMAIL
  );

  foreach($post as $key=>$val) {

    // Teste si la var est vide et contre le filter (si config dans $validation)
    if ( isset($validation[$key]) && filter_var($val, $validation[$key]) === false || empty($val))
      $is_valid = false;
   }

   return $is_valid;
}

// Check le contact avec Akismet pour vérifier si c'est du spam
function bulgroz_email_checkspam($post, $server) {

  $akismetkey = Akismet::get_api_key();

  // construire l'url avec les paramètres pour Akismet
  $messagedata = array(
      'blog=' => "www.paulsore.fr", // devrait etre $_SERVER['SERVER_NAME']; en prod
      '&user_ip=' => $server['REMOTE_ADDR'],
      '&user_agent=' => $server['HTTP_USER_AGENT'],
      '&referrer=' => $server['HTTP_REFERER'],
      '&comment_type=' => 'contact-form',
      '&comment_author=' => $post['name'],
      '&comment_author_email=' => $post['email'],
      '&comment_content' => $post['message']
  );

  // urlencode les paramètres de $messagedata
  foreach($messagedata as $paramkey=>$data) {
    $messagedata[$paramkey] = $paramkey . urlencode($data);
  }

  $messagedata = implode("", $messagedata); // concat $messagedata pour l'url
  $host = $http_host = $akismetkey.'.rest.akismet.com';
  $path = '/1.1/comment-check';
  $port = 80;
  $akismet_ua = "WordPress/{$wp_version} | Akismet/".AKISMET_VERSION; // si plugin pas activé, constant == NULL
  $content_length = strlen( $messagedata );
  $http_request  = "POST $path HTTP/1.0\r\n";
  $http_request .= "Host: $host\r\n";
  $http_request .= "Content-Type: application/x-www-form-urlencoded\r\n";
  $http_request .= "Content-Length: {$content_length}\r\n";
  $http_request .= "User-Agent: {$akismet_ua}\r\n";
  $http_request .= "\r\n";
  $http_request .= $messagedata;

  $response = '';
  // envoyer la requete
  if( false != ( $fs = @fsockopen( $host, $port, $errno, $errstr, 10 ) ) ) {
      // ecrire le retour
      fwrite( $fs, $http_request );
      while ( !feof( $fs ) ) {
        $response .= fgets( $fs, 1160 ); // One TCP-IP packet
      }
      fclose( $fs );
      // la réponse est en "raw" dans le retour :
      //  -> $response[0] = headers
      //  -> $response[1] = true/false (retour akismet)
      $response = explode( "\r\n\r\n", $response, 2 );
  }

  if ( 'true' == $response[1] ) {
    pre('spam!');
    return true; // c'est du spam :(
  }

  return false;

}
