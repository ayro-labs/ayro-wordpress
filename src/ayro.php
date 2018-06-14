<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       Ayro
 * Plugin URI:        https://ayro.io/guides/wordpress
 * Description:       Ayro is multi channel customer support software. With Ayro you can chat with your customers wherever they are, directly from your Slack workspace. Integrate your Wordpress website with Ayro within a few minutes.
 * Version:           0.0.33
 * Author:            Ayro
 * Author URI:        https://ayro.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ayro
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('AYRO_PLUGIN_NAME', 'ayro');
define('AYRO_PLUGIN_VERSION', '0.0.33');
define('AYRO_LIBRARY_VERSION', '0.0.47');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/ayro-activator.php
 */
function activateAyro() {
  require_once plugin_dir_path(__FILE__) . 'includes/ayro-activator.php';
  AyroActivator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/ayro-deactivator.php
 */
function deactivateAyro() {
  require_once plugin_dir_path(__FILE__) . 'includes/ayro-deactivator.php';
  AyroDeactivator::deactivate();
}

register_activation_hook(__FILE__, 'activateAyro');
register_deactivation_hook(__FILE__, 'deactivateAyro');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/ayro-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
$plugin = new AyroPlugin();
$plugin->run();
