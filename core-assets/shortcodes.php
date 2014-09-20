<?php
/**
  * Name: Theme Shortcodes
  * Description: Shortcodes for the Flawless theme.
  * Author: Usability Dynamics, Inc.
  * Version: 1.0
  *
  */


add_action( 'flawless_content_type_added', array( 'flawless_shortcodes', 'flawless_content_type_added' ) );

add_shortcode( 'code', array( 'flawless_shortcodes', 'code' ) );
add_shortcode( 'breadcrumbs', array( 'flawless_shortcodes', 'breadcrumbs' ) );
add_shortcode( 'current_year', array( 'flawless_shortcodes', 'current_year' ) );
add_shortcode( 'site_description', array( 'flawless_shortcodes', 'site_description' ) );
add_shortcode( 'post_link', array( 'flawless_shortcodes', 'post_link' ) );
add_shortcode( 'button', array( 'flawless_shortcodes', 'button' ) );
add_shortcode( 'get_permalink', array( 'flawless_shortcodes', 'get_permalink' ) );
add_shortcode( 'image_url', array( 'flawless_shortcodes', 'image_url' ) );
add_shortcode( 'pdf', array( 'flawless_shortcodes', 'google_docs_pdf' ) );

class flawless_shortcodes {
  
  /**
   * Add custom shortcodes for various post types
   *
   * @since 0.2.5
   *
   */
  function flawless_content_type_added( $args = false ) {
    global $flawless;

    if( !$args ) {
      return;
    }

    $post_type = get_post_type_object( $args['type'] );

    if( !$post_type->public || !$post_type->has_archive ) {
      return;
    }

    //** Check if post type has custom Root page */
    if( $flawless['post_types'][$args['type']]['root_page'] ) {
      $root_url = get_permalink( $flawless['post_types'][$args['type']]['root_page'] );
    } else {
      $root_url = get_bloginfo( 'url' ) . '/' . $post_type->rewrite['slug'] . '/';
    }

    $shortcode = "{$post_type->labels->name} URL";

    add_shortcode( $shortcode, create_function( '$atts, $content, $code, $the_url="' .  $root_url  . '"', ' return "$the_url"; ' ) );

    $flawless['documentation']['shortcodes'][$shortcode] = sprintf( __( 'Returns a URL to the main %1s page.', 'flawless' ), $post_type->labels->name );

  }
  
  /**
   * Execude only [code] shortcode
   * @param sting $content
   * @return string 
   * @author odokienko@UD
   */
  function do_code_shortcode($content){
		global $shortcode_tags;

		// Back up current registered shortcodes and clear them all out
		$orig_shortcode_tags = $shortcode_tags;
		remove_all_shortcodes();
    add_shortcode( 'code', array( 'flawless_shortcodes', 'code' ) );
		
		// Do the shortcode (only the [code] one is registered)
		$content = do_shortcode( $content );

		// Put the original shortcodes back
		$shortcode_tags = $orig_shortcode_tags;

		return $content;
	}
  
  
  /**
   * @param sting $code
   * @return string 
   * @author odokienko@UD
   */
  function flawless_stripslashes($code){
    $code=str_replace(array("\\\"", "\\\'"), array ('"', "'"),$code);
    $code=htmlspecialchars($code);
    $code=str_replace(array('<', '>'), array('&lt;', '&gt;'),$code);
    return $code;
  }

  
  
  /**
   * Convert code within [code] [/code] shortcode into printable code.
   * 
   * @since 0.3.5
   * @example [code] ... [/code]
   * @example [code linenums=74] ... [/code]
   * @example [code linenums=74 lang=lang-html class="prettyprint" container="pre"] ... [/code]
   * @author odokienko@UD
   */
  function code( $args = false , $content = null ) {  
    /**
     * !important: if we want makes this function working then we need to run "do_shortcode" twice.
     * The first time it must calls before all the rest filters (especially kses)
     * the second time - after them 
     */
  
    /** if it is the first time */
    if (empty($args['second_run'])){
      /** we remember initial arguments */
      $old_args = array();
      
      foreach ((array)$args as $key=>$val){
        $old_args[]= "$key=\"$val\"";
      }
      /** do encode the body and add flag "second_run" and left shortcode as-is */
      $old_args[] = 'second_run=true';
      $content = "[code".(($old_args)?" ".implode(' ',$old_args):'')."]".base64_encode(trim($content))."[/code]";
      
    }else{
      /** at the second time add css and js */
      wp_enqueue_style('google-pretify');
      wp_enqueue_script('google-pretify');
    
      $args = shortcode_atts( array(
        'class' => 'prettyprint',
        'linenums' => false,
        'lang'  => false,
        'container'  => 'code'
      ), $args );
      
      /** and prepare the final looking (will not forget make decode of content) */
      $content = "<{$args['container']} class='{$args['class']}".(($args['linenums'])?" linenums:{$args['linenums']}":'').(($args['lang'])? " ".$args['lang'] :'')."'>".flawless_shortcodes::flawless_stripslashes(base64_decode($content))."</{$args['container']}>";
      
    }
    return $content;
  }
  

  /**
   * Prints out breadcrumbs to current page.
   *
   * @since 0.2.5
   *
   */
  function breadcrumbs( $atts = false ) {

    if( !function_exists( 'flawless_breadcrumbs' ) ) {
      return;
    }

    return flawless_breadcrumbs( array(
      'return' => true,
      'hide_breadcrumbs' => false
    ) );

  }


  /**
   * Returns current year.
   *
   * @since 0.2.5
   *
   */
  function current_year( $atts = false ) {
    return date( 'Y' );
  }


  /**
   * URL to an image in the library
   *
   * Size arguments can be any custom size, or the default: thumbnail, medium, large or full
   *
   * @since 0.2.5
   */
  function image_url( $attr, $content ) {

    $args = wp_parse_args( $attr, array(
      'id' => false,
      'size' => 'full',
      'icon' => false
    ) );

    if( empty( $args['id'] ) ) {
      return;
    }

    $image_data = wp_get_attachment_image_src( $args['id'], $args['size'], $args['icon'] );

    $url = $image_data['0'];

    return $url;

  }


  /**
   * Render a PDF in a Google Docs viewer
   *
   * @since 0.2.5
   *
   */
  function google_docs_pdf( $attr, $content ) {
    $url = urlencode( $attr['url'] );
    return '<iframe src="http://docs.google.com/viewer?url=' . $url . ' &embedded=true" width="99%" height="800"></iframe>';
  }


  /**
   * Returns current year.
   *
   * @since 0.2.5
   *
   */
  function site_description( $atts = false ) {
    return get_bloginfo( 'description' );
  }


  /**
   * Shortcode function for getting a permalink to a specific post
   *
   * @since 0.1
   * @param array $attr Attributes attributed to the shortcode.
   */
  function post_link( $attr ) {
    $url = get_permalink( $attr['id'] );

    if( empty( $url ) ) {
      return;
    }

    if( empty( $attr['title'] ) ) {
      $attr['title'] = get_the_title( $attr['id'] );
    }

    return '<a href="'. $url . '" class="'. $attr['class'] . '">' . $attr['title']. '</a>';
  }

  /**
   * Shortcode function for getting buttons
   *
   * @since 0.2.5
   * @param array $attr Attributes attributed to the shortcode.
   */
  function button( $attr ) {
    $url = get_permalink( $attr['id'] );

    if( empty( $attr['id'] ) ) {
      $url = $attr['url'];
    }

    if( empty( $url ) ) {
      return;
    }

    if( empty( $attr['title'] ) ) {
      $attr['title'] = get_the_title( $attr['id'] );
    }

    return '<a href="'. $url . '" class="btn '. $attr['class'] . '"><i class="'. $attr['icon'] .'"></i>' . $attr['title']. '</a>';
  }


  /**
   * Shortcode function for getting a permalink to a specific post
   *
   * @since 0.1
   * @param array $attr Attributes attributed to the shortcode.
   */
  function get_permalink( $attr ) {
    return get_permalink( $attr['id'] );
  }

}