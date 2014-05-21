<?php
/**
 * Header - Contact Us
 *
 * Displays the bottom of page element on the home page.
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @package Flawless
 * @since Flawless 3.0
 *
 */


  if(!current_theme_supports('header-property-contact')) { return; }


  if($fs['hide_header_contact'] == 'true') { return; }

  $function = create_function('$c', '
    $c["contact_us"]["id"] = "dropdown_header_contact_us";
    $c["contact_us"]["title"] = __("Contact us", "wpp");
    $c["contact_us"]["class"] = "dropdown_tab_contact_us";
    $c["contact_us"]["href"] = "#";
    return $c;
  ');

  add_filter('flawless_header_links', $function, 30, 1);

  if(empty($fs['wp_crm']['header_contact'])) {
    $contact_form = 'header_contact';
  } else {
    $contact_form = $fs['wp_crm']['header_contact'];
  }

  if($contact_form != 'header_contact' && class_exists('class_contact_messages')) {
    $crm_form = class_contact_messages::shortcode_wp_crm_form(array('form' => $contact_form));
  }

  ?>

<div id="dropdown_header_contact_us" class="header_dropdown_div header_contact_div">
  <ul class="flawless_dropdown_elements container">
    <li class="continfo header_contact_section header_dropdown_section">
        <?php echo (!empty($fs['name']) ? "<h5>" . $fs['name'] . "</h5>" : ""); ?>
        <?php echo (!empty($fs['info']) ? "<p class='flawless_header_info'>" . nl2br(do_shortcode($fs['info'])) . "</p>" : ""); ?>

        <?php echo (!empty($fs['latitude']) ? '<div id="flawless_header_location_map" class="flawless_header_location_map"></div>' : ''); ?>

        <p class="contact_info">
            <?php echo (!empty($fs['name']) ? "<span class='sena'>" . $fs['name'] . "</span><br />" : ""); ?>
            <?php echo (!empty($fs['address']) ? nl2br($fs['address']) .'<br />' : ""); ?>
            <?php echo (!empty($fs['phone']) ? $fs['phone'] .'<br />' : ""); ?>
            <?php echo (!empty($fs['fax']) ? __('Fax', 'wpp') . ': '. $fs['fax'] .'<br />' : ""); ?>
         </p>
        
    </li>

  <li class="form header_contact_section header_dropdown_section">

    <?php if($crm_form) {
      echo $crm_form;
    } else { ?>

    <form action="#" id="flawless_contact_form" method="post">
    <div class="ajax_error hidden"></div>
    <div class = "contact">
      <div id = "contact_left">
        <label for="contact_name"><?php _e("Name", "wpp"); ?>: <span>*</span></label>
        <input   id="contact_name"  type="text" />
      </div>
        <div id = "contact_right">
          <label for="contact_email"><?php _e("E-mail", "wpp"); ?>: <span>*</span></label>
          <input  id="contact_email" type="text" />
        </div>
        <div id="contact_foot">
          <label for="contact_message"><?php _e("Message", "wpp"); ?>: <span>*</span></label>
          <textarea id="contact_message" class="requiredField"></textarea>
        </div>
      <input type="submit" name="submitContact" id="submitContact" value="<?php _e("Send Message", "wpp"); ?>" />
    </div>
    </form>

    <?php } ?>

  </li>
 </ul>
</div>