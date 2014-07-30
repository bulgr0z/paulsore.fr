<?php

//
// Ajoute des hooks/options manquantes a wp_nav_menu
//  - Le menu est wrappé dans un <div/> `headmenu`
//  - Ajoute la possibilité d'avoir un logo intégré (le logo doit etre un content)
//
// Usage :
//  - Configurer dans Apparence > Menus
//  - Nommer "Menu Site" et cocher "Emplacements du theme"
//  - Le logo se configure depuis l'admin grace a bulgroz_settings.php
//  - Activer/desactiver les filters qui doivent l'etre (ex, ajout du logo)
//

add_filter( 'wp_nav_menu_args', 'bulgroz_menu_set_args' ); // Fixe les arguments de wp_nav

// Ajouter un logo DANS le menu
//
// add_filter( 'wp_nav_menu_objects', 'bulgroz_menu_inject_logo', 10, 2 ); // injecte le logo dans la liste des pages du menu
// add_filter( 'the_title', 'bulgroz_menu_add_logo_img', 10, 2 ); // remplacer le titre du lien par une image du logo
// add_filter( 'nav_menu_link_attributes', 'bulgroz_menu_add_logo_link', 10, 3 );
// add_filter( 'nav_menu_css_class', 'bulgroz_menu_add_logo_class', 10, 3 );

add_action( 'init', 'bulgroz_register_menu' );


#pre($site_logo);
#$site_logo_id = 35; // ID du post image du logo
$options = get_option( 'bulgroz_section' ); // récupérer les options du theme
if (is_numeric($options['logo'])) $site_logo = get_post($options['logo']);


function bulgroz_register_menu() {
  register_nav_menu('menu-site',__( 'Menu Site' ));
}

function bulgroz_menu_set_args($args) {

  /**
   * Modifie les args de wp_nav_menu avant de commencer a créer le menu.
   * Définit un id pour le <div> et un walker custom "bulgroz_walker_nav_menu" pour générer un
   * menu plus "finement" avec le logo au milieu et les éventuelles classes "active" si necessaire.
   */
  $args = array_merge($args, array(
    'container_id' => 'headmenu',
    'echo' => true
  ));

  return $args;
}

function bulgroz_menu_inject_logo ( $items, $args ) {

  /**
   * Va chercher l'image logo depuis son id et l'injecte dans les objets du menu
   * à la bonne position
   */
  global $site_logo;

  $splitIndex = floor(count($items) / 2);
  $splitted = array_slice($items, 0, $splitIndex);

  $splitted[] = $site_logo; // ajouter le logo à notre array de menu

  $items = array_merge(
    $splitted,
    array_slice($items, $splitIndex, count($items))
  );

  return $items;

}

function bulgroz_menu_add_logo_img ( $title, $id ) {
  /**
   * Ajouter une image du logo à la place du $title qui aurait
   * du partir dans le <a></a>
   */
  global $site_logo;

  if ($id === $site_logo->ID)
    return '<img src="'. $site_logo->guid .'" />';

  return $title;
}

function bulgroz_menu_add_logo_link( $atts, $item, $args ) {
  /**
   * Ajouter le lien vers la homepage sur le le <a> qui contient l'image du logo
   */
  global $site_logo;

  if ($item->ID === $site_logo->ID)
    return array_merge($atts, array(
      "href" => get_site_url()
    ));

  return $atts;
}



function bulgroz_menu_add_logo_class( $classes, $item, $args ) {
  /**
   * Ajouter les classes css menu-item & menu-logo au <li> qui wrap notre logo
   */
  global $site_logo;

  if ($item->ID === $site_logo->ID)
    return array_merge($classes, array('menu-item', 'menu-logo'));

  return $classes;
}
