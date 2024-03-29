<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 */

get_header(); ?>

  <div id="main-content" class="main-content">
    <div id="content" class="site-content home-background" role="main">

      <div id="home-overlay">

        <?php

        if ( have_posts() ) :
          // Start the Loop.
          while ( have_posts() ) : the_post();

            /*
             * Include the post format-specific template for the content. If you want to
             * use this in a child theme, then include a file called called content-___.php
             * (where ___ is the post format) and that will be used instead.
             */
            get_template_part( 'content', get_post_format() );

          endwhile;
          // Previous/next post navigation.

        else :
          // If no content, include the "No posts found" template.
          get_template_part( 'content', 'none' );

        endif;
        ?>

      </div>

    </div><!-- #content -->
  </div><!-- #main-content -->

<?php
get_sidebar();
get_footer();
