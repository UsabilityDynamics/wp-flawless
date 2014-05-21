<?php
/**
 * Template for archives and categories.
 *
 *
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
*/

  //** Bail out if page is being loaded directly and flawless_theme does not exist */
  if(!function_exists('get_header')) {
    die();
  }
  
  if(! have_posts() && $fs[ 'no_search_result_page' ] ) {
    wp_redirect(get_permalink($fs[ 'no_search_result_page' ]));
    die();
  }
?>

<?php get_header() ?>

<?php get_template_part('attention', 'category'); ?> 

<div id="content" class="<?php flawless_wrapper_class(); ?>">
  
  <?php flawless_widget_area('left_sidebar'); ?>

  <div class="main column-block">
  
    <div class="hentry">
      <?php if(!hide_page_title()) { ?>
      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
        <?php if (trim(get_search_query()) == "") : ?>
          <h1 class="entry-title error"><?php _e( 'You forgot to enter a search term!', 'flawless' ) ?></h1>
        <?php else: ?>
          <h1 class="entry-title"><?php printf( __( 'Search: %s', 'f' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
        <?php endif; ?>
      </header>
      <?php } ?>  
    </div>   
    
    <?php if(! have_posts() ): ?>
    
    <?php endif; ?>
    <?php get_template_part( 'loop', 'blog' ); ?>
  
  </div>   
  
  <?php flawless_widget_area('right_sidebar'); ?>

</div>

<?php get_footer() ?>