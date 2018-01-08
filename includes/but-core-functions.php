<?php
/**
 * BUT Core Functions
 *
 * General core functions available on both the front-end and admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get other templates passing attributes and including the file.
 *
 */
function but_get_template( $template_name, $args = array() ) {

	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$located = BUT_TEMPLATE . $template_name;

	if ( ! file_exists( $located ) ) {
		return;
	}

	include( $located );
}

/**
 * Get all query vars.
 *
 */
function but_get_query_vars() {

	return array(
		// My account actions.
		'orders' 			=> 'orders',
		'transactions' 		=> 'transactions',
		'edit-address' 		=> 'edit-address',
		'edit-account' 		=> 'edit-account',
		'lost-password' 	=> 'lost-password'
	);
}

/**
 * Get single query vars.
 *
 */
function but_get_page_link( $endpoint = '' ) {

	$account_link = but_get_account_page_link();
	if( $endpoint == '' ) {
		return $account_link;
	}

	foreach( but_get_query_vars() as $query_key => $link ) {

		if( $endpoint == $query_key ) {
			return $account_link.$link;
		}
	}

	return '';
}

/**
 * Get current active query var.
 *
 */
function but_get_current_endpoint() {
	global $wp;

	foreach ( but_get_query_vars() as $key => $value ) {
		if ( isset( $wp->query_vars[ $key ] ) ) {
			return $key;
		}
	}
	return '';
}

/**
 * Check if is account page.
 *
 */
function but_is_account() {
    
    $account_page_obj = but_get_account_page_object();

    if( $account_page_obj->ID == get_the_ID() ) {
        return true;
    }

    return false;
}

/**
 * Get account page object.
 *
 */
function but_get_account_page_object() {
    return get_page_by_path( 'account' );
}

/**
 * Get account page link.
 *
 */
function but_get_account_page_link() {
    return get_permalink( but_get_account_page_object() );
}