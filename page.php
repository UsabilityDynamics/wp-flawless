<?php
/**
 * Template for standard pages.
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

?>

<?php get_header('page'); ?>

<div id="content" class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area('left_sidebar'); ?>
  
  <div class="main column-block">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

      <?php if(!hide_page_title()) { ?>
      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
        <h1 class="entry-title"><?php echo apply_filters('flawless_title', get_the_title(), array('title' =>  get_the_title(), 'position' => 'entry-title')); ?></h1>
      </header>
      <?php } ?>
      
      <?php get_template_part('entry-meta', 'header'); ?>

      <div class="entry-content cf">
      <?php the_content('More Info'); ?>
      <?php comments_template(); ?>
      </div>
      
      <?php get_template_part('entry-meta', 'footer'); ?>
      
    </div> <!-- post_class() -->
    
    <?php endwhile; endif; ?>
    
  </div> <!-- .main column-block -->

  <?php flawless_widget_area('right_sidebar'); ?>

</div> <!-- #content --> 

<?php get_footer(); ?>