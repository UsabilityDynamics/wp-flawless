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

?>

<?php get_header( 'archive' ) ?>

<?php get_template_part('attention', 'archive'); ?>

<div id="content" class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area('left_sidebar'); ?>

  <div class="main column-block">
    <div class="archive-hentry column-module">

      <header class="archive entry-title-wrapper">

        <?php flawless_breadcrumbs(); ?>

        <?php if(!hide_page_title()) { ?>
          <h1 class="entry-title"><?php echo single_term_title( '', false ) ? single_term_title( '', false ) : get_queried_object()->label; ?></h1>
        <?php } ?>

        <?php if(category_description() != '') { ?>
          <div class="category_description">
            <?php echo category_description(); ?>
          </div>
        <?php } ?>

      </header>

    <?php /* Display any objects matched by taxonomy*/ do_shortcode('[property_overview]'); ?>

    <div class="loop loop-blog post-listing cf">
    <?php get_template_part( 'loop', 'blog' ); ?>
    </div>

    </div> <?php /* .archive-hentry */ ?>

  </div> <?php /* .main.column-block */ ?>

  <?php flawless_widget_area('right_sidebar'); ?>

</div> <!-- #content -->

<?php get_footer(); ?>