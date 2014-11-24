<?php
/**
 * Content - Single Property Map.
 *
 * Displays the attenion grabbing element on the homage page.
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @package Flawless
 * @since Flawless 3.0
 *
 */
 
  //** Check if this post type support comments */
  if(!post_type_supports( get_queried_object()->post_type , 'comments' )) {
    return;
  }

  if(!empty($flawless['wp_crm']['inquiry_forms'][$property['property_type']])) {
    global $wp_crm;

    $contact_form = $flawless['wp_crm']['inquiry_forms'][$property['property_type']];

    if($contact_form != 'flawless_default_form') {

      if(class_exists('class_contact_messages')) {
        $crm_form = class_contact_messages::shortcode_wp_crm_form(array('form' => $contact_form));
      }

    }
  } else {

    //** Default */
    $contact_form = 'flawless_default_form';
  }

  if(!comments_open()) { return; }

  if($contact_form == 'no_form') { return; }


  if($flawless['show_property_comments'] == 'true') {
    $title_reply = __('Comment About', 'wpp') . ' ' . $post->post_title;
  } else {
    $title_reply = __('Inquire About', 'wpp') .' '. $post->post_title;
  }

  if ($crm_form) {
    echo $crm_form;
  }elseif (function_exists('wpp_inquiry_form')) {
    wpp_inquiry_form("title_reply=$title_reply&comment_notes_after=&comment_notes_before=");
  } else {
    comment_form("title_reply=$title_reply&comment_notes_after=&comment_notes_before=");
  }

  if($flawless['show_property_comments'] == 'true') {  ?>
  <ol class="commentlist">
    <?php wp_list_comments( array( 'callback' => 'flawless_comment' ), get_comments( array('post_id' => $post->ID, 'status' => 'approve', 'order' => 'ASC') ));?>
  </ol>
<?php }

