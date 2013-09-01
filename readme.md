== Description ==
* Flawless WordPress Theme By Usability Dynamics, Inc.
* Theme Homepage - http://usabilitydynamics.com/products/flawless/

component install

## To Do
- Update Carrington Build and look into integrating https://raw.github.com/crowdfavorite/wp-carrington-build-bootstrap-3/master/build-bootstrap-grid.php.

== Notes ==
The theme has a default maximum width of 1090px, which is the default Flawless width, but can be adjusted under Appearance Settings.
On screens smaller than 1199px, the configured width is ignored, and the layout switches to a maximum width of 940px.
Below 979px, we assume a mobile devices is being used, and the layout expands to full width of the browser, converts all columns to full width and renders the mobile navbar.
Uses Twitter Bootstrap framework to handle the 12-column grid. By default, all blocks are fluid. Excluded components are: Hero Unit, Breadcrumbs, and Modals.

== Debugging ==
JavaScript function flawless.toggle_visual_debug(); can be called via console to render a grid helper.

== Theming ==
As far as layout are concerned, the basic structure goes like so: div.row-fluid > div.span[x] > div.cfct-module\
To stay consistent with automatically generated elements, add a .first and .last classes to the first and last div.span[x] in each row.
The Twitter Bootstrap scaffolding structure is followed closely with the core exception of our div.span[x] elements not having any margins.
To maintain relational widths, all spacing is handled by the inner .cfct-module elements.

== Using ==
Masonry is applied to all galleries by default, unless gallery has 'disable-masonry' class.
Masonry can be applied to rows by adding a 'enable-masonry' class to the row.

== Changelog ==
= 0.1.1 =
* Globally renamed all PHP references to "flawless_theme" to "Flawless".
* Updated all JS libraries to utilize CDNs and removed from theme.
* Migrated all template components into /templates directory.
* Fixed WordPrss 3.6 compatibility bug with detection of current page on admin side.

= 0.1.0 =
* Changed wp-login.php access prevention override to ?override_wp_login_access=true
* Migrated get_template_part() into Flawless class.
* Implemented UD API Distributable version 1.0.0. 
* Flawless functionality relocated from functions.php into /core/flawless-loader.php to avoid fatal crash on older versions of PHP.
* Console Log no longer shows regular log entries unless specifically enabled. Info and Errors are always shown (when in debug mode).
* Added doing_it_wrong_run monitor that renders messages into console log when in developer mode.
* flawless_primary_notice_container() is now added via API to flawless::content_container_top action so it's position can be manipulated by plugins.
* Added option to exclude plugin-added CSS from compilation.
* Update front-end CSS generation notice to include the modified file that triggered the update.
* Added automated compiled CSS regeneration when an included CSS file is updated by comparing modified dates.
* Added automation parsing of LESS variables from variables.less into $flawless[css_options]
* Added LESS support and converted static Twitter Bootstrap to LESS. Theme creates a static compiled and minified screen-styles.css and screen-styles.dev.css in the child theme directory.
* Added flawless_add_notice() function for printing notices on the fornt-end.

= 0.0.9 =
* Modernized Carrington Build feature file structure.
* Changed filenames of Core Asset functions to match class names, when applicable.
* Renamed filter: flawless_remote_assets to flawless::remote_assets
* Removed filter: flawless::conditional_assets
* Removed loading of /css/content.css use /css/flawless-content.css
* Fixed Navbar to load at a lower level allowing Edit Page link to be used.
* Moved option to disable wp-login.php access to Advanced Tab since a user could potentially lock themselves out that way.
* Added Contextual Help to Settings page a placeholder for General Help, and examples for Theme Development and JavaScript helpers.
* Added "No Thumbnail" image placeholder to skin selection for skins that do not have a thumbnail.
* Updated Flawless::load( $name, $type ) to handle different asset types. Default is PHP file library, but $type of image can be specified as well.
* Fixed issue with Header Login notifications not being displayed on login errors and password reset.
* Improved the way Child Theme and Skin stylesheet options are handled. Stylesheet options are now stored in $flawess[current_theme_options] which is created from combination of Child Theme and Skin. Child Themes can now declare Google Fonts.
* Added array_merge_recursive_distinct() to $flawless variables so variable can be defined with default settings prior to options being loaded form DB.
* Flawless Version is now combined with Child Theme version, if set, for asset URLs. Core version is stored in Flawless_Core_Version.
* Flawless::load() function now checks all Asset Directories for a library.
* Developer Note: Action flawless_loaded renamed to flawless::loaded

= 0.0.8  =
* Added Header Actions Menu.
* Added automatic Google Font loader when fonts are defined in skin.
* Added support for 404 Redirected plugin. When present, suggestions are automatically inserted into 404 Page, and available via [wbz404_suggestions] shortcode.
* Added options to disable EqualHeights and Masonry libraries.
* Added "icon" support to [button] shortcode.
* Added WP-Property page options to: Hide Location Map, Hide Attributes and Hide Taxonomies
* Added Green Skin
* Improved WP-Property single listing pages to utilize shortcodes for attribute list, taxonomies and map.
* Added action to 404 page: flawless::404_page_content
* Improvements to flawless_page_title() to better handle post titles within loops.
* Bugfix: Business Card widget was not being loaded early enough.

= 0.0.7  =
* Added ability to customized placeholder text in search input field.
* Added option to disable automatically-growing search input field.
* Added an invisible post edit link that is located in the top right corner of the page.
* Set Default Skin to hide Page Title on Carrington Build pages automatically to avoid the ongoing spacing issue.
* Redesigned the way the overall layout and grid is handled by completely eliminating all Carrington Build styles and fully converting to the Twitter Bootstrap grid.
* Updated to Twitter Bootstrap CSS and JavaScripts to version 2.03.
* Changed the global width setting from .super_wrapper to .container, which can also be configured via control panel.
* Fixed admin JavaScript to verify UD CDN jQuery plugins are loaded before calling them.
* Changed the automatically expanding search input field to only expand when in header.
* Fixed bug with post type root pages not being loaded.

= 0.0.6 =
* Added Extended Term Editor.
* Added term taxonomy thumbnail support.
* Added default options loader, done automatically if no Flawless settings exist, and a default-configuration.json exists in the stylesheet directory.
* Added better theme support handling for Business Card and Frontend Editor features.
* Added flawless_render_in_footer() function for adding content into a queve that is rendered in the footer.
* Standardized taxonomy archive template.
* Improved breadcrumbs to handle added post type custom permalink rules.
* Added taxonomy meta handling that creates a wp_taxonomymeta table following the WP Metadata API and adds new functions: add_term_meta(), delete_term_meta(), get_term_meta(), and update_term_meta()
* Added support for custom taxonomy rewrite rules. Example, a custom taxonomy with a slug of my_taxonomy can be used in permalink rules as %my_taxonomy% in a manner similar to %category%
* Fixed issue with page title displaying a trailing dash when no site description is set.
* Added support for post-type specific rewrite rules.
* Replaced jQuery Cookie library with jquery.cookies.js by James Auldridge
* Added Settings page action that will remove all Post Revisions, only leaving the 3 most recent, and optimize MySQL tables.
* Frontend notices are now handled by the flawless_primary_notice_container() function.
* Theme automatically re-creates .htaccess if it does not exist but permalinks are setup.
* Added JavaScript Google Analytics event tracking handler. Example: jQuery( document ).trigger( 'flawless::track_event', { category: 'Movies', action: 'Downloaded', label: 'Mission Impossible' } );
* Added Google Analytics event tracking to Login Module.
* Added a post type switch option on the right side of the page editor.
* Added Show "Edit Layout" link at far right side of the Navbar. - enabled under Settings -> Appearance.
* Added flawless_main_class() in all .main.cfct-blocks to add Carrington Build classes to the main wrapper.
* Body classes now contain row classes depending on the grid layout. -  eg. row-c6-12-3456
* Added utility function in_array_like() in functions.php for comparing arrays using LIKE.
* Added default logo if a user uploaded logo is not present.

= 0.0.5 =
* Improved the way JavaScript files are loaded.
* Moved "JavaScript Enhancements" settings under the Advanced tab.
* Added Visual Debug option that for layout design and development - enabled under Settings -> Appearance.

= 0.0.4 =
* Fixed bug with third-party added taxonomies not showing up in post type association interface.
* Added a fail-safe to the Login Module for when AJAX fails due to an unforeseen JavaScript error, that will still log users in after the page is reloaded. (WIP)

= 0.0.3 =
* Usability Dynamics jQuery Plugins are now loaded by way of CDN.
* Improvements to Login Module.
* Improved and standardized structure of content stylesheet.
* Removed legacy WP-Property and WP-CRM code.
* Fixed bug with Appearance -> Background rendering function which was displaying the background CSS even if there was no background.
* Improved default page title handling.
* Option to hide Author.

= 0.0.2 =
* Added Mobile Navbar UI.
* Fixes to permalink rewrites to resolve issues with Shopp plugin.
* Carrington Build update to version 1.2.2.
* Removed 4-column CB row.
* Relocated Carrington Build modules into flawless/modules

= 0.0.1 =
* Logo handling updated - uploaded logos are now loaded and stored in the Media Library.
* Added a Header Navbar management panel which lets you select the type of Navbar, if any, to display on the front-end.  In addition to the Navbar itself, you may also add optional components such as a User Login form, a collapsible menu expander for mobile resolutions, as well as a displaying your brand.
* Added full Navbar support for the BuddyPress Admin Bar.
* Added splash screen to notify when theme has been updated, with a changelog.
* Restructured the way extra assets (e.g. fancybox, prettify, form helper) are loaded.  They are now registered automatically and then enqueued later on if enabled.  This way they can be enqueued manually since they are always registered.
* Added Google Prettify and some language styles we will use.
* Added "Users" to BuddyPress Navbar "Manage" dropdown.
* Global variable $fs replaced for $flawless. (old one still works, but should be phased out)
* Added a top Navbar which can be used to render a custom menu.
* Added responsive styles which only affect the Navbar.
* Added option to disable the BuddyPress navbar.
* Changed the theme settings page UI to match the WP Appearance / Plugins pages.
* Fixed bug with "Add Row" not working on back-end.
* Updated Carrington Build to 1.2.1
* Added shortcodes: [image_url] and [button]
* Created Flawless::extra_local_assets() function for loading things like Fancybox.
* Numerous BuddyPress updates, and new shortcodes: group_meta, group_description.
* Updated to BuddyPress Groups Carrington Build module to execute shortcodes.
* Added 4-column Carrington Build row.
* Added UD Loop, a branch of the Carrington Build Loop, although kept inactive.
* Removed the Ajax Pages Carrington Build module.
* Reactivated the Carrington Build Text module.
* Added Flawless::extra_local_assets() to handle loading of extra local assets, such as Fancybox.
* .inner_content_wrapper and all references changed to .container
* .post_listing_inner class added to CB Loop module excerpt and full content listings
* [button] shortcode added
