<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://qdev.com
 * @since             1.0.0
 * @package           TKChecker
 *
 * @wordpress-plugin
 * Plugin Name:       TK Checker
 * Plugin URI:        http://qedev.com
 * Description:       Write now. Research later.
 * Version:           1.0.0
 * Author:            QEDev
 * Author URI:        http://qedev.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tk-checker
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/activator.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/deactivator.php';

/** This action is documented in includes/activator.php */
register_activation_hook( __FILE__, array( 'TKCheckerActivator', 'activate' ) );

/** This action is documented in includes/deactivator.php */
register_deactivation_hook( __FILE__, array( 'TKCheckerDeactivator', 'deactivate' ) );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/tk-checker.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tk() {

	$plugin = new TK();
	$plugin->run();

}
run_tk();
