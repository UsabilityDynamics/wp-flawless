<?php
/**
 * Template for 404 pages.
 *
 *
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
*/

  if($fs['404_page']) {
    $wp_query->post_count = 1;
    $wp_query->posts[0] = get_post($fs['404_page']);
  }

?>

<?php get_header(); ?>

<?php get_template_part('attention', '404'); ?>

<div id="content" class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area('left_sidebar'); ?>

  <div class="main column-block">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

      <?php if(!hide_page_title()) { ?>
      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
        <h1 class="entry-title"><?php the_title();?></h1>
      </header>
      <?php } ?>

      <div class="entry-content cf">
      <?php the_content('More Info'); ?>
      <?php comments_template(); ?>
      </div>

    </article>
    <?php endwhile; else: ?>

    <article id="post-0" class="post error404 not-found">

      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
        <h1 class="entry-title"><?php _e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'flawless' ); ?></h1>
      </header>

    	<div class="entry-content cf">
					<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching, or one of the links below, can help.', 'flawless' ); ?></p>

					<?php the_widget( 'WP_Widget_Recent_Posts', array( 'number' => 10 ), array( 'widget_id' => '404' ) ); ?>

					<?php the_widget( 'WP_Widget_Tag_Cloud' ); ?>

				</div><!-- .entry-content -->
			</article><!-- #post-0 -->

    <?php endif; ?>

  </div>

  <?php flawless_widget_area('right_sidebar'); ?>

</div>

<?php get_footer() ?>