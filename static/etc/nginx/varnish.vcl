acl ClearCache {
  "localhost";
  "192.168.1.1";
}

include "/etc/varnish/cpanel.backend.vcl";
include "/etc/varnish/backends.vcl";
include "/etc/varnish/security.vcl";
include "/etc/varnish/iratelimit.vcl";

backend corporate {
  .host = "localhost";
  .port="8080";
  .connect_timeout = 600s;
  .first_byte_timeout = 600s;
  .between_bytes_timeout = 600s;
  .max_connections = 800;
}

## Normalize client-input
## Re-write client-data for web applications
## CloudFront will strip out majority of the headers, such as User Agent.
sub vcl_recv {

  if (
    req.http.host == "apex-origin.udx.io" ||
    req.http.host == "apex-origin.usabilitydynamics.com"  ||
    req.http.host == "apex-origin.pluginbrowser.com"  ||
    req.http.host == "www-origin.baldrichfalcons.com" ) {
    set req.backend = corporate;
    return (lookup);
  }

  # Use the default backend for all other requests
  set req.backend = default;

  if (req.http.User-Agent ~ "iPad" || req.http.User-Agent ~ "iPhone" || req.http.User-Agent ~ "Android") {
    set req.http.X-Device = "mobile";
  } else {
    set req.http.X-Device = "desktop";
  }

  # Setup the different backends logic
  include "/etc/varnish/acllogic.vcl";

  # Allow a grace period for offering "stale" data in case backend lags
  set req.grace = 5m;

  remove req.http.X-Forwarded-For;
  set req.http.X-Forwarded-For = client.ip;

  # cPanel URLs
  include "/etc/varnish/cpanel.url.vcl";

  # Properly handle different encoding types
  if (req.http.Accept-Encoding) {
  	if (req.url ~ "\.(jpg|jpeg|png|gif|gz|tgz|bz2|tbz|mp3|ogg|swf|ico)$") {
  		# No point in compressing these
  		remove req.http.Accept-Encoding;
  	} elsif (req.http.Accept-Encoding ~ "gzip") {
  		set req.http.Accept-Encoding = "gzip";
  	} elsif (req.http.Accept-Encoding ~ "deflate") {
  		set req.http.Accept-Encoding = "deflate";
  	} else {
  		# unkown algorithm
  		remove req.http.Accept-Encoding;
  	}
  }

  include "/etc/varnish/ratelimit.vcl";

  # Set up disabled
  set req.http.X-Jiggaboo = "true";

  ## Serve Api POST requests when we explicitly declare it with an "x-api-request" header.
  ## Do before we pipe logged in sessions.
  if ( req.request == "POST" && req.http.X-Api-Request == "true" ) {

    ## For future compat.
    set req.http.X-Veneer-Proxy = "true";

    ## Strip all Cookies from Api requests.
    unset req.http.Cookie;

  	return (lookup);

  }

  ## Strip out the "wordpress_test_cookie", unless we are on an administrative page.
  if( ! ( req.url ~ "^/manage.*|/wp-admin.*|/manage/|/manage|/wp-login.php" ) ) {
    set req.http.Cookie = regsuball(req.http.Cookie, "(^|;\s*)(wordpress_test_cookie)=[^;]*", "");
  }

  ## Dont serve cached pages to logged in users.
	if ( req.http.cookie ~ "wordpress_" || req.http.cookie ~ "wp-" ) {
		return( pipe );
	}

  ## Disable cookies on everything except management
  if( ! ( req.url ~ "^/manage.*|/wp-admin.*|/manage/|/manage|/wp-login.php" ) ) {
    unset req.http.Cookie;
  }

  # Exclude upgrade, install, server-status, etc
  include "/etc/varnish/known.exclude.vcl";

  # Set up exceptions
  include "/etc/varnish/url.exclude.vcl";

  # Set up exceptions
  include "/etc/varnish/debugurl.exclude.vcl";

  # Set up exceptions
  include "/etc/varnish/vhost.exclude.vcl";

  # Set up user defined vhost exceptions
  include "/etc/varnish/aggregates/disable_domains.vcl";

  # Bail out on CloudFront immediatly.
  # if( req.http.User-Agent ~ "Amazon CloudFront" || req.http.host ~ "origin." ) {
  if( req.http.host ~ "origin." ) {
    # set req.http.X-Varnish-Bypass = "true";
    # set req.http.connection = "close";
    return ( pipe );
  }

  # Set up vhost+url exceptions
  include "/etc/varnish/vhosturl.exclude.vcl";

  # Set up cPanel reseller exceptions
  include "/etc/varnish/reseller.exclude.vcl";

  # Restart rule for bfile recv
  include "/etc/varnish/bigfile.recv.vcl";

  if (req.request == "BAN") {
    if (!client.ip ~ ClearCache) {
        error 405 "Not allowed.";
    }

    # This option is to clear any cached object containing the req.url
    ban("req.url ~ "+req.url);

    # This option is to clear any cached object matches the exact req.url
    # ban("req.url == "+req.url);

    # This option is to clear any cached object containing the req.url
    # AND matching the hostname.
    # ban("req.url ~ "+req.url+" && req.http.host == "+req.http.host);

    error 200 "Cached Cleared Successfully.";
  }

  if (req.request == "PURGE") {
    if (!client.ip ~ acl127_0_0_1) {error 405 "Not permitted";}
    return (lookup);
  }

  ## Default request checks
  if (req.request != "GET" && req.request != "HEAD" && req.request != "PUT" && req.request != "POST" && req.request != "TRACE" && req.request != "OPTIONS" && req.request != "DELETE") {
  	return (pipe);
  }

  ## Modified from default to allow caching if cookies are set, but not http auth
  if (req.http.Authorization) {
  	return (pass);
  }

  include "/etc/varnish/versioning.static.vcl";

  ## After this point we only deal with GET and HEAD
  if (req.request != "GET" && req.request != "HEAD") {
  	return (pass);
  }

  include "/etc/varnish/slashdot.recv.vcl";

  # Cache things with these extensions
  if (req.url ~ "\.(json|js|css|jpg|jpeg|png|gif|gz|tgz|bz2|tbz|mp3|ogg|swf|pdf)$" && ! (req.url ~ "\.(php)") ) {
    unset req.http.Cookie;
    return (lookup);
  }

  return (lookup);

}

## vcl_hash defines the hash key to be used for a cached object. Or in other words: What separates one cached object from the next.
sub vcl_hash {

   hash_data(req.url);

   if (req.http.host) {
     hash_data(req.http.host);
   } else {
     hash_data(server.ip);
   }

   return (hash);

}

## Sanitize server-response
## Override cache duration
sub vcl_fetch {

  ## Note: When you perform a pass in vcl_fetch you cache the decision you made. Always set beresp.ttl when you issue a pass in vcl_fetch.

  set beresp.ttl = 40s;

  ## Remove some semi-sensitive headers.
  unset beresp.http.Pingback;
  unset beresp.http.Link;

  # Turn off Varnish gzip processing
  include "/etc/varnish/gzip.off.vcl";

  # These status codes should always pass through and never cache.
  if ( beresp.status == 404 || beresp.status == 503 || beresp.status == 500) {
  	set beresp.http.X-Cacheable = "NO: beresp.status";
  	set beresp.http.X-Cacheable-status = beresp.status;
  	return (hit_for_pass);
  }

  if (beresp.http.Cache-Control ~ "no-cache") {
  	set beresp.http.X-Cacheable = "NO: beresp.status";
  	return (hit_for_pass);
  }

  if ( req.request == "POST" && req.http.X-Api-Request == "true" ) {
    unset beresp.http.expires;
    set beresp.http.magicmarker = "1";
  	set beresp.http.X-Cacheable = "YES";
  	set beresp.ttl = 60s;

	  ## remove req.http.Cache-Control;
	  ## set obj.http.Cache-Control = "private";

  	return (deliver);
  }

  # Grace to allow varnish to serve content if backend is lagged
  set beresp.grace = 5m;

  # Restart rule bfile for fetch
  include "/etc/varnish/bigfile.fetch.vcl";

  # 404 pages not cacheed atm
  # if (beresp.status == 404) {
  # 	set beresp.http.magicmarker = "1";
  # 	set beresp.http.X-Cacheable = "YES";
  # 	set beresp.ttl = 20s;
  # 	return (deliver);
  # }

  /* Remove Expires from backend, it's not long enough */
  unset beresp.http.expires;

  if (req.url ~ "\.(js|css|jpg|jpeg|png|gif|gz|tgz|bz2|tbz|mp3|ogg|swf|pdf|ico)$" && ! (req.url ~ "\.(php)") ) {
  	unset beresp.http.set-cookie;
  	include "/etc/varnish/static.ttl.vcl";
  	include "/etc/varnish/aggregates/static_ttl.vcl";
  }

  include "/etc/varnish/slashdot.fetch.vcl";

  else {
  	include "/etc/varnish/dynamic.ttl.vcl";
  	include "/etc/varnish/aggregates/dynamic_ttl.vcl";
  }

  /* marker for vcl_deliver to reset Age: */
  set beresp.http.magicmarker = "1";

  # All tests passed, therefore item is cacheable
  set beresp.http.X-Cacheable = "YES";

  return (deliver);
}

## Common last exit point for all (except vcl_pipe) code paths
## Very useful for modifying the output of Varnish.
## If you need to remove a header, or add one that isn’t supposed to be stored in the cache
sub vcl_deliver {

  ## Add some of our branding.
  set resp.http.Via = "UDX Varnish Layer v1.1.2";
  ## set resp.http.X-Powered-By = "UDX Cluster v0.8.0";
  ## set resp.http.Server = "Veneer Web Acceleration v2.1.0";

  # From http://varnish-cache.org/wiki/VCLExampleLongerCaching
  if (resp.http.magicmarker) {

     /* Remove the magic marker */
     unset resp.http.magicmarker;

     /* By definition we have a fresh object */
     set resp.http.age = "0";
   }

   #add cache hit data
   if (obj.hits > 0) {
     set resp.http.X-Cache = "HIT";
     set resp.http.X-Cache-Hits = obj.hits;
   } else {
     set resp.http.X-Cache = "MISS";
   }

}

sub vcl_error {

  if (obj.status == 503 && req.restarts < 5) {
    set obj.http.X-Restarts = req.restarts;
    return (restart);
  }

}

## Called when the requested object was not found in the cache
## Right after an object has been found (hit) in the cache
sub vcl_hit {

  if (req.request == "PURGE") {
    error 404 "Not in cache.";
  }

  if (obj.ttl < 1s) {
  	return (pass);
  }

  if (req.http.Cache-Control ~ "no-cache") {
    # Ignore requests via proxy caches,  IE users and badly behaved crawlers
    # like msnbot that send no-cache with every request.
    if (! (req.http.Via || req.http.User-Agent ~ "bot|MSIE|HostTracker")) {
    	set obj.ttl = 0s;
    	return (restart);
    }
  }

  return (deliver);

}

## Right after an object was looked up and not found in cache
sub vcl_miss {

  if (req.request == "PURGE") {
  	purge;
  	error 200 "Purged";
  }
}
