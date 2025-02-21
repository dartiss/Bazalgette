<?php
/**
 * Setup
 *
 * Various set-up functions.
 *
 * @package bazalgette
 */

// Exit if accessed directly.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core menu array
 *
 * Build an array of either core menu slugs or names.
 *
 * @param  string $version Which version of the array to return.
 * @return array           Core menu array.
 */
function bazalgette_core_menu( $version ) {

	if ( 'slugs' === $version ) {
		$core_menus = array(
			'index.php'               => __( 'Dashboard', 'bazalgette' ),
			'edit.php'                => __( 'Posts', 'bazalgette' ),
			'upload.php'              => __( 'Media', 'bazalgette' ),
			'edit.php?post_type=page' => __( 'Pages', 'bazalgette' ),
			'edit-comments.php'       => __( 'Comments', 'bazalgette' ),
			'themes.php'              => __( 'Appearance', 'bazalgette' ),
			'plugins.php'             => __( 'Plugins', 'bazalgette' ),
			'users.php'               => __( 'Users', 'bazalgette' ),
			'tools.php'               => __( 'Tools', 'bazalgette' ),
			'options-general.php'     => __( 'Settings', 'bazalgette' ),
		);
	} else {
		$core_menus = array(
			__( 'Dashboard', 'bazalgette' )  => 'index.php',
			__( 'Posts', 'bazalgette' )      => 'edit.php',
			__( 'Media', 'bazalgette' )      => 'upload.php',
			__( 'Pages', 'bazalgette' )      => 'edit.php?post_type=page',
			__( 'Comments', 'bazalgette' )   => 'edit-comments.php',
			__( 'Appearance', 'bazalgette' ) => 'themes.php',
			__( 'Plugins', 'bazalgette' )    => 'plugins.php',
			__( 'Users', 'bazalgette' )      => 'users.php',
			__( 'Tools', 'bazalgette' )      => 'tools.php',
			__( 'Settings', 'bazalgette' )   => 'options-general.php',
		);
	}

	return $core_menus;
}

/**
 * Submenu actions array
 *
 * Build an array of words to search for in sub-menus and which actions to take with it.
 *
 * @return array  Submenu actions array.
 */
function bazalgette_submenu_actions() {

	// ADD NOTE FOR TRANSLATORS.

	$actions = array(
		__( 'About_', 'bazalgette' )       => array( 'r', '' ),
		__( 'Add-on', 'bazalgette' )       => array( 'r', '' ),
		__( 'Add-ons', 'bazalgette' )      => array( 'r', '' ),
		__( 'Addon', 'bazalgette' )        => array( 'r', '' ),
		__( 'Addons', 'bazalgette' )       => array( 'r', '' ),
		__( 'Affiliat_', 'bazalgette' )    => array( 'r', '' ),
		__( 'Dashboard', 'bazalgette' )    => array( 'm', 'dashboard.php' ),
		__( 'Extend', 'bazalgette' )       => array( 'r', '' ),
		__( 'Integrations', 'bazalgette' ) => array( 'r', '' ),
		__( 'Other', 'bazalgette' )        => array( 'r', '' ),
		__( 'Premium_', 'bazalgette' )     => array( 'r', '' ),
		__( 'Pro', 'bazalgette' )          => array( 'r', '' ),
		__( '_Trial', 'bazalgette' )       => array( 'r', '' ),
		__( 'Upgrade_', 'bazalgette' )     => array( 'r', '' ),
		__( 'Welcome', 'bazalgette' )      => array( 'r', '' ),
	);

	return $actions;
}
