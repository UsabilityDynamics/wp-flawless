<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <title><?php wp_title( ' ',true,' ' ); ?> <?php bloginfo( 'name' ); ?> </title>
  <link rel="profile" href="http://gmpg.org/xfn/11" />
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

  <?php do_action( 'header-navbar' ); ?>  

  <div class="flawless_header_expandable_sections">
    <?php get_template_part( 'header-element','contact-us' ); ?>
    <?php get_template_part( 'header-element','login' ); ?>
  </div>

  <div class="super_wrapper">
    <div class="body_upper_background"></div>
    <div class="general_header_wrapper">
      <div class="header container cf flawless_dynamic_area" container_type="header">

        <?php  if( current_theme_supports( 'header-logo' ) && $flawless['flawless_logo']['url'] ): ?>
          <div <?php flawless_element( 'logo_area_wrapper column-block inner_container' ); ?>>
            <a href="<?php echo home_url(); ?>" class="header_logo_image" title="<?php bloginfo( 'name' ); ?>">
              <img class="header_logo_image"  src="<?php echo $flawless['flawless_logo']['url']; ?>" alt="<?php bloginfo( 'name' ); ?>" />
            </a>
          </div>
        <?php endif; ?>

        <?php if( current_theme_supports( 'header-business-card' ) && flawless_have_business_card( 'header' ) ): ?>
          <div <?php flawless_element( 'header_business_card_wrapper column-block' ); ?>>
            <div class="header_business_card inner_container">
            <?php echo flawless_have_business_card( 'header' ); ?>
            </div>
          </div>
        <?php endif; ?>

        <?php  if( current_theme_supports( 'header-search' ) ): ?>
          <div <?php flawless_element( 'header_search_wrapper column-block no-print' ); ?>>
            <div class="header_search inner_container">
              <?php get_search_form(); ?>
            </div>
          </div>
        <?php endif; ?>

        <?php  if( $header_menu = wp_nav_menu( apply_filters( 'flawless_header_menu', array( 'theme_location' => 'header-menu', 'menu_class' => 'header-nav flawless-menu no-print cf', 'fallback_cb' => false, 'echo' => false ) ) ) ): ?>
          <div <?php flawless_element( 'header_menu column-block' ); ?>>
            <?php echo $header_menu; ?>
          </div>
        <?php endif; ?>

        <?php if( current_theme_supports( 'header-dropdowns' ) ): ?>

          <?php get_template_part( 'header-element','dropdown-links' ); ?>
        <?php endif; ?>

      </div>
    </div>

    <div class="content_container cf">
    
    <div class="primary_notice_container container"><?php do_action( 'flawless::primary_notice_container' ); ?></div>

    <?php wp_nav_menu( apply_filters( 'flawless_sub_header_menu', array( 'theme_location'=> 'header-sub-menu', 'menu_class' => 'header-sub-menu container flawless-menu no-print cf', 'fallback_cb' => false, 'depth' => 2 ) ) ); ?>

    <?php do_action( 'flawless_header_bottom' ); ?>
