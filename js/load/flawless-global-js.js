/**
 * Flawless - Global Frontend JavaScript
 *
 * @version 0.5.0
 * @since Flawless 0.2.3
 * @author Usability Dynamics, Inc.
 *
 */

  /* Load Defaults */
  var flawless = jQuery.extend( true, {
    fancybox_options: {
      'transitionIn':   'elastic',
      'transitionOut':   'elastic',
      'speedIn':   600,
      'speedOut':   200,
      'overlayShow':   false
    }
  }, typeof flawless_config == 'object' ? flawless_config : {});


  /**
   * Apply Masonry Layout to Elements, if library exists
   *
   * @since Flawless 0.2.3
   * @author Usability Dynamics, Inc.
   *
   */
  flawless.masonry = function() {

    if( typeof jQuery.fn.masonry != 'function' ) {
      return false;
    }

    jQuery( '.listing-masonry' ).masonry( {
      itemSelector : '.column-block'
    });

  }


  /**
   * Perform actions that require images to be fully loaded
   *
   * @since Flawless 0.3.2
   * @author Usability Dynamics, Inc.
   */
  jQuery( window ).load( function() {
    flawless_resize_dom_elements();
    flawless.masonry();

  });


  /**
   * Functions to be executed when DOM is ready or updated
   *
   * @since Flawless 0.3.2
   * @author Usability Dynamics, Inc.
   */
  jQuery( document ).bind( 'flawless::ui_refresh' , function() {
    flawless.log( 'Bound Function: flawless::ui_refresh' );

    /* Setup Helper Scripts (if they are disabled, they are not loaded */
    if( typeof jQuery.fn.lazyload == 'function' ) {
      jQuery( 'img.lazy' ).lazyload();
    }

    if( typeof jQuery.fn.form_helper == 'function' ) {
      jQuery( 'form' ).form_helper({
        debug: flawless.developer_mode
      });
    };

    if( typeof jQuery.fn.tooltip == 'function' ) {
      jQuery( '.webster' ).tooltip()
    };

    /* Enable Fancybox, if function exists, for all links with fancybox_image class and gallery itmes */
    if( typeof jQuery.fn.fancybox == 'function' ) {
      jQuery( 'a.fancybox_image, .gallery-item a' ).fancybox( flawless.fancybox_options );

      jQuery( 'a[href$="jpg"], a[href$="png"]' ).each( function() {
        jQuery( this ).fancybox( flawless.fancybox_options );
      });

    }

    /* Enable Popover Plugin */
    if( typeof jQuery.fn.popover == 'function' ) {
      jQuery( '[rel=popover]' ).popover();
    }

    flawless.masonry();

  });


  /**
   * Primary $.ready() function.
   *
   * @since Flawless 0.0.1
   * @author Usability Dynamics, Inc.
   *
   */
  jQuery( document ).ready( function() {
    jQuery( document ).trigger( 'flawless::ready::initialize' );

    flawless.log( 'Flawless Global JS Loaded.' );

    jQuery( document ).trigger( 'flawless::ready::complete' );

  });



  /**
   * Ran on flawless::ready - giving child themes and other scripts the ability to modify global flawless object
   *
   * @since Flawless 0.3.5
   * @author Usability Dynamics, Inc.
   *
   */
  jQuery( document ).bind( 'flawless::ready::complete', function() {

    jQuery( '.no-ajax' ).removeClass( 'no-ajax' );

    /* Fix hidden ad_hoc menus */
    if( jQuery( '.flawless_ad_hoc_menu_parent' ).length ) {

      jQuery( '.flawless_ad_hoc_menu_parent' ).each( function() {

        /* Display this item, and it's parents, if it is hidden, but the parents are not */
        if( !jQuery( this ).is( ':visible' ) && jQuery( this ).parents().is( ':visible').length ) {
          jQuery( this ).parents().show();
        }

      });
    }

    jQuery( "form.search_format" ).submit( function() {

      if( typeof flawless.header !== 'object' ) {
        return true;
      }

      if( flawless.header.must_enter_search_term == 'true' && jQuery( '.search_input_field', this ).val() == '' ) {
        jQuery( '.search_input_field', this ).focus();
        return false;
      }

    });

    jQuery( ".search_input_field" ).focus( function() {

      if( !flawless.search_input_field_width ) {
        flawless.search_input_field_width = parseInt( jQuery( this ).width() )
      }

      var args = {
        wrapper_width: jQuery( this ).closest( '.search_inner_wrapper' ).width(),
        search_button_width: jQuery( this ).siblings( '.search_button ' ).outerWidth(),
        expanded_width: flawless.search_input_field_width + 100
      }

      /* Prevent expanded input width + search button from being wider than the wrapper */
      if( args.expanded_width > ( args.wrapper_width - args.search_button_width ) ) {
        args.expanded_width = args.wrapper_width - args.search_button_width - 25;
      }

      /* Increase width */
      if( jQuery( this ).width() == flawless.search_input_field_width && args.expanded_width > flawless.search_input_field_width ) {
        jQuery( this ).animate( { width: ( args.expanded_width ) + 'px' }, 500 );
      }

    });

    jQuery( '.search_input_field' ).blur( function() {

      if( jQuery( this ).val() == "" ) {
        jQuery( this ).delay( 500 ).animate( { width: ( flawless.search_input_field_width ) + 'px' }, 500 );
      }

    });

    /* Handle header dropdown menus */
    flawless_header_dropdown_menus();

    /* Enable Layout Editor */
    jQuery( '.flawless_edit_layout' ).click( function( e ) {

      e.preventDefault();

      if( typeof flawless_layout_editor == 'function' ) {
        flawless_layout_editor( this, e );
      }

    });

    jQuery( document ).trigger( 'flawless::ui_refresh' );

    jQuery('a[data-toggle="tab"]').on('shown', function (e) {
      jQuery( document ).trigger( 'flawless::ui_refresh' );
    });

    if( typeof prettyPrint == 'function' ) {
      prettyPrint();
    }

  });


  /**
   * Add a message to DOM.
   *
   * Only first argument must be passed containing the text of message.
   * To set the type of message, pass secon argument as object with following 'type' options: warning, error, success, info (default is info)
   * The message will be inserted into .global_notice_wrapper, unless other specified.  If the specified container is not found, a container will be created automtaiclaly after <header>
   *
   * This function will add a "close" trigger if the Twitter Bootstrap Alert function exists.
   *
   * @author potanin@UD
   */
  if( typeof flawless.add_notice == 'undefined' ) {
    flawless.add_notice = function ( message, s ) {

      s = jQuery.extend(true, {
        type: 'info',
        heading: false,
        allow_dismissal: true,
        classes: {
          alert: 'alert'
        },
        hide: false,
        fade: false,
        wrapper: jQuery( '.primary_notice_container' ),
        remove_others: true
      }, s);

      /* If wrapper does not exist in DOM, we insert a new one */
      if( !jQuery( s.wrapper ).is( ':visible' ) && jQuery( '.content_container' ).length ) {
        s.wrapper = jQuery( '<div class="primary_notice_container container"></div>' ),
        jQuery( '.content_container' ).prepend( s.wrapper );
      }

      if( s.remove_others ) {
        jQuery( '.alert', s.wrapper ).remove();
      }

      /* If no message, we leave after we remove previous messages */
      if( message == '' ) {
        return;
      }

      /* Identify our message container. Add close option if alert function exists  */
      var element = jQuery( '<div class="' + s.classes.alert +  ' ' + s.type + '" alert_type="' + s.type +  '">' +  message + '</div>' )

      if( typeof jQuery.fn.alert == 'function' && s.allow_dismissal ) {
        element.prepend( '<a href="#" class="close">&times;</a>' );
        element.attr( 'data-dismiss', 'alert' );
      }

      jQuery( s.wrapper ).append( element );

    }
  }


  /*
   * Enables Editor, called from WP Toolbar, or BuddyPress toolbar if it exists
   *
   */
  function flawless_layout_editor( edit_button, e ) {
    var edit_button = edit_button;
    var flawless_modules = [];

    if( jQuery( edit_button ).data( 'disable_click' ) ) {
      return;
    }

    if( typeof jQuery.fn.frontend_editor != 'function' ) {
      return;
    }

    if( typeof jQuery.fn.toolbar_message == 'function' ) {
      jQuery.fn.toolbar_message( 'Layout editor enabled.' );
    } else {
      flawless.add_notice( 'Layout editor enabled.' );
    }

    if( !flawless.frontend_editor ) {

      /* Associate Frontend Editor with every element with .flawless_dynamic_area class */
      flawless.frontend_editor = jQuery( 'div.flawless_dynamic_area' ).frontend_editor( {
        settings: {
          max_width: flawless.max_width ? flawless.max_width : false
        }
      });

      /* Save Original Attribute if it has not been saved yet */
      if( !jQuery( edit_button ).attr( 'original_label' ) ) {
        jQuery( edit_button ).attr( 'original_label', jQuery( edit_button ).text() );
      }

      jQuery( edit_button ).text( 'Save Layout' );

    } else {

      jQuery( edit_button ).data( 'disable_click', true );
      jQuery( edit_button ).text( 'Saving...' );

      jQuery.ajax( {
        url: flawless.ajax_url,
        data: {
          action: 'flawless_action',
          styles: flawless.frontend_editor.styles,
          _wpnonce: flawless.nonce,
          the_action: 'save_front_end_layout'
        },
        success: function( data, textStatus, jqXHR ) {

          if( data.success ) {

            flawless.frontend_editor.disable();

            if( typeof jQuery.fn.toolbar_message == 'function' ) {
              jQuery.fn.toolbar_message( 'Layout saved.', { type: 'success' });
            } else {
              flawless.add_notice( 'Layout editor enabled.', { type: 'success' });
            }

            jQuery( edit_button ).text( jQuery( edit_button ).attr( 'original_label' ) );

            flawless.frontend_editor = false;

          } else {

            if( typeof jQuery.fn.toolbar_message == 'function' ) {
              jQuery.fn.toolbar_message( 'Error saving layout, no response from server.', { type: 'error', dim: false });
            } else {
              flawless.add_notice( 'Error saving layout, no response from server.', { type: 'error' });
            }

          }

          jQuery( edit_button ).data( 'disable_click', false );

        },
        dataType: "json"
      });

    }
  }


  /*
   * Applies equalHeights to various elements.
   *
   * Ran twice, once on document.ready and then on windows.load to avoid getting stuck on external assets
   *
   */
  function flawless_resize_dom_elements() {
    flawless.log( "Applying equalHeights()" );

    jQuery( ".cfct-build .row" ).each( function() {
      jQuery( ".equal_heights", this ).equalHeights();
    });

  }


  /*
   * Handles header dropdown menus.
   *
   */
  function flawless_header_dropdown_menus() {

    var all_tabs = jQuery( 'div.disbl div' ).length;
    var dropdown_wrapper = jQuery( ".flawless_header_dropdown_links" );
    var dropdown_section_wrapper = jQuery( ".flawless_header_expandable_sections" );
    var dropdown_sections = jQuery( ".flawless_header_expandable_sections .header_dropdown_div" );

    /* Reset sections after they are loaded to normal hidden settings */
    jQuery( dropdown_sections ).css( 'position','static' );
    jQuery( dropdown_sections ).css( 'left','0' );
    jQuery( dropdown_sections ).hide();

    jQuery( 'ul.log_menu li a' ).click( function( e ) {

      var this_link = this;
      var open_section = jQuery( ".flawless_header_expandable_sections .header_dropdown_div:visible" );
      var open_section_id = jQuery( open_section ).attr( "id" );

      /* Do nothing if a regular link was clicked */
      if( jQuery( this_link ).attr( 'href' ) != '#' ) {
        return;
      } else {
        e.preventDefault();
      }

      var this_tab = jQuery( this_link ).closest( ".flawless_tab_wrapper" );
      var section_id = jQuery( this ).attr( 'section_id' );
      var this_section = jQuery( "#" + section_id, dropdown_section_wrapper );

      if( jQuery( this_section ).is( ":visible" ) ) {
        var this_section_open = true;
        //flawless.log( "this section is open" );
      } else {
        var this_section_open = false;
        //flawless.log( "this section is closed" );
      }

      /* If clicked section is already open, we close it */
      if( this_section_open && ( section_id == open_section_id ) ) {
        jQuery( this_section ).slideUp();
        //flawless.log( "closing this section" );
        return;
      }

      /* If a section is open, and we re switching sections, close open one first */
      if( open_section.length ) {
        jQuery( open_section ).slideUp( "fast", function() {

          /* Open new section */
          jQuery( this_section ).slideDown( "slow", function() {
            flawless_header_section_opened();
          });


        });
      } else {

        /* Open new section */
        jQuery( this_section ).slideDown( "slow", function() {
          flawless_header_section_opened();
        });

      }

    });

  }


  /*
   * Executed when a header dropdown section is opened.
   *
   */
  function flawless_header_section_opened() {

    /* Render the Google Map is header location dropdown.  */
    if( jQuery( "li.header_contact_section" ).is( ":visible" ) && jQuery( "li.header_contact_section" ).height() > 0 ) {
      jQuery( "li.header_contact_section" ).equalHeights();
    }

    if( jQuery( "li.header_login_section" ).is( ":visible" ) && jQuery( "li.header_login_section" ).height() > 0 ) {
      jQuery( "li.header_login_section" ).equalHeights();
    }

  }


  /**
   * Debug Mode Log
   *
   * Displays a message in the console log if current user is in debug mode.
   *
   * @author potanin@UD
   */
  if( typeof flawless.log != 'function' ) {
    flawless.log = function( text, type ) {

      if( typeof( flawless ) === "undefined" || ( !flawless.developer_mode || typeof console != "object" || typeof console.log != "function" ) ) {
        return;
      }

      if( type == "" || typeof type == "undefined" ) {
        type = "log";
      }

      if( typeof text == 'string' ) {
        eval( "console." + type + "( 'J:' + text )" );
      } else {
        eval( "console." + type + "( text )" );
      }

    }
  }


  /**
   * Validates e-mail address.
   *
   * Source: http://www.white-hat-web-design.co.uk/articles/js-validation.php
   *
   */
  function flawless_email_validate( email ) {
     var reg = /^( [A-Za-z0-9_\-\.] )+\@( [A-Za-z0-9_\-\.] )+\.( [A-Za-z]{2,4})$/;
      if( reg.test( email ) == false ) {
        return false;
     }

     return true;
  }



