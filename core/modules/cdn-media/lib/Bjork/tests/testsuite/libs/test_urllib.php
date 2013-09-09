<?php

const RFC1808_BASE = "http://a/b/c/d;p?q#f";
const RFC2396_BASE = "http://a/b/c/d;p?q";
const RFC3986_BASE = 'http://a/b/c/d;p?q';
const SIMPLE_BASE  = 'http://a/b/c/d';

class UrlParseTests extends UnitTestCase {
    function test_qsl() {
        $testcases = array(
            array("", array()),
            array("&", array()),
            array("&&", array()),
            array("=", array(array('', ''))),
            array("=a", array(array('', 'a'))),
            array("a", array(array('a', ''))),
            array("a=", array(array('a', ''))),
            array("a=", array(array('a', ''))),
            array("&a=b", array(array('a', 'b'))),
            array("a=a+b&b=b+c", array(array('a', 'a b'),
                                 array('b', 'b c'))),
            array("a=1&a=2", array(array('a', '1'),
                             array('a', '2'))),
        );
        foreach ($testcases as $case) {
            $orig = $case[0];
            $expect = $case[1];
            $result = urllib::parse_qsl($orig, true);
            $this->assertEqual($result, $expect, "Error parsing '$orig'");
        }
    }
    
    function checkRoundtrips($url, $parsed, $split) {
        $result = urllib::parse($url);
        $this->assertEqual($result->toArray(), $parsed);
        
        // print "<pre>".print_r($result->toArray(), true)."<br>".print_r($parsed, true)."</pre>";
        
        // put it back together and it should be the same
        $result2 = urllib::unparse($result);
        $this->assertEqual($result2, $url);
        $this->assertEqual($result2, $result->getURL());
    }
    
    function test_roundtrips() {
        $testcases = array(
            array('file:///tmp/junk.txt',
                array('file', '', '/tmp/junk.txt', '', '', ''),
                array('file', '', '/tmp/junk.txt', '', '')),
            array('imap://mail.python.org/mbox1',
                array('imap', 'mail.python.org', '/mbox1', '', '', ''),
                array('imap', 'mail.python.org', '/mbox1', '', '')),
            array('mms://wms.sys.hinet.net/cts/Drama/09006251100.asf',
                array('mms', 'wms.sys.hinet.net', '/cts/Drama/09006251100.asf', '', '', ''),
                array('mms', 'wms.sys.hinet.net', '/cts/Drama/09006251100.asf', '', '')),
            array('nfs://server/path/to/file.txt',
                array('nfs', 'server', '/path/to/file.txt',  '', '', ''),
                array('nfs', 'server', '/path/to/file.txt', '', '')),
            array('svn+ssh://svn.zope.org/repos/main/ZConfig/trunk/',
                array('svn+ssh', 'svn.zope.org', '/repos/main/ZConfig/trunk/', '', '', ''),
                array('svn+ssh', 'svn.zope.org', '/repos/main/ZConfig/trunk/', '', '')),
            array('git+ssh://git@github.com/user/project.git',
                array('git+ssh', 'git@github.com','/user/project.git', '','',''),
                array('git+ssh', 'git@github.com','/user/project.git', '', ''))
        );
        foreach ($testcases as $case) {
            list($url, $parsed, $split) = $case;
            $this->checkRoundtrips($url, $parsed, $split);
        }
    }
    
    function test_http_roundtrips() {
        // urllib::split treats 'http:' as an optimized special case,
        // so we test both 'http:' and 'https:' in all the following.
        // Three cheers for white box knowledge!
        $testcases = array(
            array('://www.python.org',
                array('www.python.org', '', '', '', ''),
                array('www.python.org', '', '', '')),
            array('://www.python.org#abc',
                array('www.python.org', '', '', '', 'abc'),
                array('www.python.org', '', '', 'abc')),
            array('://www.python.org?q=abc',
                array('www.python.org', '', '', 'q=abc', ''),
                array('www.python.org', '', 'q=abc', '')),
            array('://www.python.org/#abc',
                array('www.python.org', '/', '', '', 'abc'),
                array('www.python.org', '/', '', 'abc')),
            array('://a/b/c/d;p?q#f',
                array('a', '/b/c/d', 'p', 'q', 'f'),
                array('a', '/b/c/d;p', 'q', 'f')),
        );
        foreach (array("http", "https") as $scheme) {
            foreach ($testcases as $case) {
                list($url, $parsed, $split) = $case;
                $url = $scheme . $url;
                array_unshift($parsed, $scheme);
                array_unshift($split, $scheme);
                $this->checkRoundtrips($url, $parsed, $split);
            }
        }
    }
    
    function test_unparse_parse() {
        $testcases = array(
            'Python',
            './Python',
            'x-newscheme://foo.com/stuff',
            'x://y',
            'x:/y',
            'x:/',
            '/',
        );
        foreach ($testcases as $u) {
            $this->assertEqual(urllib::unsplit(urllib::split($u)), $u);
            $this->assertEqual(urllib::unparse(urllib::parse($u)), $u);
        }
    }
    
    function checkJoin($base, $relurl, $expected) {
        $this->assertEqual(urllib::join($base, $relurl), $expected,
            print_r(array($base, $relurl, $expected), true).
            print_r(urllib::join($base, $relurl), true)."\n");
    }
    
    function test_RFC1808() {
        // "normal" cases from RFC 1808:
        $this->checkJoin(RFC1808_BASE, 'g:h', 'g:h');
        $this->checkJoin(RFC1808_BASE, 'g', 'http://a/b/c/g');
        $this->checkJoin(RFC1808_BASE, './g', 'http://a/b/c/g');
        $this->checkJoin(RFC1808_BASE, 'g/', 'http://a/b/c/g/');
        $this->checkJoin(RFC1808_BASE, '/g', 'http://a/g');
        $this->checkJoin(RFC1808_BASE, '//g', 'http://g');
        $this->checkJoin(RFC1808_BASE, 'g?y', 'http://a/b/c/g?y');
        $this->checkJoin(RFC1808_BASE, 'g?y/./x', 'http://a/b/c/g?y/./x');
        $this->checkJoin(RFC1808_BASE, '#s', 'http://a/b/c/d;p?q#s');
        $this->checkJoin(RFC1808_BASE, 'g#s', 'http://a/b/c/g#s');
        $this->checkJoin(RFC1808_BASE, 'g#s/./x', 'http://a/b/c/g#s/./x');
        $this->checkJoin(RFC1808_BASE, 'g?y#s', 'http://a/b/c/g?y#s');
        $this->checkJoin(RFC1808_BASE, 'g;x', 'http://a/b/c/g;x');
        $this->checkJoin(RFC1808_BASE, 'g;x?y#s', 'http://a/b/c/g;x?y#s');
        $this->checkJoin(RFC1808_BASE, '.', 'http://a/b/c/');
        $this->checkJoin(RFC1808_BASE, './', 'http://a/b/c/');
        $this->checkJoin(RFC1808_BASE, '..', 'http://a/b/');
        $this->checkJoin(RFC1808_BASE, '../', 'http://a/b/');
        $this->checkJoin(RFC1808_BASE, '../g', 'http://a/b/g');
        $this->checkJoin(RFC1808_BASE, '../..', 'http://a/');
        $this->checkJoin(RFC1808_BASE, '../../', 'http://a/');
        $this->checkJoin(RFC1808_BASE, '../../g', 'http://a/g');

        // "abnormal" cases from RFC 1808:
        $this->checkJoin(RFC1808_BASE, '', 'http://a/b/c/d;p?q#f');
        $this->checkJoin(RFC1808_BASE, '../../../g', 'http://a/../g');
        $this->checkJoin(RFC1808_BASE, '../../../../g', 'http://a/../../g');
        $this->checkJoin(RFC1808_BASE, '/./g', 'http://a/./g');
        $this->checkJoin(RFC1808_BASE, '/../g', 'http://a/../g');
        $this->checkJoin(RFC1808_BASE, 'g.', 'http://a/b/c/g.');
        $this->checkJoin(RFC1808_BASE, '.g', 'http://a/b/c/.g');
        $this->checkJoin(RFC1808_BASE, 'g..', 'http://a/b/c/g..');
        $this->checkJoin(RFC1808_BASE, '..g', 'http://a/b/c/..g');
        $this->checkJoin(RFC1808_BASE, './../g', 'http://a/b/g');
        $this->checkJoin(RFC1808_BASE, './g/.', 'http://a/b/c/g/');
        $this->checkJoin(RFC1808_BASE, 'g/./h', 'http://a/b/c/g/h');
        $this->checkJoin(RFC1808_BASE, 'g/../h', 'http://a/b/c/h');

        // RFC 1808 and RFC 1630 disagree on these (according to RFC 1808),
        // so we'll not actually run these tests (which expect 1808 behavior).
        // $this->checkJoin(RFC1808_BASE, 'http:g', 'http:g');
        // $this->checkJoin(RFC1808_BASE, 'http:', 'http:');
    }
    
    function test_RFC2396() {
        $this->checkJoin(RFC2396_BASE, 'g:h', 'g:h');
        $this->checkJoin(RFC2396_BASE, 'g', 'http://a/b/c/g');
        $this->checkJoin(RFC2396_BASE, './g', 'http://a/b/c/g');
        $this->checkJoin(RFC2396_BASE, 'g/', 'http://a/b/c/g/');
        $this->checkJoin(RFC2396_BASE, '/g', 'http://a/g');
        $this->checkJoin(RFC2396_BASE, '//g', 'http://g');
        $this->checkJoin(RFC2396_BASE, 'g?y', 'http://a/b/c/g?y');
        $this->checkJoin(RFC2396_BASE, '#s', 'http://a/b/c/d;p?q#s');
        $this->checkJoin(RFC2396_BASE, 'g#s', 'http://a/b/c/g#s');
        $this->checkJoin(RFC2396_BASE, 'g?y#s', 'http://a/b/c/g?y#s');
        $this->checkJoin(RFC2396_BASE, 'g;x', 'http://a/b/c/g;x');
        $this->checkJoin(RFC2396_BASE, 'g;x?y#s', 'http://a/b/c/g;x?y#s');
        $this->checkJoin(RFC2396_BASE, '.', 'http://a/b/c/');
        $this->checkJoin(RFC2396_BASE, './', 'http://a/b/c/');
        $this->checkJoin(RFC2396_BASE, '..', 'http://a/b/');
        $this->checkJoin(RFC2396_BASE, '../', 'http://a/b/');
        $this->checkJoin(RFC2396_BASE, '../g', 'http://a/b/g');
        $this->checkJoin(RFC2396_BASE, '../..', 'http://a/');
        $this->checkJoin(RFC2396_BASE, '../../', 'http://a/');
        $this->checkJoin(RFC2396_BASE, '../../g', 'http://a/g');
        $this->checkJoin(RFC2396_BASE, '', RFC2396_BASE);
        $this->checkJoin(RFC2396_BASE, '../../../g', 'http://a/../g');
        $this->checkJoin(RFC2396_BASE, '../../../../g', 'http://a/../../g');
        $this->checkJoin(RFC2396_BASE, '/./g', 'http://a/./g');
        $this->checkJoin(RFC2396_BASE, '/../g', 'http://a/../g');
        $this->checkJoin(RFC2396_BASE, 'g.', 'http://a/b/c/g.');
        $this->checkJoin(RFC2396_BASE, '.g', 'http://a/b/c/.g');
        $this->checkJoin(RFC2396_BASE, 'g..', 'http://a/b/c/g..');
        $this->checkJoin(RFC2396_BASE, '..g', 'http://a/b/c/..g');
        $this->checkJoin(RFC2396_BASE, './../g', 'http://a/b/g');
        $this->checkJoin(RFC2396_BASE, './g/.', 'http://a/b/c/g/');
        $this->checkJoin(RFC2396_BASE, 'g/./h', 'http://a/b/c/g/h');
        $this->checkJoin(RFC2396_BASE, 'g/../h', 'http://a/b/c/h');
        $this->checkJoin(RFC2396_BASE, 'g;x=1/./y', 'http://a/b/c/g;x=1/y');
        $this->checkJoin(RFC2396_BASE, 'g;x=1/../y', 'http://a/b/c/y');
        $this->checkJoin(RFC2396_BASE, 'g?y/./x', 'http://a/b/c/g?y/./x');
        $this->checkJoin(RFC2396_BASE, 'g?y/../x', 'http://a/b/c/g?y/../x');
        $this->checkJoin(RFC2396_BASE, 'g#s/./x', 'http://a/b/c/g#s/./x');
        $this->checkJoin(RFC2396_BASE, 'g#s/../x', 'http://a/b/c/g#s/../x');
    }
    
    function test_RFC3986() {
        # Test cases from RFC3986
        $this->checkJoin(RFC3986_BASE, '?y','http://a/b/c/d;p?y');
        $this->checkJoin(RFC2396_BASE, ';x', 'http://a/b/c/;x');
        $this->checkJoin(RFC3986_BASE, 'g:h','g:h');
        $this->checkJoin(RFC3986_BASE, 'g','http://a/b/c/g');
        $this->checkJoin(RFC3986_BASE, './g','http://a/b/c/g');
        $this->checkJoin(RFC3986_BASE, 'g/','http://a/b/c/g/');
        $this->checkJoin(RFC3986_BASE, '/g','http://a/g');
        $this->checkJoin(RFC3986_BASE, '//g','http://g');
        $this->checkJoin(RFC3986_BASE, '?y','http://a/b/c/d;p?y');
        $this->checkJoin(RFC3986_BASE, 'g?y','http://a/b/c/g?y');
        $this->checkJoin(RFC3986_BASE, '#s','http://a/b/c/d;p?q#s');
        $this->checkJoin(RFC3986_BASE, 'g#s','http://a/b/c/g#s');
        $this->checkJoin(RFC3986_BASE, 'g?y#s','http://a/b/c/g?y#s');
        $this->checkJoin(RFC3986_BASE, ';x','http://a/b/c/;x');
        $this->checkJoin(RFC3986_BASE, 'g;x','http://a/b/c/g;x');
        $this->checkJoin(RFC3986_BASE, 'g;x?y#s','http://a/b/c/g;x?y#s');
        $this->checkJoin(RFC3986_BASE, '','http://a/b/c/d;p?q');
        $this->checkJoin(RFC3986_BASE, '.','http://a/b/c/');
        $this->checkJoin(RFC3986_BASE, './','http://a/b/c/');
        $this->checkJoin(RFC3986_BASE, '..','http://a/b/');
        $this->checkJoin(RFC3986_BASE, '../','http://a/b/');
        $this->checkJoin(RFC3986_BASE, '../g','http://a/b/g');
        $this->checkJoin(RFC3986_BASE, '../..','http://a/');
        $this->checkJoin(RFC3986_BASE, '../../','http://a/');
        $this->checkJoin(RFC3986_BASE, '../../g','http://a/g');
        
        #Abnormal Examples
        
        // The 'abnormal scenarios' are incompatible with RFC2986 parsing
        // Tests are here for reference.
        
        //$this->checkJoin(RFC3986_BASE, '../../../g','http://a/g');
        //$this->checkJoin(RFC3986_BASE, '../../../../g','http://a/g');
        //$this->checkJoin(RFC3986_BASE, '/./g','http://a/g');
        //$this->checkJoin(RFC3986_BASE, '/../g','http://a/g');
        
        $this->checkJoin(RFC3986_BASE, 'g.','http://a/b/c/g.');
        $this->checkJoin(RFC3986_BASE, '.g','http://a/b/c/.g');
        $this->checkJoin(RFC3986_BASE, 'g..','http://a/b/c/g..');
        $this->checkJoin(RFC3986_BASE, '..g','http://a/b/c/..g');
        $this->checkJoin(RFC3986_BASE, './../g','http://a/b/g');
        $this->checkJoin(RFC3986_BASE, './g/.','http://a/b/c/g/');
        $this->checkJoin(RFC3986_BASE, 'g/./h','http://a/b/c/g/h');
        $this->checkJoin(RFC3986_BASE, 'g/../h','http://a/b/c/h');
        $this->checkJoin(RFC3986_BASE, 'g;x=1/./y','http://a/b/c/g;x=1/y');
        $this->checkJoin(RFC3986_BASE, 'g;x=1/../y','http://a/b/c/y');
        $this->checkJoin(RFC3986_BASE, 'g?y/./x','http://a/b/c/g?y/./x');
        $this->checkJoin(RFC3986_BASE, 'g?y/../x','http://a/b/c/g?y/../x');
        $this->checkJoin(RFC3986_BASE, 'g#s/./x','http://a/b/c/g#s/./x');
        $this->checkJoin(RFC3986_BASE, 'g#s/../x','http://a/b/c/g#s/../x');
        // $this->checkJoin(RFC3986_BASE, 'http:g','http:g'); # strict parser
        $this->checkJoin(RFC3986_BASE, 'http:g','http://a/b/c/g'); # relaxed parser
        
        // Test for issue9721
        $this->checkJoin('http://a/b/c/de', ';x','http://a/b/c/;x');
    }

    function test_urljoins() {
        $this->checkJoin(SIMPLE_BASE, 'g:h','g:h');
        $this->checkJoin(SIMPLE_BASE, 'http:g','http://a/b/c/g');
        $this->checkJoin(SIMPLE_BASE, 'http:','http://a/b/c/d');
        $this->checkJoin(SIMPLE_BASE, 'g','http://a/b/c/g');
        $this->checkJoin(SIMPLE_BASE, './g','http://a/b/c/g');
        $this->checkJoin(SIMPLE_BASE, 'g/','http://a/b/c/g/');
        $this->checkJoin(SIMPLE_BASE, '/g','http://a/g');
        $this->checkJoin(SIMPLE_BASE, '//g','http://g');
        $this->checkJoin(SIMPLE_BASE, '?y','http://a/b/c/d?y');
        $this->checkJoin(SIMPLE_BASE, 'g?y','http://a/b/c/g?y');
        $this->checkJoin(SIMPLE_BASE, 'g?y/./x','http://a/b/c/g?y/./x');
        $this->checkJoin(SIMPLE_BASE, '.','http://a/b/c/');
        $this->checkJoin(SIMPLE_BASE, './','http://a/b/c/');
        $this->checkJoin(SIMPLE_BASE, '..','http://a/b/');
        $this->checkJoin(SIMPLE_BASE, '../','http://a/b/');
        $this->checkJoin(SIMPLE_BASE, '../g','http://a/b/g');
        $this->checkJoin(SIMPLE_BASE, '../..','http://a/');
        $this->checkJoin(SIMPLE_BASE, '../../g','http://a/g');
        $this->checkJoin(SIMPLE_BASE, '../../../g','http://a/../g');
        $this->checkJoin(SIMPLE_BASE, './../g','http://a/b/g');
        $this->checkJoin(SIMPLE_BASE, './g/.','http://a/b/c/g/');
        $this->checkJoin(SIMPLE_BASE, '/./g','http://a/./g');
        $this->checkJoin(SIMPLE_BASE, 'g/./h','http://a/b/c/g/h');
        $this->checkJoin(SIMPLE_BASE, 'g/../h','http://a/b/c/h');
        $this->checkJoin(SIMPLE_BASE, 'http:g','http://a/b/c/g');
        $this->checkJoin(SIMPLE_BASE, 'http:','http://a/b/c/d');
        $this->checkJoin(SIMPLE_BASE, 'http:?y','http://a/b/c/d?y');
        $this->checkJoin(SIMPLE_BASE, 'http:g?y','http://a/b/c/g?y');
        $this->checkJoin(SIMPLE_BASE, 'http:g?y/./x','http://a/b/c/g?y/./x');
    }

    function test_RFC2732() {
        $testcases = array(
            array('http://Test.python.org:5432/foo/', 'test.python.org', 5432),
            array('http://12.34.56.78:5432/foo/', '12.34.56.78', 5432),
            array('http://[::1]:5432/foo/', '::1', 5432),
            array('http://[dead:beef::1]:5432/foo/', 'dead:beef::1', 5432),
            array('http://[dead:beef::]:5432/foo/', 'dead:beef::', 5432),
            array('http://[dead:beef:cafe:5417:affe:8FA3:deaf:feed]:5432/foo/', 'dead:beef:cafe:5417:affe:8fa3:deaf:feed', 5432),
            array('http://[::12.34.56.78]:5432/foo/', '::12.34.56.78', 5432),
            array('http://[::ffff:12.34.56.78]:5432/foo/', '::ffff:12.34.56.78', 5432),
            array('http://Test.python.org/foo/', 'test.python.org', null),
            array('http://12.34.56.78/foo/', '12.34.56.78', null),
            array('http://[::1]/foo/', '::1', null),
            array('http://[dead:beef::1]/foo/', 'dead:beef::1', null),
            array('http://[dead:beef::]/foo/', 'dead:beef::', null),
            array('http://[dead:beef:cafe:5417:affe:8FA3:deaf:feed]/foo/', 'dead:beef:cafe:5417:affe:8fa3:deaf:feed', null),
            array('http://[::12.34.56.78]/foo/', '::12.34.56.78', null),
            array('http://[::ffff:12.34.56.78]/foo/', '::ffff:12.34.56.78', null),
        );
        foreach ($testcases as $case) {
            list($url, $hostname, $port) = $case;
            $parsed = urllib::parse($url);
            $this->assertEqual($parsed["hostname"], $hostname);
            $this->assertEqual($parsed["port"], $port);
        }
        $invalid_urls = array(
            'http://::12.34.56.78]/',
            'http://[::1/foo/',
            'ftp://[::1/foo/bad]/bad',
            'http://[::1/foo/bad]/bad',
            'http://[::ffff:12.34.56.78'
        );
        foreach ($invalid_urls as $invalid_url) {
            $this->expectException(new urllib\InvalidIP6URL($invalid_url));
            urllib::parse($invalid_url);
        }
    }

    function test_urldefrag() {
        $testcases = array(
            array('http://python.org#frag', 'http://python.org', 'frag'),
            array('http://python.org', 'http://python.org', ''),
            array('http://python.org/#frag', 'http://python.org/', 'frag'),
            array('http://python.org/', 'http://python.org/', ''),
            array('http://python.org/?q#frag', 'http://python.org/?q', 'frag'),
            array('http://python.org/?q', 'http://python.org/?q', ''),
            array('http://python.org/p#frag', 'http://python.org/p', 'frag'),
            array('http://python.org/p?q', 'http://python.org/p?q', ''),
            array(RFC1808_BASE, 'http://a/b/c/d;p?q', 'f'),
            array(RFC2396_BASE, 'http://a/b/c/d;p?q', ''),
        );
        foreach ($testcases as $case) {
            list($url, $defrag, $frag) = $case;
            $this->assertEqual(urllib::defrag($url), array($defrag, $frag));
        }
    }
}

?>