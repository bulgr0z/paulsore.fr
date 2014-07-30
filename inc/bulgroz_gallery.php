<?php

add_filter( 'post_gallery', 'bulgroz_background_gallery', 10, 2 );

/**
 * Default gallery (placeholder).
 * Toutes les pages doivent avoir une image de fond, en conséquence bulgroz_gallery ne vaut pas null
 * mais plutot un placeholder dans le cas où aucune image n'est renseignée dans le post et le hook
 * de post_gallery n'est pas (logiquement) déclenché
 */
$options = get_option( 'bulgroz_section' ); // récupérer les options du theme
(is_numeric($options['bg'])) ? $site_bg = get_post($options['bg'])->guid // si on a une img par défaut dans les options du theme
                             : $site_bg = get_template_directory_uri() .'/images/placeholder.jpg'; // sinon placeholder

$bulgroz_gallery = '<div id="post-background" class="image">
                        <img src="'. $site_bg .'" />
                    </div>';

function bulgroz_background_gallery($out, $attr) {

  global $post, $karou, $wp_locale;

  /**
   * On récupère les détails du shortcode présent dans le post.
   * On pourra se servir de la clé "include" pour effectuer une requete get_post et récupérer les
   * images proprement sans explode ou trucs chelous avec les mime types qui vont bien
   */

  $shortcode = shortcode_atts(array(
    'order'      => 'ASC',
    'orderby'    => 'menu_order ID',
    'id'         => $post->ID,
    'itemtag'    => 'dl',
    'icontag'    => 'dt',
    'captiontag' => 'dd',
    'columns'    => 3,
    'size'       => 'thumbnail',
    'include'    => '',
    'exclude'    => ''
  ), $attr);

  $attachments = get_posts(array(
    'include' => $shortcode['include'],
    'post_type' => 'attachment',
    'post_mime_type' => 'image'
  ));

  if (is_page() && is_page_template('page-carousel.php')) // si template karou
    return bulgroz_background_karou($attachments);
  if (is_page() && is_page_template('page-joyeuse.php')) // si template bg static
    return bulgroz_background_image($attachments);

}

function bulgroz_background_placeholder() {
  global $bulgroz_gallery, $wp_locale;

  $bulgroz_gallery = '<div id="post-background" class="image">
                        <img src="http://placehold.it/1250x520" />
                      </div>';

  return ' ';

}

function bulgroz_background_image($attachments) {

  global $bulgroz_gallery, $wp_locale;

  /*$bulgroz_gallery = '<div id="post-background" class="image">
                        <img src="'. $attachments[0]->guid .'" />
                      </div>';*/

  $bulgroz_gallery = '<div id="post-background" class="image" style="background-image:url('. $attachments[0]->guid .')"></div>';

  return ' ';
}

function bulgroz_background_karou($attachments) {

  global $bulgroz_gallery, $wp_locale;

  $bulgroz_gallery = '<div id="post-background" class="karou">
                        <div class="karou-container">
                          <div class="karou-scroller">'; // start karou-container/karou-scroller
  foreach ($attachments as $image) {

    // Récupère l'image a la taille demandée, mais laisse de coté les infos "post" (caption, etc)
    // Dans le cas du karou, on veut l'image full size, ce qu'on a déjà dans $image, pas besoin
    // de demander une taille custom.
    //wp_get_attachment_image_src($image->ID, 'full');

    /*$bulgroz_gallery .= '<div class="karouel" data-caption="'. $image->post_excerpt .'">
                          <img src="'. $image->guid .'" title="'. $image->post_title .'"/>
                        </div>';*/

    $bulgroz_gallery .= '<div class="karouel" data-caption="'. $image->post_excerpt .'" style="background-image:url('. $image->guid .')"></div>';

  }
  $bulgroz_gallery .= '</div></div></div>'; // end karou-container/karou-scroller

  return ' ';

}
