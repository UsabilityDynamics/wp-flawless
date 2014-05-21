<?php
/**
 * Template for sidebars pages.
 *
 * Global $flawless_wrapper_class array contains the classes for this page
 *
 *
 * @todo have $widget_area_type and $sidebars be determined in get_current_sidebars() and then loaded into global variable for simple use here.
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
*/


  //** Figure out which sidebar this is */
  $widget_area_type = flawless_theme::backtrace_sidebar_type();

  $sidebars = flawless_theme::get_current_sidebars( $widget_area_type );

  if( !$sidebars ) {
    return;
  }

  //** Get classes of all sidebars and load into wrapper */
  foreach( $sidebars as $sidebar ) {
    $sidebar_class[] = $sidebar[ 'class' ];
  }

  //** Render all widget areas / sidebars */

  echo '<div class="sidebar column-block ' . implode( ' ', array_unique( ( array ) $sidebar_class ) ) . '">';

  do_action( 'flawless::sidebar_top', array( 'widget_area_type' => $widget_area_type ) );

  foreach( $sidebars as $sidebar ) {
    echo '<div class="column-module single-widget-area" widget_area="' . $sidebar[ 'sidebar_id' ]  . '">';
    dynamic_sidebar( $sidebar[ 'sidebar_id' ] );
    echo '</div>';
  }

  do_action( 'flawless::sidebar_bottom', array( 'widget_area_type' => $widget_area_type ) );

  echo '</div>';



