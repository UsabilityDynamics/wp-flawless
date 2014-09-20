<?php
/**
 * Property Default Template for Single Property View
 *
 *
 *
 * @version 0.3.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless 
 */

  //** Bail out if page is being loaded directly and flawless_theme does not exist */
  if(!function_exists('get_header')) {
    die();
  }
  
?>

<?php get_header( 'property' ); ?>

<div id="content" class="<?php flawless_wrapper_class( 'property_content' ); ?>">

  <?php flawless_widget_area('left_sidebar'); ?>

  <div class="main column-block">

    <div id="post-<?php the_ID(); ?>" <?php post_class('main property_page_post'); ?>>

      <?php if(!hide_page_title()) { ?>
      <header class="entry-title-wrapper property_title_wrapper">
        <?php flawless_breadcrumbs(); ?>
        <h1 class="property-title entry-title">
          <?php echo apply_filters('flawless_title', get_the_title(), 'entry-title');?>
          <span class="entry-title-subnote"><?php echo get_attribute('property_type_label', array('allow_multiple_values' =>false )); ?></span>
        </h1>
        <h3 class="entry-subtitle"><?php the_tagline(); ?></h3>
      </header>
      <?php } ?>

      <?php get_template_part('entry-meta', 'header'); ?>

      <div class="entry-content cf">
        <div class="the_content"><?php the_content('More Info'); ?></div>
      </div><!-- .entry-content -->

      <?php get_template_part('content','single-property-map'); ?>

      <?php get_template_part('content','single-property-inquiry'); ?>

    </div> <!-- post_class() -->

    <?php get_template_part('content','single-property-bottom'); ?>

  </div> <!-- .main column-block -->

  <?php flawless_widget_area('right_sidebar'); ?>

</div> <!-- #content -->

<?php get_footer( 'property' ); ?>