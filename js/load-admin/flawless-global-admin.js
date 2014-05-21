jQuery(document).ready(function() {

  if( typeof prettyPrint == 'function' ) {
    prettyPrint();
  }
  
  if(typeof jQuery.fn.sortable == 'function') {
    jQuery(".flawless_sortable_wrapper").each(function() {
      jQuery(".flawless_sortable_attributes", this).sortable();
    });
  }

  jQuery("#cfct-copy-build-data").click("live", function (event) {
    event.preventDefault();

    var params = {};

    params.action = 'cbc_get_page_build';
    params.post_id = jQuery("input#post_ID").val();

    jQuery.post(ajaxurl, params, function(result) {

      jQuery(".cb_page_data").remove();

      jQuery("<div class='cb_page_data'><textarea style='width: 100%;margin: 0 0 10px 0; height: 200px;' readonly='true'>" + result.content + "</textarea></div>").insertAfter("#titlediv");

    }, "json");

  });

  jQuery("#cfct-paste-build-data").click("live", function (event) {
    event.preventDefault();

    var params = {};

    var post_data = prompt("Paste the serialized data below.");

    params.action = 'cbc_insert_page_build';
    params.post_id = jQuery("input#post_ID").val();
    params.post_data = post_data;

    jQuery.ajax({
      url: ajaxurl,
      data: params,
      type:"post",
      dataType: "json",
      success: function(result) {

        jQuery(".cb_page_data").remove();

        if(result.success == 'true') {
          alert('Done. Reload page.');
        } else {
          alert('Error.');
        }

      }
    });

  });


  /**
   * Set custom class for entire build
   *
   * @author Usability Dynamics, Inc.
   */
  jQuery('#cfct-set-build-class').live('click', function(e) {

    e.preventDefault();

    var this_button = this;

    var current_setting = jQuery(this_button).attr('current_setting') ? jQuery(this_button).attr('current_setting') : '';
    var post_id = jQuery("#post_ID").val();

    var new_setting = prompt("Build Class:", current_setting);

    /* If "Cancel" is pressed */
    if(new_setting === null) {
      return;
    }

    if(new_setting == current_setting) {
      return;
    }

    jQuery.ajax({
      url: ajaxurl,
      data: {
        action: 'flawless_cb_build_class',
        post_id: post_id,
        new_class: new_setting
      },
      success: function(result) {
        jQuery(this_button).attr('current_setting', new_setting);
      },
      dataType: "json"
    });


  });


  /**
   * Shows custom row class entry box and saves it via AJAX
   *
   * @todo Test how this works with new posts that don't have an ID
   * @author Usability Dynamics, Inc.
   */
  jQuery('#cfct-sortables .cfct-add-row-class').live('click', function(e) {

    e.preventDefault();

    var this_button = this;

    var row_class = jQuery(this_button).attr('row_class');
    var current_setting = jQuery(this_button).attr('current_setting') ? jQuery(this_button).attr('current_setting') : '';
    var row_element = jQuery(this_button).closest("." + row_class);
    var row_id = jQuery(row_element).attr("id");
    var post_id = jQuery("#post_ID").val();

    var new_setting = prompt("Row Class:", current_setting);

    /* If "Cancel" is pressed */
    if(new_setting === null) {
      return;
    }

    if(new_setting == current_setting) {
      return;
    }

    jQuery.ajax({
      url: ajaxurl,
      data: {
        action: 'flawless_cb_row_class',
        post_id: post_id,
        row_id: row_id,
        row_class: row_class,
        new_class: new_setting
      },
      success: function(result) {
        jQuery(this_button).attr('current_setting', new_setting);
      },
      dataType: "json"
    });


  });


});
