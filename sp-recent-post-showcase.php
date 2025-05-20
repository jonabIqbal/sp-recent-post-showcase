<?php
/**
 * Plugin Name:       Recent post Showcase
 * Description:       A block for displaying a grid of posts.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Iqbal Hossain
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sp-recent-post-showcase
 *
 * @package sp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'SP_RECENT_POST_SHOWCASE_URL', plugin_dir_url( __FILE__ ) );
define( 'SP_RECENT_POST_SHOWCASE_VERSION', '0.1.0' );
/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function sp_recent_post_showcase_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'sp_recent_post_showcase_init' );
