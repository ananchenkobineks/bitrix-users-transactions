<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Btx_Api {

	/**
	 * Primary URL for requests
	 *
	 */
    private static $primary_url = '';

    /**
	 * Required actions
	 * 
	 */
    private static $actions = [
    	'create_order'				=> 'crm.deal.add.json',
    	'create_product'			=> 'crm.product.add.json',
    	'bind_products_to_order'	=> 'crm.deal.productrows.set.json',
    	'create_user'				=> 'crm.contact.add.json',
    	'get_order'					=> 'crm.deal.get.json',
    	'get_order_products'		=> 'crm.deal.productrows.get.json',
    	'get_product'				=> 'crm.product.get.json',
    	'get_user'					=> 'crm.contact.get.json',
    	'update_order'				=> 'crm.deal.update.json',
    	'update_product'			=> 'crm.product.update.json',
    	'update_user'				=> 'crm.contact.update.json',
    ];

    /**
	 * Get the full link for the query
	 *
	 * @param string $action - The name of the action
	 * @return string
	 */
    public static function get_action_url( $action ) {
    	return self::$primary_url.self::$actions[ $action ];
    }

    /**
	 * Create Bitrix Order
	 *
	 * @param int $btx_user_id - Bitrix User ID for connection to the order
	 * @return int - Order ID
	 */
    public static function create_order( $btx_user_id, $user_info ) {
	    $query_data = [
	        'fields' => [
	            "TITLE"				=> "Заказ из сайта pickitup",
	            "ASSIGNED_BY_ID"	=> $btx_user_id,
	            "PHONE" 			=> [
	            	[
	            		"VALUE" => $user_info['phone'],
	            		"VALUE_TYPE" => "WORK",
	            	],
	            ],
	            'UF_CRM_59AE8A94525B3' => $user_info['city'],
	            'UF_CRM_59A42C22BECDE' => $user_info['address'],
	        ],
	    ];

	    return self::do_webhook( __FUNCTION__, $query_data );
    }

    /**
	 * Create Bitrix Product
	 *
	 * @param array $product - Product data
	 * @return array - Bitrix Product Data
	 */
    public static function create_product( $product ) {

        $btx_product_data = [
            "NAME"          => $product['product_name'],
            "PROPERTY_111"  => $product['product_code'],
            "CURRENCY_ID"   => "USD",
            "PRICE"         => $product['product_price'],
            "PROPERTY_112"  => $product['product_size'],
            "PROPERTY_113"  => $product['product_color'],
            "DESCRIPTION"   => $product['product_comment'],
            "PROPERTY_143"  => $product['product_qty'],
            "PROPERTY_110"  => $product['product_url'],
        ];

        $query_data = [
            'fields' => $btx_product_data,
        ];

        $btx_product_id = self::do_webhook( __FUNCTION__, $query_data );

        $btx_product = [
        	'PRODUCT_ID' 	=> $btx_product_id,
        	'PRICE'			=> $btx_product_data['PRICE'],
    		'QUANTITY' 		=> $btx_product_data['PROPERTY_143'],
        ];

	    return $btx_product;
    }

    /**
	 * Inserting btx Products into an btx Order
	 *
	 * @param int $btx_order_id - Btx Order ID
	 * @param array $btx_products - Btx Products Data
	 * @return bool
	 */
    public static function bind_products_to_order( $btx_order_id, $btx_products ) {

		$query_data = [
			"id"	=> $btx_order_id,
			"rows" 	=> $btx_products
		];

		return self::do_webhook( __FUNCTION__, $query_data );
    }

    /**
	 * Create Bitrix User
	 *
	 * @param array $user - User data
	 * @return int - Btx User ID
	 */
    public static function create_user( $user ) {

    	$query_data = [
			'fields' => [
				"NAME" => $user['first_name'],
	        	"LAST_NAME" => $user['last_name'],
	        	"EMAIL" => [
	        		[
	        			"VALUE" 		=> $user['email'],
	        			"VALUE_TYPE" 	=> "WORK",
	        		],
	        	],
	        	"PHONE" => [
	        		[
	        			"VALUE" => $user['phone'],
	        			"VALUE_TYPE" => "WORK"
	        		]
	        	]
			],
    	];

		return self::do_webhook( __FUNCTION__, $query_data );
    }

    public static function get_order( $btx_order_id ) {

    	$query_data = [
			"id"	=> $btx_order_id,
		];

		return self::do_webhook( __FUNCTION__, $query_data );
    }

    public static function get_order_products( $btx_order_id ) {

    	$query_data = [
			"id"	=> $btx_order_id,
		];

		return self::do_webhook( __FUNCTION__, $query_data );
    }

    public static function get_product( $btx_product_id ) {

    	$query_data = [
			"id"	=> $btx_product_id,
		];

		return self::do_webhook( __FUNCTION__, $query_data );
    }

    public static function get_user( $btx_user_id ) {

    	$query_data = [
			"id"	=> $btx_user_id,
		];

		return self::do_webhook( __FUNCTION__, $query_data );
    }

    public static function update_order( $btx_order_id, $field, $value ) {

    	if( $field == 'status' ) {
    		$row = [
    			"STAGE_ID" => $value
    		];
    	}

    	if( $field == 'transaction' ) {
    		$btx_order = self::get_order( $btx_order_id );
			$success_transactions = $btx_order['UF_CRM_1507192235'];
			$success_transactions[] = $value;

    		$row = [
    			"UF_CRM_1507192235" => $success_transactions
    		];
    	}

    	$query_data = [
    		'id' => $btx_order_id,
			'fields' => $row,
    	];

    	return self::do_webhook( __FUNCTION__, $query_data );
    }
    
    public static function update_product( $product ) {

    	$query_data = [
    		'id' => $product['id'],
			'fields' => [
				'PROPERTY_111'  => $product['code'],
				'PROPERTY_112'  => $product['size'],
            	'PROPERTY_113'  => $product['color'],
			],
            	
    	];

    	return self::do_webhook( __FUNCTION__, $query_data );
    }

    public static function update_user( $user_id, $field, $value, $act = true ) {

    	if( $field == 'balance' ) {

    		$user = self::get_user( $user_id );
    		$current_balance = $user['UF_CRM_1505307806'];

    		if( $act ) {
    			$current_balance += $value;
    		} else {
    			$current_balance -= $value;
    		}

    		$row = [
				'UF_CRM_1505307806' => $current_balance,
			];
    	}

    	$query_data = [
    		'id' => $user_id,
			'fields' => $row
    	];

    	return self::do_webhook( __FUNCTION__, $query_data );
    }


    /**
	 * Execute the API request
	 *
	 * @param string $action - key of Required actions array
	 * @param array $query_data - Data to submit
	 * @return mixed - result
	 */
    private static function do_webhook( $action, $query_data ) {
	    $curl = curl_init();

	    curl_setopt_array( $curl, array(
	        CURLOPT_SSL_VERIFYPEER => false,
	        CURLOPT_POST => true,
	        CURLOPT_HEADER => false,
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_URL => self::get_action_url( $action ),
	        CURLOPT_POSTFIELDS => http_build_query( $query_data )
	    ) );

	    $result = curl_exec($curl);
    	curl_close($curl);

    	$result = json_decode($result, true);
    	return $result['result'];
    }

}