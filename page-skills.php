<?php
/*
Template Name: Skills
 */

get_header(); ?>

  <div id="main-content" class="main-content skills container-fluid">

    <?php
    if ( is_front_page() ) {
      // Include the featured content template.
      get_template_part( 'featured-content' );
    }
    ?>
    <div id="primary" class="content-area row">
      <div id="content" class="site-content col-lg-11 col-lg-offset-1" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

          <div class="row vantardise">
            <div class="col-lg-11">
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
                  $items = array(
                    "code", // bloc intégration html/php/resp
                    "integration", // bloc intégration sociale
                    "design" // bloc intégration SEO
                  );
                  // score des items
                  $score = array(
                    75,
                    90,
                    55
                  );

              ?>
                <div class="col-lg-4 col-sm-6 col-sm-center item">
                  <div class="radial-progress col-sm-center col-xs-center" data-item="<?php echo $items[$i] ?>" data-progress="0">
                    <div class="circle">
                      <div class="mask full">
                        <div class="fill"></div>
                      </div>
                      <div class="mask half">
                        <div class="fill"></div>
                        <div class="fill fix"></div>
                      </div>
                    </div>
                    <div class="inset">
                      <div class="percentage">
                        <div class="numbers"><span><?php echo $score[$i] ?></span></div>
                      </div>
                    </div>
                  </div>
                  <div class="caption col-sm-center col-xs-center"><?php echo $textcontent ?></div>
                </div>

              <?php

            } //end foreach

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
