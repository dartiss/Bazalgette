<?php
/**
 * Bazalgette
 *
 * @package           bazalgette
 * @author            David Artiss
 * @license           GPL-2.0-or-later
 *
 * Plugin Name:       Bazalgette
 * Plugin URI:        https://wordpress.org/plugins/bazalgette/
 * Description:       Clean your admin menus automagically
 * Version:           1.0
 * Requires at least: 4.6
 * Requires PHP:      8.0
 * Author:            David Artiss
 * Author URI:        https://artiss.blog
 * Text Domain:       bazalgette
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

// Exit if accessed directly.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define globals for plugin version, debug mode and base file name.

if ( ! defined( 'BAZALGETTE_VERSION' ) ) {
	define( 'BAZALGETTE_VERSION', '1.0' );
}
if ( ! defined( 'BAZALGETTE_DEBUG' ) ) {
	define( 'BAZALGETTE_DEBUG', true );
}
if ( ! defined( 'BAZALGETTE_PLUGIN_BASE' ) ) {
	define( 'BAZALGETTE_PLUGIN_BASE', plugin_basename( __FILE__ ) );
}

// Include the shared functions.

require_once plugin_dir_path( __FILE__ ) . 'inc/shared.php';

require_once plugin_dir_path( __FILE__ ) . 'inc/setup.php';

require_once plugin_dir_path( __FILE__ ) . 'inc/clean.php';
