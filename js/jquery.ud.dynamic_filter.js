/* =========================================================
 * jquery.ud.dynamic_filter.js v1.0.8
 * http://usabilitydynamics.com
 * =========================================================
 * Copyright 2011 Usability Dynamics, Inc.
 *
 * Version 0.0.2
 * Validation: http://www.jslint.com/
 *
 * Copyright (c) 2012 Usability Dynamics, Inc. ( usabilitydynamics.com )
 * ========================================================= */

/*jslint devel: true, undef: true, browser: true, continue: true, unparam: true, debug: true, eqeq: true, vars: true, white: true, newcap: true, plusplus: true, maxerr: 50, indent: 2 */
/*global window */
/*global console */
/*global clearTimeout */
/*global setTimeout */
/*global jQuery */

( function ( jQuery ) {
  "use strict";

  jQuery.fn.dynamic_filter = function ( s ) {

    /* Set Settings */
    this.s = s = jQuery.extend( true, {
      ajax: {
        args: {},
        async: true,
        format: 'json'
      },
      callbacks: {
        result_format: function ( result ) { return result; }
      },
      attributes: {},
      settings: {
        auto_request: true,
        server_driven: false,
        per_page: 25,
        dom_limit: 200,
        filter_query: {},
        request_range: {},
        timers: {
          notice: {
            dim: 5000,
            hide: 5000
          },
          filter_intent: 1600
        },
        debug: false,
        log_detail: {
          timers: false,
          debug: true
        },
        unique_tag: false,
        messages: {
          no_results: 'No results found.',
          loading: 'Loading...',
          server_error: 'Could not retrieve results due to a server error, please notify the website administrator.'
        }
      },
      classes: {
        ajax_loading: 'ajax_loading',
        server_fail: 'server_fail',
        have_results: 'have_results',
        results_wrapper: 'results_wrapper',
        results: 'results',
        result_row: 'result_row',
        result_data: 'result_data',
        list_item: 'list_item',
        attribute_count: 'filter_usage_count',
        attribute_value: 'attribute_value',
        filter: 'filter',
        filter_trigger: 'filter_trigger',
        filter_item_wrapper: 'filter_item_wrapper',
        inputs_list_wrapper: 'inputs_list_wrapper',
        selected_page: 'current',
        disabled_item: 'disabled_item',
        status: {
          success: 'alert-success',
          error: 'alert-error'
        }
      },
      ux: {
        element: this,
        results_wrapper: jQuery( '<div class="results_wrapper"></div>' ),
        results: jQuery( '<div class="results"></div>' ),
        filter: jQuery( '<div class="filter"></div>' ),
        load_more: jQuery( '<div class="load_more"></div>' ),
        status: jQuery( '<div class="status_container"></div>' )
      },
      data: {
        filterable_attributes: {},
        unique_ids: []
      },
      active_timers: {
        status: {}
      }
    }, s );


    /**
     * Internal logging function
     *
     * Builds array of filtertable attributes, which are necessary for server-driven ajax calls.
     * Should not return anything.
     *
     * @author potanin@UD
     */
    var log = this.log = function ( something, type ) {

      if ( !s.settings.debug || !window.console ) {
        return;
      }

      if ( window.console && console.debug ) {

        switch ( type ) {

          case 'error':
            console.error( something );
          break;

          case 'info':
            console.info( something );
          break;

          case 'time':
            if( typeof console.time != 'undefined' ) { console.time( something ); }
          break;

          case 'timeEnd':
            if( s.settings.log_detail.timers && typeof console.timeEnd != 'undefined' ) { console.timeEnd( something ); }
          break;

          case 'debug':
            if( s.settings.log_detail.debug && typeof console.debug != 'undefined' ) { console.debug( something );  } else { log( something ); }
          break;

          case 'dir':
            if( s.settings.log_detail.debug && typeof console.dir != 'undefined' ) { console.dir( something ); } else { console.log( something ); }
          break;

          case 'warn':
            if( typeof console.warn != 'undefined' ) { console.warn( something ); } else { console.log( something ); }
          break;

          case 'clear':
            if( typeof console.clear != 'undefined' ) { console.clear(); }
          break;

          default:
            console.log( something );
          break;

        }

      }

    };


    /**
     * Create the main status bar container or add a message to it
     *
     * @author potanin@UD
     */
    var status = this.status = function ( message, this_status ) {

      /* Set Settings */
      this_status = jQuery.extend( true, {
        element: s.ux.status,
        type: 'default',
        hide: s.settings.timers.notice.hide
      }, this_status );

      log( 'dynamic_filter::status( ' + message + ' ), type: ' + this_status.type );

      if( message == '' ) {
        jQuery( s.ux.status ).html( '' );
        jQuery( s.ux.status ).hide();
      }

      /* Save original classes, if they are not yet saved */
      if( !jQuery( s.ux.status ).data( 'original_classes' ) ) {
        jQuery( s.ux.status ).data( 'original_classes' , jQuery( s.ux.status ).attr( 'class' ) );
      }

      /* Set classes to original ( to clear out any new classes added previously by this function */
      jQuery( s.ux.status ).attr( 'class' , jQuery( s.ux.status ).data( 'original_classes' ) );

      /* Remove any old timers */
      clearTimeout( s.active_timers.status.hide );

      /* Show the message container */
      if( !jQuery( s.ux.status ).is( ':visible' ) ) {
        jQuery( s.ux.element ).before( s.ux.status );
      }

      /* Add a custom class if passeed */
      if( typeof s.classes.status[ this_status.type ] == 'string' ) {
        jQuery( s.ux.status ).addClass( s.classes.status[ this_status.type ] );
      }

      s.ux.status.html( message );

      /* If Trigger callback is set, we call it, otherwise bind Dismiss action */
      if( typeof this_status.click_trigger != 'undefined' ) {
        jQuery( s.ux.status ).one( 'click', function() {
          jQuery( document ).trigger( this_status.click_trigger );
        } );

      } else {

        if( typeof jQuery.fn.alert == 'function' ) {
          jQuery( s.ux.status ).prepend( jQuery( '<a class="close" data-dismiss="alert" href="#">&times;</a>' ) );
          jQuery( s.ux.status ).alert();
        }

      }

      /* Schedule removal */
      if ( this_status.hide ) {
        s.active_timers.status.hide = setTimeout( function () {
          jQuery( s.ux.status ).fadeTo( 3000, 0, function () {
            jQuery( s.ux.status ).hide();
          } );
        }, this_status.hide );
      }

    };


    /**
     * Analyze the specified attributes, must be run before AJAX request.
     *
     * Builds array of filtertable attributes, which are necessary for server-driven ajax calls.
     *
     * @author potanin@UD
     */
    var analyze_attributes = this.analyze_attributes = function () {
      log( 'dynamic_filter::analyze_attributes' );

      jQuery( document ).trigger( 'dynamic_filter::analyze_attributes::initialize' );

      s.ajax.args.attributes = {};
      s.ajax.args.filter_query = {};

      jQuery.each( s.attributes, function ( attribute_key , attribute_settings ) {

        /* Merge Attribute Settings with defaults */
        s.attributes[ attribute_key ] = jQuery.extend( true, {
          label: '',
          display: true,
          collapse: false,
          filter: false,
          filter_type: 'checkbox',
          values: {},
          render_callback: function( default_value , args ) {
            return default_value;
          }
        }, s.attributes[ attribute_key ] );

        if( attribute_settings.filter ) {
          s.data.filterable_attributes[ attribute_key ] = {
            filter_type: s.attributes[ attribute_key ].filter_type
          };

          s.ajax.args.filter_query[ attribute_key ] = [];

          s.ajax.args.attributes[ attribute_key ] = {
            label: s.attributes[ attribute_key ].label,
            filter_type: s.attributes[ attribute_key ].filter_type,
            values: s.attributes[ attribute_key ].values
          };

        }

      });

      jQuery( document ).trigger( 'dynamic_filter::analyze_attributes::complete' );

    };


    /**
     * Prepare DOM by rendering elements and adding custom classes. Ran once.
     *
     * @author potanin@UD
     */
    var prepare_ui = this.prepare_ui = function () {
      log( 'dynamic_filter::prepare_ui' );

      /* The Results Wrapper is rendered automatically */
      if( jQuery( s.ux.results_wrapper ).not( ':visible' ) ) {
        //log( 'dynamic_filter::prepare_ui - s.ux.results_wrapper is :not( :visible ) - Rendering the Results Wrapper ', 'debug' );
        jQuery( s.ux.element ).prepend( s.ux.results_wrapper );

      }

      /* The Results Container is rendered automatically */
      if( jQuery( s.ux.results ).not( ':visible' ) ) {
        //log( 'dynamic_filter::prepare_ui - s.ux.results is :not( :visible ) - Rendering the Results container ', 'debug' );
        jQuery( s.ux.results_wrapper ).append( s.ux.results );

      }

      /* Append the results DOM element to the wrapper element. */
      if( !jQuery( s.ux.filter, 'body' ).is( ':visible' ) ){
        jQuery( s.ux.element ).prepend( s.ux.filter );
        jQuery( s.ux.filter ).addClass( s.classes.filter );

      }

      /* Add Standard Class to all UX Elements */
      jQuery( s.ux ).each( function() {
        jQuery( s.ux.element ).addClass( 'df_element' );

      } );

      /* Add custom classes to UX elements */
      jQuery( s.ux.results_wrapper ).addClass( s.classes.results_wrapper );

    };


    /**
     * Renders the filters. Ran once.
     *
     * @author potanin@UD
     */
    var render_filters = this.render_filters = function () {
      log( 'dynamic_filter::render_filters' );

      jQuery( document ).trigger( 'dynamic_filter::render_filters::initiate' );

      if( typeof s.ux.filters != 'object' ) {
        log( 'dynamic_filter::render_filters - Creating DOM References for filters.' );
        s.ux.filters = {}
      }

      jQuery.each( s.data.filterable_attributes, function ( attribute_key , filter_data ) {

        /* Create references for Wrapper, Label and List - if they don't exist */
        s.ux.filters[ attribute_key ] = {
          wrapper: jQuery( '<div class="' + s.classes.inputs_list_wrapper + '" attribute_key="' + attribute_key +'"></div>' ),
          list: jQuery( '<ul class="inputs-list ' + s.attributes[ attribute_key ][ 'filter_type' ] +'" attribute_key="' + attribute_key + '"></ul>' ),
          items: {}
        };

        s.ux.filters[ attribute_key ].wrapper.attr( 'filter_type', s.attributes[ attribute_key ][ 'filter_type' ] );

        if( !s.attributes[ attribute_key ].hide_filter_label ) {
          switch ( s.attributes[ attribute_key ][ 'filter_type' ] ) {

            case 'input':
              s.ux.filters[ attribute_key ].label = jQuery( '<div class="filter_label">' + s.attributes[ attribute_key ].label + '</label>' );
            break;

            case 'slider':
            case 'range':
            case 'date_range':
              s.ux.filters[ attribute_key ].label = jQuery( '<div class="filter_label">' + s.attributes[ attribute_key ].label + '</label>' );
            break;

            case 'checkbox':
              s.ux.filters[ attribute_key ].label = jQuery( '<div class="filter_label">' + s.attributes[ attribute_key ].label + '</label>' );
            break;

          }
        }

        /* Create DOM elements for Wraper, Label and List */
        jQuery( s.ux.filter ).append( s.ux.filters[ attribute_key ][ 'wrapper' ] );
        jQuery( s.ux.filters[ attribute_key ][ 'wrapper' ] ).append( s.ux.filters[ attribute_key ][ 'label' ] );
        jQuery( s.ux.filters[ attribute_key ][ 'wrapper' ] ).append( jQuery( s.ux.filters[ attribute_key ][ 'list' ] ) );

        if( s.attributes[ attribute_key ].collapse ) {
          jQuery( s.ux.filters[ attribute_key ][ 'label' ] ).append( s.ux.filters[ attribute_key ].toggler = jQuery( '<span class="toggle_list">Toggle Filters</span>' ) );
          jQuery( s.ux.filters[ attribute_key ][ 'list' ] ).hide()
        }

        jQuery( s.ux.filters[ attribute_key ].label ).click( function () {
           jQuery( s.ux.filters[ attribute_key ][ 'list' ] ).toggle()
        } );

      } );

      jQuery( document ).trigger( 'dynamic_filter::render_filters::complete' );

    }


    /**
     * Gets data to match the Requested Range
     *
     * @author potanin@UD
     */
    var get_data = this.get_data = function ( event, args ) {
      jQuery( document ).trigger( 'dynamic_filter::get_data::initialize' );

      /* Merge defaults with passed args */
      args = jQuery.extend( true, {
        render_data: true,
        silent_fetch: false
      }, args );

      /* Set ranges */
      if( !s.settings.request_range.start ) {
        s.settings.request_range.start = 0;
      }

      if( !s.settings.request_range.end ) {
        s.settings.request_range.end =  s.settings.dom_limit - s.settings.per_page;
      }

      /* Combine defined AJAX args with settings, which include query */
      var ajax_request = jQuery.extend( true, s.ajax.args, s.settings, {
        filterable_attributes: s.data.filterable_attributes
      });

      if( !args.silent_fetch ) {
        jQuery( document ).trigger( 'dynamic_filter::doing_ajax' );
      }
      
      jQuery.ajax({
        dataType: s.ajax.format,
        async: s.ajax.async,
        url: s.ajax.url,
        data: ajax_request,
        success: function ( ajax_response, textStatus, jqXHR ) {
          log( 'dynamic_filter::get_data - have AJAX response.', 'debug' );

          /* Apply any filters and callbacks to the s.data.all_results */
          var maybe_ajax_response = s.callbacks.result_format( ajax_response );

          /* Make sure callback returns something */
          if( maybe_ajax_response ) {
            ajax_response = maybe_ajax_response;
          }

          if( typeof ajax_response.all_results == 'object' ) {

            /* Merge existing All Results with new All Results */
            ajax_response.all_results = jQuery.merge( s.data.all_results ? s.data.all_results : [] , ajax_response.all_results );

            /* Blank our All Results */
            s.data.all_results = [];
            s.data.current_filters = {};

            /* jQuery.merge() does something weird where this is necessary to make it into an array where .length can be calculated */
            jQuery.each( ajax_response.all_results, function( index, data ) {
              s.data.all_results[ index ] = data;
            });

            jQuery.each( ajax_response.current_filters, function( attribute_key, attribute_filters ) {
              s.data.current_filters[ attribute_key ] = {};
              jQuery.each( attribute_filters, function( index, data ) {

                if( typeof data.label != 'undefined' ) {
                  s.data.current_filters[ attribute_key ][ data.label ] = data;
                } else {
                  s.data.current_filters[ attribute_key ][ index ] = data;
                }

              });
            });

            //log( s.data.current_filters, 'dir' );

            s.data.total_results = ajax_response.total_results ? ajax_response.total_results : s.data.all_results.length;

            s.data.server_driven = true;

          } else {
            /* If response is Client Handled Data, load response directly into s.data.all_results */
            s.data.all_results = ajax_response;

          }

          /* Make sure that All Results contains data, or else we fail */
          if( !jQuery.isEmptyObject( s.data.all_results ) ) {
            jQuery( document ).trigger( 'dynamic_filter::get_data::complete', args );

          } else {
            status( s.settings.messages.no_results , { type: 'error', hide: false, click_trigger: ( s.settings.debug ? 'dynamic_filter::get_data' : '' ) });
            jQuery( document ).trigger( 'dynamic_filter::get_data::fail', args );

          }

        },
        error: function ( jqXHR, textStatus, errorThrown ) {
          status( s.settings.messages.server_error , { type: 'error', hide: false, click_trigger: ( s.settings.debug ? 'dynamic_filter::get_data' : '' ) });
          jQuery( document ).trigger( 'dynamic_filter::get_data::fail', args );

        }
      });


    };


    /**
     * Load results if they are not set, analayze the results, create DOM object, and create filters ( if needed )
     *
     * Converts s.data.all_results into jQuery DOM Objects
     *
     * @author potanin@UD
     */
    var render_data = this.render_data = function ( event, args ) {
      log( 'dynamic_filter::render_data' );

      jQuery( document ).trigger( 'dynamic_filter::render_data::initialize' );

      /* Merge defaults with passed args */
      args = jQuery.extend( true, {}, args );

      /* Set default visible range */
      if( !s.settings.visible_range ) {
        s.settings.visible_range = {
          start: 0,
          end: s.settings.per_page
        }
      }

      /* If Rendered Query does not match Filter Query, then we clear out all old results */
      if( typeof s.data.rendered_query != 'undefined'  && s.data.rendered_query != JSON.stringify( s.ajax.args.filter_query ) ) {
        log( 'Query has been changed, blanking out results.' );
        jQuery( s.ux.results ).html( '' );
      }

      /* Fix opacity no matter what */
      jQuery( s.ux.results ).css( 'opacity', 1 );

      jQuery( s.ux.element ).addClass( s.classes.have_results );

      s.data.rendered_query = JSON.stringify( s.ajax.args.filter_query );

      log( 'dynamic_filter::render_data - s.data.total_results:' +  s.data.total_results );

      /* Cycle through All Results, and add them to s.ux.results DOM object */
      jQuery.each( s.data.all_results , function ( count, result_row ) {

        /* If this row already has a DOM object, skip it */
        if( typeof s.data.all_results[ count ].dom_object == 'object' ) {
          return;
        }

        var this_item = {
          row: jQuery( '<div></div>' ),
          attribute_wrapper: jQuery( '<ul></ul>' ),
          result_count: ( parseInt( count ) + 1 )
        };

        /* If Unique IDs are used */
        if( s.settings.unique_tag ) {

          this_item.unique_id = result_row[ s.settings.unique_tag ];

          if( this_item.unique_id != '' ) {

            /* Add this Unique ID to array of Uniques */
            s.data.unique_ids.push( this_item.unique_id );

            /* Set Unique ID */
            this_item.row.attr( 'unique_id' , this_item.unique_id );

          } else {
            log( 'Warning - result item #' + this_item.result_count + ' missing Unique ID', 'error' );
          }

        }

        /* Cycle through individual attributes in the result row */
        jQuery.each( result_row , function ( attribute_key, attribute_value ) {

          /* Skip if returned attribute has not been defined */
          if( !s.attributes[ attribute_key ] ) {
            return;
          }

          /* Create blank DOM container element for current attribute */
          var this_attribute = jQuery( '<li></li>' );

          if( s.attributes[ attribute_key ].sort_order ) {
            this_attribute.attr( 'sort_order',  parseInt( s.attributes[ attribute_key ].sort_order ) );
          }

          /* Concatenate array values */
          if( jQuery.isArray( attribute_value ) ) {
            attribute_value = attribute_value.join( ', ' );
          }

          /* Apply filters to attribute value and create printable_value, which is used for display only */
          var printable_value = s.attributes[ attribute_key ].render_callback( attribute_value, { result_row: result_row } );

          /* If displayable attribute, insert into the container element, and append the container element to the row's item DOM container ( this_item.data ) */
          if( s.attributes[ attribute_key ].display ) {
            this_attribute.html( printable_value );
            this_item.attribute_wrapper.prepend( this_attribute );
          }

          /* Add our filter as a DOM argument to the row */
          if( s.attributes[ attribute_key ].filter ) {
            this_item.row.attr( attribute_key, attribute_value );
          }

          /* Add CSS Classes and Attribute Key argument */
          this_attribute.addClass( s.classes.list_item ).attr( 'attribute_key', attribute_key );

        });

        sort_elements( this_item.attribute_wrapper, jQuery( '.' + s.classes.list_item, this_item.attribute_wrapper ) );

        /* Append the attribute list element to the row element */
        this_item.row.append( this_item.attribute_wrapper );

        /* Update count agument  */
        this_item.row.attr( 'result_count' , this_item.result_count );

        /* Add CSS Classes */
        this_item.row.addClass( s.classes.result_row );
        this_item.attribute_wrapper.addClass( s.classes.result_data );

        /* Save the newly created jQuery DOM Object back into the All Results object, ovewritting the regular array object */
        s.data.all_results[ count ].dom_object = this_item.row;

        /* Append row to DOM */
        s.ux.results.append( this_item.row.hide() );

        /* Show current row if it is within the Visible Range */
        if( s.settings.visible_range.start <= count && count < s.settings.visible_range.end ) {
          this_item.row.show().css( 'opacity' , 0 ).delay( count * 20 ).fadeTo( 500, 1 );
        };

      }); /* Individual row item processing complete */

      jQuery( document ).trigger( 'dynamic_filter::render_data::complete' );

      return true;

    }


    /**
     * Update filters' values and counts.
     *
     * @author potanin@UD
     */
    var update_filters = this.update_filters = function () {
      jQuery( document ).trigger( 'dynamic_filter::update_filters::initialize' );

      jQuery.each( s.data.current_filters , function ( attribute_key, attribute_filters ) {

        /* Can happen if server returns a non-existing Current Filter - e.g. if result was cached */
        if( typeof s.attributes[ attribute_key ] == 'undefined' ) {
          return;
        }

        /* Default Filter Type is a checkbox */
        switch ( s.attributes[ attribute_key ][ 'filter_type' ] ) {

          case 'input':

            if( !s.ux.filters[ attribute_key ][ 'filters_rendered' ] ) {

              s.ux.filters[ attribute_key ][ 'filters_rendered' ] = true;

              var this_element = {
                wrapper: jQuery( '<li class="' + s.classes.filter_item_wrapper + '"></li>' ),
              }

              jQuery( s.ux.filters[ attribute_key ][ 'list' ] ).append( this_element.wrapper );

              this_element.trigger = jQuery( '<input type="text" class="' + s.classes.filter_trigger + '" attribute_key="' + attribute_key + '" placeholder="' + s.attributes[ attribute_key ].label + '" >' );
              this_element.label_wrapper = jQuery( '<label class="input"></label>' );

              jQuery( this_element.wrapper ).append( this_element.label_wrapper );

              jQuery( this_element.label_wrapper ).append( this_element.trigger );
              jQuery( this_element.label_wrapper ).append( this_element.label );

              jQuery( document ).bind( 'dynamic_filter::update_filters::complete', function() {});

              jQuery( this_element.trigger ).keyup( function( event ) {
                s.ajax.args.filter_query[ attribute_key ] = this_element.trigger.val();
                jQuery( document ).trigger( 'dynamic_filter::execute_filters' );

              });

            }

          break;

          case 'slider':
          case 'range':

            if( !s.ux.filters[ attribute_key ][ 'filters_rendered' ] ) {

              s.ux.filters[ attribute_key ][ 'filters_rendered' ] = true;

              var this_element = {
                min: jQuery( '<input type="text" class="start_range" />' ),
                max: jQuery( '<input  type="text" class="end_range" />' ),
                slider: jQuery( '<div class="range_slider" /></div>' ),
                slider_label: jQuery( '<div class="range_slider_label"></div>' )
              }

              s.ux.filters[ attribute_key ][ 'items' ] = this_element;

              jQuery( s.ux.filters[ attribute_key ][ 'list' ] ).append( this_element.min );
              jQuery( s.ux.filters[ attribute_key ][ 'list' ] ).append( this_element.max );
              jQuery( s.ux.filters[ attribute_key ][ 'list' ] ).append( this_element.slider );
              jQuery( s.ux.filters[ attribute_key ][ 'list' ] ).append( this_element.slider_label );
              
              var filter_settings = s.data.current_filters[ attribute_key ];         

              if( typeof jQuery.fn.slider == 'function' ) {
              
                s.ux.filters[ attribute_key ][ 'items' ].min.hide();
                s.ux.filters[ attribute_key ][ 'items' ].max.hide();

                s.ux.filters[ attribute_key ][ 'items' ].slider.slider({
                  range: true,
                  min: parseInt( filter_settings.values.min ),
                  max: parseInt( filter_settings.values.max ),
                  values: [ parseInt( filter_settings.values.min ), parseInt( filter_settings.values.max) ],
                  slide: function( event, ui ) {
                    s.ux.filters[ attribute_key ][ 'items' ].min.val( ui.values[ 0 ] );
                    s.ux.filters[ attribute_key ][ 'items' ].max.val( ui.values[ 1 ] );
                    
                    s.ajax.args.filter_query[ attribute_key ] = { 
                      min: ui.values[ 0 ],
                      max: ui.values[ 1 ]
                    };
                    
                    jQuery( ui.handle ).html( ui.value );
                    
                    jQuery( document ).trigger( 'dynamic_filter::execute_filters' );
                    
                  }
                });

              }
              
              jQuery( document ).bind( 'dynamic_filter::update_filters::complete', function() {});

           }

          break;
          case 'date_range':

            if( !s.ux.filters[ attribute_key ][ 'filters_rendered' ] ) {

              s.ux.filters[ attribute_key ][ 'filters_rendered' ] = true;

              var this_element = {
                min: jQuery( '<input  type="text" class="start_range" />' ),
                separator: jQuery( '<span class="separator"> - </span>' ),
                max: jQuery( '<input  type="text" class="end_range" />' )
              }

              s.ux.filters[ attribute_key ][ 'items' ] = this_element;

              jQuery( s.ux.filters[ attribute_key ][ 'list' ] ).append( this_element.min );
              jQuery( s.ux.filters[ attribute_key ][ 'list' ] ).append( this_element.separator );
              jQuery( s.ux.filters[ attribute_key ][ 'list' ] ).append( this_element.max );

               jQuery( document ).bind( 'dynamic_filter::update_filters::complete', function() {

                var filter_settings = s.data.current_filters[ attribute_key ];

                if( typeof jQuery.fn.datepicker == 'function' ) {

                  s.ux.filters[ attribute_key ][ 'items' ].min.datepicker({
                    defaultDate: new Date( filter_settings.min ),
                    minDate: new Date( filter_settings.min ),
                    maxDate: new Date( filter_settings.max ),
                    onSelect: function() {
                      jQuery( document ).trigger( 'dynamic_filter::execute_filters' );
                    },
                    changeMonth: true
                  });

                  s.ux.filters[ attribute_key ][ 'items' ].max.datepicker({
                    defaultDate: new Date( filter_settings.max ),
                    minDate: new Date( filter_settings.min ),
                    maxDate: new Date( filter_settings.max ),
                    onSelect: function() {
                      jQuery( document ).trigger( 'dynamic_filter::execute_filters' );
                    },
                    changeMonth: true
                  });

                }

             });

           }

          break;

          case 'checkbox':

            jQuery.each( attribute_filters, function ( filter_key, filter_settings ) {

              if( !s.ux.filters[ attribute_key ][ 'items' ][ filter_key ] ) {

                var this_element = {
                  wrapper: jQuery( '<li class="' + s.classes.filter_item_wrapper + '"></li>' ),
                  value: filter_settings.value ? filter_settings.value : filter_settings.label
                };

                jQuery( s.ux.filters[ attribute_key ][ 'list' ] ).append( this_element.wrapper );

                s.ux.filters[ attribute_key ][ 'items' ][ filter_key ] = this_element;

                this_element.wrapper.attr( 'filter_key', filter_key );

                if( typeof s.attributes[ attribute_key ].values[ filter_settings.label ] != 'undefined' && typeof s.attributes[ attribute_key ].values[ filter_settings.label ].label != 'undefined' ) {
                  filter_settings.print_label = s.attributes[ attribute_key ].values[ filter_settings.label ].label;
                } else {
                  filter_settings.print_label = filter_settings.label;
                }

                this_element.label = jQuery( '<span class="' + s.classes.attribute_value + '">' + filter_settings.print_label + '</span> ' );

                this_element.trigger = jQuery( '<input type="checkbox" class="' + s.classes.filter_trigger + '" attribute_key="' + attribute_key + '" value="' + this_element.value + '">' );
                this_element.label_wrapper = jQuery( '<label class="checkbox"></label>' );
                this_element.count = jQuery( '<span class="' + s.classes.attribute_count + '"></span>' );

                jQuery( this_element.wrapper ).append( this_element.label_wrapper );

                jQuery( this_element.label_wrapper ).append( this_element.trigger );
                jQuery( this_element.label_wrapper ).append( this_element.label );
                jQuery( this_element.label_wrapper ).append( s.ux.filters[ attribute_key ][ 'items' ][ filter_key ].count );

                jQuery( this_element.trigger ).change( function () {

                  if( jQuery.inArray( this_element.value, s.ajax.args.filter_query[ attribute_key ] ) == -1 ) {
                    s.ajax.args.filter_query[ attribute_key ].push( this_element.value );

                  } else {
                    s.ajax.args.filter_query[ attribute_key ] = remove_from_array( this_element.value , s.ajax.args.filter_query[ attribute_key ] );

                  }

                  jQuery( document ).trigger( 'dynamic_filter::execute_filters' );

                });

              }

            });

            if( !s.ux.filters[ attribute_key ][ 'filters_rendered' ] ) {

              s.ux.filters[ attribute_key ][ 'filters_rendered' ] = true;

              jQuery( document ).bind( 'dynamic_filter::update_filters::complete', function() {
                var filter_settings = s.data.current_filters[ attribute_key ];

                jQuery.each( s.ux.filters[ attribute_key ][ 'items' ], function() {
                  var this_checkbox = filter_settings[ this.value ];

                  /* Update the displayed count - the number of times this attribute occurs in the available data set */
                  if( this_checkbox && this_checkbox.usage_count > 0 ) {
                    jQuery( this.wrapper ).removeClass( s.classes.disabled_item );
                    jQuery( this.count ).text( this_checkbox.usage_count ).show();
                    jQuery( this.trigger ).removeAttr( 'disabled' );

                  } else {
                    jQuery( this.wrapper ).addClass( s.classes.disabled_item );
                    jQuery( this.trigger ).attr( 'disabled', 'disabled' );
                    jQuery( this.count ).text( '' ).hide();

                  }

                });

              });

            }

          break;

          default:

            /* need a callback here */

          break;

        }

      });

      jQuery( document ).trigger( 'dynamic_filter::update_filters::complete' );

    }


    /**
     * Initiated after a filter has been changed, and user intent has been established.
     *
     * No triggers on purpose since this function can be called very often and rapidly when triggered on keypresses.
     *
     * @author potanin@UD
     */
    var execute_filters = this.execute_filters = function ( value ) {

      clearTimeout( s.active_timers.filter_intent );

      s.active_timers.filter_intent = setTimeout( function () {

        /* Blank our current result set */
        s.data.all_results = false;

        jQuery( s.ux.results ).css( 'opacity', 0.4 );

        /* Force reset of request range */
        s.settings.request_range = {
          start: 0,
          end: false
        }

        jQuery( document ).trigger( 'dynamic_filter::get_data' );

      }, s.settings.timers.filter_intent );

    }


    /**
     * Sort DOM items. Ran for every single row item.
     *
     * wrapper is the container of the items to be sorted
     * items is the jQuery DOM objects of the items to be sorted
     */
    var sort_elements = this.sort_elements = function ( wrapper, items ) {

      items.sort( function ( a, b ) {

        var compA = jQuery( a ).attr( 'sort_order' ) ? jQuery( a ).attr( 'sort_order' ).toUpperCase() : -1;
        var compB = jQuery( b ).attr( 'sort_order' ) ? jQuery( b ).attr( 'sort_order' ).toUpperCase() : -1;

        return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;

      } )

      jQuery.each( items, function ( idx, itm ) { wrapper.append( itm ); } );

    }


    /**
     * Prepares results to be displayed, and displays pagination UI..
     *
     * @author potanin@UD
     */
    var render_pagination = this.render_pagination = function () {
      jQuery( document ).trigger( 'dynamic_filter::render_pagination::initialize' );

      s.data.now_visible = jQuery( '.result_row:visible' , s.ux.all_results ).length;
      s.data.more_available = jQuery( '.result_row:not(:visible)' , s.ux.all_results ).length;
      s.data.next_batch = ( s.data.more_available < s.settings.per_page ) ? s.data.more_available : s.settings.per_page;

      /* Append Load More element to wrapper if it has not already */
      if( !jQuery( s.ux.load_more ).data( 'visible' ) ) {

        jQuery( s.ux.results_wrapper ).append( s.ux.load_more );

        jQuery( s.ux.load_more ).data( 'visible', true );

        jQuery( s.ux.load_more ).click( function ( event ) {
          jQuery( document ).trigger( 'dynamic_filter::load_more' );
        });

      }

      /* If less results than rendered in first view, no "Load More" button */
      if( s.data.all_results.length < s.settings.per_page || s.data.next_batch === 0 ) {
        status( 'There are ' + s.data.total_results + ' total results.', { hide: false, type: 'success' } );
        jQuery( s.ux.load_more ).data( 'visible', false ).hide();

      } else {
        status( 'There are ' + s.data.total_results + ' total results, with ' + s.settings.per_page + ' results per page.', { hide: false, type: 'success' } );
      }

      /* Always update count */
      jQuery( s.ux.load_more ).html( 'Load ' + s.data.next_batch + ' more.' );

      jQuery( document ).trigger( 'dynamic_filter::render_pagination::complete' );

    }


    /**
     * Loads more listings, does not render the button though.
     *
     * Will hide button if no more results to be rendered.
     *
     * @author potanin@UD
     */
    var load_more = this.load_more = function () {
      jQuery( document ).trigger( 'dynamic_filter::load_more::initialize' );

      s.data.now_visible = jQuery( '.result_row:visible' , s.ux.all_results ).length;
      s.data.more_available = jQuery( '.result_row:not(:visible)' , s.ux.all_results ).length;
      s.data.next_batch = ( s.data.more_available < s.settings.per_page ) ? s.data.more_available : s.settings.per_page;

      var something = s.data.now_visible + s.settings.per_page;

      jQuery( jQuery( '.result_row:not(:visible):lt(' + ( s.settings.per_page  ) + ')' , s.ux.all_results ) ).each( function( count ) {
        jQuery( this ).show().css( 'opacity' , 0 ).delay( count * 20 ).fadeTo( 500, 1 );
      });

      s.settings.visible_range.end = jQuery( '.result_row:visible' , s.ux.all_results ).length;

      s.ux.load_more.html( 'Load ' + s.data.next_batch + ' more.' );

      /* If next batch requires a server call, make it now */
      if( something >=  s.data.all_results.length ) {

        log( 'dynamic_filter::load_more - fetching more results. ', 'debug' );

        /* Update ranges */
        s.settings.request_range = {
          start:  s.data.all_results.length,
          end: s.data.all_results.length + s.settings.dom_limit
        };

        jQuery( document ).trigger( 'dynamic_filter::get_data' );

      }

      /* If no more to show. hide Load More button */
      if( s.data.next_batch == 0 ) {
        s.ux.load_more.hide();
      }

      jQuery( document ).trigger( 'dynamic_filter::load_more::complete' );

    }

    /**
     * Helper
     *
     * @author potanin@UD
     */
    var remove_from_array = this.remove_from_array = function( value, arr ) {
      return jQuery.grep(arr, function(elem, index) {
        return elem !== value;
      });
    }

    /**
     * Enable the script, ran once on initialization
     *
     */
    var enable = this.enable = function () {

      /* Bind Document-wide accessible functions */
      jQuery( document ).bind( 'dynamic_filter::doing_ajax', function() {
        log( 'dynamic_filter::doing_ajax' );
        jQuery( s.ux.element ).removeClass( s.classes.server_fail );
        jQuery( s.ux.element ).addClass( s.classes.ajax_loading );
      });

      jQuery( document ).bind( 'dynamic_filter::ajax_complete', function() {
        log( 'dynamic_filter::ajax_complete' );
        jQuery( s.ux.element ).removeClass( s.classes.ajax_loading );
      });

      jQuery( document ).bind( 'dynamic_filter::get_data', function() {
        log( 'dynamic_filter::get_data' );
        get_data();
      });

      jQuery( document ).bind( 'dynamic_filter::get_data::complete', function() {
        log( 'dynamic_filter::get_data::complete' );
        jQuery( document ).trigger( 'dynamic_filter::ajax_complete' );
        render_data();
        update_filters();
      });

      jQuery( document ).bind( 'dynamic_filter::get_data::fail', function() {
        jQuery( document ).trigger( 'dynamic_filter::ajax_complete' );
        jQuery( s.ux.element ).addClass( s.classes.server_fail );
      });

      jQuery( document ).bind( 'dynamic_filter::render_data', function() {
        log( 'dynamic_filter::render_data' );
        render_data();
      });

      jQuery( document ).bind( 'dynamic_filter::render_data::complete', function() {
        log( 'dynamic_filter::render_data::complete' );
        render_pagination();
      });

      jQuery( document ).bind( 'dynamic_filter::execute_filters', function() {
        log( 'dynamic_filter::execute_filters' );
        execute_filters();
      });

      jQuery( document ).bind( 'dynamic_filter::update_filters::complete', function() {
        log( 'dynamic_filter::update_filters::complete' );
      });

      jQuery( document ).bind( 'dynamic_filter::render_pagination', function() {
        log( 'dynamic_filter::render_pagination' );
        render_pagination();
      });

      jQuery( document ).bind( 'dynamic_filter::load_more', function() {
        log( 'dynamic_filter::load_more' );
        load_more();
      });

      /* We analyze the passed attributes */
      analyze_attributes();

      /* Render UI elements and add custom classes */
      prepare_ui();

      /* Render the filters */
      render_filters();

      /* If Auto Request enable, Get Data */
      if( s.settings.auto_request ) {
        jQuery( document ).trigger( 'dynamic_filter::get_data' );
      }

      /* Return object for chaining */
      return this;

    }


    /* Initialize the script */
    return enable();

  };


} ( jQuery ) );


