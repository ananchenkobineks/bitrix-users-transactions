<?php

/**
 * Contains the query functions for BUT which alter the front-end post queries and loops
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BUT_Query {

	/**
	 * Query vars to add to wp.
	 *
	 * @var array
	 */
	private $query_vars = array();

	/**
	 * Constructor for the query class. Hooks in methods.
	 */
	public function __construct() {
		// Actions used to insert a new endpoint in the WordPress.
		add_action( 'init', array( $this, 'add_endpoints' ) );

		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
			add_action( 'parse_request', array( $this, 'parse_request' ), 0 );
		}

		add_filter( 'the_title', array( $this, 'endpoint_title' ) );
		
		$this->init_query_vars();
	}

	/**
	 * Init query vars by loading options.
	 */
	public function init_query_vars() {
		// Query vars to add to WP.
		$this->query_vars = but_get_query_vars();
	}

	/**
	 * Add endpoints for query vars.
	 */
	public function add_endpoints() {

		foreach ( $this->query_vars as $key => $var ) {
			if ( ! empty( $var ) ) {
				add_rewrite_endpoint( $var, EP_ROOT | EP_PAGES );
			}
		}
	}

	/**
	 * Add query vars.
	 *
	 * @access public
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {

		foreach ( $this->query_vars as $key => $var ) {
			$vars[] = $key;
		}

		return $vars;
	}

	/**
	 * Parse the request and look for query vars - endpoints may not be supported.
	 */
	public function parse_request() {
		global $wp;

		// Map query vars to their keys, or get them if endpoints are not supported
		foreach ( $this->query_vars as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) {
				$wp->query_vars[ $key ] = $_GET[ $var ];
			} elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}

	/**
	 * Set endpoint title.
	 *
	 * @param string $title
	 * @return string
	 */
	public function endpoint_title( $title ) {
		global $wp_query, $wp;
		
		if ( ! is_null( $wp_query ) && ! is_admin() && is_main_query() 
			&& in_the_loop() && is_page() && $this->is_but_endpoint_url() ) {

			$endpoint = but_get_current_endpoint();

			if ( $endpoint_title = $this->get_endpoint_title( $endpoint ) ) {
				$title = $endpoint_title;
			}

			remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
		}

		return $title;
	}

	/**
	 * Get page title for an endpoint.
	 * @param  string
	 * @return string
	 */
	public function get_endpoint_title( $endpoint ) {
		global $wp;

		switch ( $endpoint ) {
			case 'orders' :
				$title = __( 'Заказы' );
				break;
			case 'edit-address' :
				$title = __( 'Адреса' );
				break;
			case 'edit-account' :
				$title = __( 'Учетная запись' );
				break;
			default :
				$title = '';
				break;
		}

		return $title;
	}

	public function is_but_endpoint_url() {
        global $wp;

        $but_endpoints = $this->query_vars;

    	foreach ( $this->query_vars as $key => $value ) {
			if ( isset( $wp->query_vars[ $key ] ) ) {
				return true;
			}
		}

        return false;
    }
    
}

new BUT_Query();