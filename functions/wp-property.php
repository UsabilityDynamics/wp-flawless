<?php

if(!class_exists('WPP_F')) {
  return;
}


/**
  * Name: WP-Property Extensions
  * Description: Extra functionality for WP-Property elements.
  * Author: Usability Dynamics, Inc.
  * Version: 1.0
  *
  */


add_action('flawless_theme_setup', array('flawless_wpp_extensions', 'flawless_theme_setup'));

class flawless_wpp_extensions {

  /**
    * {missing description}
    *
    * @since Flawless 0.2.3
    */
  function flawless_theme_setup() {

    add_theme_support( 'header-property-search' );

    add_filter('flawless_exclude_sidebar', array('flawless_wpp_extensions', 'flawless_exclude_sidebar'), 10, 2);
    add_action('flawless_options_ui_header_elements_elements', array('flawless_wpp_extensions', 'flawless_options_ui_header_elements'));

    add_action('flawless::init_lower', array('flawless_wpp_extensions', 'flawless_init'));

    add_action('wpp_admin_tools_property_type_options', array('flawless_wpp_extensions', 'wpp_admin_tools_property_type_options'));
    add_action('flawless::breadcrumb_trail', array('flawless_wpp_extensions', 'breadcrumb_trail'));
    add_filter( 'nav_menu_css_class', array('flawless_wpp_extensions','nav_menu_css_class' ), 10, 3);

    //add_filter('flawless_option_tabs', array('flawless_wpp_extensions', 'option_tabs'));
    //add_action('flawless_widgets_init', array('flawless_wpp_extensions', 'flawless_widgets_init'));

  }


  /**
   * Add menu classes to menu ancestors of the current property when a property type landing page is set (Flawless Feature)
   *
   * @since Flawless 0.2.3
   *
   */
  static function nav_menu_css_class($classes, $item, $args) {
    global $wpdb, $post, $wp_properties, $property;

    if(!$property || !$wp_properties['extra']['property_type_landing_pages'][$post->property_type]) {
      return $classes;
    }

    //** Check if the currently rendered item is a child of this link */
    if($item->object_id == $wp_properties['extra']['property_type_landing_pages'][$post->property_type]) {
      $classes[] = 'current-page-ancestor current-menu-ancestor current-menu-parent current-page-parent current_page_parent flawless_ad_hoc_menu_parent';
    }

    return $classes;

  }


  /**
    * Modify breadcrumb trail for WPP Objects
    *
    * @since Flawless 0.2.3
    */
  function breadcrumb_trail( $html ) {
    global $post, $wp_properties;

    if($post->post_type != 'property') {
      return $html;
    }

    if(empty($post->property_type) || empty($wp_properties['extra']['property_type_landing_pages'][$post->property_type])) {
      return $html;
    }

    $landing_page_id = $wp_properties['extra']['property_type_landing_pages'][$post->property_type];

    $url = get_permalink($landing_page_id);
    $title = get_the_title($landing_page_id);


    $html['content_type_home'] = '<a href="' . $url . '">' . $title . '</a>';

    return $html;



  }



  /**
    * Add option to Developer Tools to select a Landing Page for a property type
    *
    * @since Flawless 0.2.3
    */
  function wpp_admin_tools_property_type_options( $property_type ) {
    global $wp_properties;

    echo '<label>' . __('Landing page:');

    flawless_theme::wp_dropdown_objects( array(
      'name' => 'wpp_settings[extra][property_type_landing_pages][' . $property_type . ']',
      'show_option_none' => __( '&mdash; Select &mdash;' ),
      'option_none_value' => '0',
      'post_type' => get_post_types( array( 'hierarchical' => true ) ),
      'selected' => $wp_properties['extra']['property_type_landing_pages'][$property_type]
    ));

    echo '</label>';

  }




  /**
    * Hook into set_current_view() and manually exclude property-type specific sidebars
    *
    * Loaded before WPP loads values into $property
    *
    * @since Flawless 0.2.3
    */
  function flawless_exclude_sidebar( $default, $sidebar_id ) {
    global $post, $property;

    if($post->post_type != 'property') {
      return $default;
    }

    $property_type = get_post_meta($post->ID, 'property_type', true);

    if(strpos($sidebar_id, 'pp_sidebar_')) {
      if($sidebar_id != 'wpp_sidebar_' . $property_type) {
        return true;
      }

    }

  }



  /**
    * Runs template_redirect for WPP pages.
    *
    * Disabled for now, need to identify exactly when it has to be ran to avoid running template_redirect on non-WPP pages
    *
    * @since Flawless 0.2.3
    */
  function template_redirect() {

    //flawless_theme::template_redirect();

  }


  function flawless_init() {

    //** Must force WPP to run the template redirect of theme, otherwise it'll ignore it and not load styles */
    add_action('wpp_template_redirect_post_scripts', array('flawless_wpp_extensions', 'template_redirect'));

    add_filter('wpp_property_page_vars', array('flawless_wpp_extensions', 'wpp_page_vars'));
    add_filter('wpp_overview_page_vars', array('flawless_wpp_extensions', 'wpp_page_vars'));
    add_filter('wpp_overview_shortcode_vars', array('flawless_wpp_extensions', 'wpp_page_vars'));

    add_filter('flawless_sidebar_data', array('flawless_wpp_extensions', 'flawless_sidebar_data'));

  }



  /**
    * Add information about property-specific sidebars
    *
    * Loaded into query_vars on overview and single property pages via 'wpp_overview_page_vars' and 'wpp_property_page_vars' hooks
    *
    * @since Flawless 0.2.3
    */
  function flawless_sidebar_data($data) {
    global $post, $property;

    if($post->post_type != 'property') {
      return $data;
    }

    if($data['requested_widget_area_type'] == 'right_sidebar') {
    flawless_theme::console_log('doing wpp' . $data['requested_widget_area_type']);
      $data = array(
        'sidebar_id' => 'wpp_sidebar_' . $property['property_type'] ,
        'class' => 'right-sidebar wpp-right-sidebar',
        'widget_area_type' => 'right_sidebar'
      );
    }


    return $data;

  }


  /**
    * Load denali specific vars into query_vars to be used in templates.
    *
    * Loaded into query_vars on overview and single property pages via 'wpp_overview_page_vars' and 'wpp_property_page_vars' hooks
    *
    * @since Flawless 0.2.3
    */
   static function wpp_page_vars($current) {
    global $flawless, $page;

    //** Load denali global settings on all WPP pages */
    $current['flawless_settings'] = $flawless;
    $current['page'] = $page;
    $current['paged'] = $paged;

    return $current;

   }


  /**
    * {missing description}
    *
    * @since Flawless 0.2.3
    */
  function flawless_options_ui_header_elements($flawless) { ?>
    <li class="conditional_dependency" required_condition="header_property_search">
      <input <?php echo checked('true', $flawless['break_out_global_property_search_areas']); ?> type="checkbox"  name='flawless_settings[break_out_global_property_search_areas]' id="break_out_global_property_search_areas"  value="true" />
      <label for="break_out_global_property_search_areas">
        <?php _e('Create separate widget area for the header property search. The widget is called <b>Header: Property Search</b>.', 'wpp'); ?>
      </label>
    </li>
  <?php

  }


  /**
    * {missing description}
    *
    * @since Flawless 0.2.3
    */
  function option_tabs($tabs) {
    global $wp_properties;

    if(!$wp_properties) {
      return $tabs;
    }

    $tabs['options_ui_footer'] = array(
      'label' => __('Inquiry','wpp'),
      'callback' => array('flawless_wpp_extensions','options_ui_inquiry')
    );

    $tabs['options_ui_footer'] = array(
      'label' => __('Property Data','wpp'),
      'callback' => array('flawless_wpp_extensions','options_ui_property_display')
    );

    return $tabs;

  }


  /**
    * {missing description}
    *
    * @since Flawless 0.2.3
    */
  function options_ui_property_display($flawless) {
    global $wp_properties;

    $wp_properties['property_meta']['post_content'] = 'Property Content';

    ?>

  <table class="form-table">
  <tbody>
  <tr>
    <th><?php _e('Overview Attributes'); ?>
    <div class="description"><?php _e('Select the attributes to display in the [property_overview] shortcodes.', 'wpp'); ?></div>
    </th>
    <td>
      <div class="alignleft">
      <b>Horizontal List - does not display titles, just the values with an icon, if one exists</b>
      <div class="wp-tab-panel">
      <ul>
      <?php foreach($wp_properties['property_meta'] as $attrib_slug => $attrib_title): ?>
        <li><?php echo Flawless_UI::checkbox("id=property_overview_attributes_{$attrib_title}_stats&name=flawless_settings[property_overview_attributes][stats][]&label=$attrib_title&value={$attrib_slug}", (is_array($flawless['property_overview_attributes']['stats']) && in_array($attrib_slug, (array)$flawless['property_overview_attributes']['stats']) ? true : false)); ?></li>
      <?php endforeach; ?>
      <?php foreach($wp_properties['property_stats'] as $attrib_slug => $attrib_title): ?>
        <li><?php echo Flawless_UI::checkbox("id=property_overview_attributes_{$attrib_title}_stats&name=flawless_settings[property_overview_attributes][stats][]&label=$attrib_title&value={$attrib_slug}", (is_array($flawless['property_overview_attributes']['stats']) && in_array($attrib_slug, (array)$flawless['property_overview_attributes']['stats']) ? true : false)); ?></li>
      <?php endforeach; ?>

      </ul>
      </div>
      </div>

      <div class="alignright">
      <b>Detailed list below the horizontal list, includes titles and values.</b>
      <div class="wp-tab-panel">
      <ul>
      <?php foreach($wp_properties['property_stats'] as $attrib_slug => $attrib_title): ?>
        <li><?php echo Flawless_UI::checkbox("id=property_overview_attributes_{$attrib_title}_detail&name=flawless_settings[property_overview_attributes][detail][]&label=$attrib_title&value={$attrib_slug}", (is_array($flawless['property_overview_attributes']['detail']) && in_array($attrib_slug, (array)$flawless['property_overview_attributes']['detail']) ? true : false)); ?></li>
      <?php endforeach; ?>
      <?php foreach($wp_properties['property_meta'] as $attrib_slug => $attrib_title): ?>
        <li><?php echo Flawless_UI::checkbox("id=property_overview_attributes_{$attrib_title}_detail&name=flawless_settings[property_overview_attributes][detail][]&label=$attrib_title&value={$attrib_slug}", (is_array($flawless['property_overview_attributes']['stats']) && in_array($attrib_slug, (array)$flawless['property_overview_attributes']['detail']) ? true : false)); ?></li>
      <?php endforeach; ?>

      </ul>
      </div>
      </div>

    </td>
  </tr>

  <tr>
    <th><?php _e('Overview Attributes - Grid'); ?>
    <div class="description"><?php _e('Select the attributes to display in the [property_overview template=grid] shortcodes.'); ?></div>
    </th>
    <td>
       <div class="wp-tab-panel">
      <ul>
      <?php foreach($wp_properties['property_stats'] as $attrib_slug => $attrib_title): ?>
        <li><?php echo Flawless_UI::checkbox("id=property_overview_attributes_grid_{$attrib_title}_stats&name=flawless_settings[grid_property_overview_attributes][stats][]&label=$attrib_title&value={$attrib_slug}", (is_array($flawless['grid_property_overview_attributes']['stats']) && in_array($attrib_slug, (array)$flawless['grid_property_overview_attributes']['stats']) ? true : false)); ?></li>
      <?php endforeach; ?>
      <?php foreach($wp_properties['property_meta'] as $attrib_slug => $attrib_title): ?>
        <li><?php echo Flawless_UI::checkbox("id=property_overview_attributes_grid_{$attrib_title}_stats&name=flawless_settings[property_overview_attributes][stats][]&label=$attrib_title&value={$attrib_slug}", (is_array($flawless['property_overview_attributes']['stats']) && in_array($attrib_slug, (array)$flawless['grid_property_overview_attributes']['stats']) ? true : false)); ?></li>
      <?php endforeach; ?>

      </ul>
      </div>
      </td>
    </tr>



    <tr>
      <th>
        <?php _e('1) Attention Area Widgets', 'wpp'); ?>
        <div class="description"><?php _e('Property Type-specific widget area is first checked. If widgets found, they will be displayed.', 'wpp'); ?></div>
      </th>
      <td>

      <div class="options_page_message">
        <p><?php _e('The attention grabber area is displayed above the property-specific content on the property pages. Flawless checks several settings to determine what to display within the attention grabbing area of a property. '); ?></p>
      </div>

        <p><?php _e('A custom Attention Grabber widget area is made available for every property type.','wpp'); ?></p>
        <ul>
        <?php //** Check if custom widget areas exist */
        foreach($property_types as $property_type => $property_title) {

          if($tabs = flawless_theme::widget_area_tabs("wpp_header_{$property_type}")) {

            if(count($tabs) > 1) {
              echo '<li>' . sprintf(__('%1s has %2s widgets in the attention grabber area which will be displayed as dynamic tabs.', 'wpp'), '<b>' . $property_title . '</b>', count($tabs)) . '</li>';
            } else {
              echo '<li>' . sprintf(__('%1s has one widget in the attention grabber area which will be displayed.', 'wpp'), '<b>' . $property_title . '</b>') . '</li>';
            }

          } else {
            echo '<li>' . sprintf(__('%1s does not have any widgets in the attention grabber area, a slideshow and featured image will be checked for.','wpp'),  '<b>' . $property_title . '</b>') . '</li>';
          }

        }
        ?>
        </ul>
        <span class="description"><?php _e('To select which widgets to display in this section, visit the Widgets page. ','wpp'); ?></span>

      </td>
    </tr>


    <tr valign="top">
      <th>
        <?php _e('2) Property Slideshow', 'wpp'); ?>
        <div class="description"><?php _e('If no widgets exist in widget area for a property type, we will attempt to display a slideshow.', 'wpp'); ?></div>
      </th>
      <td>
        <p><?php _e('In order for a slideshow to be displayed, the images attached to the property must be of a certain size - WordPress will never enlarge your images to avoid pixelation. ','wpp'); ?></p>
        <ul class="wpp_something_advanced_wrapper">

         <li>
            <input toggle_logic="reverse" class="wpp_show_advanced"  <?php echo checked('true', $flawless['never_show_property_slideshow']); ?> type="checkbox"  name='flawless_settings[never_show_property_slideshow]' id="never_show_property_slideshow"  value="true" />
            <label for="never_show_property_slideshow"><?php _e("Never display a slideshow on property pages.", 'wpp'); ?></label>
          </li>

          <li class="<?php echo $flawless['never_show_property_slideshow'] == 'true' ? 'hidden' : '' ?> wpp_development_advanced_option">
            <?php
            $slideshow_size = $wp_properties['configuration']['feature_settings']['slideshow']['property']['image_size'];
            $image_dimensions = WPP_F::image_sizes($slideshow_size);
            ?>
            <?php printf(__('Property Slideshow Size: %1dpx by %1dpx.', 'wpp'), $image_dimensions['width'], $image_dimensions['height'] ); ?>
            <span class="description"><?php _e('The slideshow size is set on the the Properties -> Settings -> Slideshow page.','wpp'); ?></span>
          </li>

        </ul>

      </td>
    </tr>


    <tr valign="top">
      <th>
        <?php _e('3) Featured Image in Header', 'wpp'); ?>
        <div class="description"><?php _e('If neither an Attention Grabber widget area, nor the slideshow, can be displayed - the featured image can be displayed.', 'wpp'); ?></div>
        </th>
      <td>
        <ul>
        <li>
        <?php WPP_F::image_sizes_dropdown("blank_selection_label= No Static Header Image &name=flawless_settings[property_static_image_size]&selected={$flawless['property_static_image_size']}"); ?>

        </li>
        <li>
          <input <?php echo checked('true', $flawless['hide_single_page_header_if_image_too_small']); ?> type="checkbox"  name='flawless_settings[hide_single_page_header_if_image_too_small]' id="hide_single_page_header_if_image_too_small"  value="true" />
          <label for="hide_single_page_header_if_image_too_small"><?php _e("If the image size you selected above does not exist, and there is no slidshow, do not show header area at all on property pages.", 'wpp'); ?></label>
        </li>


        </ul>
        </li>
      </td>
      </tr>

      </tbody>
  </table>

  </div>

  <?php }


  /**
    * {missing description}
    *
    * @since Flawless 0.2.3
    */
    function options_ui_inquiry($flawless) {
      global $wp_properties;

      $property_types = $wp_properties['property_types'];

    ?>

    <table class="form-table">
    <tbody>
    <tr>
      <th></th>
      <td>
        <ul>
          <li>
              <input type='hidden' name='flawless_settings[show_property_comments]' value='false' /><input type='checkbox' id="show_property_comments" name='flawless_settings[show_property_comments]' value='true'  <?php if($flawless['show_property_comments'] == 'true') echo " CHECKED " ?>/>
              <label for="show_property_comments">Don't treat property comments as inquiries.</label>
              <br />
              <span class="description">If enabled, property comments will be displayed on front-end and handled as comments.  If left disabled, comments will be treated as inquiries. You can enable/disable comments on individual property pages.</span>

          </li>
        </ul>
      </td>

      </tr>
      <tr>
        <th>WP-CRM Inquiry Forms</th>
        <td>
        <?php

        if(!$wp_crm) {
          echo '<p>' . __('You can use WP-CRM to have more flexibility over the inquiry data and form customization. Install WP-CRM to be able to use it for property inquiries.', 'wpp') . '</p>';
        } elseif(!$shortcode_forms) {
          echo '<p>' . __('Please visit CRM -> Settings -> Shortcode Forms to add a contact form.', 'wpp') . '</p>';
        } else {  ?>

          <p>
          <?php _e('WP-CRM contact forms can be used to display property inquries. Please visit <b>CRM -> Settings -> Shortcode Forms</b> to add a more contact forms form.', 'wpp'); ?>
          </p>

          <table class="widefat">
          <?php foreach($property_types as $property_slug => $property_title) {

            if(isset($flawless['wp_crm']['inquiry_forms'][$property_slug])) {
              $selection = $flawless['wp_crm']['inquiry_forms'][$property_slug];
            } else {
              $selection = 'flawless_default_form';
            }

          ?>

            <tr>
              <th>
                <label for="flawless_settings_inquiry_crm_form_<?php echo $property_slug; ?>"><?php echo $property_title; ?></label>
              </th>
              <td>

            <select id="flawless_settings_inquiry_crm_form_<?php echo $property_slug; ?>" name="flawless_settings[wp_crm][inquiry_forms][<?php echo $property_slug; ?>]" class="flawless_settings_inquiry_crm_forms">
              <option></option>
              <option <?php selected($selection, 'flawless_default_form'); ?> value="flawless_default_form"><?php _e('Default Form', 'wpp'); ?></option>
            <?php foreach($shortcode_forms as  $form) {  ?>
              <option <?php selected($selection, $form['current_form_slug']); ?> value="<?php echo esc_attr($form['current_form_slug']); ?>"><?php echo $form['title']; ?> <?php echo (count($form['fields']) < 1 ? __('(No Fields)', 'wpp') : ''); ?></option>
            <?php } ?>
            </select>
          </td>


        <?php } ?>
        </table>
        <div class="description"><?php _e('Please visit <a target="_blank" href="http://usabilitydynamics.com/products/wp-crm/">Usability Dynamics, Inc.</a> to learn more about WP-CRM.', 'wpp'); ?></div>
        <?php } ?>


        </td>

      </tr>

      <tr class="flawless_default_inquiry_form_fields">
        <th><?php _e('Default Form Fields', 'wpp'); ?></th>
        <td>
        <p><?php _e('Add any additional input fields you would like to be displayed on the property inquiry forms. Name and e-mail address are required and already displayed.', 'wpp'); ?>

     <table class="ud_ui_dynamic_table widefat" id="wpp_d_inquiry_fields">
      <thead>
        <tr>
          <th><?php _e('Field Name'); ?></th>
          <th style="width:50px;">Slug</th>
          <th style="width:90px;">Required</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>

        <?php foreach($flawless['wpp_d_inquiry_fields'] as $field_slug => $field_data): $field_value = $field_data['name']; ?>
          <tr new_row="false" slug="<?php echo $field_slug; ?>" class="wpp_dynamic_table_row">
            <td><input type="text" value="<?php echo $field_value; ?>" name="flawless_settings[wpp_d_inquiry_fields][<?php echo $field_slug; ?>][name]" class="slug_setter"></td>
            <td><input type="text" class="slug" readonly="readonly" value="<?php echo $field_slug; ?>"></td>
            <td><input type="checkbox" value="on" name="flawless_settings[wpp_d_inquiry_fields][<?php echo $field_slug; ?>][required]" <?php checked('on', $field_data['required']); ?>></td>
            <td><span class="wpp_delete_row wpp_link"><?php _e('Delete'); ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      <tfoot>
        <tr>
          <td colspan="4"><input type="button" value="<?php _e('Add Row'); ?>" class="wpp_add_row button-secondary"></td>
        </tr>
      </tfoot>
    </table>

    <ul>
    <?php if(class_exists('class_agents')) { ?>
      <li>
        <input <?php checked('on', $flawless['wpp_d_show_agent_dropdown_on_inquiry']); ?> type="checkbox" name="flawless_settings[wpp_d_show_agent_dropdown_on_inquiry]" value="on" id="wpp_d_show_agent_dropdown_on_inquiry" />
        <label for="wpp_d_show_agent_dropdown_on_inquiry"><?php _e('Show agent dropdown on inquiry form listing all agents associated with the property.'); ?></label>
      </li>
    <?php } ?>
    </ul>

    </td>
    </tr>
    </table>


    <?php

  }

}





