<?php
/**
 * Search form template.
 *
 *
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
*/
?>

<form class="search_format" role="search" method="get" action="<?php echo  home_url( '/' ); ?>" >
  <div class="search_inner_wrapper">
    <label class="screen-reader-text" for="s"><?php _e('Search for:'); ?></label>
    <input class="search_input_field" type="text" value="<?php echo trim( get_search_query() ); ?>" name="s" id="s" placeholder="<?php printf(__('Search %1s', 'flawless'), get_bloginfo('name')); ?>" />
    <input class="action_button search_button btn"  type="submit" id="searchsubmit" value="<?php echo esc_attr__('Search'); ?>" />
  </div>
</form>