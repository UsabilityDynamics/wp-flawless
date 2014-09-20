<?php
/**
 * Displays Entry Meta on single pages, below the content.
 *
 * @package Flawless
 * @since Flawless 3.0
 *
 */

  if($fs['post_types'][$post->post_type]['show_post_meta'] == 'true') {

    if(get_the_category_list()) {
      $meta_html[] = '<li class="posted-in">' . __('Categories: ', 'flawless') . get_the_category_list(', ') . '</li>';
    }

  }

  $meta_html = apply_filters('flawless_meta_header', $meta_html, array('location' => 'footer'));

  //* Leave if no HTML generated */
  if(empty($meta_html)) {
    return;
  }
  
?>

<ul class="entry-meta footer">
  <?php echo implode('', $meta_html); ?>
</ul>
