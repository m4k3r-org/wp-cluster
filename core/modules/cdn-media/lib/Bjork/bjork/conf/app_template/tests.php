<{?php

namespace <?=$namespace?>\tests;

/*
    This file demonstrates writing tests using the SimpleTest module. These
    will pass when you run "manage.php test".
    
    Replace this with more appropriate tests for your application.
*/

use bjork\test\TestCase;

class ExampleTest extends TestCase {
    /*
      Tests that 1 + 1 always equals 2.
    */
    function test_basic_addition() {
        $this->assertEqual(1 + 1, 2);
    }
}
