/* =========================================================
 * jquery.ud.form_helper.js v1.0.0
 * http://usabilitydynamics.com
 * =========================================================
 * Copyright 2011 Usability Dynamics, Inc.
 *
 * Version 0.0.1
 * Validation: http://www.jslint.com/
 *
 * Handles various functions related to forms.
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

  jQuery.fn.form_helper = function ( s ) {

    /* Set Settings */
    s = jQuery.extend( {
      element: this,
      ajax: {},
      ux: {},
      timers: {},
      helpers: {},
      classes: [ 'error', 'success', 'warning', 'trip', 'blank' ],
      input_classes: {
        checkbox: {
          on:  'c_on',
          off: 'c_off'
        }
      },
      ajax_form_class: 'form-ajax',
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
     * Style checkboxes
     *
     * @author potanin@UD
     */
    function setup_labels() {

      if( !jQuery('.checkbox.styled input').length) {
        return;
      }

      jQuery( '.checkbox.styled' ).each( function() {
        jQuery( this ).closest( 'label' ).removeClass( s.input_classes.checkbox.on ).addClass( s.input_classes.checkbox.off );
      });

      jQuery( '.checkbox.styled input:checked' ).each( function() {
        jQuery( this ).closest( 'label' ).addClass( s.input_classes.checkbox.on ).removeClass( s.input_classes.checkbox.off );
      });

      jQuery('.checkbox.styled input').click(setup_labels);

    };


    /**
     * Prepare Form Handler to work with HTML5 Elements and Tags
     *
     * @author potanin@UD
     */
    function html5_support( form ) {

      jQuery( 'input[required]', form).each( function() {
        jQuery( this ).closest( '.control-group' ).addClass( 'validate' );
        jQuery( this ).closest( '.control-group' ).attr( 'validation_required', true );
        jQuery( this ).removeAttr( 'required' );

        switch( jQuery( this ).attr( 'type' ).toLowerCase() ) {

          case 'email' :
            jQuery( this ).closest( '.control-group' ).attr( 'validation_type', 'email' );
          break;

          case 'url' :
            jQuery( this ).closest( '.control-group' ).attr( 'validation_type', 'url' );
          break;

          case 'tel' :
            jQuery( this ).closest( '.control-group' ).attr( 'validation_type', 'tel' );
          break;

        }

        /* If pattern exists, we add our own CG class, but leave it in place for newer browsers */
        if( jQuery( this ).attr( 'pattern') ){
          jQuery( this ).closest( '.control-group' ).attr( 'validation_type', 'regex_pattern' );
          jQuery( this ).closest( '.control-group' ).attr( 'regex_pattern', jQuery( this).attr( 'pattern') );
        }

        if( jQuery( this ).attr( 'matches') ){
          jQuery( this ).closest( '.control-group' ).attr( 'validation_type', 'matches' );
          jQuery( this ).closest( '.control-group' ).attr( 'matches', jQuery( this).attr( 'matches') );
        }


      });

    }

    /**
     * Prepare Form Handler to work with ajax (server-side validation)
     *
     * @author odokienko@UD
     */
    function ajax_validation_support( form ) {

      jQuery( 'input[validation_ajax]', form).each( function() {
        jQuery( this ).closest( '.control-group' ).addClass( 'validate' );
        jQuery( this ).closest( '.control-group' ).attr( 'validation_ajax', jQuery(this).attr('validation_ajax'));
      });

    }


    /**
     * Handle form submission and prepare form element
     *
     *
     * @todo Minor bug if "enter" is pressed while editing the first field, the form will refocus user back on same field if there are errors elsewhere.
     * @author potanin@UD
     */
    function handle_submission( form ) {

      /* Prepare the forms data variable */
      if( typeof(  jQuery( form ).data( 'validation_fail' ) ) != 'object' ) {
         jQuery( form ).data( 'validation_fail', [] )
      }

      jQuery( form ).submit( function() {

        log( 'do_not_process: ' + jQuery( form ).data( 'do_not_process' ) );

        /* Check if there are any failed fields in the data field */
        if( jQuery( form ).data( 'validation_fail' ).length ) {

          /* Get first failed field */
          var field_name =  jQuery( form ).data( 'validation_fail' ).slice( 0, 1 );

          /* Focus on first failed field */
          jQuery( '[name="' + field_name + '"]', form ).focus();

          /* Run validation on the focused field so user knows of the problem */
          jQuery( form ).trigger( 'form_helper::' + field_name );

        }

        /* Stop the form. */
        if( jQuery( form ).data( 'do_not_process' ) ) {
          return false;
        }

        log( 'form_helper::success' );
        jQuery( form ).trigger('form_helper::success');

        if ( is_ajax_form( form ) ) return false;

      });

      return true;

    }

    /**
     * Check if form is ajax form
     *
     * @author korotkov@UD
     */
    var is_ajax_form = this.is_ajax_form = function( form ) {
      return jQuery( form ).hasClass( s.ajax_form_class );
    }

    /**
     * The main function ran when the script is initialized.
     *
     * @author potanin@UD
     */
    var enable = this.enable = function() {
      log( 'form_helper::enable()' );
      setup_labels();

      /* Monitor submit event */
      jQuery( s.element ).each( function() {

        var args = {
          form: this,
          initial_run: true
        };

        html5_support( args.form );

        ajax_validation_support ( args.form );

        handle_submission( args.form );

        /* Get all validated fields, perform initial validation, and attach $.change event */
        jQuery( '.control-group.validate', args.form ).each( function() {

          args.control_group = this;
          args.validation_required = jQuery( args.control_group ).attr( 'validation_required' ) == 'false' ? false : true;
          args.validation_ajax = jQuery( args.control_group ).attr( 'validation_ajax' );
          args.validation_type = jQuery( args.control_group ).attr( 'validation_type' );
          args.matches = jQuery( args.control_group ).attr( 'matches' );
          args.attributes = [];

          /* Convert all element attributes into an array */
          jQuery.each( args.control_group.attributes, function( index, attr ) {
            args.attributes[attr.name] = attr.value;
          });

          /* Check if conditional help exists */
          if( jQuery( '.conditional-help', args.control_group ).length ) {
            args.helpers = jQuery( '.conditional-help', args.control_group );
          } else {
            args.helpers = false;
          }

          jQuery( 'input, textarea, select', args.control_group ).each( function() {

            args.this_field = this;
            args.name = jQuery( this ).attr( 'name' );
            args.title = jQuery( this ).attr( 'title' );

            args.element_type = jQuery( args.this_field ).get(0).tagName.toLowerCase();

            if( args.validation_required ) {
              jQuery( args.this_field ).attr( 'aria-required', 'true' );
            }

            validate_field( args.this_field, args );

          });


        });

      });


    };


    /**
     * Validate Field
     *
     * Function executed on $.ready() and everytime the input is updated.
     *
     * @author potanin@UD
     */
    var validate_field = this.validate_field = function( field, args ) {

      /* Merge passed args with defaults */
      var args = jQuery.extend( true, {
        validation_type: 'not_empty',
        result: 'success'
      }, args );

      /* Always get the current value */
      args.value = jQuery( args.this_field ).val();

      log( '1) ' + args.name + ' value: ' + jQuery( args.this_field ).val()  + ' - ' + ( args.initial_run ? ' Initial Run ' : ' Secondary Run' ) );

      switch ( args.validation_type ) {
        //** @author korotkov@ud */
        case 'checked':

          if ( jQuery(args.this_field).attr('type') != 'checkbox' ) {
            args.result = 'success';
          } else {
            if ( jQuery(args.this_field).is(':checked') ) {
              args.result = 'success';
            } else {
              args.result = 'error';
            }
          }

          log( '2) ' + args.name + ' - Checked Validation. Result: ' + args.result );

          break;

        case 'not_empty':

          if( args.value == '' ) {
            args.result = 'blank';
            args.message = "This is a required field";
          } else {
            args.result = 'success';
          }

          log( '2) ' + args.name + ' - Not Empty Validation. Result: ' + args.result );

        break;

        case 'matches':

          if( args.value == '' ) {
            args.result = 'blank';
          } else if (jQuery("[name="+args.matches+"]:input",args.form).val() == args.value){
            args.result = 'success';
          } else {
            args.result = 'error';
            args.message= (typeof args.title != 'undefined') ? args.title : "The field are not equal!";
          }

          log( '2) ' + args.name + ' - Matches Validation. Result: ' + args.result );

        break;

        case 'email':

          if( args.value == '' ) {
            args.result = 'blank';
          } else if( s.helpers.validate_email( args.value ) ) {
            args.result = 'success';
          } else {
            args.result = 'error';
          }

          log( '2) ' + args.name + ' - Email Validation. Result: ' + args.result );

        break;

        case 'url':

          if( args.value == '' ) {
            args.result = 'blank';

          } else if( s.helpers.validate_url( args.value ) ) {
            args.result = 'success';

          } else {
            args.result = 'error';

          }

          log( '2) ' + args.name + ' - URL Validation. Result: ' + args.result );

        break;

        case 'domain':

          if( args.value == '' ) {
            args.result = 'blank';

          } else if( s.helpers.validate_url( args.value, {use_http:false} ) ) {
            args.result = 'success';

          } else {
            args.result = 'error';

          }

          log( '2) ' + args.name + ' - Domain Validation. Result: ' + args.result );

        break;


        case 'regex_pattern':

          var this_regex = new RegExp( args.attributes.regex_pattern ,"g");

          if ( args.value == '' ) {
            args.result = 'blank';

          } else if( this_regex.test( args.value ) ) {
            args.result = 'success';

          } else {
            args.result = 'error';
            args.message= "Please, match the requested format" + ((typeof args.title != 'undefined') ? ":" + args.title : '') ;
          }

          log( '2) ' + args.name + ' - Custom Regex Validation. Result: ' + args.result );

        break;

        default:

          log( '2) ' + args.name + ' - Unknown Validation (' + args.validation_type + ') . Result: ' + args.result );

        break;

      }

      if( args.result == 'success' && args.validation_ajax ) {

        jQuery.ajax({
          url: ajaxurl,
          data: {
            action: args.validation_ajax,
            field_name: args.name,
            field_value: args.value,
            field_type: args.element_type
          },
          success: function(result) {

            args.result = (result.success=='false') ? 'error' : 'success';
            args.message = result.message;
            /*if( args.result != 'success' ) {
              jQuery(args.control_group).removeClass('success');
              jQuery(args.control_group).addClass( args.result );
            }else{
              jQuery(args.control_group).removeClass('error');
            }*/
            markup_element( args );

          },
          dataType: "json"
        });
        log( '2) ' + args.name + ' - Custom Ajax Validation. Result: ' + args.result );
      }

      if( args.validation_required ) {

        var validation_fail = jQuery( args.form ).data( 'validation_fail' );

        var temp1 = validation_fail;
        log( temp1 );

        /* If Validation is Required for submissing, and the result is anything other than success - we stop the form */
        if( args.result != 'success' ) {
          log( '3) Whoa! ' + args.name + ' IS REQUIRED! inArray(): ' + jQuery.inArray( args.name, validation_fail )  );

          if( jQuery.inArray( args.name, validation_fail ) < 0 ) {
            validation_fail.push( args.name );
          }

        } else {
          log( '3) Whoop whoop. ' + args.name + ' IS CLEARED HOT!' );

          /* If the element passed validation, ensure it's not in the validation_fail array */

          validation_fail = s.helpers.remove_from_array( args.name, validation_fail );

        }

        var temp = validation_fail;
        log( temp);

        /* Class added for quick reference, not markup */
        if( validation_fail.length ) {
          log( "4) This form will not submit." );

          jQuery( args.form ).addClass( 'validation_fail' );
          jQuery( args.form ).data( 'do_not_process', true );
        } else {

          log( "4) Form is validated and ready to process!" );

          jQuery( args.form ).removeClass( 'validation_fail' );
          jQuery( args.form ).removeData( 'do_not_process' );
        }

        jQuery( args.form ).data( 'validation_fail', validation_fail );

      }


      /* If this is not initialization, do the markup */
      if( !args.initial_run ) {
        markup_element( args )
      }

      /* Unset the initial_run trigger */
      args.initial_run = false;

      jQuery( args.this_field ).unbind( 'change' ).bind( 'change', function() {
        validate_field( args.this_field, args );
      });

      jQuery( args.form ).unbind( 'form_helper::' + args.name ).bind( 'form_helper::' + args.name, function(      ) {
        log( 'Trigger: form_helper::' + args.name );
        validate_field( args.this_field, args );
      });


    }


    /**
     * Assist with form interaction
     *
     * @author potanin@UD
     */
    var markup_element = this.markup_element = function( args ) {

      /* Remove all classes from CG */
      jQuery( args.control_group ).removeClass( s.classes.join( ' ' ) );

      /* If this is not the initial run - render Applicable Notices, if exists */
      if( args.helpers ) {

        /* Hide all immediate children and remove any .active classes */
        jQuery( '> *', args.helpers ).hide().removeClass( 'active' );

        /* Find the helper by class, if exists, and show it */
        jQuery( '.' + args.result, args.helpers ).addClass( 'active' ).show();

      }

      if( args.validation_required && args.result != 'success' ) {
        jQuery( args.control_group ).addClass( args.result );

        if( args.result == 'blank' ) {
          jQuery( args.control_group ).addClass( 'error' );
        }
        s.helpers.message (args.control_group,args.message);
      } else if ( args.result == 'success' ) {
        jQuery( args.control_group ).addClass( args.result );
        s.helpers.message (args.control_group,'');
      }


    }


    /**
     * Helper
     *
     * @author potanin@UD
     */
    if( typeof s.helpers.remove_from_array != 'function' ) {
      s.helpers.remove_from_array = function( value, arr ) {
        return jQuery.grep(arr, function(elem, index) {
          return elem !== value;
        });
      }
    }

    /**
     * Helper
     *
     * @author potanin@UD
     */
    if( typeof s.helpers.validate_url != 'function' ) {
      s.helpers.validate_url = function( value, args ) {
        args = jQuery.extend({
          use_http:true
        }, args);
        if ( args.use_http ) return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value);
        return /^(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value);
      }
    }



    /**
     * Helper
     *
     * @author potanin@UD
     */
    if( typeof s.helpers.validate_email != 'function' ) {
      s.helpers.validate_email = function( value ) {
       var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
       return re.test( value );
      }
    }

    /**
     * Helper
     *
     * @author odokienko@UD
     */
    if( typeof s.helpers.message != 'function' ) {
      s.helpers.message = function( for_field,  text ) {

        var control_group = (jQuery(for_field).hasClass('control-group')) ? for_field : jquery(for_field).closest( '.control-group');
        var inline_help = jQuery("span.help-inline",control_group);

        if (inline_help.length){
          inline_help.remove();
        }
        if(text)
          jQuery(".controls",control_group).append("<span class=\"help-inline\">" + text + "</span>");

      }
    }


    /**
     * Assist with form interaction
     *
     * @author potanin@UD
     */
    var legacy = this.legacy = function( form ) {

      /* Label clicking automatically defaults to input element */
      jQuery( '.control-group > .control-label', form ).click( function() {
        jQuery( 'input', jQuery( this ).closest( '.control-group' ) ).focus();
      });

      /* Monitor selection limits when enforced */
      jQuery( '.selection_limit', form ).each( function() {
        var args = {
          wrapper: this,
          limit: jQuery( this ).attr( 'selection_limit' )
        }

        if( jQuery( args.wrapper ).closest( '.group-assistant' ).length ) {
          args.assistant =  jQuery( '.assistant-content', jQuery(args.wrapper).closest( '.group-assistant' ) );
        } else {
          args.assistant = false;
        }

        jQuery( 'input[type="checkbox"]', args.wrapper ).change( function() {

          args.current_selection = jQuery( 'input[type="checkbox"]:checked', args.wrapper ).length;

          if( args.current_selection > args.limit ) {
            jQuery( this ).removeAttr( 'checked' );
            args.current_selection = ( args.current_selection - 1 );
          }

          if( args.assistant ) {
            args.more_items = args.limit - args.current_selection;

            if( jQuery( '.limit_counter', args.assistant ).length ) {
              args.limit_counter = jQuery( '.limit_counter', args.assistant );
            } else {
              jQuery( args.assistant ).append( args.limit_counter = '<p class="limit_counter"></p>' );
            }

            if( args.more_items == 0 ) {
              jQuery( args.limit_counter ).html( 'You may not select any more items.' );
            } else {
              jQuery( args.limit_counter ).html( 'You may select ' + args.more_items + ' more item' +  ( args.more_items > 1 ? 's' : '' ) + '.' );
            }

          }

        });

      });

      /* Render focus help for individual items */
      jQuery( '.group-assistant' , form ).each( function() {
        var args = {
          wrapper: jQuery( this ),
          assistant: jQuery( '.control-group-assistant', this ),
          form: jQuery( this ).closest( 'form' ),
          widths: {},
          timer: false
        }

        /* Get widths */
        args.widths.wrapper = jQuery( args.wrapper ).width();
        args.widths.group = jQuery( '.control-group', args.wrapper ).width();
        args.widths.assistant = ( args.widths.wrapper - args.widths.group );

        /* Reposition assistant */
        args.assistant.width( args.widths.assistant );

        /* Monitor user interaction */
        jQuery( args.wrapper ).mouseover( function() {
          clearTimeout( args.timer );

          /* Hide all other assistants */
          //jQuery( '.control-group-assistant', args.form ).fadeTo( 1, 0 );

          jQuery( args.assistant ).fadeTo( 500, 1 );

        }).mouseout( function() {
          args.timer = setTimeout( function() {
            jQuery( args.assistant ).fadeTo( 500, 0 );
          }, 1000);

        });

      });

      /* Render focus help for individual items */
      jQuery( '.control-group .focus-help', form ).each( function() {
        var help_container = jQuery( this );
        var control_group = help_container.closest( '.control-group');

        /* Hide on default */
        jQuery( help_container ).hide();

        jQuery( '.primary-input', control_group ).focus( function() {
          help_container.fadeIn();

        }).focusout( function() {
          help_container.fadeOut();

        });

      });

    }

    /* Enable functionality on each instance */
    enable( this );

    /* Return object for chaining */
    return this;

  };

} ( jQuery ) );