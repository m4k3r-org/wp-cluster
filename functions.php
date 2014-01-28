<?php
/**
 * Author: UsabilityDynamics, Inc.
 * Author URI: http://www.usabilitydynamics.com/
 *
 * @version 1.0.0
 * @author UsabilityDynamics
 * @subpackage WP-Drop
 * @package WP-Drop
 */

// Legacy Flawless Libraries.
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/legacy/ud_saas.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/legacy/ud_functions.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/legacy/ud_tests.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/legacy/backend-functions.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/legacy/business-card.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/legacy/class-flawless-utility.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/legacy/front-end-editor.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/legacy/login_module.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/legacy/theme_ui.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/legacy/shortcodes.php' );

// Disco Libraries.
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/class-bootstrap.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/class-disco.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/widgets.php' );
include_once( untrailingslashit( STYLESHEETPATH ) . '/lib/template.php' );

new UsabilityDynamics\Theme\Disco\Boostrap;
// Bootstrap WP-Disco Theme.
if( class_exists( 'UsabilityDynamics\Theme\Disco\Boostrap' ) ) {
}
