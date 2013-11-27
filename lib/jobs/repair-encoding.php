<?php

class repairEncoding {

  static public function worker() {
    wp_die( 'repairEncoding::worker' );

    $utf_map = array(
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
      "ÃƒÂ­"      => "í",
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
      "ÃƒÂ±"      => "ñ"
    );

  }

// Perhaps replace with https://github.com/neitanod/forceutf8/blob/master/src/ForceUTF8/Encoding.php
  static public function fix_double_encoding( $string ) {
    $utf8_chars          = explode( ' ', 'À Á Â Ã Ä Å Æ Ç È É Ê Ë Ì Í Î Ï Ð Ñ Ò Ó Ô Õ Ö × Ø Ù Ú Û Ü Ý Þ ß à á â ã ä å æ ç è é ê ë ì í î ï ð ñ ò ó ô õ ö' );
    $utf8_double_encoded = array();
    foreach( $utf8_chars as $utf8_char ) {
      $utf8_double_encoded[ ] = utf8_encode( utf8_encode( $utf8_char ) );
    }

    $string = str_replace( $utf8_double_encoded, $utf8_chars, $string );

    return $string;

  }

}
