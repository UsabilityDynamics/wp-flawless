/* =========================================================
 * jquery.ud.execute_triggers.js v1.0.0
 * http://usabilitydynamics.com
 * =========================================================
 * Copyright 2011 Usability Dynamics, Inc.
 *
 * Version 0.0.1
 * Validation: http://www.jslint.com/
 *
 * The plugin handles jQuery Trigger execution by DOM elements.
 *
 * Copyright ( c ) 2011 Usability Dynamics, Inc. ( usabilitydynamics.com )
 * ========================================================= */

/*jslint indent: 2 */
/*global window */
/*global console */
/*global clearTimeout */
/*global setTimeout */
/*global jQuery */

( function ( jQuery ) {
  "use strict";

  jQuery.fn.execute_triggers = function ( s ) {

    /* Set Settings */
    s = jQuery.extend( {
      element: this,
      ajax: {},
      ux: {},
      timers: {},
      debug: false
    }, s );


    /* Internal logging function */
    var log = this.log = function ( something, type ) {

      if ( !s.debug ) {
        return;
      }

      if ( window.console && console.debug ) {

        if ( type === 'error' ) {
          console.error( something );
        } else {
          console.log( something );
        }

      }

    };


    /**
     * The main function ran when the script is initialized.
     *
     * @author potanin@UD
     */
    var enable = this.enable = function ( ) {
      log( 'execute_triggers::enable()' );

      /* Cycle through all triggers */
      jQuery( s.element ).each( function() {

        /* Attach "click" listener event to this trgger. Unbind first incase this function is ran more than once. */
        jQuery( this ).unbind( 'click' ).bind( 'click', function( event ) {
          execute_triggers( event );
        });

      });

    };


    /**
     * The main function ran when the script is executed.
     *
     * @author potanin@UD
     */
    var execute_triggers = this.execute_triggers = function ( event ) {
      log( 'execute_triggers::execute_triggers()' );

      var args = jQuery.extend( true, {
        position: [ event.clientX, event.clientY ],
        attributes: {},
        element: event.currentTarget,
        triggers: jQuery( event.currentTarget ).attr( 'execute_triggers' )
      }, event );

      /* Bail if there is are no triggers */
      if( typeof( args.triggers ) == 'undefined' || args.triggers == '' ) {
        return;
      }

      /* Convert all element attributes into an array */
      jQuery.each( args.element.attributes, function( index, attr ) {
        args.attributes[attr.name] = attr.value;
      });

      /* Convert trigger string into array */
      args.triggers = args.triggers.split( ',' );

      /* Do some data cleansing */
      jQuery.each( args.triggers , function( index, trigger_name ) {
         args.triggers[ index ] = jQuery.trim( trigger_name );
      })

      /* Cycle through and actually execute */
      jQuery.each( args.triggers , function( index, trigger_name ) {

        args.this_trigger_count = index;

        jQuery( document ).trigger( trigger_name, args );

      });

    };

    /* Enable */
    this.enable( );

    /* Return object for chaining */
    return this;

  };

} ( jQuery ) );
