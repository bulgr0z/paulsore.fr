<?php
/*
Template Name: About
 */

get_header(); ?>

  <div id="main-content" class="main-content about container-fluid">

    <?php
    if ( is_front_page() ) {
      // Include the featured content template.
      get_template_part( 'featured-content' );
    }
    ?>
    <div id="primary" class="content-area row">
      <div id="content" class="site-content col-lg-11 col-lg-offset-1" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

          <div class="row">
            <div class="col-lg-9">
              <?php the_content() ?>
            </div>
          </div>

          <div class="row">

            <?php

              # Iterer sur les champs présents dans le gorupe "Extra text" de la page
              # Voir -> http://simple-fields.com/documentation/getting-started/tutorial-part-2/
              #
              foreach(simple_fields_get_post_group_values(get_the_id(), "Extra text", false, 1)[1] as $i=>$textcontent) {

                  // icones au dessus de chaque post
                  $icons = array(
                    "flaticon-responsive", // bloc intégration html/php/resp
                    "flaticon-like3", // bloc intégration sociale
                    "flaticon-stats" // bloc intégration SEO
                  );

                  echo '<div class="col-lg-4 col-sm-6 item">';
                      echo '<i class="icon '. $icons[$i] .' text-center no-margin"></i>';
                      echo '<div class="text">'. $textcontent .'</div>';
                  echo '</div>';

              }

            ?>

          </div>

        <?php endwhile; ?>

      </div><!-- #content -->
    </div><!-- #primary -->
    <?php get_sidebar( 'content' ); ?>
  </div><!-- #main-content -->

<?php
get_sidebar();
get_footer();
