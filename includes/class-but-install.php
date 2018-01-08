<?php
/**
 * Installation related functions and actions.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUT_Install {

	public static function install() {

		if ( ! is_blog_installed() ) {
			return;
		}
		
		self::create_tables();
		
		flush_rewrite_rules();
	}

	private static function create_tables() {
		global $wpdb;

		$table_name = $wpdb->prefix . "btx_transactions";

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			order_id int(11) NOT NULL,
			status varchar(100) DEFAULT '' NOT NULL,
			type int(11) NOT NULL,
			amount decimal(10,2) NOT NULL,
			comment text DEFAULT '' NOT NULL,
			date datetime NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}