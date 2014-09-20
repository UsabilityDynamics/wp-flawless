<?php get_header( 'buddypress' ); ?>

	<div id="content" class="<?php flawless_wrapper_class(); ?>">
		<div class="main column-block">

			<?php do_action( 'bp_before_directory_forums_content' ); ?>

				<div id="item-header" class="item-header" role="complementary">

					<?php locate_template( array( 'forums/single/forum-header.php' ), true ); ?>

				</div><!-- #item-header -->

				<div id="item-nav" class="item-nav">
					<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
						<ul class="pills">

							<li>
								<a href="#post-new" class="btn success large show-hide-new"><?php _e( 'New Topic', 'buddypress' ); ?></a>
							</li>

							<?php if ( bp_forums_has_directory() ) : ?>

								<li>
									<a href="<?php bp_forums_directory_permalink() ?>"><?php _e( 'Forum Directory', 'buddypress'); ?></a>
								</li>

							<?php endif; ?>

							<?php do_action( 'bp_forums_directory_group_sub_types' ); ?>

							<li id="forums-order-select" class="last filter">

								<label for="forums-order-by"><?php _e( 'Order By:', 'buddypress' ); ?></label>
								<select id="forums-order-by">
									<option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>
									<option value="popular"><?php _e( 'Most Posts', 'buddypress' ); ?></option>
									<option value="unreplied"><?php _e( 'Unreplied', 'buddypress' ); ?></option>

									<?php do_action( 'bp_forums_directory_order_options' ); ?>

								</select>
							</li>
						</ul>
					</div>
				</div><!-- #item-nav -->

				<div id="item-body" class="item-body">

					<div id="forums-dir-list" class="forums dir-list" role="main">

						<?php locate_template( array( 'forums/forums-loop.php' ), true ); ?>

					</div>

					<?php do_action( 'bp_directory_forums_content' ); ?>

				</div>
			</div>
      
      <?php do_action( 'bp_after_directory_forums_content' ); ?>
      
		</div><!-- .main  --> 

	 <?php flawless_widget_area( 'buddypress' ) ?>
  
</div><!-- #content -->

<?php get_footer( 'buddypress' ); ?>