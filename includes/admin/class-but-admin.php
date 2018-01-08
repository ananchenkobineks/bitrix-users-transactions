<?php
/**
 * Admin Class
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUT_Admin {

	public static function init() {

		add_action( 'admin_menu', array( __CLASS__, 'admin_nav' ) );

		add_filter( 'set-screen-option', array( __CLASS__, 'save_screen_options' ), 10, 3 );

		add_action( 'admin_post_process_transaction', array( __CLASS__, 'process_transaction' ) );
	}

	public static function admin_nav() {
		$hook = add_submenu_page(
			'edit.php?post_type=bitrix_orders',
			'Список всех транзакций',
			'Все транзакции <span class="update-plugins"><span class="update-count">'.but_get_waiting_transaction_count().'</span></span>',
			'manage_options',
			'bitrix_transactions',
			array( __CLASS__, 'all_transaction_list' )
		);

		add_action( "load-$hook", array( __CLASS__, 'add_options' ) );
	}

	public static function add_options() {
		$option = 'per_page';
		$args = array(
			'label' => 'Количество транзакций на странице',
			'default' => 50,
			'option' => 'transactions_per_page'
		);
		add_screen_option( $option, $args );
	}

	public static function save_screen_options($status, $option, $value) {
  		return $value;
	}

	public static function all_transaction_list() {

		include( 'views/html-all-transaction-list.php' );
	}

	public static function process_transaction() {
		global $wpdb;

		$table_name = $wpdb->prefix . "btx_transactions";

		$data = $_REQUEST;

		if( wp_verify_nonce( $data['_wpnonce'], 'accept_'.$data['transaction_id'] ) ) {

			$amount = $wpdb->get_var( $wpdb->prepare("SELECT amount FROM $table_name WHERE id = '%d'", $data['transaction_id']) );

			Btx_Api::update_order( $data['btx_order_id'], 'transaction', $amount );

			$wpdb->update( $table_name,
				array( 'status' => '1' ),
				array( 'id' => $data['transaction_id'] )
			);

			$post_author_id = get_post_field( 'post_author', $data['wp_order_id'] );
			$btx_user_id = get_user_meta( $post_author_id, '_btx_user_id', true );

    		Btx_Api::update_user( $btx_user_id, 'balance', $amount, true );

		} elseif( wp_verify_nonce( $data['_wpnonce'], 'dismiss_'.$data['transaction_id'] ) ) {

			$wpdb->update( $table_name,
				array( 'status' => '2' ),
				array( 'id' => $data['transaction_id'] )
			);
		}

		wp_safe_redirect( wp_get_referer() );
		exit;
	}
}

BUT_Admin::init();

