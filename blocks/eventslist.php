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
function eventpost_list_block_init() {
	global $EventPost;
	$dir = dirname( __FILE__ );

	wp_register_script(
		'eventpost-blocks',
		plugins_url( 'index.js', __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
		)
	);
	wp_enqueue_script('eventpost-blocks');
	// Get translations
    $locale  = gutenberg_get_jed_locale_data( 'event-post' );
	wp_add_inline_script('eventpost-blocks', 'wp.i18n.setLocaleData(' . json_encode( $locale ) . ', "event-post");');


	$block_js = 'eventslist/build/index.js';
	wp_register_script(
		'eventslist-block-editor',
		plugins_url( $block_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		filemtime( "$dir/$block_js" )
	);

	$editor_css = 'eventslist/build/style.css';
	wp_register_style(
		'eventslist-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(
			'wp-blocks',
		),
		filemtime( "$dir/$editor_css" )
	);

	register_block_type( 'eventpost/list', array(
		'editor_script' => 'eventslist-block-editor',
		'editor_style'  => 'eventslist-block-editor',
		'render_callback' => array($EventPost->Shortcodes, 'shortcode_list'),
	) );
}
add_action( 'init', 'eventpost_list_block_init' );


function _eventpost_gutengerg_i18n_nouse(){
	__('Filters', 'event-post');
	__('Display', 'event-post');
	__('Display upcoming events', 'event-post');
	__('Post title', 'event-post');
	__('Events Map Preview', 'event-post');__('Geolocalized events', 'event-post');
	__('Default date (YYYY-m)', 'event-post');
	__('No list', 'event-post');
	__('Disable Interactions:');
	__('Wrap title, if not empty', 'event-post');
}
