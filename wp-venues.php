<?php

/*
Plugin Name: WP Venues
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: Guillermo
Author URI: http://guillermogf.com
License: A "Slug" license name e.g. GPL2
*/

error_reporting(E_ALL);

//include the functions for calling google places
require_once( dirname( __FILE__ ) . "/includes/google-places-api.php" );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function Places_add_meta_box() {

	$screens = array( 'post', 'page' );

	foreach ( $screens as $screen ) {

		add_meta_box(
			'Places_sectionid',
			__( 'Establecimiento', 'Places_textdomain' ),
			'Places_meta_box_callback',
			$screen, 'normal', 'high'
		);
	}
}
add_action( 'add_meta_boxes', 'Places_add_meta_box' );

/**
 * Prints the box content.
 */
function places_meta_box_callback() {

	wp_register_script(
		'places-autocomplete',
		plugins_url( '/js/admin.js', __FILE__ ),
		['jquery', 'jquery-ui-autocomplete'],
		'20120208',
		true
	);
	wp_enqueue_script( 'places-autocomplete' );

	wp_register_style(
		'places',
		plugins_url( '/css/places.css', __FILE__ ),
		[],
		'20120208',
		'all'
	);
	wp_enqueue_style( 'places' );

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'places_save_meta_box_data', 'places_meta_box_nonce' );

	wp_localize_script( 'places-autocomplete', 'PlacesAutocomplete', array('url' => admin_url( 'admin-ajax.php' )));

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */

	/*echo '<label for="Places_new_field">';
	_e( 'Nombre', 'Places_textdomain' );
	echo ':</label></br> ';*/
	//echo '<input type="text" id="Places_new_field" name="Places_new_field" placeholder="Escribe el nombre" value="' . esc_attr( $value ) . '" size="25" style="width:100%;" />';

	include_once (dirname( __FILE__ ) . '/html/search-place-post.php');

	/**if (!NULL==get_post_meta($post->ID, '_Places_meta_Google_response', true)) {
	$googleResponse = GooglePlacesApi::placeDetails(get_post_meta($post->ID, '_Places_meta_Google_response', true));
	//$googleResponse=get_post_meta($post->ID, '_WP_Places_meta_Google_response', true);
	echo "<h4>Here's the place WP_Places thinks you're talking about:</h4>";
	echo "<h5>".$googleResponse['name']."<BR>";
	echo $googleResponse['formattedAddress']."</h5>";
	}*/
}

/**
 * @return string
 */
function places_place_id() {
	echo places_get_place_id();
}

/**
 * @return string
 */
function places_get_place_id() {
	return get_post_meta( get_the_ID(), '_places_meta_place_id', true );
}

/**
 * @return string
 */
function places_place_name() {
	echo places_get_place_name();
}

/**
 * @return string
 */
function places_get_place_name() {
	return get_post_meta( get_the_ID(), '_places_meta_place_name', true );
}

/**
 * @return bool
 */
function places_has_place_saved() {
	return (places_get_place_id() != '');
}

function places_get_detail_place_saved() {
	return GooglePlacesApi::placeDetails( places_get_place_id() );
}


/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function places_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */
//var_dump($_POST); die($_POST['places-search-term']);
	// Check if our nonce is set.
	if ( ! isset( $_POST['places_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['places_meta_box_nonce'], 'places_save_meta_box_data' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	/* OK, it's safe for us to save the data now. */

	// Make sure that it is set.
	if ( ! isset( $_POST['places-search-id'] ) ) {
		return;
	}

	// Sanitize user input.
	$place_id = sanitize_text_field( $_POST['places-search-id'] );
	// Update the meta field in the database.
	update_post_meta( $post_id, '_places_meta_place_id', $place_id );

	$place_name = sanitize_text_field( $_POST['places-search-term'] );
	update_post_meta( $post_id, '_places_meta_place_name', $place_name );

	//Check with the Google and grab the meta
	//_WP_Places_meta_places_id, _WP_Places_meta_hours, _WP_Places_meta_reviews, _WP_Places_meta_closed, _WP_Places_meta_lat, _WP_Places_meta_lon,
	$result = GooglePlacesApi::placeDetails( $place_id );
	//print_r($result);
	update_post_meta( $post_id, '_places_meta_Google_response', $result);



}
add_action( 'save_post', 'places_save_meta_box_data' );

function places_autocomplete_search() {
	$term = strtolower( $_GET['term'] );

	$response = GooglePlacesApi::search($term);

	$suggestions = [];
	if (isset($response['predictions']) && count($response['predictions']) > 0) {
		foreach ($response['predictions'] as $place) {
			$suggestion = [];
			$suggestion['label'] = $place['description'];
			$suggestion['link'] = $place['place_id'];

			$suggestions[] = $suggestion;

		}
	} elseif (isset($response['error'])) {
	    responseError($response['error']);
    }

	responseJson($suggestions);
}

add_action( 'wp_ajax_places_autocomplete_search', 'places_autocomplete_search' );
add_action( 'wp_ajax_nopriv_places_autocomplete_search', 'places_autocomplete_search' );

//places_detail
function places_detail() {
	$response = GooglePlacesApi::placeDetails( $_GET['placeId'] );

	responseJson($response);
}

add_action( 'wp_ajax_places_detail', 'places_detail' );
add_action( 'wp_ajax_nopriv_places_detail', 'places_detail' );

function responseJson($response) {
	header('Content-Type: application/json');
	echo  json_encode($response);
	exit();
}

function responseError($response) {
    header($_SERVER['SERVER_PROTOCOL'] . ' ' . $response, true, 500);
    exit();
}

function places_get_url_photo($photoData) {

	$maxWidth = ($photoData['width'] < 800) ? $photoData['width'] : 800;

	return GooglePlacesApi::urlPhoto($photoData['photo_reference'], $maxWidth);
}
