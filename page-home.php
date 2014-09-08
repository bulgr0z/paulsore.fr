<?php
/*
Template Name: Home
 */

get_header(); ?>

  <div id="main-content" class="main-content home-background container-fluid">

    <?php
    if ( is_front_page() ) {
      // Include the featured content template.
      get_template_part( 'featured-content' );
    }
    ?>
    <div id="primary" class="content-area home-overlay row">
      <div id="content" class="site-content col-lg-offset-1 col-lg-6 col-xs-12 col-sm-11" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

          <?php the_content() ?>

          <a href="#footer" class="bulbtn green">Contact</a>

        <?php endwhile; ?>

      </div><!-- #content -->
    </div><!-- #primary -->
    <?php get_sidebar( 'content' ); ?>
  </div><!-- #main-content -->

<?php
get_sidebar();
get_footer();
