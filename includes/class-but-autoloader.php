<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BUT_Autoloader {

	/**
	 * Path to the includes directory.
	 *
	 * @var string
	 */
	private $include_path = '';

	/**
	 * The Constructor.
	 */
	public function __construct() {
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->include_path = untrailingslashit( plugin_dir_path( BUT_PLUGIN_FILE ) ) . '/includes/';
	}

	/**
	 * Take a class name and turn it into a file name.
	 *
	 * @param  string $class
	 * @return string
	 */
	private function get_file_name_from_class( $class ) {
		return 'class-' . str_replace( '_', '-', $class ) . '.php';
	}

	/**
	 * Include a class file.
	 *
	 * @param  string $path
	 * @return bool successful or not
	 */
	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			include_once( $path );
			return true;
		}
		return false;
	}

	public function autoload( $class ) {

		$class = strtolower( $class );

		if ( 0 !== strpos( $class, 'but_' ) ) {
			return;
		}

		$file  = $this->get_file_name_from_class( $class );
		$path  = '';

		if ( 0 === strpos( $class, 'but_shortcode_' )  ) {
			$path = $this->include_path . 'shortcodes/';
		}

		$this->load_file( $path . $file );
	}
}

new BUT_Autoloader();
