<?php
/**
 * Class repairEncoding
 *
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ¡','á');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ¤','ä');
 * update wp_12_posts set post_content = replace( post_content ,'Ãƒâ€ž','ä');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ§','ç');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ©','é');
 * update wp_12_posts set post_content = replace( post_content ,'Ãƒâ€°','É');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ¨','è');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ¬','ě');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂª','ê');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ¯','ï');
 * update wp_12_posts set post_content = replace( post_content ,'Ã„Â©','ĩ');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ³','ó');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ¸','ø');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ¶','ö');
 * update wp_12_posts set post_content = replace( post_content ,'Ãƒâ€','ö');
 * update wp_12_posts set post_content = replace( post_content ,'Ã…Â¡“','š');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ¼','ü');
 * update wp_12_posts set post_content = replace( post_content ,'LÃƒÂº','ú');
 * update wp_12_posts set post_content = replace( post_content ,'Ã…Â©','ũ');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ±','ñ');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ«','ë');
 * update wp_12_posts set post_content = replace( post_content ,'ÃƒÂ','í');
 *
 */


class repairEncoding {

  static public $utf_map = array(
    //"Ã¢â‚¬â„¢S" => "'s",
    //"Ã¢â‚¬â„¢A" => "'a",
    "ÃƒÂ¡"      => "á",
    "ÃƒÂ¤"      => "ä",
    "Ãƒâ€ž"     => "ä",
    "ÃƒÂ§"      => "ç",
    "ÃƒÂ©"      => "é",
    "Ãƒâ€°"     => "É",
    "ÃƒÂ¨"      => "è",
    "ÃƒÂ¬"      => "ě",
    "ÃƒÂª"      => "ê",
    "ÃƒÂ¯"      => "ï",
    "Ã„Â©"      => "ĩ",
    "ÃƒÂ³"      => "ó",
    "ÃƒÂ¸"      => "ø",
    "ÃƒÂ¶"      => "ö",
    "Ãƒâ€“"     => "ö",
    "Ã…Â¡"      => "š",
    "ÃƒÂ¼"      => "ü",
    "LÃƒÂº"     => "ú",
    "Ã…Â©"      => "ũ",
    "ÃƒÂ±"      => "ñ",
    "ÃƒÂ«"      => "ë",
    "ÃƒÂ"       => "í"
  );

  /**
   * @param $job
   * @param $count
   *
   * @internal param $args
   *
   * @return array
   */
  static public function worker( $job, $count ) {
    global $wpdb;

    //echo "Doing job [type:{$job->type}] [id:{$job->ID}].";

    if( !$job ) {
      return new WP_Error( 'Missing Job args.' );
    }

    $_results = array();

    die( '<pre>' . print_r( $job, true ) . '</pre>' );

    foreach( (array) $job->post_content->table as $table ) {

      foreach( (array) repairEncoding::$utf_map as $search => $replace ) {
        $_results[ $table . '.post_content' ]   = "UPDATE {$table} set post_content = replace( post_content, '{$search}', '{$replace}' );";
        $_results[ $table . '.post_title' ]     = "UPDATE {$table} set post_title = replace( post_title, '{$search}', '{$replace}' );";
        //$_results[ $table . '.post_content' ] = $wpdb->query( "UPDATE {$table} set post_content = replace( post_content, '{$search}', '{$replace}' );" );
        //$_results[ $table . '.post_title' ] = $wpdb->query( "UPDATE {$table} set post_title = replace( post_title, '{$search}', '{$replace}' );" );
      }

    }

    return implode( "<br />", $_results );

  }

}
