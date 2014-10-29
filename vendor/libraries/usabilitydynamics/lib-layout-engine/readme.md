## Objectives
* Layout engine handler for WordPress that abstract CarringtonBuild/SiteOrigin/etc.

## Usage

```php
<?php

// Bootstrap library
$engine = new UsabilityDynamics\LayoutEngine\Bootstrap();

// Output version
echo 'Version: ' . $engine::$version;

// Register module
$engine->register( 'my-module', array(
  "options" => array()
));

// Set options
$engine->set( 'setting1', 'value1' );
$engine->set( 'setting2', 'value2' );

// Get an option
$engine->get( 'setting1' );

```

## Classes
The Bootstrap is generally the only class you will need to instantiate.

 - Bootstrap: Singleton that detects environment, verifies dependencies, registers activation and deactivation and enqueues scripts.
 - Module: Abstract module factory.
 - Widget: Handles WordPress widget registration.
 - Shortcode: Handles WordPress shortcode registration.

## License

(The MIT License)

Copyright (c) 2013 Usability Dynamics, Inc. &lt;info@usabilitydynamics.com&gt;

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
'Software'), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.