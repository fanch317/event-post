<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package event-post
 */

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type/#enqueuing-block-scripts
 */
function eventscalendar_block_init() {
	global $EventPost;
	$dir = dirname( __FILE__ );

	$block_js = 'eventscalendar/build/index.js';
	wp_register_script(
		'eventscalendar-block-editor',
		plugins_url( $block_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		filemtime( "$dir/$block_js" )
	);

	$editor_css = 'eventscalendar/build/style.css';
	wp_register_style(
		'eventscalendar-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(
			'wp-blocks',
		),
		filemtime( "$dir/$editor_css" )
	);

	register_block_type( 'eventpost/calendar', array(
		'editor_script' => 'eventscalendar-block-editor',
		'editor_style'  => 'eventscalendar-block-editor',
		'render_callback' => array($EventPost->Shortcodes, 'shortcode_cal'),
	) );
}
add_action( 'init', 'eventscalendar_block_init' );
