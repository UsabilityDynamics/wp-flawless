<?php
/**
 * Template for custom taxonomies, categories will use archive.php
 *
 * Taxonomies may be related to different post types.
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
*/

  //** Bail out if page is being loaded directly and flawless_theme does not exist */
  if(!function_exists('get_header')) {
    die();
  }

  //** Get ID of the term */
  $taxonomy = get_taxonomy($wp_query->query_vars['taxonomy']);
  $term = get_term($wp_query->queried_object_id, $taxonomy->name);

  //** Get all content types that use this taxonomy, and get their content coutns */
  foreach($fs['post_types'] as $post_type => $post_data) {
  
    $this_query = array( 'post_type' => $post_type, 'numberposts' => -1, $taxonomy->name => $term->slug );
    if($have_content = get_posts($this_query)) {        
      $found_content[$post_type] = count($have_content);
    }
  }
  
  get_header();
?>

<?php get_template_part('attention', 'category'); ?>

<div id="content" class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area('left_sidebar'); ?>

  <div class="main column-block">

    <div class="hentry">
      <?php if(!hide_page_title()) { ?>
      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
        <h1 class="entry-title"><?php echo single_term_title( '', false ); ?></h1>
      </header>
      <?php } ?>

      <?php echo (category_description() != '' ? '<div class="category_description">' . category_description() . '</div>' : ''); ?>

    </div>

    <?php /* Display any objects matched by taxonomy*/ do_shortcode('[property_overview]'); ?>

    <?php foreach($found_content as $post_type => $this_query) { query_posts($this_query); ?>
    <?php get_template_part( 'loop', 'blog' ); ?>
    <?php } ?>

  </div>

  <?php flawless_widget_area('right_sidebar'); ?>

</div>

<?php get_footer() ?>