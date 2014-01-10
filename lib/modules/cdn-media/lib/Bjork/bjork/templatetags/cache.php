<?php

use bjork\template\Library;

use bjork\core\cache,
    bjork\template\TemplateSyntaxError;

$library = new Library();

$library->tag('cache', function($self, $expire_time, $fragment_name /*, $vary_on_var1, $vary_on_var2, ..., $vary_on_varN, */, $fn) {
    $vary_on = func_get_args();
    array_shift($vary_on); // $expire_time
    array_shift($vary_on); // $fragment_name
    $fn = array_pop($vary_on); // $fn
    if (!is_callable($fn))
        throw new TemplateSyntaxError('last argument must be a callable');
    
    // Create a cache key depending on vary_on
    $args = md5(implode(':', array_map('strval', $vary_on)));
    $cache_key = "template.cache.{$fragment_name}.{$args}";
    
    $cache = cache::get_default_cache();
    
    // Fetch the value from cache
    $value = $cache->get($cache_key);
    
    if ($value === null) { // Not found in cache
        ob_start();
        $fn($self);
        $value = ob_get_clean();
        $cache->set($key, $value);
    }
    
    return $value;
    
}, array(
    'takes_self' => true,
));

return $library;
