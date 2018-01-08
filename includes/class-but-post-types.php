<?php
/**
 * Post Types
 *
 * Registers post types.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUT_Post_types {

	public static function init() {
		
		// Register Post Type
		add_action( 'init', array( __CLASS__, 'register_post_types' ) );
		// Disable unnecessary metaboxes
		add_action( 'admin_init', array( __CLASS__, 'disable_metabox' ) );
		// Register meta-boxes
		add_action('add_meta_boxes', array( __CLASS__, 'bitrix_orders_meta_boxes' ) );
		
		// Save transactions
		add_action('save_post', array( __CLASS__, 'save_bitrix_transaction' ) );

		/**
		 * Filtering post actions/data
		 */
		// Bulk actions
		add_filter( 'bulk_actions-edit-bitrix_orders', array( __CLASS__, 'register_bulk_actions' ) );
		// Row actions
		add_filter( 'post_row_actions', array( __CLASS__, 'action_row' ), 10, 2 );
		// SubSubSub menu
		add_filter( 'views_edit-bitrix_orders', array( __CLASS__, 'edit_submenu_links' ) );
		// Change Transaction added message
		add_filter( 'post_updated_messages', array( __CLASS__, 'save_post_message' ) );

		/**
		 * List Table changes
		 */
		// Edit Table columns
		add_filter( 'manage_edit-bitrix_orders_columns', array( __CLASS__, 'table_columns' ) );
		// View Table columns Data
		add_action( 'manage_bitrix_orders_posts_custom_column', array( __CLASS__, 'view_table_columns_data' ), 10, 2 );
	}

	public static function register_post_types() {

        $args = array(
            'labels'				=> array(
	            'name'                => __( 'Заказы', 'bitrix-ut' ),
	            'singular_name'       => _x( 'Заказ', 'bitrix_order post type singular name', 'bitrix-ut' ),
	            'menu_name'           => __( 'Bitrix Заказы', 'bitrix-ut' ),
	            'edit'                => __( 'Детали', 'bitrix-ut' ),
				'edit_item'           => __( 'Транзакции', 'bitrix-ut' ),
				'view'                => __( 'View order', 'bitrix-ut' ),
	            'view_item'           => __( 'View order', 'bitrix-ut' ),
	            'search_items'        => __( 'Поиск Заказа', 'bitrix-ut' ),
	            'not_found'           => __( 'Заказы не найдены', 'bitrix-ut' ),
	            'not_found_in_trash'  => __( 'Заказы не найдены в Корзине', 'bitrix-ut' ),
	        ),
	        'public'              	=> false,
	        'publicly_queryable'  	=> true,
            'rewrite'				=> false,
            'supports'            	=> array(''),
            'menu_position'       	=> 30,
            'menu_icon'           	=> 'dashicons-clipboard',
            'show_ui'             	=> true,
            'show_in_menu'        	=> true,
            'show_in_admin_bar'   	=> true,
            'show_in_nav_menus'   	=> false,
            'hierarchical'        	=> false,
            'description'         	=> 'Orders',
            'exclude_from_search' 	=> true,
            'has_archive'         	=> false,
            'query_var'           	=> false,
            
            'capabilities' => array(
				'create_posts' => 'do_not_allow'
			),
			'map_meta_cap' => true
        );

        register_post_type( 'bitrix_orders', $args );
	}

    public static function disable_metabox() {
        wp_deregister_script('postbox');
        remove_meta_box( 'submitdiv', 'bitrix_orders', 'side' );
    }

	public static function bitrix_orders_meta_boxes() {
		global $post;

		add_meta_box( 'btx_order_info_meta_box',
			__( 'Информация Bitrix закаказа', 'bitrix-ut' ),
			array(
				__CLASS__, 'show_bitrix_order_info_meta_box'
			),
			'bitrix_orders', 'side', 'high'
		);

		add_meta_box( 'transaction_form_meta_box',
			__( 'Добавить транзакцию', 'bitrix-ut' ),
			array(
				__CLASS__, 'show_bitrix_transaction_form_meta_box'
			),
			'bitrix_orders', 'normal', 'high'
		);

		add_meta_box( 'transaction_listing_meta_box',
			__( 'История транзакций', 'bitrix-ut' ),
			array(
				__CLASS__, 'show_bitrix_transaction_lising_meta_box'
			),
			'bitrix_orders', 'normal', 'high'
		);
	}
	
	public static function show_bitrix_order_info_meta_box() {
		global $post;

		$wp_order = $post;

		$user = get_user_by( 'ID', $wp_order->post_author );

		$btx_order_id = get_post_meta( $wp_order->ID, '_btx_order_id', true );
		$btx_order = Btx_Api::get_order( $btx_order_id );

		include( 'admin/views/html-order-info.php' );
	}

	public static function show_bitrix_transaction_form_meta_box() {

		include( 'admin/views/html-order-transaction-form.php' );
	}

	public static function show_bitrix_transaction_lising_meta_box() {
		global $post, $wpdb;

		$table_name = $wpdb->prefix . "btx_transactions";
		$transactions = $wpdb->get_results("SELECT * FROM $table_name WHERE order_id = '$post->ID' ORDER BY date DESC ");

		include( 'admin/views/html-order-transaction-listing.php' );
	}


	public static function save_bitrix_transaction( $post_id ) {
		global $post, $wpdb;

	    if ($post->post_type != 'bitrix_orders'){
	        return;
	    }

	    $transaction = $_POST['transaction'];

	    if( $transaction['type'] == 2 || $transaction['type'] == 3 ) {

	    	$post_author_id = get_post_field( 'post_author', $post->ID );
			$btx_user_id = get_user_meta( $post_author_id, '_btx_user_id', true );

	    	if( $transaction['status'] == 0 ) {

	    		Btx_Api::update_user( $btx_user_id, 'balance', $transaction['amount'], false );

	    	} elseif( $transaction['status'] == 1 ) {

	    		$btx_order_id = get_post_meta( $post->ID, '_btx_order_id', true );

	    		Btx_Api::update_order( $btx_order_id, 'transaction', $transaction['amount'] );
	    		Btx_Api::update_user( $btx_user_id, 'balance', $transaction['amount'], true );
	    	}
	    }

	    $wpdb->insert(
			$wpdb->prefix . 'btx_transactions',
			array( 
				'order_id' => $post->ID, 
				'status' => (!isset($transaction['status']) ? '' : $transaction['status']),
				'type' => $transaction['type'],
				'amount' => (!isset($transaction['amount']) ? '' : $transaction['amount'] ),
				'comment' => $transaction['comment'],
				'date' => date( 'Y-m-d H:i:s', current_time('timestamp') )
			),
			array(
				'%d', '%d', '%d', '%f', '%s', '%s'
			)
		);
	}


	public static function register_bulk_actions( $bulk_actions ) {
		unset( $bulk_actions['edit'] );
  		return $bulk_actions;
	}
	
	public static function action_row( $actions, $post ) {
	    
	    if ( $post->post_type == "bitrix_orders" ){
	        unset( $actions['inline hide-if-no-js'] );
	        unset( $actions['view'] );
	    }

	    return $actions;
	}

	public static function edit_submenu_links( $views ) {

		unset( $views['publish'] );
		return $views;
	}

	public static function save_post_message( $messages ) {

		$post = get_post();
		
		if( $post->post_type == 'bitrix_orders' ) {
			$messages['post'][1] = __( 'Транзакция успешно добавлена', 'bitrix-ut' );
		}

		return $messages;
	}

	public static function table_columns( $columns ) {

		unset( $columns['tcb_post_thumb'] );
		unset( $columns['date'] );

		$columns["title"] = "WordPress ID";
		$columns["btx_title"] = "Bitrix ID";
		$columns["btx_status"] = "Статус";
		$columns['date'] = 'Дата';

    	return $columns;
	}

	public static function view_table_columns_data( $colname, $post_id ) {

		if ( $colname == 'btx_title')
			echo get_post_meta( $post_id, '_btx_order_id', true );

		if ( $colname == 'btx_status') {
			$btx_order_id = get_post_meta( $post_id, '_btx_order_id', true );
			$btx_order = Btx_Api::get_order( $btx_order_id );
			echo but_get_order_status( $btx_order['STAGE_ID'] );
		}
	}

}

BUT_Post_types::init();