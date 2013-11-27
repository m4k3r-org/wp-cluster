<?php
/**
 * Class repairEncoding
 *
 */
class repairEncoding {

  static public $utf_map = array(
    "Ã¢â‚¬â„¢S" => "'s",
    "Ã¢â‚¬â„¢A" => "'a",
    "ÃƒÂ¡"      => "á",
    "ÃƒÂ¤"      => "ä",
    "Ãƒâ€ž"     => "ä",
    "ÃƒÂ§"      => "ç",
    "ÃƒÂ©"      => "é",
    "Ãƒâ€°"     => "É",
    "ÃƒÂ¨"      => "è",
    "ÃƒÂ¬"      => "ě",
    "ÃƒÂª"      => "ê",
    "ÃƒÂ"       => "í",
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
  );

  /**
   * @param $args
   *
   * @return array
   */
  static public function worker( $args ) {
    global $wpdb;

    $_results = array();

    foreach( (array) $args->tables as $table ) {

      foreach( (array) repairEncoding::$utf_map as $search => $replace ) {
        $_results[ $table ] = $wpdb->query( "UPDATE {$table} set post_content = replace( post_content ,'{$search}', '{$replace}' ); ");
      }

    }

    return $_results;

  }

}
