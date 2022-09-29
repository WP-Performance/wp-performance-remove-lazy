<?php

namespace WPPerfomance\RemoveLazy;

/**
 * Plugin Name:       Remove Lazy
 * Description:       Remove lazy loading on img with class nolazy
 * Update URI:        wpperformance-remove-lazy
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Version:           0.0.1
 * Author:            Faramaz Patrick <infos@goodmotion.fr>
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpperformance-remove-lazy
 *
 * @package           wp-performance
 */
require_once(dirname(__FILE__) . '/inc/parser.php');

if (!is_admin()) {
    add_action('loop_start', 'WPPerfomance\inc\parser\parsing_start');
}
