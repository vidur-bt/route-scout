<?php
/**
 * Plugin Name:       Route Scout
 * Plugin URI:        https://wordpress.org/plugins/route-scout/
 * Description:       Browse, test, and document your WordPress REST API endpoints without leaving wp-admin. Authenticated requests handled automatically.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Route Scout
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       route-scout
 * Domain Path:       /languages
 *
 * @package RouteScout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ROUTE_SCOUT_VERSION', '1.0.0' );
define( 'ROUTE_SCOUT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ROUTE_SCOUT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once ROUTE_SCOUT_PLUGIN_DIR . 'includes/class-route-discovery.php';
require_once ROUTE_SCOUT_PLUGIN_DIR . 'includes/class-rest-proxy.php';
require_once ROUTE_SCOUT_PLUGIN_DIR . 'includes/class-admin.php';

/**
 * Initializes the plugin.
 */
function route_scout_init(): void {
	$admin     = new RouteScout\Admin();
	$proxy     = new RouteScout\Rest_Proxy();
	$discovery = new RouteScout\Route_Discovery();

	$admin->register();
	$proxy->register();
	$discovery->register();
}
add_action( 'plugins_loaded', 'route_scout_init' );
