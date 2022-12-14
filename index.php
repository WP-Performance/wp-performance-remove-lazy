<?php

namespace WPPerfomance\RemoveLazy;

/**
 * Plugin Name:       Remove Lazy
 * Description:       Remove lazy loading on img with class nolazy
 * Update URI:        wp-performance-remove-lazy
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Version:           0.0.1
 * Author:            Faramaz Patrick <infos@goodmotion.fr>
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-performance-remove-lazy
 *
 * @package           wp-performance
 */
require_once(dirname(__FILE__) . '/inc/parser.php');

// only front
if (!is_admin()) {
    // test with hook send_headers
    add_action('send_headers', 'WPPerformance\RemoveLazy\inc\parser\parsing_start');
}
