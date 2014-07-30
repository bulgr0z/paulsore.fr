<?php

## GESTION DU MENU
require_once('inc/bulgroz_menu.php');

## GESTION DE LA GALLERY
require_once('inc/bulgroz_gallery.php');

## GESTION DES PARAMETRES ADMIN
require_once('inc/bulgroz_settings.php');

add_action( 'wp_enqueue_scripts', 'bulgroz_scripts' );

function bulgroz_scripts() {

  // ne pas utiliser le jQuery de wordpress
  wp_deregister_script('jquery');

  // ajouter le style du theme
  wp_enqueue_style( 'bulgroz-style', get_stylesheet_uri() );

  // ajouter jQuery et les scripts qui en dépendent dans le footer
  wp_enqueue_script( 'jquery', get_template_directory_uri() . '/js/vendors/jquery-1.10.2.min.js', null, 01, true);
  wp_enqueue_script( 'karou', get_template_directory_uri() . '/js/vendors/karou.js', array( 'jquery' ), 01, true);
  wp_enqueue_script( 'tinyscroll', get_template_directory_uri() . '/js/vendors/tinyscrollbar.js', array( 'jquery' ), 01, true);

  wp_enqueue_script( 'joyeuse', get_template_directory_uri() . '/js/joyeuse.js', array( 'jquery' ), time(), true);
}

function pre($dump) {
  echo '<pre>';
  var_dump($dump);
  echo '</pre>';
}
