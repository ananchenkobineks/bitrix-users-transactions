<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUT_Scripts {

	/**
	 * Hook in methods.
	 */
	public static function init() {

        add_action( 'wp_enqueue_scripts',     array( __CLASS__, 'front_load_scripts' ) );
		add_action( 'admin_enqueue_scripts',  array( __CLASS__, 'admin_load_scripts' ) );
	}

	/**
	 * Return asset URL.
	 *
	 */
	private static function get_asset_url( $path ) {
		return plugins_url( $path, BUT_PLUGIN_FILE );
	}

	/**
	 * Register all BUT front styles.
	 */
	private static function front_register_styles() {
		$register_styles = array(
			'but-account' => array(
				'src'     => self::get_asset_url( 'assets/css/but-account.css' )
			),
			'but-front' => array(
				'src'     => self::get_asset_url( 'assets/css/but-front.css' )
			)
		);
		foreach ( $register_styles as $handle => $props ) {
			wp_register_style( $handle, $props['src'], array(), false, 'all' );
		}
	}

	/**
	 * Register all BUT front scripts.
	 */
	private static function front_register_scripts() {
		$register_scripts = array(
			'but-account' => array(
				'src'     => self::get_asset_url( 'assets/js/but-account.js' ),
				'deps'    => array( 'jquery' )
			),
			'but-front' => array(
				'src'     => self::get_asset_url( 'assets/js/but-front.js' ),
				'deps'    => array( 'jquery' )
			)
		);
		foreach ( $register_scripts as $handle => $props ) {
			wp_register_script( $handle, $props['src'], $props['deps'], false, true );
		}
	}

	/**
	 * Register/queue frontend scripts.
	 */
	public static function front_load_scripts() {
		
		self::front_register_styles();
		self::front_register_scripts();

		// Frontend scripts
		if( !is_admin() && but_is_account() ) {

			wp_enqueue_style( 'dashicons' );
            wp_enqueue_style( 'but-account' );
			wp_enqueue_script( 'but-account' );

		} else if( !is_admin() && !but_is_account() ) {

			wp_enqueue_style( 'but-front' );
			
			if( is_user_logged_in() ) {
				wp_enqueue_script( 'but-front' );
				wp_localize_script( 'but-front', 'user', array(
					'user_address' => get_user_meta( get_current_user_id(), 'address' )
				) );	
			}
			
		}

	}

        /**
     * Register all BUT admin styles.
     */
    private static function admin_register_styles() {
        $register_styles = array(
            'but-admin' => array(
                'src'     => self::get_asset_url( 'assets/css/but-admin.css' )
            )
        );
        foreach ( $register_styles as $handle => $props ) {
            wp_register_style( $handle, $props['src'], array(), false, 'all' );
        }
    }

    /**
     * Register all BUT admin scripts.
     */
    private static function admin_register_scripts() {
        $register_scripts = array(
            'but-admin' => array(
                'src'     => self::get_asset_url( 'assets/js/but-admin.js' ),
                'deps'    => array( 'jquery' )
            )
        );
        foreach ( $register_scripts as $handle => $props ) {
            wp_register_script( $handle, $props['src'], $props['deps'], false, true );
        }
    }

    /**
     * Register/queue admin scripts.
     */
    public static function admin_load_scripts( $hook ) {
        
        self::admin_register_styles();
        self::admin_register_scripts();

        if( get_current_screen()->post_type == 'bitrix_orders' ) {
            
            wp_enqueue_style( 'but-admin' );
            wp_enqueue_script( 'but-admin' );
        }

    }

}

BUT_Scripts::init();
