<?php
/**
 * Functions for the transactions.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}


function but_get_waiting_transaction_count() {
	global $wpdb;

	$table_name = $wpdb->prefix . "btx_transactions";
	$waiting_transaction_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE (type = '2' OR type = '3') AND status = '0'");

	return $waiting_transaction_count;
}

function but_transaction_nonce_url( $nonce_name, $order_id ) {

	$url = wp_nonce_url( but_get_account_page_link(), $nonce_name.'_'.$order_id );

	$query = build_query( array(
		'order_id' => $order_id,
		'order_action' => true,
	) );

	return $url.'&'.$query;
}

function but_transaction_verify_nonce( $nonce, $nonce_name, $order_id ) {

	if ( wp_verify_nonce( $nonce, $nonce_name.'_'.$order_id ) ) {
		return true;
	} else {
		return false;
	}
}

function but_get_user_waiting_transaction_count() {
	global $wpdb;

	$args = [
	    'author'            => get_current_user_id(),
	    'post_type'         => 'bitrix_orders',
	    'posts_per_page'    => -1
	];

	$the_query = new WP_Query( $args );

	foreach( $the_query->posts as $post ) {
		$id_list .= $post->ID.',';
	}
	$id_list = rtrim( $id_list, ',' );

	$table_name = $wpdb->prefix . "btx_transactions";
	$waiting_transaction_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE order_id IN ( $id_list ) AND (type = '0' OR type = '1') AND status = '0'");

	return $waiting_transaction_count;
}

function but_get_user_transactions() {
	global $wpdb;

	$args = [
	    'author'            => get_current_user_id(),
	    'post_type'         => 'bitrix_orders',
	    'posts_per_page'    => -1
	];

	$the_query = new WP_Query( $args );

	foreach( $the_query->posts as $post ) {
		$id_list .= $post->ID.',';
	}
	$id_list = rtrim( $id_list, ',' );

	$table_name = $wpdb->prefix . "btx_transactions";
	$transactions = $wpdb->get_results("SELECT * FROM $table_name WHERE order_id IN ( $id_list ) ORDER BY id DESC");

	return $transactions;
}

function but_update_seen_transactions() {
	global $wpdb;

	$args = [
	    'author'            => get_current_user_id(),
	    'post_type'         => 'bitrix_orders',
	    'posts_per_page'    => -1
	];

	$the_query = new WP_Query( $args );

	foreach( $the_query->posts as $post ) {
		$id_list .= $post->ID.',';
	}
	$id_list = rtrim( $id_list, ',' );

	$table_name = $wpdb->prefix . "btx_transactions";
	$wpdb->query(
		$wpdb->prepare(
			"UPDATE $table_name SET status = %d WHERE order_id IN ( $id_list ) AND (type = %d OR type = %d) AND status = %d",
        	1, 0, 1, 0
		)
	);

}