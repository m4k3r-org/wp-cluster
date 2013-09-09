<?php

use \bjork\utils\encoding;

// require_once BJORK_ROOT."/bjork/utils/encoding.php";

class EncodingTests extends UnitTestCase {
    function test_iri_to_uri() {
        $iris = array(
            // safe characters
            "/#%[]=:;$&()+,!?*@'~" => "/#%[]=:;$&()+,!?*@'~",
            
            // simple url
            "http://www.google.com/" => "http://www.google.com/",
            
            // path with spaces
            "/path with spaces" => "/path%20with%20spaces",
            
            // unicode
            "/αμπέ" => "/%CE%B1%CE%BC%CF%80%CE%AD",
            
            // already encoded
            "/%CE%B1%CE%BC" => "/%CE%B1%CE%BC",
        );
        
//         $i = array(
// "/#%[]=:;$&()+,!?*@'~",
// "http://www.google.com/",
// "/path%20with%20spaces",
// "/%E1%ED%F0%DB",
// "/%CE%B1%CE%BC",
//         );
//         
//         foreach ($i as $a)
//             print rawurldecode($a);
        
        foreach ($iris as $iri => $uri)
            $this->assertEqual($uri, encoding::iri_to_uri($iri));
    }
}

?>