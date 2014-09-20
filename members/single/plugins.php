<?php

/**
 * BuddyPress - Users Plugins
 *
 * This is a fallback file that external plugins can use if the template they
 * need is not installed in the current theme. Use the actions in this template
 * to output everything your plugin needs.
 *
 * @package BuddyPress
 * @subpackage bp-default
 */
?>

<?php get_header( 'buddypress' ); ?>

	<div id="content" class="<?php flawless_wrapper_class(); ?>">
  
    <?php flawless_widget_area('left_sidebar'); ?>
  
		<div class="main column-block">
    
      <div class="column-module">

        <?php do_action( 'bp_before_member_plugin_template' ); ?>

        <div id="item-header" class="item-header">

          <?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

        </div><!-- #item-header -->

        <?php Flawless_BuddyPress::render_navigation(); ?>

        <div class="item-body cf">

          <?php do_action( 'bp_before_member_body' ); ?>

          <h3><?php do_action( 'bp_template_title' ); ?></h3>

          <?php do_action( 'bp_template_content' ); ?>

          <?php do_action( 'bp_after_member_body' ); ?>

        </div><!-- #item-body -->

        <?php do_action( 'bp_after_member_plugin_template' ); ?>
        
      </div><!-- .column-module  -->
    </div><!-- .main  -->

	 <?php flawless_widget_area('right_sidebar'); ?>

	</div><!-- #content -->

<?php get_footer( 'buddypress' ); ?>
