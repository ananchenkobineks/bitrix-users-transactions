<?php
/**
 * Action/filter hooks used for BUT templates.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * My Account.
 */
add_action( 'but_account_navigation', 'but_account_navigation' );
add_action( 'but_account_content', 'but_account_content' );
add_action( 'but_account_orders_endpoint', 'but_account_orders' );
add_action( 'but_account_transactions_endpoint', 'but_account_transactions' );
add_action( 'but_account_edit-address_endpoint', 'but_account_edit_address' );
add_action( 'but_account_edit-account_endpoint', 'but_account_edit_account' );