Festival Theme for the EDM DiscoDonniePresents Network.

### JavaScript Asset Naming Convention
* init.{version-hash}.js
* boot.{version-hash}.js
* {feature}.{version-hash}.js

### WordPress Methods

* includes_url() - Use this to reference all assets, to include those in theme, such as app.js, app.css, etc.
* content_url() - Use this to reference all media assets to include logos, touch icons, favicon and other uploads.
* home_url() - Use this to reference all media assets.

### PHP Methods
The following PHP methods are public.

* wp_festival2()->section() - Group of dynamic/static asides.
* wp_festival2()->aside() - Single dynamic aside.
* wp_festival2()->nav() - Navigation wrappper.
* wp_festival2()->widget_area() - Widget area wrapper.

### Layout Sections
* header
* banner
* footer

### Conditional Body Classes
* home
* page
* page-template
* logged-in
* admin-bar
* internal-referrer
* debug-bar-maximized
* customize-support
* menufication-is-logged-in

### Notes
* For the header image and text, the following meta keys are used for posts/pages
  - headerImage
  - headerTitle
  - headerSubtitle
