<?php

namespace {

final class os {
    
    public static function getloadavg() {
        return sys_getloadavg();
    }
    
    public static function server_info() {
        $info = array();
        
        if (function_exists('memory_get_usage'))
            $info['memory_usage'] = memory_get_usage();
        
        $info['current_user'] = get_current_user();
        $info['memory_limit'] = get_cfg_var('memory_limit');
        $info['max_execution_time'] = get_cfg_var('max_execution_time');
        $info['php_version'] = phpversion();
        $info['zend_version'] = zend_version();
        $info['system_signature'] = php_uname();
        
        return $info;
    }
    
    // -- Filesystem helpers -------------------------------------------------
    
    /**
    * Generate the file names in a directory tree by walking the tree
    * top-down. For each directory in the tree rooted at directory top
    * (including top itself), it yields a 3-tuple (dirpath, dirnames,
    * filenames).
    * 
    * dirpath is a string, the path to the directory. dirnames is a list of
    * the names of the subdirectories in dirpath (excluding '.' and '..').
    * filenames is a list of the names of the non-directory files in dirpath.
    * Note that the names in the lists contain no path components. To get a
    * full path (which begins with top) to a file or directory in dirpath,
    * do os.path.join(dirpath, name).
    * 
    * By default, walk() will not walk down into symbolic links that resolve
    * to directories. Set followlinks to True to visit directories pointed to
    * by symlinks, on systems that support them.
    * 
    * Note: Be aware that setting followlinks to True can lead to infinite
    * recursion if a link points to a parent directory of itself. walk() does
    * not keep track of the directories it visited already.
    * 
    * Note: If you pass a relative pathname, don’t change the current working
    * directory between resumptions of walk(). walk() never changes the
    * current directory, and assumes that its caller doesn’t either.
    *
    * Props:
    *   <http://www.php.net/manual/en/function.scandir.php#102505>
    */
    public static function walk($top, $followlinks=false) {
        $paths = array();
        self::walk2($top, $followlinks, function($thisdir, $dirnames, $filenames) use (&$paths) {
            $paths[] = array($thisdir, $dirnames, $filenames);
        });
        return $paths;
    }
    
    public static function walk2($top, $followlinks=false, $fn) {
        $stack = array($top);
        while (!empty($stack)) {
            $thisdir = realpath(array_pop($stack));
            if (false !== ($dircontents = scandir($thisdir))) {
                $filenames = array();
                $dirnames = array();
                while (null !== ($currentfile = array_pop($dircontents))) {
                    if ($currentfile != '.' && $currentfile != '..') {
                        $currentfilepath = $thisdir . DIRECTORY_SEPARATOR . $currentfile;
                        if (is_link($currentfilepath))
                            if ($followlinks)
                                $currentfilepath = realpath($currentfilepath);
                            else
                                continue;
                        if (is_file($currentfilepath)) {
                            $filenames[] = $currentfile;
                        } else if (is_dir($currentfilepath)) {
                            $dirnames[] = $currentfile;
                            $stack[] = $currentfilepath;
                        }
                    }
                }
                $fn($thisdir, $dirnames, $filenames);
            }
        }
    }
    
    /**
    * Copy the permission bits from $src to $dst. The file contents, owner,
    * and group are unaffected. $src and $dst are path names given as strings.
    */
    public static function copymode($src, $dst) {
        $st = stat($src);
        $mode = $st['mode'];
        chmod($dst, $mode);
    }
    
    /**
    * Recursively delete a directory tree.
    * 
    * If ignore_errors is set, errors are ignored; otherwise, if onerror
    * is set, it is called to handle the error with arguments (func,
    * path, exc_info) where func is os.listdir, os.remove, or os.rmdir;
    * path is the argument to that function that caused it to fail; and
    * exc_info is a tuple returned by sys.exc_info().  If ignore_errors
    * is false and onerror is None, an exception is raised.
    */
    public static function rmtree($path, $ignore_errors=false, $onerror=null) {
        if ($ignore_errors)
            $onerror = function($fntype, $path, $e) {};
        else if ($onerror === null)
            $onerror = function ($fntype, $path, $e) { throw $e; };
        
        try {
            if (is_link($path))
                throw new \ErrorException('Cannot call rmtree on symbolic link');
        } catch (\ErrorException $e) {
            $onerror('is_link', $e, $path);
            return;
        }
        
        $names = array();
        try {
            $names = glob("{$path}/*", GLOB_ERR);
            if (false === $names)
                throw new \ErrorException(
                    "Error listing directory contents: {$path}");
        } catch (\ErrorException $e) {
            $onerror('listdir', $path, $e);
        }
        
        foreach ($names as $name) {
            $fullname = $path . DIRECTORY_SEPARATOR . $name;
            if (is_dir($fullname))
                self::rmtree($fullname, $ignore_errors, $onerror);
            else {
                try {
                    unlink($fullname);
                } catch (\ErrorException $e) {
                    $onerror('unlink', $fullname, $e);
                }
            }
        }
        
        try {
            rmdir($path);
        } catch (\ErrorException $e) {
            $onerror('rmdir', $path, $e);
        }
    }
}

}

namespace os {

environ::load();

final class environ {
    
    static $env = null;
    
    public static function get($key, $default=null) {
        return self::has_key($key) ? self::$env[$key] : $default;
    }
    
    public static function set($key, $value) {
        self::load();
        self::$env[$key] = $value;
        putenv("{$key}={$value}");
    }
    
    public static function has_key($key) {
        self::load();
        return array_key_exists($key, self::$env);
    }
    
    public static function remove($key) {
        self::load();
        unset(self::$env[$key]);
        putenv($key); // removes $key from environment
    }
    
    /* private */ static function load() {
        if (self::$env === null) {
            $env = array();
            foreach (array_merge($_ENV, $_SERVER) as $key => $value)
                $env[$key] = $value;
            self::$env = $env;
        }
    }
}

}

namespace os {

use strutils;
    
// Replaces all '/' and '\' characters with $to_slash.
function normslashes($path, $to_slash='/') {
    return preg_replace('/[\/\\\\]+/', $to_slash, $path);
}

final class path {
    
    /**
    * Test whether a path is absolute
    */
    public static function isabs($s) {
        return strutils::startswith(normslashes($s), '/');
    }
    
    /**
    * Join two or more pathname components, inserting '/' as needed.
    * If any component is an absolute path, all previous path components
    * will be discarded.
    */
    public static function join($path /*, $path1, $path2, ... */) {
        $p = func_get_args();
        $path = array_shift($p);
        if (is_array($path)) {
            $p = $path;
            $path = array_shift($p);
        }
        
        $path = normslashes($path);
        foreach ($p as $b) {
            $b = normslashes($b);
            if (strutils::startswith($b, '/'))
                $path = $b;
            else if ($path === '' || strutils::endswith($path, '/'))
                $path .= $b;
            else
                $path .= '/' . $b;
        }
        return normslashes($path, DIRECTORY_SEPARATOR);
    }
    
    /**
    * Normalize a path, e.g. A//B, A/./B and A/foo/../B all become A/B.
    * It should be understood that this may change the meaning of the path
    * if it contains symbolic links!
    */
    public static function normpath($path) {
        $slash = DIRECTORY_SEPARATOR;
        $dot = '.';
        if ($path === '')
            return $dot;
        $path = normslashes($path);
        $initial_slashes = strutils::startswith($path, '/') ? 1 : 0;
        if ($initial_slashes &&
            strutils::startswith($path, '//') &&
            !strutils::startswith($path, '///'))
        {
            $initial_slashes = 2;
        }
        $comps = strutils::split($path, '/');
        $new_comps = array();
        foreach ($comps as $comp) {
            if (in_array($comp, array('', '.')))
                continue;
            if ($comp !== '..' || (!$initial_slashes && empty($new_comps)) ||
                (!empty($new_comps) && end($new_comps) === '..'))
                $new_comps[] = $comp;
            else if (!empty($new_comps))
                array_pop($new_comps);
        }
        $comps = $new_comps;
        $path = strutils::join($slash, $comps);
        if ($initial_slashes)
            $path = str_repeat($slash, $initial_slashes) . $path;
        return normslashes($path ?: $dot, DIRECTORY_SEPARATOR);
    }
    
    /**
    * Return an absolute path.
    */
    public static function abspath($path) {
        if (!self::isabs($path))
            $path = self::join(getcwd(), $path);
        return self::normpath($path);
    }
    
    /**
    * Return a relative version of a path.
    */
    public static function relpath($path, $start='.') {
        if (!$path)
            throw new \Exception('no path specified');
        
        $start_list = array_values(array_filter(
            strutils::split(self::abspath($start), DIRECTORY_SEPARATOR), 
            function($x) { return !empty($x); }));
        $path_list = array_values(array_filter(
            strutils::split(self::abspath($path), DIRECTORY_SEPARATOR), 
            function($x) { return !empty($x); }));
        
        $i = count(self::commonprefix(array($start_list, $path_list)));
        
        $rel_list = array_merge(
            array_pad(array(), count($start_list) - $i, '..'),
            array_slice($path_list, $i));
        
        if (empty($rel_list))
            return '.';
        return self::join($rel_list);
    }
    
    /**
    * Given a list of pathnames, returns the longest common leading component.
    */
    public static function commonprefix($m) {
        if (empty($m))
            return '';
        $s1 = min($m);
        $s2 = max($m);
        for ($i=0; $i < count($s1); $i++) {
            if ($s1[$i] !== $s2[$i])
                return array_slice($s1, 0, $i);
        }
        return $s1;
    }
}

}
