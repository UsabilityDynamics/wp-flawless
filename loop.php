<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package Flawless
 * @since Flawless 1.7
 */
?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?>>
  <div class="column-module post_listing_inner">

    <h2 class="entry-title">
      <a href="<?php the_permalink(); ?>" alt="<?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a>
    </h2>

    <?php get_template_part('entry-utility'); ?>

    <div class="entry-content entry-summary cf">
      <?php if ( post_password_required() ) { ?>
      <?php the_content(); ?>
      <?php } else { ?>

       <?php flawless_thumbnail(); ?>

        <?php the_excerpt('More Info'); ?>
      <?php } ?>
    </div>
  </div><?php /* .post_listing_inner */ ?>
</div><?php /* post_class() */ ?>
<?php endwhile; endif; ?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if (  $wp_query->max_num_pages > 1 ) : ?>
  <div id="nav-below" class="navigation">
    <span class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'flawless' ) ); ?></span>
    <span class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'flawless' ) ); ?></span>
  </div><!-- #nav-below -->
<?php endif; ?>

