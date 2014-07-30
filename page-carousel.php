<?php
/*
Template Name: Caroussel
 */

get_header(); ?>

  <div id="main-content" class="main-content">

    <?php
    if ( is_front_page() ) {
      // Include the featured content template.
      get_template_part( 'featured-content' );
    }
    ?>
    <div id="primary" class="content-area">
      <div id="content" class="site-content" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

          <article id="post-<?php the_ID(); ?>" <?php post_class(array($options['karou'])); ?>>

            <div class="entry-content hidden">
              <div class="scroller">
                <?php the_content() ?>
              </div>
            </div><!-- .entry-content -->

            <div id="karou-caption"></div>
            <?php echo $bulgroz_gallery ?>

            <footer class="entry-meta">
              <?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
            </footer><!-- .entry-meta -->
          </article><!-- #post -->

          <?php comments_template(); ?>
        <?php endwhile; ?>

      </div><!-- #content -->
    </div><!-- #primary -->
    <?php get_sidebar( 'content' ); ?>
  </div><!-- #main-content -->

<?php
get_sidebar();
get_footer();
