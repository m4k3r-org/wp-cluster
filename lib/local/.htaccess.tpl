# Cluster Rewrites
#
# This file should be symbolically linked to Cluster Network root.
#
# @version 3.0.1
# @author potanin@UD

# Application Environment BEGIN
# Options are: local, development, staging, or production
# Should use (In scripts):
# export ENVIRONMENT=development
# cat .htaccess.tpl | sed -e s@{ENVIRONMENT}@$ENVIRONMENT@g > .htaccess
# Will replace: SetEnv ENVIRONMENT "{ENVIRONMENT}"
SetEnv ENVIRONMENT "{ENVIRONMENT}"
# Application Environment END

# Cluster Mimes BEGIN
<IfModule mod_mime.c>
  AddEncoding gzip                                    svgz
  AddType audio/mp4                                   m4a f4a f4b
  AddType audio/ogg                                   oga ogg
  AddType application/javascript                      js
  AddType application/json                            json
  AddType video/mp4                                   mp4 m4v f4v f4p
  AddType video/ogg                                   ogv
  AddType video/webm                                  webm
  AddType video/x-flv                                 flv
  AddType application/font-woff                       woff
  AddType application/vnd.ms-fontobject               eot
  AddType application/x-font-ttf                      ttc ttf
  AddType font/opentype                               otf
  AddType application/octet-stream                    safariextz
  AddType application/x-chrome-extension              crx
  AddType application/x-opera-extension               oex
  AddType application/x-shockwave-flash               swf
  AddType application/x-web-app-manifest+json         webapp
  AddType application/x-xpinstall                     xpi
  AddType application/xml                             atom rdf rss xml
  AddType image/webp                                  webp
  AddType image/x-icon                                ico
  AddType text/cache-manifest                         appcache manifest
  AddType text/vtt                                    vtt
  AddType text/x-component                            htc
  AddType text/x-vcard                                vcf
</IfModule>
# Cluster Mimes END

# Cluster Format BEGIN
<IfModule mod_mime.c>
  AddCharset utf-8 .atom .css .js .json .rss .vtt .webapp .xml .html .htm
</IfModule>
# Cluster Format END

# Cluster Static and Media BEGIN
<IfModule mod_rewrite.c>

  RewriteEngine On

  # Rewrite assets or media when there is a subdomain
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{HTTP_HOST}::%{REQUEST_URI} ^(?:origin\.)?(assets|static|cache|media)\.(.*)::/(?:assets/|cache/|media/|static/)?(.*)$
  RewriteCond %{DOCUMENT_ROOT}/static/storage/%2/%1/%3 -f
  RewriteRule ^.*$ /static/storage/%2/%1/%3 [L]

  # Rewrite local when the domain name isn't involved
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{HTTP_HOST}::%{REQUEST_URI} ^(.*)::/(assets|static|cache|media|)/(.*)$
  RewriteCond %{DOCUMENT_ROOT}/static/storage/%1/%2/%3 -f
  RewriteRule ^.*$ /static/storage/%1/%2/%3 [L]

  # Rewrite other media URLs
  RewriteRule ^system/files/(.*)$ /static/storage/%{HTTP_HOST}/media/$1 [L]
  RewriteRule ^system/media/(.*)$ /static/storage/%{HTTP_HOST}/media/$1 [L]
  RewriteRule ^files/(.*)$ /static/storage/%{HTTP_HOST}/media/$1 [L]
  RewriteRule ^uploads/(.*)$ /static/storage/%{HTTP_HOST}/media/$1 [L]

  # TODO LATER!

  # Look to rewrite for the root specifically to index.html if it exists
  #RewriteCond %{DOCUMENT_ROOT}/static/storage/%{HTTP_HOST}/(static|cache)/index\.html -f
  #RewriteRule ^$ /static/storage/%{HTTP_HOST}/$1/index.html [NC,QSA,L]

  # Rewrite static assets that exist in the storage cache or static directory
  #RewriteCond %{DOCUMENT_ROOT}/static/storage/%{HTTP_HOST}/(static|cache)%{REQUEST_URI}.html -f
  #RewriteRule ^(.*)$ /static/storage/%{HTTP_HOST}/%1%{REQUEST_URI}.html [NC,QSA,L]

  #RewriteRule $(.*)$ http://yahoo.com/%{DOCUMENT_ROOT}/static/storage/%{HTTP_HOST}/(static|cache)%{REQUEST_URI}.html [R=301,L]

</IfModule>
# Cluster Static and Media END

# Cluster Modules BEGIN
<IfModule mod_rewrite.c>
  RewriteRule ^vendor/wordpress/core/wp-content/plugins/(.*)$ modules/$1 [R=301,L]
  RewriteRule ^wp-content/plugins/(.*)$ modules/$1 [L]
</IfModule>
# Cluster Modules END

# Cluster Theme BEGIN
<IfModule mod_rewrite.c>
  RewriteRule ^wp-content/themes/(.*)$ themes/$1 [L]
</IfModule>
# Cluster Theme END

# Cluster Management BEGIN
<IfModule mod_rewrite.c>
  RewriteRule ^xmlrpc.php$ /vendor/wordpress/core/xmlrpc.php [L]
  RewriteRule ^wp-comments-post.php$ /vendor/wordpress/core/wp-comments-post.php [L]
  RewriteRule ^wp-admin(.*)$ /vendor/wordpress/core/wp-admin/$1 [L]
  RewriteRule ^wp-includes(.*)$ /vendor/wordpress/core/wp-includes/$1 [L]
  RewriteRule ^includes(.*)$ /vendor/wordpress/core/wp-includes/$1 [L]
  RewriteRule ^wp-signup.php$ /vendor/wordpress/core/wp-signup.php$1 [L]
  RewriteRule ^manage/login/$ /vendor/wordpress/core/wp-login.php [L]
  RewriteRule ^manage/login$ /vendor/wordpress/core/wp-login.php [L]
  RewriteRule ^manage/wp-login.php$ /manage/login [R=301,L] # /manage/wp-login.php => /manage/login?loggedout=true
  RewriteRule ^manage/(.*)$ /vendor/wordpress/core/wp-admin/$1 [L]
  RewriteRule ^manage$ /manage/ [R=301,L]
</IfModule>
# Cluster Management END

# Cluster API BEGIN
<IfModule mod_rewrite.c>
  RewriteRule ^api/status.json$ /manage/admin-ajax.php?action=cluster_uptime_status [L]
</IfModule>
# Cluster API END

# Cluster Access Control BEGIN
<IfModule mod_headers.c>
  Header set Access-Control-Allow-Origin "*"
</IfModule>
# Cluster Access Control End

# Cluster Security BEGIN
<IfModule mod_rewrite.c>
  RewriteRule ^node_modules/(.*)$ /index.php [L]
  RewriteRule ^readme.md$ /index.php [L]
  RewriteRule ^wp-cli.yml$ /index.php [L]
  RewriteRule ^package.json$ /index.php [L]
  RewriteRule ^gruntfile.js$ /index.php [L]
  RewriteRule ^composer.json$ /index.php [L]
  RewriteRule ^composer.lock$ /index.php [L]
  RewriteRule ^wp-config.php$ /index.php [L]
  RewriteRule ^w3tc-config/master.php$ /index.php [L]
  RewriteRule ^w3tc-config/master-admin.php$ /index.php [L]
  RewriteRule ^application/$ /index.php [L]
  RewriteRule ^application/(config|defaults|lib)/(.*)$ /index.php [L]
</IfModule>
<FilesMatch "(^#.*#|\.(md|lock|yml|json|bak|config|dist|fla|inc|ini|log|psd|sh|sql|sw[op])|~)$">
  Order allow,deny
  Deny from all
  Satisfy All
</FilesMatch>
# Cluster Security END

# Cluster Expires BEGIN
<IfModule mod_expires.c>
  ExpiresActive on
  ExpiresDefault                                      "access plus 1 month"
  ExpiresByType text/css                              "access plus 1 year"
  ExpiresByType application/json                      "access plus 0 seconds"
  ExpiresByType application/xml                       "access plus 0 seconds"
  ExpiresByType text/xml                              "access plus 0 seconds"
  ExpiresByType image/x-icon                          "access plus 1 week"
  ExpiresByType text/x-component                      "access plus 1 month"
  ExpiresByType text/html                             "access plus 0 seconds"
  ExpiresByType application/javascript                "access plus 1 year"
  ExpiresByType application/x-web-app-manifest+json   "access plus 0 seconds"
  ExpiresByType text/cache-manifest                   "access plus 0 seconds"
  ExpiresByType audio/ogg                             "access plus 1 month"
  ExpiresByType image/gif                             "access plus 1 month"
  ExpiresByType image/jpeg                            "access plus 1 month"
  ExpiresByType image/png                             "access plus 1 month"
  ExpiresByType video/mp4                             "access plus 1 month"
  ExpiresByType video/ogg                             "access plus 1 month"
  ExpiresByType video/webm                            "access plus 1 month"
  ExpiresByType application/atom+xml                  "access plus 1 hour"
  ExpiresByType application/rss+xml                   "access plus 1 hour"
  ExpiresByType application/font-woff                 "access plus 1 month"
  ExpiresByType application/vnd.ms-fontobject         "access plus 1 month"
  ExpiresByType application/x-font-ttf                "access plus 1 month"
  ExpiresByType font/opentype                         "access plus 1 month"
  ExpiresByType image/svg+xml                         "access plus 1 month"
</IfModule>
# Cluster Expires END

# Cluster Cache BEGIN
<IfModule mod_deflate.c>

  <IfModule mod_headers.c>
    Header append Vary User-Agent env=!dont-vary
  </IfModule>

  AddOutputFilterByType DEFLATE text/css text/x-component application/x-javascript application/javascript text/javascript text/x-js text/html text/richtext image/svg+xml text/plain text/xsd text/xsl text/xml image/x-icon application/json

  # DEFLATE by extension
  <IfModule mod_mime.c>
    AddOutputFilter DEFLATE js css htm html xml
  </IfModule>

</IfModule>
# Cluster Cache END

# BEGIN W3TC CDN
<FilesMatch "\.(ttf|ttc|otf|eot|woff|font.css)$">
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
</IfModule>
</FilesMatch>
# END W3TC CDN

# BEGIN W3TC Browser Cache
<IfModule mod_deflate.c>
    <IfModule mod_headers.c>
        Header append Vary User-Agent env=!dont-vary
    </IfModule>
        AddOutputFilterByType DEFLATE text/css text/x-component application/x-javascript application/javascript text/javascript text/x-js text/html text/richtext image/svg+xml text/plain text/xsd text/xsl text/xml image/x-icon application/json
    <IfModule mod_mime.c>
        # DEFLATE by extension
        AddOutputFilter DEFLATE js css htm html xml
    </IfModule>
</IfModule>
# END W3TC Browser Cache

# WordPress BEGIN
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /
  RewriteRule ^index\.php$ - [L]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . /index.php [L]
</IfModule>
# WordPress END