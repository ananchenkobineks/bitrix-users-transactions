<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUT_Form_Handler {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'wp_loaded', array( __CLASS__, 'process_login' ), 10 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_change_password' ), 10 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_add_transaction' ), 10 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_user_order_action' ), 10 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_user_address_action' ), 10 );
	}

	/**
	 * Process the login form.
	 */
	public static function process_login() {
		$nonce_value = isset( $_POST['but-login-nonce'] ) ? $_POST['but-login-nonce'] : '';

		if ( ! empty( $_POST['login'] ) && wp_verify_nonce( $nonce_value, 'but-login' ) ) {

			try {
				$creds = array(
					'user_password' => $_POST['password'],
					'remember'      => isset( $_POST['rememberme'] ),
				);

				$user_login = trim( $_POST['user_login'] );

				if ( is_email( $user_login ) ) {
					$user = get_user_by( 'email', $user_login );

					if ( isset( $user->user_login ) ) {
						$creds['user_login'] = $user->user_login;
					}
				} else {
					$creds['user_login'] = $user_login;
				}

				// Perform the login
				$user = wp_signon( $creds );

				if ( is_wp_error( $user ) ) {
          			
          			but_add_notice('Пользователь с данным email не зарегестрирован.', 'error');

				} else {

					if ( ! empty( $_POST['redirect'] ) ) {
						$redirect = $_POST['redirect'];
					} else {
						$redirect = but_get_account_page_link();
					}

					wp_redirect( $redirect );
					exit;
				}
				
			} catch ( Exception $e ) {
       			 but_add_notice('Неверный email или пароль. Проверьте данные и попробуйте еще раз', 'error' );
				echo $e->getMessage();
			}
		}
	}

	public static function process_change_password() {

		if(isset( $_POST['action'] ) && 'save_account_details' == $_POST['action']) {
			$current_user = get_user_by( 'id', get_current_user_id() );

			if (!wp_check_password($_POST['password_current'], $current_user->data->user_pass, $current_user->ID)) {
				but_add_notice('Текущий пароль заполнен неверно', 'error');
			}

			if (empty($_POST['password_1']) && empty($_POST['password_2'])) {
				but_add_notice('Заполните все поля', 'error');
			}

			if (strlen($_POST['password_1']) < 8) {
				but_add_notice('Пароль должен быть минимум 8 знаков', 'error');
			}

			if ($_POST['password_1'] != $_POST['password_2']) {
				but_add_notice('Введенные пароли не совпадают', 'error');
			}

			if(but_notice_count('error') == 0) {
				wp_set_password($_POST['password_1'], $current_user->id);
				wp_set_auth_cookie($current_user->ID);
				but_add_notice('Пароль успешно изменен', 'success');
			}
		}
	}

	public static function process_add_transaction() {
		global $wpdb;

		if( $_POST['save_transaction'] && wp_verify_nonce( $_POST['transaction_form'], 'save_transaction' ) ) {

			$order = explode("/", $_POST['_wp_http_referer']);
	        $order_id = !empty($order[3]) ? intval($order[3]) : false;

	        if( $order_id ) {

	        	$transaction = $_POST['transaction'];

	        	if( $transaction['type'] == 2 || $transaction['type'] == 3 ) {
        			$wpdb->insert(
						$wpdb->prefix . 'btx_transactions',
						array( 
							'order_id' => $order_id, 
							'status' => 0,
							'type' => $transaction['type'],
							'amount' => $transaction['amount'],
							'comment' => $transaction['comment'],
							'date' => date( 'Y-m-d H:i:s', current_time('timestamp') )
						),
						array(
							'%d', '%d', '%d', '%f', '%s', '%s'
						)
					);
	        	}
	        	
				wp_redirect( $_POST['_wp_http_referer'] );
				exit;
	        }
		}

	}

	public static function process_user_order_action() {

		if( $_REQUEST['order_action'] ) {

			$redirect = false;
			$data = $_REQUEST;
			
			if( but_transaction_verify_nonce( $data['_wpnonce'], 'confirm', $data['order_id'] ) ) {

				$redirect = true;
				$btx_order_id = get_post_meta( $data['order_id'], '_btx_order_id', true );
				Btx_Api::update_order( $btx_order_id, 'status', '1' );

				but_add_notice( __('Заказ подтвержден.') );
				
			} elseif( but_transaction_verify_nonce( $data['_wpnonce'], 'reject', $data['order_id'] ) ) {

				$redirect = true;
				$btx_order_id = get_post_meta( $data['order_id'], '_btx_order_id', true );
				Btx_Api::update_order( $btx_order_id, 'status', 'LOSE' );

				but_add_notice( __('Заказ отменен.') );

			} elseif( but_transaction_verify_nonce( $data['_wpnonce'], 'edit', $data['order_id'] ) ) {

				$redirect = true;

				$btx_order_id = get_post_meta( $data['order_id'], '_btx_order_id', true );

				foreach( $data['product'] as $btx_product ) {

					Btx_Api::update_product( $btx_product );
				}

				Btx_Api::update_order( $btx_order_id, 'status', 'NEW' );
				but_add_notice( __('Заказ отправлен на обработку менеджером.') );
			}
			
			if( $redirect ) {
				if ( wp_get_referer() ) {
				    wp_safe_redirect( wp_get_referer() );
				} else {
				    wp_safe_redirect( get_home_url() );
				}
				exit;
			}
			
		}
	}

	public static function process_user_address_action() {

		if( $_POST['add-user-address'] ) {

			if( !empty($_POST['address']) ) {
				add_user_meta( get_current_user_id(), 'address', $_POST['address'] );
				but_add_notice( __('Адрес успешно добавлен.') );
			}

		} elseif( $_POST['edit-user-address'] ) {

			$user_id = get_current_user_id();
			$addresses = array_values( array_filter($_POST['address']) );

			delete_user_meta( $user_id, 'address' );
			foreach( $addresses as $address ) {
				add_user_meta( $user_id, 'address', $address );
			}

			but_add_notice( __('Адреса успешно отредактированы.') );
		}
	}

}

BUT_Form_Handler::init();