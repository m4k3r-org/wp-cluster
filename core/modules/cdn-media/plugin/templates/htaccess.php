
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ <?=$amazon_path?>$1 [QSA,L,NC]
