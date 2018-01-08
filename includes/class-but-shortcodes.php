<?php
/**
 * Shortcodes
 *
 * @category Class
 * @package  BitrixUsersTransactions/Classes
 */

if ( ! defined( 'ABSPATH' ) ) { 
	exit;
}

/**
 * BitrixUsersTransactions Shortcodes class.
 */
class BUT_Shortcodes {
	
	/**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'but_is_account'	=> __CLASS__ . '::is_account',
			'but_account'		=> __CLASS__ . '::account'
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( $shortcode, $function );
		}
	}

	/**
	 * Login form shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function is_account( $atts ) {
		return self::shortcode_wrapper( array( 'BUT_Shortcode_Is_Account', 'output' ), $atts );
	}

	/**
	 * Login form shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function account( $atts ) {
		return self::shortcode_wrapper( array( 'BUT_Shortcode_Account', 'output' ), $atts );
	}

	/**
	 * Shortcode Wrapper.
	 *
	 * @param string[] $function Callback function.
	 * @param array    $atts     Attributes. Default to empty array.
	 * @param array    $wrapper  Customer wrapper data.
	 *
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts = array(),
		$wrapper = array(
			'class'  => 'but-is-logged-container',
			'before' => null,
			'after'  => null,
		)
	) {
		ob_start();

		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		return ob_get_clean();
	}

}