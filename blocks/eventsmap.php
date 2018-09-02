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
function eventsmap_block_init() {
	global $EventPost;
	$dir = dirname( __FILE__ );

	$block_js = 'eventsmap/build/index.js';
	wp_register_script(
		'eventsmap-block-editor',
		plugins_url( $block_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		filemtime( "$dir/$block_js" )
	);

	$editor_css = 'eventsmap/build/style.css';
	wp_register_style(
		'eventsmap-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(
			'wp-blocks',
		),
		filemtime( "$dir/$editor_css" )
	);

	register_block_type( 'eventpost/map', array(
		'editor_script' => 'eventsmap-block-editor',
		'editor_style'  => 'eventsmap-block-editor',
		'render_callback' => array($EventPost->Shortcodes, 'shortcode_map'),
	) );

	wp_localize_script('eventsmap-block-editor', 'eventpost_gut_params', array(
		'maptiles' => $EventPost->maps,
		'map_interactions'=>$EventPost->map_interactions,
	));
}
add_action( 'init', 'eventsmap_block_init' );
