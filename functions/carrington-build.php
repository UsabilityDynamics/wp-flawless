<?php
/*
	Name: Carrington Build Framework
	Description: Extra functionality for WP-Property elements.
	Author: Usability Dynamics, Inc., Crowd Favorite
	Version: 1.0
*/

add_action('flawless_theme_setup', array('flawless_carrington', 'flawless_theme_setup'));

/* Disable Carrington Taxonomy Landing Page plugin */
define('CFCT_BUILD_TAXONOMY_LANDING', false);

class flawless_carrington {

  /**
   * Primary Carrington Build custom functionality loader ran on
   *
   * @action flawless_theme_setup
   * @wp_action after_setup_theme
   * @author potanin@UD
   * @version 1.0
   */
  function flawless_theme_setup() {
  
    add_theme_support( 'carrington_build' );

    add_filter('cfct-row-defaults', array('flawless_carrington', 'cfct_row_defaults'));
    add_filter('body_class', array('flawless_carrington', 'cfct_body_class'), 10, 2);
    add_filter('cfct-row-admin-html', array('flawless_carrington', 'cfct_row_admin_html'), 10, 4);
    add_filter('cfct-row-html', array('flawless_carrington', 'cfct_row_html'), 10, 4);
    add_filter('cfct-generated-row-classes', array('flawless_carrington', 'cfct_generated_row_classes'), 10, 4);
    add_filter('cfct-build-display-class', array('flawless_carrington', 'cfct_build_display_class'), 10, 4);


    add_filter('cfct-build-loc' ,array('flawless_carrington', 'cfct_build_loc'));

    //** Include Carrington Build Framework */
    include_once trailingslashit(TEMPLATEPATH) . 'functions/carrington-build/carrington-build.php';

    add_action('flawless::init_lower', array('flawless_carrington', 'flawless_init'), 0, 20);

    add_action('admin_init', array('flawless_carrington', 'admin_init'));
    add_action('wp_ajax_flawless_cb_row_class', array('flawless_carrington', 'flawless_cb_row_class'));
    add_action('wp_ajax_flawless_cb_build_class', array('flawless_carrington', 'flawless_cb_build_class'));
    add_action('wp_ajax_flawless_cb_save_tabbed_blocks', array('flawless_carrington', 'flawless_cb_save_tabbed_blocks'));

    add_action('flawless::extra_local_assets', array('flawless_carrington', 'extra_local_assets'), 100);

    add_filter('cfct-module-cfct-callout-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser') , 10, 2);
    add_filter('cfct-module-cf-post-callout-module-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser') , 10, 2);
    add_filter('cfct-module-cfct-heading-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser') , 10, 2);
    add_filter('cfct-module-cfct-plain-text-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser') , 10, 2);
    add_filter('cfct-module-cfct-rich-text-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser') , 10, 2);
    add_filter('cfct-module-cfct-module-loop-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser') , 10, 2);
    add_filter('cfct-module-cfct-module-loop-subpages-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser') , 10, 2);

    add_action('cfct-widget-module-registered', array( 'flawless_carrington', 'cfct_widget_modules_register_theme_admin_form') , 10, 2);
    add_filter('cfct-get-extras-modules-css-admin', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser_css') , 10, 1);
    add_filter('cfct-get-extras-modules-js-admin', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser_js') , 10, 1);

    add_filter('cfct-build-module-class', array( 'flawless_carrington', 'cfct_module_wrapper_classes') , 10, 2);

    add_filter('cfct-module-display', array( 'flawless_carrington', 'cfct_module_display') , 10, 3);

    //* TESTING */
    define( 'CFCT_BUILD_ENABLE_TEMPLATES', true );

    //** Add extra directories to scan for modules */
    add_filter('cfct-module-dirs', array( 'flawless_carrington', 'cfct_module_dirs') );

    add_action('wp_footer', array('flawless_carrington', 'wp_print_footer_scripts'));
  }


  /**
   * Having CB Scan theme/modules and child-theme/modules for additional modules.
   *
   * @author potanin@UD
   */
  function cfct_module_dirs( $dirs ) {  
    global $flawless;
    
    foreach( $flawless['asset_directories'] as $path => $url ) {
      $dirs[] = $path . '/modules';
    }
  
    return $dirs;
    
  }
  
  
  /**
   * Footer scripts on front-end.  Also for debugging.
   *
   * @author potanin@UD
   */
  function wp_print_footer_scripts() {
    global $post, $flawless;

    if( $flawless['developer_mode'] != 'true' ) {
      return;
    }

    $_cfct_build_data = get_post_meta($post->ID, '_cfct_build_data', true);

    //die( '<pre>' . print_r(  $_cfct_build_data , true ) . '</pre>' );

  }


  /**
   * Primary Carrington Build custom functionality loader ran on
   *
   * @action flawless_init
   * @wp_action init
   * @author potanin@UD
   * @version 1.0
   */
  function flawless_init() {

    add_action('cfct-build-enabled-post-types', array('flawless_carrington', 'add_custom_post_types'));

    //** Add option to enable Carrington Build on custom post types */
    add_action('flawless_post_types_advanced_options', array('flawless_carrington', 'flawless_post_types_advanced_options'));

    //** Functionality for exporting/importing CB layouts */
    add_action('wp_ajax_cbc_get_page_build', create_function('', ' die(flawless_carrington::get_page_build($_REQUEST[\'post_id\'])); '));
    add_action('wp_ajax_cbc_insert_page_build', create_function('', 'die(flawless_carrington::insert_page_build($_REQUEST[\'post_id\'], $_REQUEST[\'post_data\'])); '));

    add_cb_module_style( 'listing-masonry', get_template_directory_uri() . '/img/styles/listing-masonry.png' );

  }


  /**
   * Back-end CB settings
   *
   * @author potanin@UD
   * @version 1.0
   */
  function admin_init() {

    //** Carrington Build Mods (Export / Import layouts) */
    add_filter('cfct-build-page-options', array('flawless_carrington', 'cfct_build_page_options'));
    add_filter('cfct-admin-pre-build', array('flawless_carrington', 'cfct_admin_pre_build'));

  }


  /**
   * Setsup a global JS array with any Module Blocks that have tabs
   *
   * @author potanin@UD
   * @version 1.0
   */
  function cfct_admin_pre_build( $this_build ) {
    global $cfct_build, $post_id;

    $tabbed_blocks = $this_build->data[ 'tabbed_blocks' ];

    if( empty( $tabbed_blocks ) ) {
      return;
    }

    echo '<script type="text/javascript">var cb_ud_tabbed_blocks = jQuery.parseJSON( ' . json_encode( json_encode( $tabbed_blocks ) ) . ' ); </script>';

  }


  /**
  * Style -> image mapping for style chooser
  * @return array
  */
  function admin_theme_style_images( $type ) {

    $options['general'] = array();

    $options['post_callout_module'] = array();

    $options['cfct_module_callout'] = array();

    $options = apply_filters( 'flawless_carrington_module_styles', $options );

    //** Merge General Styles into Post Callout Module */
    $options['post_callout_module'] = array_merge( $options['post_callout_module'], $options['general'] );

    //** Merge Post Callout module (and thus General styles) into regular Callout */
    $options['cfct_module_callout'] = array_merge( $options['cfct_module_callout'], $options['post_callout_module'] );

    flawless_theme::console_log('P: admin_theme_style_images() for Module Type: ' . $type);

    //** Return either a specific module style or general */
    $return = (isset($options[$type]) ? $options[$type] : $options['general']);

    return $return;

  }


  /**
   * Common function for adding style chooser
   *
   * @param string $form_html - HTML of module admin form
   * @param array $data - form save data
   * @return string HTML
   */
  function cfct_module_admin_theme_chooser($form_html, $data) {

    $type = $data['module_type'];
    $img_url_base = trailingslashit(get_template_directory_uri());

    $style_image_config = flawless_carrington::admin_theme_style_images($type);

    $selected = null;

    if (!empty($data['cfct-custom-theme-style']) && !empty($style_image_config[$data['cfct-custom-theme-style']])) {
      $selected = $data['cfct-custom-theme-style'];
    }

    $onclick = 'onclick="cfct_set_theme_choice(this); return false;"';

    $form_html .= '
      <fieldset class="cfct-custom-theme-style">
        <div id="cfct-custom-theme-style-chooser" class="cfct-custom-theme-style-chooser cfct-image-select-b">
          <input type="hidden" id="cfct-custom-theme-style" class="cfct-custom-theme-style-input" name="cfct-custom-theme-style" value="'.(!empty($data['cfct-custom-theme-style']) ? esc_attr($data['cfct-custom-theme-style']) : '').'" />

          <label onclick="cfct_toggle_theme_chooser(this); return false;">Style</label>
          <div class="cfct-image-select-current-image cfct-image-select-items-list-item cfct-theme-style-chooser-current-image" onclick="cfct_toggle_theme_chooser(this); return false;">';

    if (!empty($selected) && !empty($style_image_config[$selected])) {
        $form_html .= '
            <div class="cfct-image-select-items-list-item">
              <div class="test1" style="background: #d2cfcf url('.$style_image_config[$selected].') 0 0 no-repeat;"></div>
            </div>';

    } else {
      $form_html .= '
      <div class="cfct-image-select-items-list-item"><div style="background: #d2cfcf url('.$img_url_base.'functions/carrington-build/img/none-icon.png) 50% 50% no-repeat;"></div></div>';
    }

    $form_html .= '
      </div>

      <div class="clear"></div>

      <div id="cfct-theme-select-images-wrapper">
        <h4>'.__('Select a style...', 'favebusiness').'</h4>
        <div class="cfct-image-select-items-list cfct-image-select-items-list-horizontal cfct-theme-select-items-list">
          <ul class="cfct-image-select-items">
            <li class="cfct-image-select-items-list-item '.(empty($selected) ? ' active' : '').'" data-image-id="0" '.$onclick.'>
              <div style="background: #d2cfcf url('.$img_url_base.'functions/carrington-build/img/none-icon.png) no-repeat 50% 50%;"></div>
            </li>';

    foreach ( (array) $style_image_config as $style => $image) {
      $form_html .= '<li class="cfct-image-select-items-list-item'.($selected == $style ? ' active' : '').'" data-image-id="'.$style.'" '.$onclick.'>
        <div class="test2" style="background: url('.$image.') 0 0 no-repeat;"></div>
        </li>';
    }

    $form_html .='
                </ul>
              </div>
            </div>
          </div>
        </fieldset>
      ';

    return $form_html;
  }


  /**
   * Apply the custom theme style
   *
   * @param string $class_string - base module wrapper classes
   * @param array $data - module save data
   * @return string
   */
  function cfct_module_wrapper_classes( $class, $data ) {
    $type = $data['module_type'];

    $classes = explode(' ', $class );

    if( $type == 'cfct_module_notice' ) {
      $classes[] = 'alert';
    }

    // see if we have a custom theme style to apply
    if ( !empty( $data[ 'cfct-custom-theme-style' ] ) ) {
      $classes[] = esc_attr( $data[ 'cfct-custom-theme-style' ] );
    }

    $class = trim( implode(' ', $classes) );

    return $class;
  }


  /**
   * JS for Theme Chooser in individual Module Admin Screens
   *
   * @param string $js
   * @return string
   */
  function cfct_module_admin_theme_chooser_js($js) {
    $js .= preg_replace('/^(\t){2}/m', '', '

      cfct_set_theme_choice = function(clicked) {
        _this = $(clicked);
        _this.addClass("active").siblings().removeClass("active");
        _wrapper = _this.parents(".cfct-custom-theme-style-chooser");
        _val = _this.attr("data-image-id");
        _background_pos = (_val == "0" ? "50% 50%" : "0 0");

        $("input:hidden", _wrapper).val(_val);

        $(".cfct-image-select-current-image .cfct-image-select-items-list-item > div", _wrapper)
          .css({"background-image": _this.children(":first").css("backgroundImage"), "background-position": _background_pos});

        $("#cfct-theme-select-images-wrapper").slideToggle("fast");
        return false;
      };

      cfct_toggle_theme_chooser = function(clicked) {
        $("#cfct-theme-select-images-wrapper").slideToggle("fast");
        return false;
      }

    ');
    return $js;
  }


  /**
   * CSS for Theme Chooser in individual Module Admin Screens
   *
   * @param string $css
   * @return string
   */
  function cfct_module_admin_theme_chooser_css($css) {
    $css .= preg_replace('/^(\t){2}/m', '', '
      /* Theme Chooser Additions */
      #cfct-custom-theme-style-chooser .cfct-image-select-current-image {
        display: block;
        height: 100px;
        width: auto;
      }
      #cfct-custom-theme-style-chooser .cfct-image-select-current-image p {
        text-align: left;
        font-size: 1em;
      }
      #cfct-custom-theme-style-chooser .cfct-image-select-current-image,
      #cfct-custom-theme-style-chooser .cfct-image-select-current-image>div {
        cursor: pointer;
      }
      #cfct-custom-theme-style-chooser .cfct-image-select-current-image .cfct-image-select-items-list-item,
      #cfct-custom-theme-style-chooser .cfct-image-select-current-image .cfct-image-select-items-list-item>div {
        height: 55px;
      }

      #cfct-custom-theme-style-chooser .cfct-theme-style-chooser-current-image {
        height: 75px;
      }
      #cfct-custom-theme-style-chooser label {
        float: left;
        display: block;
        width: 120px;
        margin-top: 25px;
      }
      #cfct-custom-theme-style-chooser #cfct-theme-select-images-wrapper {
        display: none;
      }
      .cfct-popup-content.cfct-popup-content-fullscreen fieldset.cfct-custom-theme-style {
        margin: 12px;
      }
      #cfct-theme-select-images-wrapper h4 {
        color: #666;
        font-weight: normal;
        margin: 0 0 5px;
      }
    ');
    return $css;
  }

  /**
   * Register a filter for each widget module loaded
   *
   * @param string $widget_id - standard wordpress widget_id
   * @param string $module_id - id of module in build
   * @return void
   */
  function cfct_widget_modules_register_theme_admin_form($widget_id, $module_id) {
    add_filter('cfct-module-'.$module_id.'-admin-form', array( 'flawless_carrington', 'cfct_module_admin_theme_chooser') , 10, 2);
  }


  /**
   * Load custom carrington CSS
   *
   */
  function extra_local_assets() {

    //** Render CSS styles for print as well */
    wp_enqueue_style('cfct-build-css-print',site_url('/?cfct_action=cfct_css'), array(), CFCT_BUILD_VERSION, 'print');

    //** Always load bootstrap.css */
    if(file_exists( TEMPLATEPATH . '/css/carrington.css') ) {
      wp_enqueue_style('flawless-carrington', get_bloginfo('template_url') . '/css/carrington.css', array(), Flawless_Version, 'all');
    }

  }


  /**
   * Sets highest possible Carrington Build styles
   *
   * @todo '_cfct_build_data' may already be in a global variable and may not need to be loaded using gpm again
   * @author potanin@UD
   * @version 1.0
   */
  function cfct_build_display_class($current) {
    global $post;

    $cfct_data = get_post_meta($post->ID, '_cfct_build_data', true);

    $custom_class = !empty($cfct_data['template']['custom_class']) ? $cfct_data['template']['custom_class'] : 'cfct-build-default';

    return $current . ' ' . $custom_class;
  }


  /**
   * Saves custom row class
   *
   * @author potanin@UD
   * @version 1.0
   */
  function flawless_cb_row_class() {

    $post_id = $_REQUEST['post_id'];
    $row_id = $_REQUEST['row_id'];
    $new_class = $_REQUEST['new_class'];

    if(!$post_id || !is_numeric($post_id)) {
      $response = array(
        'success' => 'false',
        'message' => __('No post ID.', 'flawless')
      );

    } else {

      $cfct_data = get_post_meta($post_id, '_cfct_build_data', true);
      $rows = $cfct_data['template']['rows'];

      foreach( (array) $rows as $row_guid => $row_data ) {

        if($row_guid == $row_id) {
          $cfct_data['template']['rows'][$row_guid]['custom_class'] = !empty($new_class) ? $new_class : 'default-row-class';
          continue;
        }

      }

      update_post_meta($post_id, '_cfct_build_data', $cfct_data);

      $response = array(
        'success' => 'true',
        'message' => __('Custom row class saved.', 'flawless'),
        'row_guid' => $row_guid
      );

    }

    die(json_encode($response));
  }


  /**
   * Saves custom Build class
   *
   * @author potanin@UD
   * @version 1.0
   */
  function flawless_cb_build_class() {

    $post_id = $_REQUEST['post_id'];
    $new_class = $_REQUEST['new_class'];

    if(!$post_id || !is_numeric($post_id)) {
      $response = array(
        'success' => 'false',
        'message' => __('No post ID.', 'flawless')
      );

    } else {

      $cfct_data = get_post_meta($post_id, '_cfct_build_data', true);

      $cfct_data['template']['custom_class'] = !empty($new_class) ? $new_class : '';

      update_post_meta($post_id, '_cfct_build_data', $cfct_data);

      $response = array(
        'success' => 'true',
        'message' => __('Custom build class saved.', 'flawless')
      );

    }

    die(json_encode($response));
  }


  /**
   * Hooks into HTML output for back-end row displsy
   *
   * @filter cfct-row-admin-html
   * @version 1.0
   */
  function cfct_row_admin_html($html, $classname = false, $classes = false, $opts = false) {

    //** Get just the unique class or row */
    $unique_class = implode('.', $classes);

    //** Load custom class if it exists from row $opts
    $current_setting = $opts['custom_class'] ? $opts['custom_class'] : '';

    $html = str_replace('<a class="cfct-row-delete" href="#">Remove</a>', '<a class="cfct-row-delete" href="#">Remove</a><a class="cfct-add-row-class" current_setting="'. $current_setting . '" row_class="'. $unique_class . '" title="Set, or change, custom row class." href="#">Change Class</a>', $html);

    return $html;

  }


  /**
   * Saves custom row from AJAX post
   *
   * This may not be the most efficient function for this.
   *
   * @author potanin@UD
   * @version 1.0
   */
  function flawless_cb_save_tabbed_blocks() {

    $args = $_REQUEST[ 'args' ];

    if( empty( $args ) || empty(  $args['post_id'] )) {
      die( json_encode( array(
        'success' => 'false',
        'message' => __( 'Failure, no post received.' , 'flawless')
      )));
    }

    $cfct_data = get_post_meta( $args['post_id'], '_cfct_build_data', true);

    //** Blank out any old settings, they should all be updated */
    foreach( (array) $cfct_data['data']['modules'] as $module_id => $module_data ) {
      unset( $cfct_data['data']['modules'][ $module_id ][ 'tabbed_block' ] );
    }

    /* Save data as is */
    $cfct_data[ 'data' ][ 'tabbed_blocks' ] = $args['blocks'];

    foreach( (array) $args[ 'blocks' ] as $block_data ) {

      if( !empty( $block_data['tabbed_sections'] ) ) {

        $block_id = $block_data[ 'id' ];

        foreach( (array) $block_data['tabbed_sections'] as $tabbed_sections ) {

          $sanitized_labels = array();

          foreach( (array) $tabbed_sections[ 'tabs' ] as $tab_index => $tab_label ) {
            $sanitized_labels[ $tab_index ] = sanitize_title($tab_label);
          }

          foreach( (array) $tabbed_sections[ 'modules' ] as $tab_index => $modules_in_tab ) {

            foreach( (array) $modules_in_tab as $module_index_in_tab => $module_id ) {

              $cfct_data[ 'data' ][ 'modules' ][ $module_id ][ 'tabbed_block' ][ 'tabbed' ] = true;
              $cfct_data[ 'data' ][ 'modules' ][ $module_id ][ 'tabbed_block' ][ 'module_index_in_tab' ] = $module_index_in_tab;
              $cfct_data[ 'data' ][ 'modules' ][ $module_id ][ 'tabbed_block' ][ 'tab_index' ] = $tab_index;
              $cfct_data[ 'data' ][ 'modules' ][ $module_id ][ 'tabbed_block' ][ 'tab_sanitized_label' ] = $sanitized_labels[ $tab_index ];
              $cfct_data[ 'data' ][ 'modules' ][ $module_id ][ 'tabbed_block' ][ 'tab_label' ] = $tabbed_sections[ 'tabs' ][ $tab_index ];

              if( $tab_index === 0 ) {
                $cfct_data[ 'data' ][ 'modules' ][ $module_id ][ 'tabbed_block' ][ 'first_tab' ] = true;
              }

              if( $module_index_in_tab === 0 ) {
                $cfct_data[ 'data' ][ 'modules' ][ $module_id ][ 'tabbed_block' ][ 'first_module' ] = true;
              }

              if( count( $modules_in_tab ) - 1 ===  $module_index_in_tab ) {
                $cfct_data[ 'data' ][ 'modules' ][ $module_id ][ 'tabbed_block' ][ 'last_module' ] = true;
              }

              if( count( $tabbed_sections[ 'modules' ] ) - 1  === $tab_index ) {
                $cfct_data[ 'data' ][ 'modules' ][ $module_id ][ 'tabbed_block' ][ 'last_tab' ] = true;
              }

              /* First Module in First Tab */
              if( $cfct_data[ 'data' ][ 'modules' ][ $module_id ][ 'tabbed_block' ][ 'first_tab' ] && $cfct_data[ 'data' ][ 'modules' ][ $module_id ][ 'tabbed_block' ][ 'first_module' ] ) {
                $cfct_data[ 'data' ][ 'modules' ][ $module_id ][ 'tabbed_block' ][ 'tab_labels' ] = $tabbed_sections[ 'tabs' ];
                $cfct_data[ 'data' ][ 'modules' ][ $module_id ][ 'tabbed_block' ][ 'tab_sanitized_labels' ] = $sanitized_labels;
              }

            }

          }

        }

      }

    }

    //echo '<pre>' . print_r( $cfct_data , true ) . '</pre>';
    //echo '<pre>' . print_r( $args , true ) . '</pre>';
    //die();

    update_post_meta( $args['post_id'], '_cfct_build_data', $cfct_data );

    die( json_encode( array(
      'success' => 'true',
      'message' => __('Block tabbed modules saved.', 'flawless')
    )));

  }


  /**
   * Called in cfct_build_module::html()
   *
   * Hooks into the content, within the wrapper, of a module.
   * We use this to get the $id_bases of all the modules in this request.
   *
   * @author potanin@UD
   */
  function cfct_module_display( $display, $id_base, $data ) {

    add_filter( 'cfct-module-' . $id_base . '-html' , array(  'flawless_carrington', 'cfct_module_display_full' ), 10, 2 );

    return $display;

  }


  /**
   * Can return the module HTML, including the wrapper
   *
   * Hooks into the content, within the wrapper, of a module.
   * We use this to get the $id_bases of all the modules in this request.
   *
   * @author potanin@UD
   */
  function cfct_module_display_full( $default, $data ) {

    $html = array();

    if( $data[ 'tabbed_block' ]['first_tab'] && $data[ 'tabbed_block' ]['first_module'] ) {

      //** This is done once when the first module in the first tab is being rendered */
      $html[] = '<ul class="nav nav-tabs tabbed-module">';

      foreach( (array) $data[ 'tabbed_block' ][ 'tab_labels' ] as $tab_index => $tab_label ) {

        $tab_classes = array( 'tab' );

        if( $data[ 'tabbed_block' ][ 'first_tab' ] && $data[ 'tabbed_block' ][ 'first_module' ] && $tab_index === 0 ) {
          $tab_classes[] = 'active';
        }

        $html[] = '<li class="' . implode( ' ', $tab_classes) . '" tab_index="' . $tab_index . '"  tab_target="' . $data[ 'tabbed_block' ][ 'tab_sanitized_labels' ][ $tab_index ] . '">
        <a href="#' . $data[ 'tabbed_block' ][ 'tab_sanitized_labels' ][ $tab_index ] . '" data-toggle="tab" >' . $tab_label . '</a>
        </li>';

      }

      $html[] = '</ul><!-- .tabbed-module -->';

      //* Begin the wrapper for the tabs */
      $html[] = '<div class="tab-content tabbed-module">';

    }


    //** Done for the first module in every tab - setting up the openin wrapper */
    if( $data[ 'tabbed_block' ][ 'first_module' ] ) {

      $pane_class = array( 'tab-pane' );

      if( $data[ 'tabbed_block' ][ 'first_tab' ] ) {
        $pane_class[] = 'active';
      }

      $html[] = '<div id="' . $data[ 'tabbed_block' ][ 'tab_sanitized_label' ] . '" class="' . implode( ' ', $pane_class)  . '" tab_index="' . $data[ 'tabbed_block' ][ 'tab_index' ] . '" module_index_in_tab="' . $data[ 'tabbed_block' ][ 'module_index_in_tab' ] . '">';

    }

    //** Return the actual module content - ran on every iteration */
    $html[] = $default;

    //** Ran for every last module in each tab to close the tab element */
    if( $data[ 'tabbed_block' ][ 'last_module' ] ) {
      $html[] = '</div><!-- .tab-pane -->';
    }

    //** Ran after the final module in the last tab, to close the entire element */
    if( $data[ 'tabbed_block' ][ 'last_tab' ] && $data[ 'tabbed_block' ][ 'last_module' ] ) {
      $html[] = '</div><!-- .tabbed-module -->';
    }

    //die( '<pre>' . print_r( $html , true ) . '</pre>' );

    return implode( "\n", (array) $html );

  }


  /**
   * Loads custom row class, if it exists
   *
   * @filter cfct-generated-row-classes
   * @version 1.0
   */
  function cfct_generated_row_classes($nothing, $module_types, $that, $opts) {

    if(is_admin()) {
      return $nothing;
    }

    $custom_class = !empty($opts['custom_class']) ? $opts['custom_class'] : 'default-row-class';

    return array($custom_class);

  }


  /**
   * Front-end row output
   *
   * Not used, only here for future reference.
   * @author potanin@UD
   * @version 1.0
   */
  function cfct_row_html($html = false, $classname = false, $classes = false, $opts = false) {

    return $html;
  }


  /**
   * Adds custom row actions for CB
   *
   * Doesn't seem to be used, here for reference.
   * @author potanin@UD
   * @version 1.0
   */
  function cfct_row_defaults($s) {

    $s['add_row_class'] = 'cfct-add-row-class';

    return $s;

  }


  /**
   *  Adds a class to the body of pages using CB
   *
   * @author potanin@UD
   */
  function cfct_body_class($classes, $class) {
    global $post;

    if(!$_cfct_build_data = get_post_meta($post->ID, '_cfct_build_data', true)) {
      $classes[] = 'non_carrington_layout';
    }

    if($_cfct_build_data['active_state'] == 'build') {
      $classes[] = 'carrington_layout';
    } else {
      $classes[] = 'non_carrington_layout';
    }

    $classes = array_unique($classes);

    return $classes;

  }


  /**
   * Tells CB where it is located (always in parent theme)
   *
   * @author potanin@UD
   * @version 1.0
   */
  function cfct_build_loc($location) {

    $location['loc'] = 'theme';
    $location['path'] = TEMPLATEPATH . '/functions';
    $location['url'] = get_bloginfo('template_url') . '/functions';

    return $location;

  }


  /**
   * Adds Flawless Custom Post Types to Carrington editor, and removes defaults if setting exists in Flawless
   *
   * @author potanin@UD
   * @version 1.0
   */
	function add_custom_post_types($types) {
    global $fs;

    //** Should never happen, but return default settings if no configuration exists */
    if(!is_array($fs['post_types'])) {
      return $types;
    }

    //** Cycle through Flawless settings */
    foreach($fs['post_types'] as $type => $data) {
      if($data['use_carrington'] == 'true') {
        $types[] = $type;

      } elseif( $data['use_carrington'] == 'false' ) {

        //** Disable only if specifically disabled by Flawless. */
        unset($types[array_search($type, $types)]);

      } else {
        //** Do nothing if there is no explicit setting for this type */
      }
    }

    return $types;

	}


  /**
   * Adds option to enable Carrington Editor for the post tpe
   *
   * If post type is a page, and not setting exists, we enable editing since it is the default setting.
   *
   * @author potanin@UD
   * @version 1.0
   */
  function flawless_post_types_advanced_options($args) {

    extract($args);

    if($type == 'page' && !isset($data['use_carrington'])) {
      $data['use_carrington'] = 'true';
    }

  ?>
    <li class="flawless_advanced_option">
      <input type="hidden" name="flawless_settings[post_types][<?php echo $type; ?>][use_carrington]" value="false" />
      <input id="<?php echo $type; ?>_use_carrington" type="checkbox" <?php checked('true', $data['use_carrington']); ?> name="flawless_settings[post_types][<?php echo $type; ?>][use_carrington]" value="true" />
      <label for="<?php echo $type; ?>_use_carrington"><?php _e('Use Carrington Build for editing.','flawless') ?></label>
    </li>
  <?php
  }


  /**
   * Loads extra options into CB Settings dropdown
   *
   * @author potanin@UD
   * @version 1.0
   */
  function cfct_build_page_options() {
    global $post;

    $cfct_data = get_post_meta($post->ID, '_cfct_build_data', true);

    $current_setting = !empty($cfct_data['template']['custom_class']) ? $cfct_data['template']['custom_class'] : '';

    $options[] = '<li><a id="cfct-set-build-class" href="#cfct-set-build-class" current_setting="'. $current_setting . '" >Set Build Class</a></li>';
    $options[] = '<li><a id="cfct-copy-build-data" href="#cfct-copy-build">Copy Layout</a></li>';
    $options[] = '<li><a id="cfct-paste-build-data" href="#cfct-paste-build">Paste Layout</a></li>';

    return implode('', $options);

  }


  /**
   * {description missing}
   *
   * @author potanin@UD
   * @version 1.0
   */
  function get_page_build($post_id) {

    $content = get_post_meta($post_id, '_cfct_build_data', true);

    $results['success'] = 'true';
    $results['content'] = base64_encode(serialize($content));

    return json_encode($results);
  }


  /**
   * {description missing}
   *
   * @author potanin@UD
   * @version 1.0
   */
  function insert_page_build($post_id, $post_data) {
    global $wpdb;

    $post_data = stripslashes($post_data);

    $post_data = unserialize(base64_decode($post_data));

    if(update_post_meta($post_id, '_cfct_build_data', $post_data)) {
      $results['success'] = 'true';
    }

    return json_encode($results);
  }


}


  /**
   * Easy way of adding custom styles to Carrington Build Module style selector
   *
   * Type Options:
   *  - cfct_module_rich_text
   *  - cfct_module_callout
   *  - cfct_module_heading
   *  - cfct_module_loop_subpages
   *
   * @todo Add check to make sure image file exists
   * @return string HTML
   */
  function add_cb_module_style( $class = false, $image_path = '', $type = 'general' ) {

    if( !$image_path || !$class ) {
      return;
    }

    add_filter( 'flawless_carrington_module_styles', create_function( '$options, $type="' . $type . '", $image_path="' . $image_path . '", $class="' . $class . '" ', '  $options[$type][$class] = $image_path;  return $options; ' ) );

  }
