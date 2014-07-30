<?php

add_action('admin_menu', 'bulgroz_admin_init');
add_action('admin_footer', 'bulgroz_admin_javascript');
// Ajouter un data-id a l'image pour retrouver plus facilement l'ID de l'img sans regexp
add_filter('image_send_to_editor', 'bulgroz_image_to_editor', 1, 8);


function bulgroz_admin_init() {

  // ajouter une PAGE dans l'admin pour regrouper nos options de config. Le est le meme que la la première optin
  // du sous menu (kapsettings-theme) pour ramener sur la config du theme par défaut au clic sur Kappuccino Settings
  add_menu_page('Kappuccino settings', 'Kappuccino', 'manage_options', 'kapsettings-theme', 'bulgroz_admin_setup');
  // ajouter un item au menu kapsettings
  add_submenu_page('kapsettings', 'Configuration du theme', 'Theme', 'manage_options', 'kapsettings-theme', 'bulgroz_admin_addmenu_theme');

  // Ajouter les options appartenant à kapsettings-theme
  add_settings_section( 'bulgroz_section', 'Configuration du thême', 'bulgroz_settings_section', 'kapsettings-theme' );
  add_settings_field( 'bulgroz_logo', 'Logo du site', 'bulgroz_settings_set_logo', 'kapsettings-theme', 'bulgroz_section' );
  add_settings_field( 'bulgroz_bg', 'Arrière plan', 'bulgroz_settings_set_bg', 'kapsettings-theme', 'bulgroz_section' );
  add_settings_field( 'bulgroz_karou', 'Taille du carousel', 'bulgroz_settings_set_karou', 'kapsettings-theme', 'bulgroz_section' );

  add_settings_field( 'bulgroz_onair', 'Site en prod', 'bulgroz_settings_set_onair', 'kapsettings-theme', 'bulgroz_section' );

  register_setting( 'bulgroz_section', 'bulgroz_section', 'bulgroz_validate_options' );
}

function bulgroz_admin_addmenu_theme() {
  /**
   *  - mettre en place la form des options (post toujours vers options.php)
   *  - demander de cracher ses champs hidden avec settings_fields
   *  - demander les "lignes" de la form avec do_settings_sections qui va renvoyer chaque add_settings_field enregistré sur le slug
   *  - submit_button renvoie le bouton submit (qui ne semble rien avoir de particulier)
   */
  echo '<form method="post" action="options.php">';

  settings_fields( 'bulgroz_section' );
  do_settings_sections( 'kapsettings-theme' );
  submit_button();

  echo '</form>';
}

function bulgroz_admin_setup() {
  // noop
}

function bulgroz_validate_options($options) {

  /**
   * Validator (vide pour le moment)
   */

  return $options;
}

function bulgroz_settings_section($arg) {
  // EMPTY, les add_settings_field mettront en place le html
}

function bulgroz_settings_set_karou() {

  $options = get_option( 'bulgroz_section' ); // si on a déjà une option dessus ?

  if (is_string($options['karou'])) $value = $options['karou'];

  ?>
  <p>Le carousel doit-il avoir une hauteur fixe ou être fluide ?</p>
  <select name="bulgroz_section[karou]" id="bulgroz_section_karou">
    <option value="fixed" <?php if ($value === 'fixed') echo "selected" ?>>Fixe</option>
    <option value="fluid" <?php if ($value === 'fluid') echo "selected" ?>>Fluide</option>
  </select>

  <?php

}

function bulgroz_settings_set_onair() {

  $options = get_option( 'bulgroz_section' ); // si on a déjà une option dessus ?

	$value = false;
  if (!empty($options['onair']) && is_string($options['onair'])){
	  $value = $options['onair'] == true;
  }

  ?>
     <p>Le site est il en prod</p>
	<input type="checkbox" name="bulgroz_section[onair]" value="1" <?php if($value) echo 'checked' ?> >

  <?php

}

function bulgroz_settings_set_logo() {

  $options = get_option( 'bulgroz_section' ); // si on a déjà une option dessus ?

  $logo = '';
  if (is_numeric($options['logo'])) $logo = get_post($options['logo'])->guid;

  ?>
  <p>Sélectionnez ou téléchargez un logo dans votre bibliothèque de médias</p>
  <input id="bulgroz_logo_button" type="button" class="bulgroz-admin-upload button action" value="Selectionner une image" style="vertical-align: middle; margin-right:20px" />
  <input id="bulgroz-logo" type="text" name="bulgroz_section[logo]" class="bulgroz-admin-input" value="<?php echo $options['logo'] ?>" style="display: none"/>
  <img src="<?php echo $logo ?>" height="100" width="100" class="preview" style="display: inline-block; vertical-align: middle;"/>

<?php

}

function bulgroz_settings_set_bg() {

  $options = get_option( 'bulgroz_section' ); // si on a déjà une option dessus ?

  if (is_numeric($options['bg'])) $bg = get_post($options['bg'])->guid;

  ?>

  <p>Sélectionnez ou téléchargez une image de fond par défaut pour les articles</p>
  <input id="bulgroz_bg_button" type="button" class="bulgroz-admin-upload button action" value="Selectionner une image" style="vertical-align: middle; margin-right:20px" />
  <input id="bulgroz-bg" type="text" name="bulgroz_section[bg]" class="bulgroz-admin-input" value="<?php echo $options['bg'] ?>" style="display: none"/>
  <img src="<?php echo $bg ?>" height="100" width="100" class="preview" style="display: inline-block; vertical-align: middle;"/>

<?php

}

function bulgroz_admin_javascript() {

  wp_enqueue_style( 'thickbox' ); // Stylesheet used by Thickbox
  wp_enqueue_script( 'thickbox' );
  wp_enqueue_script( 'media-upload' );

  wp_enqueue_script( 'bulgroz-settings', get_template_directory_uri() . '/js/admin_settings.js', array( 'thickbox', 'media-upload' ) );
}


function bulgroz_image_to_editor($html, $id, $caption, $title, $align, $url, $size, $alt){
  /**
   * Ajouter un attribut html5 [data-id] sur l'image renvoyée par la thickbox (via tb_show)
   * Utilisé pour les settings du theme, qui se fera renvoyer un ID correspondant a l'image
   * du logo/favicon plutot qu'une url où on aurait du regexp l'id dans les classes (ex wp-image-XX)
   */
  $dom = new DOMDocument();
  @$dom->loadHTML($html);

  $x = new DOMXPath($dom);
  foreach($x->query("//img") as $node){
    $node->setAttribute("data-id", $id);
  }

  if($dom->getElementsByTagName("a")->length == 0){
    $newHtml = $dom->saveXML($dom->getElementsByTagName('img')->item(0));
  }else{
    $newHtml = $dom->saveXML($dom->getElementsByTagName('a')->item(0));
  }

  return $newHtml;
}
