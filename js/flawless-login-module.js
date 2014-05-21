/* =========================================================
 * flawless-login-module.js
 * http://usabilitydynamics.com
 * =========================================================
 * Copyright 2011 Usability Dynamics, Inc.
 *
 * Version 0.0.1
 *
 * Copyright ( c ) 2011 Usability Dynamics, Inc. ( usabilitydynamics.com )
 * ========================================================= */

/**
 * Renders notice.
 *
 * @param string text. HTML
 * @param object el. Optional DOM element
 * @author peshkov@UD
 */
if( typeof flawless.ajax_response_notice != 'function' ) {
  flawless.ajax_response_notice = function( text, el, type) {
    var container = jQuery('.flawless_ajax_response');
    if( typeof( el ) === "object" ) {
      if(el.length > 0) container = el;
    }
    if( typeof( type ) === 'undefined') {
      type = 'success';
    }

    if(type == 'error') {
      container.addClass('label-important error');
      container.removeClass('label-success');
    } else {
      container.removeClass('label-important error');
      container.addClass('label-success');
      
    }

    if(!container.length > 0) return;
    container.html(text);
    container.show();

    setTimeout(function(){
      container.fadeOut(3000);
    }, 5000);
  }
}

jQuery( document ).ready( function() {
  
  /** 
   * Switch log in / forget password forms in navbar.
   * 
   * @author peshkov@UD
   */
  jQuery('a#nav_forget_password').click(function(e){
    var el = jQuery(this);
    var wrap = jQuery('div.nav-collapse.pull-right ul');
    e.preventDefault();
    el.trigger('flawless::nav_forget_password');
    if(el.hasClass('default')) {
      jQuery('.navbar_login_form .flawless_ajax_response', wrap).hide();
      jQuery('.navbar_login_form', wrap).hide();
      jQuery('.navbar_reset_password_form', wrap).show();
      el.removeClass('default');
      el.html(lm_l10n.log_in);
    } else {
      jQuery('.navbar_reset_password_form .flawless_ajax_response', wrap).hide();
      jQuery('.navbar_reset_password_form', wrap).hide();
      jQuery('.navbar_login_form', wrap).show();
      el.addClass('default');
      el.html(lm_l10n.forget_password);
    }
  });
  
  /** 
   * 'Forget password' AJAX form submitting
   * 
   * @author peshkov@UD
   */
  jQuery('form[name="resetpassform"]').submit(function(e){
    e.preventDefault();
    jQuery(this).trigger('flawless::reset_password_submit');
    var nwrap = jQuery('.flawless_ajax_response', this);
    /** Validate form data */
    if(jQuery('input[name="user_login"]', this).val() == '') {
      flawless.ajax_response_notice(lm_l10n.enter_fields_properly, nwrap, 'error');
      return false;
    }
    /** Now do request */
    jQuery.ajax({
      'url': flawless.ajax_url,
      'type': 'POST',
      'data' : jQuery(this).serialize(),
      'complete': function( r, status ) {
        if(status == 'success') {
          data = jQuery.parseJSON(r.responseText);
          if(!data.error) {
            flawless.ajax_response_notice(lm_l10n.email_was_sent, nwrap);
          } else {
            flawless.ajax_response_notice(data.error, nwrap, 'error');
          }
        } else {
          flawless.ajax_response_notice(lm_l10n.something_wrong, nwrap, 'error');
        }
      }
    });
    return false;
  });
  
  /**
   * Logout AJAX functionality
   * 
   * @author peshkov@UD
   */
  jQuery('a.f_ajax_logout_link').click(function(e){
    e.preventDefault();
    jQuery(this).trigger('flawless::log_out');
    /** Now do request */
    jQuery.ajax({
      'url': flawless.ajax_url,
      'type': 'POST',
      'data' : {'action':'flawless_ajax_logout'},
      'complete': function( r, status ) {
        window.location.reload(true);
      }
    });
    return false;
  });
  
  /**
   * 'Log in' AJAX form submitting
   * 
   * @author peshkov@UD
   */
  jQuery('form[name="loginform"]').submit(function(e){
    e.preventDefault();
    jQuery(this).trigger('flawless::login_form_submit');
    var nwrap = jQuery('.flawless_ajax_response', this);
    /** Validate form data */
    if(jQuery('input[name="log"]', this).val() == '') {
      flawless.ajax_response_notice(lm_l10n.enter_login, nwrap, 'error');
      return false;
    } else if (jQuery('input[name="pwd"]', this).val() == '') {
      flawless.ajax_response_notice(lm_l10n.enter_password, nwrap, 'error');
      return false;
    }
    /** Now do request */
    jQuery.ajax({
      'url': flawless.ajax_url,
      'type': 'POST',
      'data' : jQuery(this).serialize(),
      'complete': function( r, status ) {
        if(status == 'success') {
          data = jQuery.parseJSON(r.responseText);
          if(!data.error) {
            if(data.redirect_to) window.location.href = data.redirect_to;
            else window.location.reload(true);
          } else {
            flawless.ajax_response_notice(data.error, nwrap, 'error');
          }
        } else {
          flawless.ajax_response_notice(lm_l10n.something_wrong, nwrap, 'error');
        }
      }
    });
    return false;
  });
});

