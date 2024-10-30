<?php
/**
 * Plugin Name:       JAMP Notes
 * Plugin URI:        https://github.com/andreaporotti/just-another-memo-plugin
 * Description:       This plugin allows you to attach notes to some WordPress elements like posts, pages, dashboard sections and others.
 * Version:           1.5.1
 * Requires at least: 4.9
 * Requires PHP:      5.6
 * Author:            Andrea Porotti
 * Author URI:        https://www.andreaporotti.it
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       jamp
 * Domain Path:       /languages
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, see http://www.gnu.org/licenses/gpl-2.0.txt.
 *
 * =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
 *
 * @since             1.0.0
 * @package           Jamp
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Uses SemVer ( https://semver.org ).
 */
define( 'JAMP_VERSION', '1.5.1' );

/**
 * Plugin name.
 */
define( 'JAMP_PLUGIN_NAME', 'JAMP Notes' );

/**
 * The code that runs during plugin activation.
 */
function activate_jamp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jamp-activator.php';
	Jamp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_jamp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jamp-deactivator.php';
	Jamp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_jamp' );
register_deactivation_hook( __FILE__, 'deactivate_jamp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-jamp.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_jamp() {

	$plugin = new Jamp();
	$plugin->run();

}
run_jamp();
