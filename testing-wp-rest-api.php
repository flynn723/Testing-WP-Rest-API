<?php
/*
Plugin Name: Testing WP REST API
Plugin URI: https://mydigitalsauce.com
Description: Cupcake Voting Plugin test example
Author: MyDigitalSauce
Author URI: https://mydigitalsauce.com
Version: 1.0
License: GPLv3 or later
*/

// website/wp-json/cupcake-voting/v1/votes

define ( "CUPCAKE_VOTING_VERSION", 1.3 );

// a flush rewrite rules func, you can also flush by resaving the permalinks structure
function cupcake_activate () {
	flush_rewrite_rules();
}
// when the plugin is activated the rewrite rules will be flushed and the endpoint will work
register_activation_hook( __FILE__, 'cupcake_activate' );

function cupcake_register_endpoints () {
	register_rest_route(
		'cupcake-voting/v1',
		'/votes/',
		array (
			'methods' => 'POST',
			'callback' => 'cupcake_add_vote',
			'args' => array (
				'id' => array (
					'required' => true,
					'validate_callback' => function ($param, $request, $key) {
						return is_numeric( $param) && ! is_null( get_post($param) );
					},
					'sanitize_callback' => 'absint'
				)
			),
			'permission_callback' => function() {
				return is_user_logged_in() && current_user_can( 'manage_options' );
			}
		)
	);
}
add_action ( 'rest_api_init', 'cupcake_register_endpoints' );

function cupcake_enqueue_scripts() {
	wp_enqueue_script(
		'cupcake-voting-js',
		plugins_url( 'js/cupcake-voting.js', __FILE__),
		array( 'jquery' ),
		CUPCAKE_VOTING_VERSION,
		true
	);
	wp_localize_script(
		'cupcake-voting-js',
		'cupcake_voting_data',
		array (
			'nonce' => wp_create_nonce( 'wp-rest' )
		)
	);
}
add_action( 'wp_enqueue_scripts', 'cupcake_enqueue_scripts' );

// when the endpoint is accessed & our cupcake_add_vote() callback func is called
// we are passed in a WP_REST_Request $request parameter
// contains details of the request
// func return the data
function cupcake_add_vote( WP_REST_Request $request ) {
	$votes = intval( get_post_meta( $request->get_param( 'id' ), 'votes', true ) );
	if ( false === (bool) update_post_meta( $request->get_param( 'id' ), 'votes', $votes + 1 ) ) {
		return new WP_Error( 'vote_error', __( 'Unable to add vote', 'cupcake-voting' ), $request->get_param( 'id' ) );		
	}
	return $votes + 1;

	/*
	return $request->get_params();
	*/
}