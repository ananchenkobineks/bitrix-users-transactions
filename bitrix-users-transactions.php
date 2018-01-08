<?php
/**
 * Plugin Name: Bitrix Users Transactions
 * Description: Custom orders and transactions for interaction with Bitrix
 * Version: 1.0
 * Author: Jack Ananchenko
 *
 * Text Domain: bitrix-ut
 * Domain Path: /i18n/languages/
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists('Bitrix_Users_Transactions') ) :

final class Bitrix_Users_Transactions {

    protected static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    private function define_constants() {

        $this->define( 'BUT_PLUGIN_FILE', __FILE__ );
        $this->define( 'BUT_ABSPATH', dirname( __FILE__ ) . '/' );
        $this->define( 'BUT_TEMPLATE', dirname( __FILE__ ) . '/templates/' );
        $this->define( 'BUT_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
    }

    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined( 'DOING_AJAX' );
            case 'cron' :
                return defined( 'DOING_CRON' );
            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

     private function init_hooks() {
        register_activation_hook( BUT_PLUGIN_FILE, array( 'BUT_Install', 'install' ) );

        add_action( 'init', array( $this, 'init' ) );
        add_action( 'init', array( 'BUT_Shortcodes', 'init' ) );

        add_action( 'after_setup_theme', array( $this, 'remove_admin_bar' ) );
        add_action( 'admin_init', array( $this, 'redirect_admin' ) );
    }

    private function includes() {

        // Custom Bitrix API library
        include_once( BUT_ABSPATH . 'lib/class-bitrix-api.php' );

        /**
         * Class autoloader.
         */
        include_once( BUT_ABSPATH . 'includes/class-but-autoloader.php' );

        /**
         * Core classes.
         */
        include_once( BUT_ABSPATH . 'includes/class-but-install.php' );

        include_once( BUT_ABSPATH . 'includes/but-core-functions.php' );
        include_once( BUT_ABSPATH . 'includes/but-order-functions.php' );
        include_once( BUT_ABSPATH . 'includes/but-transaction-functions.php' );

        include_once( BUT_ABSPATH . 'includes/class-but-query.php' );
        include_once( BUT_ABSPATH . 'includes/class-but-post-types.php' );

        // Form Handler.
        include_once( BUT_ABSPATH . 'includes/class-but-form-handler.php' );
        // Shortcodes class.
        include_once( BUT_ABSPATH . 'includes/class-but-shortcodes.php' );
         // Scripts.
        include_once( BUT_ABSPATH . 'includes/class-but-scripts.php' );
        // Notices
        include_once( BUT_ABSPATH . 'includes/but-notice-functions.php' );
        // Make orders
        include_once( BUT_ABSPATH . 'includes/class-but-make-order.php' );

        /**
         * Admin
         */
        include_once( BUT_ABSPATH . 'includes/admin/class-but-transaction-list-table.php' );
        include_once( BUT_ABSPATH . 'includes/admin/class-but-admin.php' );

        if ( $this->is_request( 'frontend' ) ) {

            include_once( BUT_ABSPATH . 'includes/but-template-hooks.php' );
            include_once( BUT_ABSPATH . 'includes/but-template-functions.php' );
        }
    }

    public function init() {
        if(session_id() == '' || !isset($_SESSION)) {
            session_start();
        }

        add_filter( 'page_template', array( $this, 'but_account_modify_page_template' ), 1000 );
        
    }

    public function but_account_modify_page_template($template) {
        if( !is_front_page() && but_is_account() ) {
            $template =  BUT_TEMPLATE . "myaccount/page.php";
        }
        return $template;
    }

    public function remove_admin_bar() {
        if (!current_user_can('administrator') && !is_admin()) {
            show_admin_bar(false);
        }
    }

    public function redirect_admin() {
        if ( ! $this->is_request( 'ajax' ) && ! current_user_can('edit_posts') ) {
            wp_redirect( but_get_account_page_link() );
            exit;      
        }
    }

}

endif;

Bitrix_Users_Transactions::instance();