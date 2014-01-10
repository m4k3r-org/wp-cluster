<?php

use bjork\conf\urls,
    bjork\core\urlresolvers;

function noargview($request) {}
function anothernoargview($request) {}
function aview($request, $arg) {}
function anotherview($request, $arg=null) {}
function unicodeview($request, $arg=null) {}

$app_urlconf = urls::patterns("",
    array("^$", "anothernoargview"),
    array("^$", "noargview", "name"=>"shadowed_by_previous"),
    array("^b/$", "anothernoargview"),
    array("^b/(\d+)/$", "aview"),
    array("^c/$", "anotherview"),
    array("^d/(?P<arg>\w+)/$", "anotherview"),
    array("^e/(?P<arg>\X+)/$", "unicodeview")
);

$root_urlconf = urls::patterns("",
    array("^$", "noargview", "name"=>"match_root"),
    array("^a/", urls::import($app_urlconf)),
    array("^b/$", "anotherview", array("arg"=>"qazwsx"), "name"=>"fixed_kwargs"),
    array("^c/(?P<arg>\w+)/$", "anotherview", array("arg"=>"qazwsx"), "name"=>"fixed_kwargs_2")
);

class UrlResolverTests extends UnitTestCase {
    
    function resolvePaths($urlconf, $cases) {
        $resolver = urlresolvers::get_resolver($urlconf);
        foreach ($cases as $case) {
            list($path, $expect) = $case;
            if ($expect[0] === null) {
                $this->expectException(new urlresolvers\Resolver404($path));
                $resolver->resolve($path);
            } else {
                $match = $resolver->resolve($path);
                $this->assertEqual($expect, array(
                    $match["view"],
                    $match["args"],
                    $match["kwargs"]));
            }
        }
        urlresolvers::clear_cache();
    }
    
    function test_resolve() {
        global $root_urlconf;
        $this->resolvePaths($root_urlconf, array(
            array("/",           array("noargview", array(), array())),
            array("/a/",         array("anothernoargview", array(), array())),
            array("/a/b/",       array("anothernoargview", array(), array())),
            array("/a/b/3456/",  array("aview", array("3456"), array())),
            array("/a/c/",       array("anotherview", array(), array())),
            array("/a/d/aoua/",  array("anotherview", array(), array("arg"=>"aoua"))),
            array("/a/e/αμπε/",  array("unicodeview", array(), array("arg"=>"αμπε"))),
            array("/b/",         array("anotherview", array(), array("arg"=>"qazwsx"))),
            array("/c/3456/",    array("anotherview", array(), array("arg"=>"qazwsx"))),
            array("/x/",         null),
        ));
    }
    
    function test_resolve_shortcut() {
        global $root_urlconf;
        $match = urlresolvers::resolve("/a/d/aoua/", $root_urlconf);
        $this->assertEqual(
            array("anotherview", array(), array("arg"=>"aoua")),
            array($match["view"], $match["args"], $match["kwargs"]));
    }
}

?>