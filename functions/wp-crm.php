<?php

if(!class_exists('WP_CRM_Core')) {
  return;
}

/**
  * Name: WP-CRM Extensions
  * Description: Extra functionality for WP-CRM elements.
  * Author: Usability Dynamics, Inc.
  * Version: 1.0
  *
  */

add_action('flawless_options_ui_header', array('flawless_wp_crm_extensions', 'flawless_options_ui_header'));


class flawless_wp_crm_extensions {


  function flawless_options_ui_header($flawless) {
    global $wp_crm;
    
    $shortcode_forms = $wp_crm['wp_crm_contact_system_data'];
  ?>
  
  <tr class="conditional_dependency" required_condition="header-dropdowns-option">
      <th><?php _e('Header Contact Form', 'wpp'); ?></th>
      <td>
      <?php

      if(!$wp_crm) {
        echo '<p>' . __('You can use WP-CRM to have more flexibility over your contact forms, to include the header contact form.', 'wpp') . '</p>';
      } elseif(!$shortcode_forms) {
        echo '<p>' . __('Please visit CRM -> Settings -> Shortcode Forms to add a contact form.', 'wpp') . '</p>';
      } else {

        if(isset($flawless['wp_crm']['header_contact'])) {
          $selection = $flawless['wp_crm']['header_contact'];
        } else {
          $selection = 'flawless_header_form';
        }

        ?>

      <table class="widefat wpp_something_advanced_wrapper">
      <tr>
        <th><label for="flawless_settings_header_crm_form"><?php _e('Form: ', 'wpp'); ?></label></th>
        <td>
          <select id="flawless_settings_header_crm_form" name="flawless_settings[wp_crm][header_contact]">
          <option <?php selected($selection, 'flawless_header_form'); ?> value="flawless_header_form"><?php _e('Default Form', 'wpp'); ?></option>
          <?php foreach($shortcode_forms as  $form) {  ?>
          <option <?php selected($selection, $form['current_form_slug']); ?> value="<?php echo esc_attr($form['current_form_slug']); ?>"><?php echo $form['title']; ?></option>
          <?php } ?>
        </select>
        </td>
      </tr>

      <tr valign="top" class="flawless_header_crm_form_settings <?php echo $selection == 'flawless_header_form' ? 'hidden' : ''; ?>">
        <td colspan="2">
            <?php  _e('Visit CRM -> Settings -> Shortcode Forms to add a contact form.', 'wpp'); ?>
        </td>
      </tr>

      <tr valign="top" class="flawless_header_regular_form_settings <?php echo $selection != 'flawless_header_form' ? 'hidden' : ''; ?>">
        <th>Send Notifications To:</th>
        <td>
            <input type="text" name="flawless_settings[email]" id="email" value="<?php echo $flawless['email'];?>" />
            <br /><span class="description">Messages submitted via the "Contact Us" form will be sent here. Separate multiple recipients with a comma.</span>
        </td>
      </tr>

      <tr valign="top" class="flawless_header_regular_form_settings <?php echo $selection != 'flawless_header_form' ? 'hidden' : ''; ?>">
        <th>Email From Address:</th>
        <td>
            <input type="text" name="flawless_settings[email_from]" id="email" value="<?php echo $flawless['email_from'];?>" />
            <br /><span class="description">This is the email messaged sent by the website will appear to be sent from. You can do something like this: <b>Contact Form &lt;website@mydomain.com&gt;</b></span>
        </td>
      </tr>
      </table>

      <?php } ?>


      </td>

      </tr>
      
      
      
    <?php 
    
    }
      


}
  