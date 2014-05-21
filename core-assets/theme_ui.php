<?php
/**
  * Name: Flawless Theme UI
  * Description: The UI for the Flawless theme.
  * Author: Usability Dynamics, Inc.
  * Version: 1.0
  *
  */

class flawless_theme_ui {


  /**
   * Display a splash screen on update or new install
   *
   * @todo Once flawless_theme::parse_readme() is updated to return data via associative array, this can be improved - potanin@UD
   * @author potanin@UD
   */
  function show_update_screen( $splash_type ) {

    $change_log = flawless_theme::parse_readme();

  ?>

  <div class="wrap flawless-update about-wrap ">

    <h1>Welcome to Flawless <?php echo Flawless_Version; ?></h1>

    <div class="about-text">Thank you for updating to the latest version! Please take a look at some of the updates. </div>

    <div class="changelog point-releases">
      <?php echo $change_log; ?>
    </div>

    <div class="return-to-dashboard">
      <a href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Go to Dashboard &rarr; Home' ); ?></a>
    </div>

  </div>
  <?php

  }


  function options_ui_main( $flawless ) { ?>

    <table class="form-table">
      <tbody>

      <?php if( $page_selection_404 = flawless_theme::wp_dropdown_objects( array(
        'name' => "flawless_settings[404_page]",
        'show_option_none' => __( '&mdash; Select &mdash;' ),
        'option_none_value' => '0',
        'echo' => false,
        'post_type' => get_post_types( array( 'hierarchical' => true ) ),
        'selected' => $flawless[ '404_page' ]
        ) ) ) { ?>
      <tr valign="top">
        <th><?php _e( '404 Page: ' , 'flawless' ); ?></th>
        <td>
          <ul>
            <li>
              <label for="404_page"></label>
              <?php echo $page_selection_404; ?>
            </li>
          </ul>
        </td>
      </tr>
      <?php  } ?>

      <?php if( $page_selection_not_found = flawless_theme::wp_dropdown_objects( array(
        'name' => "flawless_settings[no_search_result_page]",
        'show_option_none' => __( '&mdash; Select &mdash;' ),
        'option_none_value' => '0',
        'echo' => false,
        'post_type' => get_post_types( array( 'hierarchical' => true ) ),
        'selected' => $flawless[ 'no_search_result_page' ]
        ) ) ) { ?>
      <tr valign="top">
        <th><?php _e( 'No Result Page: ' , 'flawless' ); ?></th>
        <td>
          <ul>
            <li>
              <label for="404_page"></label>
              <?php echo $page_selection_not_found; ?>
              <div class="description"><?php _e( 'Page to display when a search has no results.' , 'flawless' ); ?></div>
            </li>
          </ul>
        </td>
      </tr>
      <?php  } ?>

      <tr valign="top">
        <th><?php _e( 'Other Settings', 'flawless' ); ?></th>
        <td>
          <ul>

            <li>
              <input <?php echo checked( 'true', $flawless[ 'hide_breadcrumbs' ] ); ?> type="checkbox"  name='flawless_settings[hide_breadcrumbs]' id="hide_breadcrumbs"  value="true" />
              <label for="hide_breadcrumbs"><?php _e( 'Globally disable breadcrumbs.', 'flawless' ); ?></label>
            </li>

          </ul>
        </td>
      </tr>

      <tr valign="top">
        <th><?php _e( 'JavaScript Enhancements', 'flawless' ); ?></th>
        <td>
          <ul>
            <li><label><input type="checkbox" <?php echo checked( 'true', $flawless[ 'enable_lazyload' ] ); ?>  name="flawless_settings[enable_lazyload]" value="true" /> <?php _e( 'Enable LazyLoad.', 'flawless' ); ?> <span class="description"><?php _e( 'Add .lazy class to images to use.', 'flawless' ); ?></label></li>
            <li><label><input type="checkbox" <?php echo checked( 'true', $flawless[ 'enable_google_pretify' ] ); ?>  name="flawless_settings[enable_google_pretify]" value="true" /> <?php _e( 'Enable Google Pretify.', 'flawless' ); ?></label></li>
            <li><label><input type="checkbox" <?php echo checked( 'true', $flawless[ 'enable_dynamic_filter' ] ); ?>  name="flawless_settings[enable_dynamic_filter]" value="true" /> <?php _e( 'Enable Dynamic Filter.', 'flawless' ); ?></label></li>
            <li><label><input type="checkbox" <?php echo checked( 'true', $flawless[ 'disable_form_helper' ] ); ?>  name="flawless_settings[disable_form_helper]" value="true" /> <?php _e( 'Disable Form Helper.', 'flawless' ); ?> </label></li>
            <li><label><input type="checkbox" <?php echo checked( 'true', $flawless[ 'disable_fancybox' ] ); ?>  name="flawless_settings[disable_fancybox]" value="true" /> <?php _e( 'Disable FancyBox.', 'flawless' ); ?> <span class="description"><?php _e( 'Enabled by default and applied to all images.', 'flawless' ); ?></span></label></li>
          </ul>
        </td>
      </tr>

      <?php do_action( 'flawless_options_ui_main', $flawless ); ?>

      </tbody>
      </table>

    <?php }



    function options_ui_post_types( $flawless ) {
      global $wp_post_types, $_wp_post_type_features;


    ?>

    <div class="tab_description"><?php _e( 'Create new content types and organized widget areas.', 'flawless' ); ?></div>

    <div class="flawless_content_ui">

      <div class="widget_area_sidebar">
        <div class="flawless_available_widget_areas"><?php _e( 'Available Widget Areas', 'flawless' ); ?></div>
        <ul class="flawless_widget_area_list" type="widget_area_selector">

          <?php foreach( ( array ) $flawless[ 'widget_areas' ][ 'all' ] as $sidebar_id => $sidebar_data ) {
            flawless_theme_ui::flawless_widget_item( array(
              'sidebar_id' => $sidebar_id,
              'widget_area_selector' => true,
              'sidebar_data' => $sidebar_data
            ) );
            } ?>

          <li class="flawless_add_new_widget_area">
            <?php _e( 'Add New Widget Area', 'flawless' ); ?>
          </li>

        </ul>
      </div>

      <div class="flawless_content_body">

        <div class="ud_ui_dynamic_table flawless_content_inner">

          <?php foreach( ( array ) $flawless[ 'post_types' ] as $type => $data ) { ?>

          <div class="flawless_content_type_module flawless_dynamic_table_row" content_type="post_types" slug="<?php echo $type; ?>" new_row="false" lock_row="<?php echo ( $data[ 'flawless_post_type' ] == "true" ? "false" : "true" ); ?>" >

            <div class="ct_header">
              <input class="slug_setter" type="text" name="flawless_settings[post_types][<?php echo $type; ?>][name]" value="<?php echo $data[ 'name' ]; ?>" />

              <ul class="flawless_dropdown_options" show_on_clone="true">
                <li class="flawless_delete_row" verify_action="true">Delete</li>
              </ul>

              <span class="content_type_label"><?php _e( 'Post Type', 'flawless' ); ?></span>

            </div>

            <div class="flawless_content_type_options flawless_half_width">

              <ul class="flawless_options_wrapper flawless_advanced_content_type_options">

                <li class="flawless_option">
                  <label>
                    <input type="checkbox" <?php checked( 'true', $data[ 'exclude_from_search' ] ); ?>  name="flawless_settings[post_types][<?php echo $type; ?>][exclude_from_search]" value="true" />
                    <?php _e( 'Exclude from search.' , 'flawless' ) ?>
                  </label>
                </li>

                <li class="flawless_option">
                  <label>
                    <input type="checkbox" <?php checked( 'true', $data[ 'disable_comments' ] ); ?> name="flawless_settings[post_types][<?php echo $type; ?>][disable_comments]" value="true" />
                    <?php _e( 'Disable comments.' , 'flawless' ) ?>
                  </label>
                </li>

                <li class="flawless_option">
                  <label>
                    <input type="checkbox" <?php checked( 'true', $data[ 'show_post_meta' ] ); ?> name="flawless_settings[post_types][<?php echo $type; ?>][show_post_meta]" value="true" />
                    <?php _e( 'Show post meta.' , 'flawless' ) ?>
                  </label>
                </li>

                <li class="flawless_option">
                  <label>
                    <input type="checkbox" <?php checked( 'true', $data[ 'disable_author' ] ); ?> name="flawless_settings[post_types][<?php echo $type; ?>][disable_author]" value="true" />
                    <?php _e( 'Disable authors.', 'flawless' ) ?>
                  </label>
                </li>

                <li settings_wrapper="flawless_options_wrapper" class="flawless_show_advanced" text_if_shown="<?php _e( 'Hide Advanced' , 'flawless' ) ?>" text_if_hidden="<?php _e( 'Show Advanced' , 'flawless' ) ?>">
                  <?php _e( 'Show Advanced' , 'flawless' ) ?>
                </li>

                <li class="flawless_advanced_option">
                  <label for="<?php echo $type; ?>_root_page"><?php _e( 'Root page:' , 'flawless' ) ?></label>
                  <?php flawless_theme::wp_dropdown_objects( array(
                    'name' => "flawless_settings[post_types][{$type}][root_page]",
                    'show_option_none' => __( '&mdash; Select &mdash;' ),
                    'option_none_value' => '0',
                    'post_type' => get_post_types( array( 'hierarchical' => true ) ),
                    'selected' => $data[ 'root_page' ]
                    ) ); ?>
                </li>

                <?php do_action( 'flawless_post_types_advanced_options', array(
                  'type' => $type,
                  'data' => $data,
                  'fs' => $flawless )
                ); ?>

                <li class="flawless_advanced_option">
                  <label>
                    <input type="checkbox" <?php checked( 'true', $data[ 'disabled' ] ); ?> name="flawless_settings[post_types][<?php echo $type; ?>][disabled]" value="true" />
                    <?php _e( 'Disable this content type, and hide all related content.' , 'flawless' ) ?>
                  </label>
                </li>

                <li class="flawless_advanced_option">
                  <label>
                    <input type="checkbox" <?php checked( 'true', $data[ 'hierarchical' ] ); ?>  name="flawless_settings[post_types][<?php echo $type; ?>][hierarchical]" value="true" />
                    <?php _e( 'Use this content in a hierarchical manner.' , 'flawless' ) ?>
                  </label>
                </li>

                <li class="flawless_advanced_option">
                  <?php _e( 'Taxonomies:', 'flawless' ); ?>
                  <ul class="wp-tab-panel">
                  <?php foreach( $data[ 'taxonomies' ] as $taxonomy => $enabled ) { ?>
                    <li>
                      <input id="<?php echo $taxonomy; ?>_to_<?php echo $type; ?>" <?php checked( 'enabled', $enabled ); ?> type="checkbox" name="flawless_settings[post_types][<?php echo $type; ?>][taxonomies][<?php echo $taxonomy; ?>]" value="enabled" />
                      <label for="<?php echo $taxonomy; ?>_to_<?php echo $type; ?>"><?php echo $flawless[ 'taxonomies' ][$taxonomy][ 'label' ]; ?></label>
                    </li>
                  <?php } ?>
                  </ul>
                </li>
              </ul> <?php /* .flawless_options_wrapper */ ?>

            </div> <?php /* .flawless_content_type_options.flawless_half_width */ ?>

            <div class="flawless_associated_widget_areas flawless_half_width">
              <?php foreach( ( array ) $flawless[ 'widget_area_sections' ] as $was_slug => $was_data ) {  $these_sidebars = $flawless[ 'views' ][ 'post_types' ][$type][ 'widget_areas' ][$was_slug]; ?>
              <div class="flawless_was_pane" was_slug="<?php echo $was_slug; ?>">
                <h3 class="flawless_was_pane_title"><?php echo $was_data[ 'label' ]; ?></h3>
                    <ul class="flawless_widget_area_list" type="widget_area_holder">

                    <?php foreach( ( array ) $these_sidebars as $sidebar_id ) {

                      flawless_theme_ui::flawless_widget_item( array(
                        'sidebar_id' => $sidebar_id,
                        'was_slug' => $was_slug,
                        'post_type' => $type,
                        'sidebar_data' => $sidebar_data
                      ) );

                      }
                    ?>

                  </ul>
              </div>
              <?php } ?>
            </div>

            <input type="hidden" class="flawless_added_post_type" name="flawless_settings[post_types][<?php echo $type; ?>][flawless_post_type]" value="<?php echo $data[ 'flawless_post_type' ] ? $data[ 'flawless_post_type' ] : 'false'; ?>" />

          </div> <?php /*  .flawless_content_type_module*/ ?>

          <?php } /* end post_type loop */ ?>

          <div class="flawless_actions">
            <input type="button" element_wrapper=".flawless_actions" class="flawless_add_row button-secondary" callback_function="flawless_added_custom_post_type" value="<?php _e( 'Add Content Type', 'flawless' ) ?>" />
          </div>

        </div> <?php /*  .ud_ui_dynamic_table */ ?>

        <div class="ud_ui_dynamic_table flawless_content_inner">

          <?php foreach( ( array ) $flawless[ 'taxonomies' ] as $taxonomy_type => $taxonomy_data ) { ?>

          <div class="flawless_content_type_module flawless_dynamic_table_row" content_type="taxonomies" slug="<?php echo $taxonomy_type; ?>" new_row="false" lock_row="<?php echo ( $taxonomy_data[ 'flawless_taxonomy' ] == 'true' ? 'false' : 'true' ); ?>" >

            <div class="ct_header">
              <input class="slug_setter" type="text" name="flawless_settings[taxonomies][<?php echo $taxonomy_type; ?>][label]" value="<?php echo $taxonomy_data[ 'label' ]; ?>" />

              <ul class="flawless_dropdown_options" show_on_clone="true">
                <li class="flawless_delete_row" verify_action="true">Delete</li>
              </ul>

              <span class="content_type_label"><?php _e( 'Taxonomy', 'flawless' ); ?></span>

            </div>

            <div class="flawless_taxonomy_options flawless_half_width">

              <ul class="flawless_options_wrapper flawless_advanced_content_type_options">

                <li class="flawless_option">
                  <label>
                    <input type="checkbox" <?php checked( 'true', $taxonomy_data[ 'hierarchical' ] ); ?> name="flawless_settings[taxonomies][<?php echo $taxonomy_type; ?>][hierarchical]" value="true" />
                    <?php _e( 'Hierarchical.' , 'flawless' ) ?>
                  </label>
                </li>

                <li class="flawless_option">
                  <label>
                    <input type="checkbox" <?php checked( 'true', $taxonomy_data[ 'exclude_from_search' ] ); ?>  name="flawless_settings[taxonomies][<?php echo $taxonomy_type; ?>][exclude_from_search]" value="true" />
                    <?php _e( 'Exclude from search.' , 'flawless' ) ?>
                  </label>
                </li>

                <?php /*
                <li settings_wrapper="flawless_options_wrapper" class="flawless_show_advanced" text_if_shown="<?php _e( 'Hide Advanced' , 'flawless' ) ?>" text_if_hidden="<?php _e( 'Show Advanced' , 'flawless' ) ?>">
                  <?php _e( 'Show Advanced' , 'flawless' ) ?>
                </li>

                <li class="flawless_advanced_option">
                  <label for="<?php echo $hierarchical; ?>_root_page"><?php _e( 'Root page:', 'flawless' ) ?></label>
                  <?php flawless_theme::wp_dropdown_objects( array(
                    'name' => "flawless_settings[taxonomies][{$taxonomy_type}][root_page]",
                    'show_option_none' => __( '-- Automatically Generated --', 'flawless' ),
                    'option_none_value' => '0',
                    'post_type' => get_post_types( array( 'hierarchical' => true ) ),
                    'selected' => $taxonomy_data[ 'root_page' ]
                    ) ); ?>
                </li>
                */ ?>

                <?php do_action( 'flawless_taxonomies_advanced_options', array(
                  'type' => $taxonomy_type,
                  'data' => $taxonomy_data,
                  'fs' => $flawless )
                ); ?>

              </ul> <?php /* .flawless_options_wrapper */ ?>

            </div> <?php /* .flawless_taxonomy_options.flawless_half_width */ ?>

            <div class="flawless_associated_widget_areas flawless_half_width">
              <?php  foreach( ( array ) $flawless[ 'widget_area_sections' ] as $was_slug => $was_data ) {

              $these_sidebars = $taxonomy_data[ 'widget_areas' ][$was_slug];

              ?>
              <div class="flawless_was_pane" was_slug="<?php echo $was_slug; ?>">
                <h3 class="flawless_was_pane_title"><?php echo $was_data[ 'label' ]; ?></h3>
                    <ul class="flawless_widget_area_list" type="widget_area_holder">

                    <?php foreach( ( array ) $these_sidebars as $sidebar_id ) {

                      flawless_theme_ui::flawless_widget_item( array(
                        'sidebar_id' => $sidebar_id,
                        'was_slug' => $was_slug,
                        'taxonomy_type' => $taxonomy_type,
                        'sidebar_data' => $sidebar_data
                      ) );

                      }
                    ?>

                  </ul>
              </div>
              <?php } ?>
            </div>

            <input type="hidden" class="flawless_added_taxonomy" name="flawless_settings[taxonomies][<?php echo $taxonomy_type; ?>][flawless_taxonomy]" value="<?php echo $taxonomy_data[ 'flawless_taxonomy' ] ? $taxonomy_data[ 'flawless_taxonomy' ]  : 'false'; ?>" />

          </div> <?php /*  .flawless_content_type_module*/ ?>

          <?php } /* end taxonomies loop */ ?>

          <div class="flawless_actions">
            <input type="button" element_wrapper=".flawless_actions" class="flawless_add_row button-secondary" callback_function="flawless_added_custom_taxonomy" value="<?php _e( 'Add Taxonomy', 'flawless' ) ?>" />
          </div>

        </div> <?php /*  .ud_ui_dynamic_table */ ?>

      </div> <?php /* .flawless_content_body */ ?>

      <div class="clear"></div>
    </div> <?php /* .flawless_content_ui */ ?>




    <?php

    }


  /**
   * Header element configuration.
   *
   * This tab has two sections - the first displays a list of checkboxes expandable by the flawless_option_header_elements filter.
   * The checkboxes can enable or disable more advanced configuration settings which are rendered in the second major section
   * using tabs.
   *
   *
   * @since Flawless 0.2.3
   */
    function options_ui_header( $flawless ) {

      $flawless[ 'options_ui' ][ 'header_elements' ] = apply_filters( 'flawless_option_header_elements', array(
        'navbar' => array(
          'label' => __( 'Header Navbar' , 'flawless' ),
          'id' => 'header-navbar',
          'name' => 'flawless_settings[disabled_features][header-navbar]',
          'position' => 10,
          'setting' => $flawless[ 'disabled_features' ][ 'header-navbar' ],
          'callback' => array( 'flawless_theme_ui' , 'options_header_navbar' ),
          'toggle_label' => __( 'Do not show the Navbar.', 'flawless' )
        ),
        'search' => array(
          'label' => __( 'Header Search' , 'flawless' ),
          'id' => 'header-search',
          'name' => 'flawless_settings[disabled_features][header-search]',
          'position' => 20,
          'setting' => $flawless[ 'disabled_features' ][ 'header-search' ],
          'callback' => array( 'flawless_theme_ui' , 'options_header_search' ),
          'toggle_label' => __( 'Do not show search box in header.', 'flawless' )
        ),
        'logo' => array(
          'label' => __( 'Header Logo' , 'flawless' ),
          'id' => 'options_header_logo',
          'name' => 'flawless_settings[disabled_features][header-logo]',
          'position' => 40,
          'setting' => $flawless[ 'disabled_features' ][ 'header-logo' ],
          'toggle_label' => __( 'Hide logo from header.', 'flawless' ),
          'callback' => array( 'flawless_theme_ui' , 'options_header_logo' )
        ),
        'dropdowns' => array(
          'label' => __( 'Header Dropdowns' , 'flawless' ),
          'id' => 'header-dropdowns',
          'name' => 'flawless_settings[disabled_features][header-dropdowns]',
          'position' => 50,
          'setting' => $flawless[ 'disabled_features' ][ 'header-dropdowns' ],
          'toggle_label' => __( 'Disable the header dropdown sections.', 'flawless' )
        )
      ) );

      //** Put the tabs into position */
      usort( $flawless[ 'options_ui' ][ 'header_elements' ], create_function( '$a,$b', ' return $a["position"] - $b["position"]; ' ) );

      //** Check if sections have advanced configuration menus */
      foreach( $flawless[ 'options_ui' ][ 'header_elements' ] as $tab_id => $tab ) {
        if( is_callable( $tab[ 'callback' ] ) )  {
          $header_panels[$tab_id] = $tab;
        }
      }

    ?>

    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th><?php _e( 'Header Elements', 'flawless' ); ?></th>
          <td>
            <ul>
             <?php foreach( $flawless[ 'options_ui' ][ 'header_elements' ] as $tab ) {  ?>
              <li>
                <label>
                  <input <?php echo checked( 'true', $tab[ 'setting' ] ); ?> type="checkbox" name="<?php echo $tab[ 'name' ]; ?>" class="sets_conditional" affects="<?php echo $tab[ 'callback' ][1]; ?>"  value="true" />
                  <?php printf( __( 'Disable %1s.', 'wpp' ), $tab[ 'label' ] ); ?>
                </label>
              </li>
            <?php } ?>
            </ul>
          </td>
        </tr>

        <tr valign="top">
          <th><?php _e( 'Mobile Navbar', 'flawless' ); ?></th>
          <td>
            <ul>
              <li>
                <label>
                  <input <?php echo checked( 'true', $flawless[ 'mobile' ][ 'use_mobile_navbar'] ); ?> type="checkbox" name="flawless_settings[mobile][use_mobile_navbar]" class="sets_conditional" affects="use_mobile_navbar"  show_on="enable" value="true" />
                  <?php _e( 'Use custom Navbar for mobile devices.', 'flawless' ); ?>
                </label>
              </li>
            </ul>
          </td>
        </tr>


        <tr>
          <th></th>
          <td>
            <div class="flawless_tabs flawless_section_specific_tabs">

              <ul class="tabs">
               <?php foreach( (array) $header_panels as $tab ) {  ?>
                <li class="conditional_dependency" required_condition="<?php echo $tab[ 'callback' ][1]; ?>">
                  <a href="#flawless_header_tab_<?php echo $tab[ 'id' ]; ?>"><?php echo $tab[ 'label' ]; ?></a>
                </li>
              <?php } ?>
                <li class="conditional_dependency" required_condition="use_mobile_navbar">
                  <a href="#flawless_header_tab_mobile_navbar"><?php _e( 'Mobile Navbar', 'flawless' ); ?></a>
                </li>
              </ul>

              <?php foreach( (array) $header_panels as $tab ) {  ?>
              <div id="flawless_header_tab_<?php echo $tab[ 'id' ]; ?>" class="flawless_tab <?php echo $tab[ 'panel_class' ]; ?> conditional_dependency" required_condition="<?php echo $tab[ 'callback' ][1]; ?>">
              <?php call_user_func( $tab[ 'callback' ], $flawless ); ?>
              </div>

              <?php } ?>
              <div id="flawless_header_tab_mobile_navbar" class="flawless_tab flawless_header_tab_mobile_navbar conditional_dependency" required_condition="use_mobile_navbar">

                <div class="flawless_tab_description"><?php _e( 'A Navbar displayed only for mobile devices  When enabled, the standard Navbar will be hidden on mobile devices.' , 'flawless' ); ?></div>

                <ul>
                  <li>
                    <label><?php _e( 'Navbar Type:', 'flawless' ); ?>
                      <select name="flawless_settings[mobile_navbar][type]">
                        <option value="-1"> - </option>
                        <?php foreach( (array) $flawless['navbar_options'] as $navbar_type => $navbar_data ) { ?>
                          <option <?php selected( $navbar_type, $flawless[ 'mobile_navbar' ][ 'type' ] ); ?> value="<?php echo $navbar_type; ?>"><?php echo $navbar_data[ 'label' ]; ?></option>
                        <?php } ?>
                      </select>
                    </label>
                  </li>

                  <li>
                    <input type="hidden" name="flawless_settings[mobile_navbar][show_brand]" value="false" />
                    <label>
                      <input type="checkbox" name="flawless_settings[mobile_navbar][show_brand]" value="true" <?php checked( $flawless[ 'mobile_navbar' ][ 'show_brand' ], 'true' ); ?> />
                      <?php _e( 'Show your website\'s brand on far left side of the Navbar.', 'flawless' ); ?>
                    </label>
                  <li>

                  <li>
                    <input type="hidden" name="flawless_settings[mobile_navbar][show_login]" value="false" />
                    <label>
                      <input type="checkbox" name="flawless_settings[mobile_navbar][show_login]" value="true" <?php checked( $flawless[ 'mobile_navbar' ][ 'show_login' ], 'true' ); ?> />
                      <?php _e( 'Show user login on the far right side of the Navbar.', 'flawless' ); ?>
                    </label>
                  <li>

                </ul>

                <?php do_action( 'flawless_options::mobile_navbar', $flawless ); ?>

              </div>

            </div>
          </td>
        </tr>


        <tr valign="top">
          <th><?php _e( 'Top Navigation', 'flawless' ); ?></th>
          <td>
            <ul>
              <li>
                <input <?php echo checked( 'true', $flawless[ 'menus' ][ 'header-menu' ][ 'show_descriptions' ] ); ?> id="flawless_top_navigation_show_descriptions" type="checkbox" name="flawless_settings[menus][header-menu][show_descriptions]" value="true" />
                <label for="flawless_top_navigation_show_descriptions"><?php _e( 'Show menu item descriptions below the titles in the top navigation.', 'wpp' ); ?></label>
              </li>
            </ul>
          </td>
        </tr>

      </tbody>
    </table>

    <?php }

  /**
   * Footer element configuration.
   *
   * This tab has two sections - the first displays a list of checkboxes expandable by the flawless_option_header_elements filter.
   * The checkboxes can enable or disable more advanced configuration settings which are rendered in the second major section
   * using tabs.
   *
   *
   * @since Flawless 0.2.3
   */
    function options_ui_footer( $flawless ) {

      $flawless[ 'options_ui' ][ 'footer_elements' ] = apply_filters( 'flawless_option_footer_elements', array(
        'search' => array(
          'label' => __( 'Copyright' , 'flawless' ),
          'id' => 'footer-copyright',
          'name' => 'flawless_settings[disabled_features][footer-copyright]',
          'position' => 20,
          'setting' => $flawless[ 'disabled_features' ][ 'footer-copyright' ],
          'toggle_label' => __( 'Do not show copyright in footer.', 'flawless' ),
          'callback' => array( 'flawless_theme_ui' , 'options_footer_copyright' )
        )
      ) );

      //** Put the tabs into position */
      usort( $flawless[ 'options_ui' ][ 'footer_elements' ], create_function( '$a,$b', ' return $a["position"] - $b["position"]; ' ) );

      //** Check if sections have advanced configuration menus */
      foreach( $flawless[ 'options_ui' ][ 'footer_elements' ] as $tab_id => $tab ) {
        if( is_callable( $tab[ 'callback' ] ) )  {
          $header_panels[$tab_id] = $tab;
        }
      }

    ?>

    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th><?php _e( 'Footer Elements', 'flawless' ); ?></th>
          <td>
            <ul>
             <?php foreach( $flawless[ 'options_ui' ][ 'footer_elements' ] as $tab ) {  ?>
              <li>
                <input <?php echo checked( 'true', $tab[ 'setting' ] ); ?> id="flawless_header_<?php echo $tab[ 'id' ]; ?>" type="checkbox" name="<?php echo $tab[ 'name' ]; ?>" class="sets_conditional" affects="<?php echo $tab[ 'callback' ][1]; ?>"  valule="on" />
                <label for="flawless_header_<?php echo $tab[ 'id' ]; ?>"><?php printf( __( 'Disable %1s.', 'wpp' ), $tab[ 'label' ] ); ?></label>
              </li>
            <?php } ?>
            </ul>
          </td>
        </tr>

        <?php if( is_array( $header_panels ) ) { ?>
        <tr>
          <th></th>
          <td>
            <div class="flawless_tabs flawless_section_specific_tabs">

              <ul class="tabs">
               <?php foreach( $header_panels as $tab ) {  ?>
                <li class="conditional_dependency" required_condition="<?php echo $tab[ 'callback' ][1]; ?>">
                  <a href="#flawless_header_tab_<?php echo $tab[ 'id' ]; ?>"><?php echo $tab[ 'label' ]; ?></a>
                </li>
              <?php } ?>
              </ul>

              <?php foreach( $header_panels as $tab ) {  ?>
              <div id="flawless_header_tab_<?php echo $tab[ 'id' ]; ?>" class="flawless_tab <?php echo $tab[ 'panel_class' ]; ?> conditional_dependency" required_condition="<?php echo $tab[ 'callback' ][1]; ?>">
              <?php call_user_func( $tab[ 'callback' ], $flawless ); ?>
              </div>
              <?php } ?>

            </div>
          </td>
        </tr>
        <?php } ?>

        <tr valign="top">
          <th><?php _e( 'Footer Navigation', 'flawless' ); ?></th>
          <td>
            <ul>
              <li>
                <input <?php echo checked( 'true', $flawless[ 'menus' ][ 'footer-menu' ][ 'show_descriptions' ] ); ?> id="flawless_footer_navigation_show_descriptions" type="checkbox" name="flawless_settings[menus][footer-menu][show_descriptions]" value="true" />
                <label for="flawless_footer_navigation_show_descriptions"><?php _e( 'Show menu item descriptions below the titles in the footer navigation.', 'wpp' ); ?></label>
              </li>
            </ul>
          </td>
        </tr>

      </tbody>
    </table>

    <?php }

    function options_footer_copyright( $flawless ) { ?>

    <label for="footer_copyright"><?php _e( 'Copyright content to be displayed at the bottom of all pages:', 'flawless' ); ?></label>
    <textarea id="footer_copyright" class="large-text" name="flawless_settings[footer][copyright]" ><?php echo $flawless[ 'footer' ][ 'copyright' ]; ?></textarea>
    <div class="description"><?php _e( 'Shortcodes can be used here. Useful shortcodes: [current_year], [site_description]. ', 'flawless' ); ?></div>

    <?php

    }


    function options_header_navbar( $flawless ) {


    ?>

    <div class="flawless_tab_description"><?php _e( 'A Navbar is displayed at the very top of your site.  If a custom Mobile Navbar is setup, this Navbar will not be displayed on mobile devices.' , 'flawless' ); ?></div>

    <ul>
      <li>
        <label><?php _e( 'Navbar Type:', 'flawless' ); ?>
          <select name="flawless_settings[navbar][type]">
            <option value="-1"> - </option>
            <?php foreach( (array) $flawless['navbar_options'] as $navbar_type => $navbar_data ) { ?>
              <option <?php selected( $navbar_type, $flawless[ 'navbar' ][ 'type' ] ); ?> value="<?php echo $navbar_type; ?>"><?php echo $navbar_data[ 'label' ]; ?></option>
            <?php } ?>
          </select>
        </label>
      </li>

      <li>
        <input type="hidden" name="flawless_settings[navbar][show_brand]" value="false" />
        <label>
          <input type="checkbox" name="flawless_settings[navbar][show_brand]" value="true" <?php checked( $flawless[ 'navbar' ][ 'show_brand' ], 'true' ); ?> />
          <?php _e( 'Show your website\'s brand on far left side of the Navbar.', 'flawless' ); ?>
        </label>
      <li>

      <li>
        <input type="hidden" name="flawless_settings[navbar][show_login]" value="false" />
        <label>
          <input type="checkbox" name="flawless_settings[navbar][show_login]" value="true" <?php checked( $flawless[ 'navbar' ][ 'show_login' ], 'true' ); ?> />
          <?php _e( 'Show user login on the far right side of the Navbar.', 'flawless' ); ?>
        </label>
      <li>

      <li>
        <input type="hidden" name="flawless_settings[navbar][collapse]" value="false" />
        <label>
          <input type="checkbox" name="flawless_settings[navbar][collapse]" value="true" <?php checked( $flawless[ 'navbar' ][ 'collapse' ], 'true' ); ?> />
          <?php _e( 'Show a menu expansion button when it is collapsed on small screens.', 'flawless' ); ?>
        </label>
      <li>

    </ul>

    <?php do_action( 'flawless_options::navbar', $flawless ); ?>

    <?php }


    function options_header_search( $flawless ) {  ?>

    <ul>
      <li>
        <input type="hidden" name="flawless_settings[header][must_enter_search_term]" value="false" />
        <input type="checkbox" id="must_enter_search_term" name="flawless_settings[header][must_enter_search_term]" value="true" <?php checked( $flawless[ 'header' ][ 'must_enter_search_term' ], 'true' ); ?> />
        <label for="must_enter_search_term"><?php _e( 'Users must enter a search term for search form to work.', 'flawless' ); ?></label>
      <li>
    </ul>

    <?php }

    function options_header_logo( $flawless ) {  ?>

    <ul class="flawless_logo_upload">

      <?php if( !empty( $flawless[ 'flawless_logo' ][ 'url' ] ) ) { ?>
      <li class="current_flawless_logo">
        <input type="hidden" name="flawless_settings[flawless_logo][post_id]" value="<?php echo  $flawless[ 'flawless_logo' ][ 'post_id' ]; ?>" />
        <input type="hidden" name="flawless_settings[flawless_logo][url]" value="<?php echo  $flawless[ 'flawless_logo' ][ 'url' ]; ?>" />
        <input type="hidden" name="flawless_settings[flawless_logo][width]" value="<?php echo  $flawless[ 'flawless_logo' ][ 'width' ]; ?>" />
        <input type="hidden" name="flawless_settings[flawless_logo][height]" value="<?php echo  $flawless[ 'flawless_logo' ][ 'height' ]; ?>" />

        <?php if( flawless_theme::can_get_image( $flawless[ 'flawless_logo' ][ 'url' ] ) ) {
            echo '<img src="' . $flawless[ 'flawless_logo' ][ 'url' ] . '" class="flawless_logo" />';
          } else { ?>
        <div class="flawless_asset_missing flawless_logo"><?php printf( __( 'Warning: Logo ( %1s ) Not Found', 'flawless' ), $flawless[ 'flawless_logo' ][ 'url' ] ); ?></div>
        <?php } ?>

        <div class="flawless_delete_logo"><span class="button "><?php _e( 'Delete Logo', 'flawless' ); ?></span></div>

      </li>
      <?php } ?>

      <li class="upload_new_logo">
        <label for="flawless_text_logo"><?php _e( 'To upload new logo, choose an image from your computer:', 'flawless' ); ?></label>
        <input id="flawless_text_logo" type="file" name="flawless_logo" />
      <li>
    </ul>

    <?php }

  /**
    * Advanced Options Page
    *
    * @todo 'Reset Flexible Layout' should be inserted via Editor API
    * @since Flawless 0.2.3
    */
  function options_ui_advanced( $flawless ) {

    $current_theme = get_theme( get_current_theme() );

    //** Determine if current theme is a child theme theme */
    if( $current_theme[ 'Template Dir' ] != $current_theme[ 'Stylesheet Dir' ] ) {
      $child_theme_screen =  trailingslashit( get_stylesheet_directory_uri() ) . $current_theme[ 'Screenshot' ];
    } else {
      $child_theme_screen = false;
    }

  ?>

  <table class="form-table">
    <tbody>
    <tr>
    <th><?php _e( 'General Settings' , 'flawless' ); ?></th>
      <td>
        <ul>
          <li>
            <label>
              <input type="checkbox"<?php checked( $flawless[ 'maintanance_mode' ], 'true' ); ?>name="flawless_settings[maintanance_mode]" value="true" />
              <?php _e( 'Put site into maintanance mode.', 'flawless' ); ?>
            </label>
            <div class="description"><?php _e( 'Maintanance mode will display a splash image on front-end for non-administrators while you make changes.', 'flawless' ); ?></div>
          </li>

          <li>
            <label>
              <input type="checkbox" <?php checked( $flawless[ 'developer_mode' ], 'true' ); ?> name="flawless_settings[developer_mode]" value="true" />
              <?php _e( 'Enable developer and debug mode.', 'flawless' ); ?>
            </label>
          </li>

          <li>
            <label>
              <input type="checkbox" <?php checked( $flawless[ 'disable_updates' ][ 'plugins' ], 'true' ); ?> name="flawless_settings[disable_updates][plugins]" value="true" />
              <?php _e( 'Disable WordPress plugin update notifications.', 'flawless' ); ?>
            </label>
          </li>

        </ul>
      </td>
    </tr>

    <tr valign="top" class="color_schemes">
      <th><label for="name"><?php _e( 'Color Scheme', 'flawless' ); ?><label></th>
      <td>

        <ul class="flawless_color_schemes block_options">

         <?php if( $child_theme_screen ) { ?>
          <li class="flawless_setup_option_block required_option">
            <div class="skin_thumb_placeholder">
              <img class="skin_thumb" src="<?php echo $child_theme_screen; ?>" title="<?php _e( 'Child Theme ( Required When Active )', 'flawless' ); ?>" />
            </div>
            <div class="option_note"><?php _e( 'Child Theme ( Required When Active )', 'flawless' ); ?></div>
          </li>
         <?php } ?>

      <?php if( $color_schemes = flawless_theme::get_color_schemes() ) { ?>

        <?php foreach( ( array ) $color_schemes as $scheme => $scheme_data ) { ?>
          <li class="flawless_setup_option_block">
            <?php if( $scheme_data[ 'thumb_url' ] ) { ?>
            <div class="skin_thumb_placeholder">
              <img class="skin_thumb" src="<?php echo $scheme_data[ 'thumb_url' ]; ?>" title="<?php echo esc_attr( $scheme_data[ 'name' ] ); ?>" />
            </div>
            <?php } ?>
            <input group="flawless_color_scheme" <?php echo checked( $scheme, $flawless[ 'color_scheme' ] ); ?> type="checkbox" name="flawless_settings[color_scheme]" id="color_scheme_<?php echo $scheme; ?>"  value="<?php echo $scheme; ?>" />
            <div class="option_note"><?php echo $scheme_data[ 'name' ]; ?> - <?php echo $scheme_data[ 'description' ]; ?></div>
          </li>
        <?php } ?>
          <li class="flawless_setup_option_block">
            <div class="skin_thumb_placeholder"></div>
            <input group="flawless_color_scheme" <?php echo checked( false, $flawless[ 'color_scheme' ] ); ?> type="checkbox" name="flawless_settings[color_scheme]" id="color_scheme_<?php echo $scheme; ?>"  value="" />
            <div class="option_note"><?php _e( 'No Skin - base CSS only.', 'flawless' ); ?></div>
          </li>

        <?php } ?>

        </ul>

      </td>
    </tr>

    <tr>
      <th><?php _e( 'Backup and Restoration' , 'flawless' ); ?></th>
      <td>
        <ul class="flawless" flawless="action_list">
          <li>
            <a class="button" href="<?php echo wp_nonce_url( "themes.php?page=functions.php&flawless_action=download-backup", 'download-flawless-backup' ); ?>">
              <?php _e( 'Download Configuration Backup', 'flawless' );?>
            </a>
            <span class="description"><?php _e( 'Export the entire configuration into a .json file, which may be restored to this site, or another.', 'flawless' ); ?></span>
          </li>
          <li>
            <?php _e( 'Restore from file', 'wpp' ); ?>: <input name="flawless_settings[settings_from_backup]" type="file" />
            <span class="description"><?php _e( 'Backup will overwrite all current settings.', 'flawless' ); ?></span>
          </li>
        </ul>
      </td>
    </tr>

    <tr>
      <th><?php _e( 'Advanced Actions' , 'flawless' ); ?></th>
      <td>
        <ul class="flawless" flawless="action_list">

          <li flawless_action="delete_flex_settings" class="flawless_action" processing_label="<?php _e( 'Processing...', 'flawless' ); ?>" verify_action="<?php _e( 'Are you sure?', 'flawless' ); ?>">
            <span class="button execute_action"><?php _e( 'Reset Flexible Layout', 'flawless' ); ?></span>
            <span class="description"><?php _e( 'Delete all flexible layout ( header and footer ) settings and reset to default.', 'flawless' ); ?></span>
          </li>

          <li flawless_action="delete_all_settings" class="flawless_action" processing_label="<?php _e( 'Processing...', 'flawless' ); ?>" verify_action="<?php _e( 'You are about to delete all theme settings, are you sure?', 'flawless' ); ?>">
            <span class="button execute_action"><?php _e( 'Delete all Theme Settings', 'flawless' ); ?></span>
            <span class="description"><?php _e( 'Completely remove all Flawless Theme settings and reset to default.', 'flawless' ); ?></span>
          </li>

          <li>
            <span class="flawless_link button" flawless_action="ui" toggle=".flawless_debug.pre_flawless_settings"><?php _e( 'Toggle Flawless Settings', 'flawless' ); ?></span>
            <span class="description"><?php _e( 'Display $flawless variable, for debugging purposes.', 'flawless' ); ?></span>
          </li>

        </ul>

      </td>
    </tr>

    <tr valign="top" class="<?php echo $flawless[ 'developer_mode' ] == 'true' ? '' : 'hidden'; ?> flawless_debug pre_flawless_settings">
      <th><?php _e( 'Debugging', 'flawless' ); ?></th>
      <td>
        <ul>
        <li>
        <pre class="flawless_class_pre"><?php echo htmlspecialchars( print_r( $flawless, true ) ); ?></pre>
        </li>
        </ul>
      </td>
    </tr>

    </tbody>
  </table>


  <?php
  }


  /**
    * Render widget area item
    *
    * @todo 'Reset Flexible Layout' should be inserted via Editor API
    * @since Flawless 0.2.3
    */
  function flawless_widget_item( $args = false ) {
    global $flawless;

    $sidebar_id = $args[ 'sidebar_id' ];

    $description = $flawless[ 'widget_areas' ][ 'all' ][$sidebar_id][ 'description' ] ? $flawless[ 'widget_areas' ][ 'all' ][$sidebar_id][ 'description' ] : '';
    $sidebar_data = $flawless[ 'widget_areas' ][ 'all' ][$sidebar_id];

    $sidebar_data[ 'name' ] = $sidebar_data[ 'name' ] ? $sidebar_data[ 'name' ] : __( 'Missing Title: ', 'flawless' ) . $sidebar_id;

    $classes = array( 'flawless_widget_item' );

    if( $args[ 'sidebar_data' ][ 'flawless_widget_area' ] ) {
      $classes[] = 'flawless_widget_area';
    }

    if( !empty( $sidebar_data[ 'description' ] ) ) {
      $classes[] = 'have_description';
    }

    ?>

    <li class="<?php echo implode( ' ', ( array ) $classes ); ?>" sidebar_name="<?php echo $sidebar_id; ?>" do_not_clone="true" flawless_widget_area="<?php echo $args[ 'sidebar_data' ][ 'flawless_widget_area' ] ? 'true' : 'false'; ?>">
      <div class="handle"></div>

      <?php if( $args[ 'sidebar_data' ][ 'flawless_widget_area' ] == 'true' ) { ?>

      <input type="text" name="flawless_settings[flawless_widget_areas][<?php echo $sidebar_id; ?>][label]" class="flawless_wa" attribute="name" value="<?php echo $sidebar_data[ 'name' ] ?>" />
      <input type="hidden" name="flawless_settings[flawless_widget_areas][<?php echo $sidebar_id; ?>][class]"  class="flawless_wa" attribute="class" value="<?php echo $sidebar_data[ 'class' ]; ?>" />

      <?php } else { ?>

      <input type="text" class="flawless_wa" attribute="name" value="<?php echo $sidebar_data[ 'name' ] ?>" readonly="true" />
      <div class="flawless_wa" attribute="description"><?php echo $sidebar_data[ 'description' ]; ?></div>

      <?php } ?>

      <?php if( isset( $args[ 'post_type' ] ) ) { ?>
      <input do_not_clone="true"  type="hidden" name="flawless_settings[post_types][<?php echo $args[ 'post_type' ]; ?>][widget_areas][<?php echo $args[ 'was_slug' ]; ?>][]" value="<?php echo $sidebar_id; ?>" />
      <?php } ?>

      <?php if( isset( $args[ 'taxonomy_type' ] ) ) { ?>
      <input do_not_clone="true"  type="hidden" name="flawless_settings[taxonomies][<?php echo $args[ 'taxonomy_type' ]; ?>][widget_areas][<?php echo $args[ 'was_slug' ]; ?>][]" value="<?php echo $sidebar_id; ?>" />
      <?php } ?>

      <div class="delete" <?php echo $args[ 'widget_area_selector' ] ? 'verify_action="Are you sure? You cannot undo this."' : ''; ?>></div>
    </li>

    <?php

  }

}






