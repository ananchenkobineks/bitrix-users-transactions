<?php

class BUT_Shortcode_Is_Account {

	public static function output( $atts ) {

		if ( is_user_logged_in() ) {
			
			load_template( BUT_TEMPLATE . 'myaccount/user-loggedin.php', false );

		} else {

			load_template( BUT_TEMPLATE . 'myaccount/is-account.php', false );
		}
		
	}
	
}