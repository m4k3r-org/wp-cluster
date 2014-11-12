On Ramadi, this repository is checked out into /opt/sources/DiscoDonniePresents/mocks and is symbolically linked to any appropriate directories in /var/www/storage/public/.

Also, be aware that the static-page logic expects files to have "html" extension, so if PHP logic is needed to be executed, add a PHP handler to .htaccess for html files, as seen in the umesouthpadre.com directory.

### Caching
Since we use Varnish, static pages can be configured to establish caching policies.

For example, caching policy can be set in HTML meta tags, in this example we cache for up to 30 minutes.

```
<META HTTP-EQUIV="Pragma" CONTENT="public">
<META HTTP-EQUIV="Cache-Control" CONTENT="public, max-age=1800, must-revalidate">
<META HTTP-EQUIV="Expires" CONTENT="Tue, 21 Oct 2014 16:34:04 GMT">
```
