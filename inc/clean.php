<?php
/**
 * Clean the menu
 *
 * Process the menu and sub-menu items and clean.
 *
 * @package bazalgette
 */

// Exit if accessed directly.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Process the menus
 *
 * Loops through the menu and sub-menu global arrays and processes them for cleaning.
 */
function bazalgette_process_menus() {

	bazalgette_log( 'Starting Log' );

	// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
	global $menu, $submenu;

	// Make a copy of the menu arrays - these are the ones that we'll read.
	$menu_copy       = $menu;
	$submenu_copy    = $submenu;
	$core_menu_slugs = bazalgette_core_menu( 'slugs' );

	// Read through each main menu option.
	foreach ( $menu_copy as $menu_array_key => $menu_record ) {

		// Make sure the menu being processed isn't one of the core ones.
		if ( ! array_key_exists( $menu_record[2], $core_menu_slugs ) ) {

			bazalgette_log( 'Processing menu "' . $menu_record[0] . '"', 1 );

			// Check if a menu title exists - this rules out menu spacing.
			if ( $menu_record[0] ) {

				// Clean the menu title.
				$menu[ $menu_array_key ][0] = bazalgette_clean_title( ( $menu_record[0] ) );

				// Remove any menu classes other than a default.
				$menu[ $menu_array_key ][4] = 'menu-top';
				$menu[ $menu_array_key ][5] = '';

				// Look for a non-standard menu icon and change to a default,if required.
				if ( ( 'none' === $menu_record[6] ) || ( 'dashicons-' !== substr( $menu_record[6], 0, 10 ) && 'data:image/svg' !== substr( $menu_record[6], 0, 14 ) ) ) {
					$menu[ $menu_array_key ][6] = 'dashicons-admin-plugins';
					bazalgette_log( 'Menu icon changed', 1 );
				}

				// Check if a sub-menu exists for the current menu. If so, process that.
				if ( isset( $submenu_copy[ $menu_record[2] ] ) ) {

					$menus   = 0;
					$removed = false;

					// Process each sub-menu record for the current record.
					foreach ( $submenu_copy[ $menu_record[2] ] as $submenu_array_key => $submenu_record ) {

						bazalgette_log( 'Processing sub-menu "' . $submenu_record[0] . '"', 2 );

						++$menus;

						// Check if a submenu title exists - this rules out menu spacing.
						if ( $submenu_record[0] ) {

							// Clean the sub-menu title.
							$title = bazalgette_clean_title( $submenu_record[0] );
							$submenu[ $menu_record[2] ][ $submenu_array_key ][0] = $title;

							// Remove any menu classes.
							$submenu[ $menu_record[2] ][ $submenu_array_key ][4] = '';

							$kept = bazalgette_evaluate_submenus( $menu_record, $submenu_record, $title );
							if ( ! $kept ) {
								--$menus;
								$remove = true;
							}
						} else {

							bazalgette_log( 'Removed blank sub-menu', 3 );

							// Remove any blank sub-menus.
							remove_submenu_page( $menu_record[2], $submenu_record[2] );
							--$menus;
							$remove = true;
						}
					}

					// Remove any menus that are now empty.
					if ( 0 === $menus & $removed ) {
						bazalgette_log( 'Menu is now empty, so removing it!', 2 );
					}
				}
			} else {

				bazalgette_log( 'Removed empty menu', 2 );

				// Remove any empty menu names OPTIONAL!
				remove_menu_page( $menu_record[2] );
			}
		}
	}

	bazalgette_log( 'Log complete' );
}

add_action( 'admin_head', 'bazalgette_process_menus', 9999 );

/**
 * Clean menu title
 *
 * Cleans up the HTML in a menu or sub-menu title.
 *
 * @param    string $title  The title to be cleaned.
 * @return   string         The cleaned title.
 */
function bazalgette_clean_title( $title ) {

	$number = '';

	// Regular expression to match numbers inside <span> elements.
	preg_match_all( '/<span[^>]*>(\d+)<\/span>/', $title, $matches );
	if ( isset( $matches[1][0] ) ) {
		$number = $matches[1][0];
	}

	// Regular expression to match numbers inside <span> elements, removing the number but keeping the span tags.
	$title = preg_replace( '/(<span[^>]*>)(\d+)(<\/span>)/', '$1$3', $title );

	// Convert any HTML entities for space back to an actual space.
	$title = str_replace( '&nbsp;', ' ', $title );

	// Strip the title of all HTML tags and decode any special characters.
	$title = html_entity_decode( wp_strip_all_tags( $title ) );

	// If there was a counter, re-add it.
	if ( isset( $number ) && $number > 0 ) {
		$title .= '<span class="update-plugins count-' . $number . '">' . $number . '</span>';
	}

	// If "NEW" or "NEW!" is on the end of the title, remove it.
	if ( ' new' === strtolower( substr( rtrim( $title ), -4, 4 ) ) ) {
		$title = substr( $title, 0, strlen( $title ) - 4 );
	}
	if ( ' new!' === strtolower( substr( rtrim( $title ), -5, 5 ) ) ) {
		$title = substr( $title, 0, strlen( $title ) - 5 );
	}

	return trim( $title );
}

/**
 * Evaluate Sub-menus
 *
 * Looks at a sub-menu and decide if it can be deleted or removed.
 *
 * @param array  $menu_record     Array of menu being processed.
 * @param array  $submenu_record  Array of sub-menu being processed.
 * @param string $title           Cleaned sub-menu title.
 */
function bazalgette_evaluate_submenus( $menu_record, $submenu_record, $title ) {

	$kept    = true;   // Whether the sub-menu was kept.
	$todo    = false;  // Any kind of action that needs to be performed on the sub-menu.
	$matches = 0;      // The number of matches found.

	global $wp_filter;

	bazalgette_log( 'Looking for text within the sub-menu named "' . strtolower( $title ) . '"', 3 );

	// Grab the array of actions to look for and perform on the sub-menus.
	$actions = bazalgette_submenu_actions();

	// Loop through the above array.
	foreach ( $actions as $action_text => $action ) {

		// Look for the text in passed sub-menu title.
		$found = bazalgette_word_search( strtolower( $action_text ), strtolower( $title ) );
		if ( $found ) {
			bazalgette_log( 'A search term match was found - "' . $action_text . '"', 3 );
			$todo = $action;
			++$matches;
		}
	}

	if ( $matches > 1 ) {
		bazalgette_log( 'More than one match occurred', 3, 'w' );
	}

	// If any kind of action needs to take place, then do it here.
	if ( false !== $todo ) {

		// If the text has been found and it's a removal then, well, remove it!
		if ( 'r' === strtolower( $todo[0] ) ) {
			bazalgette_log( 'Removing sub-menu with slug of "' . $submenu_record[2] . '"', 3 );
			$removed = remove_submenu_page( $menu_record[2], $submenu_record[2] );
			if ( ! $removed ) {
				bazalgette_log( 'Sub-menu removal did not work', 3, 'e' );
			} else {
				bazalgette_log( 'Sub-menu removed', 3 );
			}
			$kept = false;
		}

		// If the text has been found and it needs moving then, you guessed it - move it!
		if ( 'm' === strtolower( $todo[0] ) ) {

			bazalgette_log( 'Moving sub-menu with slug of "' . $submenu_record[2] . '"', 3 ); // Add details of where it's going from/to

			// We need to work out the callback for any menu moves.
			$callback = bazalgette_get_callback( $menu_record[2], $submenu_record[2] );

			// Add the menu into a new location.
			//$added = add_submenu_page();
			if ( ! $added ) {
				bazalgette_log( 'Adding the sub-menu to the new location did not work', 3, 'e' );
			} else {
				bazalgette_log( 'The sub-menu has been added to the new location', 3 );
			}

			// Now we remove the original menu.
			//$removed = remove_submenu_page( $menu_record[2], $submenu_record[2] );
			if ( ! $removed ) {
				bazalgette_log( 'Sub-menu removal did not work', 3, 'e' );
			} else {
				bazalgette_log( 'Sub-menu removed', 3 );
			}
		}
	}

	return $kept;
}

/**
 * Word Search
 *
 * Perform various ways of searching for a specific word in the sub-menu title.
 *
 * @param string $search  Word(s) to be looked for.
 * @param string $title   Sub-menu title.
 * @return boolean        Whether the word was found.
 */
function bazalgette_word_search( $search, $title ) {

	$found      = false;
	$search_len = strlen( $search );

	// If an underscore at the beginning, look for the search term at the end of the title.
	if ( '_' === substr( $search, 0, 1 ) ) {
		$search = substr( $search, 1 );
		if ( substr( $title, ( $search_len - 1 ) * -1, $search_len ) === $search ) {
			$found = true;
		}
		return $found;
	}

	// If an underscore at the end, look for the search term at the beginning of the title.
	if ( '_' === substr( $search, -1, 1 ) ) {
		$search = substr( $search, 0, $search_len - 1 );
		if ( substr( $title, 0, $search_len - 1 ) === $search ) {
			$found = true;
		}
		return $found;
	}

	// Perform a 4-way search for possibilities to find the text within the menu name.
	if ( $title === $search ) {
		$found = true;
	} elseif ( substr( $title, 0, $search_len + 1 ) === $search . ' ' ) {
		$found = true;
	} elseif ( substr( $title, ( ( $search_len + 1 ) * -1 ), $search_len + 1 ) === ' ' . $search ) {
		$found = true;
	} elseif ( false !== stripos( $title, ' ' . $search . ' ' ) ) {
		$found = true;
	}
	return $found;
}


/**
 * Get the callback.
 *
 * Reverse engineer the callback from the menu slugs.
 *
 * @param  string $menu_slug    The slug of the menu.
 * @param  string $submenu_slug The slug of the sub-menu.
 * @return string               The name of the callback.
 */
function bazalgette_get_callback( $menu_slug, $submenu_slug ) {

	return $submenu_slug;

	global $wp_filter;
	$callback = false;

	// Get the hook name.
	$hookname = get_plugin_page_hookname( $submenu_slug, $menu_slug );

	if ( isset( $wp_filter[ $hookname ] ) && is_a( $wp_filter[ $hookname ], 'WP_Hook' ) ) {

		// Convert the object to an array.
		$filters_array = $wp_filter[ $hookname ]->callbacks;

		// Grab the correct key which holds the call back name, which is the key value.
		$callback = key( $filters_array[10] );
	} else {
		bazalgette_log( 'The hookname of "' . $hookname . '" does not exist in $wp_filter', 3, 'e' );
	}

	return $callback;
}

/**
 * Log
 *
 * Add a message to the auditing log.
 *
 * @param string $text  Output text.
 * @param string $level The embedded level of output.
 * @param string $type  The type of message being repored. i/w/e = information/warning/error.
 */
function bazalgette_log( $text, $level = 1, $type = 'i' ) {

	// Format the message type.
	$type = strtolower( $type );
	if ( 'i' === $type ) {
		$type = 'info';
	} elseif ( 'w' === $type ) {
		$type = 'warning';
	} elseif ( 'e' === $type ) {
		$type = 'error';
	}

	// If a level is specified, embed it appropriately.
	if ( 1 < $level ) {
		$text = str_repeat( "\t", $level - 1 ) . $text;
	}

	// Add the output to Query Monitor if debug mode is on.
	if ( BAZALGETTE_DEBUG ) {
		// phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
		do_action( 'qm/' . $type, $text );
	}
}
