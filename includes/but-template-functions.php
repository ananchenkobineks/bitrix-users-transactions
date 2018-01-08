<?php
/**
 * Functions for the templating system.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! function_exists( 'but_account_navigation' ) ) {

	/**
	 * Account navigation template.
	 */
	function but_account_navigation() {
		but_get_template( 'myaccount/navigation.php', array(
			'account_link'	=> but_get_account_page_link()
		) );
	}
}


if ( ! function_exists( 'but_account_content' ) ) {

	function but_account_content() {
		global $wp;

		if ( ! empty( $wp->query_vars ) ) {
			foreach ( $wp->query_vars as $key => $value ) {
				// Ignore pagename param.
				if ( 'pagename' === $key ) {
					continue;
				}

				if ( has_action( 'but_account_' . $key . '_endpoint' ) ) {
					do_action( 'but_account_' . $key . '_endpoint', $value );
					return;
				}
			}
		}

		// No endpoint found? Default to dashboard.
		but_get_template('myaccount/dashboard.php', array(
			'current_user' => get_user_by( 'id', get_current_user_id() ),
		) );

	}

}


if ( ! function_exists( 'but_account_orders' ) ) {

    function but_account_orders( $order_id ) {

        if( $order_id ) {
        	global $wpdb;

        	$order_id = intval($order_id);
        	
            $order = get_post($order_id);

            $table_name = $wpdb->prefix . "btx_transactions";
			$transactions = $wpdb->get_results("SELECT * FROM $table_name WHERE order_id = '$order_id' ORDER BY date DESC ");

            but_get_template('myaccount/single-order.php', array(
                'wp_order' => $order,
                'order_id' => $order_id,
                'transactions' => $transactions
            ) );
        } else {

            but_get_template('myaccount/account-orders.php');
        }
        
    }

}

if ( ! function_exists( 'but_account_transactions' ) ) {

    function but_account_transactions() {

    	but_update_seen_transactions();    	

    	but_get_template('myaccount/account-transactions.php', array(
            'transactions' => but_get_user_transactions()
        ) );
    }

}


if ( ! function_exists( 'but_account_edit_address' ) ) {

	function but_account_edit_address() {
		
		$user_address = get_user_meta( get_current_user_id(), 'address' );

		but_get_template('myaccount/account-address.php', array(
			"user_addresses" => $user_address
		) );
	}

}


if ( ! function_exists( 'but_account_edit_account' ) ) {

	function but_account_edit_account() {
        $current_user = get_user_by( 'id', get_current_user_id());
		but_get_template('myaccount/account-edit.php', compact('current_user'));
	}

}