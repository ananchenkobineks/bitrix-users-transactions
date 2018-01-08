<?php
/**
 * Create/save order and user.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUT_Make_Order {

	public static function init() {

		// Сheck if email exists in Wordpress Users
		add_filter( 'wpcf7_validate_email*', array( __CLASS__, 'validate_user_email' ), 10, 2 );
		// Create order in WP and Bitrix before submit form
		add_action( 'wpcf7_before_send_mail', array( __CLASS__, 'wpcf7_get_data_form_before_submit' ) );
	}

	public static function validate_user_email( $result, $tag ) {

	    if ( !is_user_logged_in() ) {

	    	$input_email = trim( $_POST['email'] );

	        if( email_exists( $input_email ) ) {
	            $result->invalidate( $tag, __('Этот email уже используется! Пожалуйста войдите в вашу учетную запись.') );
	        }	

	    }

        return $result;
	}

	public static function wpcf7_get_data_form_before_submit( $contact_form ) {

        $data = $_POST;

        $user_info = [
            'first_name'    => $data['lname'],
            'last_name'     => $data['fname'],
            'email'         => $data['email'],
            'phone'         => $data['phone'],
            'city'          => $data['city'],
            'address'       => $data['address'],
        ];

        $user_id = get_current_user_id();

        if( !$user_id ) {

            $userdata = [
                'user_login'    =>  $user_info['email'],
                'user_email'    =>  $user_info['email'],
                'first_name'    =>  $user_info['first_name'],
                'last_name'     =>  $user_info['last_name'],
                'user_pass'     =>  wp_generate_password()
            ];

            $user_id = wp_insert_user( $userdata );
            $btx_user_id = Btx_Api::create_user( $user_info );
            add_user_meta( $user_id, '_btx_user_id', $btx_user_id, true );
            add_user_meta( $user_id, 'phone', $user_info['phone'], true );
            add_user_meta( $user_id, 'address', $user_info['address'], true );

            self::wp_new_user_notification( $user_info['email'], $userdata['user_pass'] );
        } else {

            $btx_user_id = get_user_meta( $user_id, '_btx_user_id', true );
        }

        $btx_products_data = [];
        $item_num = '';

        for( $i=1; $i<=10; $i++ ) {

            if ($i < 10) {
                $item_num = str_pad($i, 2, "0", STR_PAD_LEFT);
            } else {
                $item_num = $i;
            }

            $products_data = array(
                'product_url'           => $data['url'.$item_num],
                'product_name'          => ( !empty($data['title'.$item_num]) ? $data['title'.$item_num] : __('Без Имени', 'bitrix-ut') ),
                'product_code'          => $data['code'.$item_num],
                'product_size'          => $data['size'.$item_num],
                'product_color'         => $data['color'.$item_num],
                'product_price'         => number_format( str_replace( ',', '.', $data['price'.$item_num] ), 2, '.', '' ),
                'product_qty'           => intval( $data['count'.$item_num] ),
                'product_comment'       => $data['comment'.$item_num],
            );

            $btx_products_data[] = Btx_Api::create_product( $products_data );

            if( !isset($data['checkbox'.$item_num]) ) {
                break;
            }
        }

        $btx_order_id = Btx_Api::create_order( $btx_user_id = 1, $user_info );
        $btx_products_to_order = Btx_Api::bind_products_to_order( $btx_order_id, $btx_products_data );
        
        $new_order_params = [
            'post_title'    => '',
            'post_status'   => 'publish',
            'post_type'     => 'bitrix_orders',
            'post_author'   => $user_id,
            'meta_input'        => [
                '_btx_order_id' => $btx_order_id,
            ]
        ];
        
        $wp_order_id = wp_insert_post( $new_order_params );

        $update_post = [
            'ID'           => $wp_order_id,
            'post_title'   => "№ $wp_order_id",
        ];
        wp_update_post( $update_post );
	}

    private static function wp_new_user_notification( $user_email, $plaintext_pass ) {

        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        if ( empty($plaintext_pass) )
            return;

        $message  = sprintf(__('Имя пользователя: %s'), $user_email) . "\r\n\r\n";
        $message .= sprintf(__('Пароль: %s'), $plaintext_pass) . "\r\n\r\n";
        $message .= but_get_account_page_link() . "\r\n";

        $headers = "From: Pick It UP <info@pickitup.com.ua>" . "\r\n";

        wp_mail($user_email, sprintf(__('[%s] Ваше имя пользователя и пароль'), $blogname), $message, $headers);
    }

}

BUT_Make_Order::init();
