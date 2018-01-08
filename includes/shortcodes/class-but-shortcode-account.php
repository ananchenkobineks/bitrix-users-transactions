<?php

class BUT_Shortcode_Account {

	public static function output( $atts ) {
		
		if ( is_user_logged_in() ) {
			load_template( BUT_TEMPLATE . 'myaccount/account.php', false );
		} else {
			load_template( BUT_TEMPLATE . 'myaccount/form-login.php', false );
		}
		
	}
	
}