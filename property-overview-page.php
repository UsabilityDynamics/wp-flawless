<?php
/**
 * The default page for property overview page.
 *
 * Used when no WordPress page is setup to display overview via shortcode.
 * Will be rendered as a 404 not-found, but still can display properties.
 *
 * @package WP-Property
 */
?>

<?php get_header(); ?>

<?php get_template_part('attention', 'property-overview'); ?>

<div id="content" class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area('left_sidebar'); ?>

  <div class="main column-block">

  <article id="post-0" class="post error404 not-found">

      <?php if(!hide_page_title()) { ?>
      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
        <h1 class="entry-title"><?php echo apply_filters('flawless_title', get_the_title(), 'entry-title');?></h1>
      </header>
      <?php } ?>
      
      <?php get_template_part('entry-meta', 'header'); ?>

      <div class="entry-content cf">
        
        <?php if(is_404()): ?>
          <p><?php _e('Sorry, we could not find what you were looking for.  Since you are here, take a look at some of our properties.','wpp') ?></p>
        <?php endif; ?>
        
        <?php if($wp_properties['configuration']['do_not_override_search_result_page'] == 'true'): ?>            
          <?php echo $content = apply_filters('the_content', $post->post_content);  ?>
        <?php endif; ?>
        
        <?php echo WPP_Core::shortcode_property_overview(); ?>        
        
      </div>
      
      <?php get_template_part('entry-meta', 'footer'); ?>
      
    </div> <!-- post_class() -->

    
  </div> <!-- .main column-block -->

  <?php flawless_widget_area('right_sidebar'); ?>

</div> <!-- #content --> 

<?php get_footer(); ?>