<?php
/**
 *  Message Functions
 *
 * Functions for error/message handling and display.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the count of notices added, either for all notices (default) or for one.
 * particular notice type specified by $notice_type.
 *
 * @param string $notice_type The name of the notice type - either error, success or notice. [optional]
 * @return int
 */
function but_notice_count( $notice_type = '' ) {
	$notice_count = 0;
	$all_notices  = isset($_SESSION['but_notices']) ? $_SESSION['but_notices'] : [];
	if (isset( $all_notices[$notice_type])) {
		$notice_count = absint( sizeof( $all_notices[$notice_type]));

	} elseif (empty($notice_type )) {
		foreach ( $all_notices as $notices ) {
			$notice_count += absint( sizeof($all_notices));
		}
	}

	return $notice_count;
}

/**
 * Add and store a notice.
 *
 * @param string $message The text to display in the notice.
 * @param string $notice_type The singular name of the notice type - either error, success or notice. [optional]
 */
function but_add_notice($message, $notice_type = 'success') {
  $notices =  isset($_SESSION['but_notices']) ? $_SESSION['but_notices'] : [];
  $notices[$notice_type][] =  $message;
  $_SESSION['but_notices'] =  $notices;
}

/**
 * Set all notices at once.
 *
 * @param mixed $notices
 */
function but_set_notices( $notices ) {
  $_SESSION['but_notices'] =  $notices;
}

/**
 * Unset all notices.
 *
 */
function but_clear_notices() {
  if(isset($_SESSION['but_notices'])) {
    unset($_SESSION['but_notices']);
  }
}

/**
 * Prints messages and errors which are stored in the session, then clears them.
 *
 */
function but_print_notices($return_template = FALSE) {
	$all_notices   = isset($_SESSION['but_notices']) ? $_SESSION['but_notices'] : [];
	$notice_types = apply_filters( 'but_notice_types', array( 'error', 'success', 'notice' ));
	foreach ( $notice_types as $notice_type ) {
		if ( but_notice_count( $notice_type ) > 0 ) {
			$messages = $all_notices[$notice_type];

			if($return_template) {
				ob_start();
			}
			include(BUT_TEMPLATE . "notices/{$notice_type}.php");

		}
	}

	but_clear_notices();
  	if($return_template){
		return ob_get_clean();
	}
}

/**
 * Print a single notice immediately.
 *
 * @param string $message The text to display in the notice.
 * @param string $notice_type The singular name of the notice type - either error, success or notice. [optional]
 */
function but_print_notice( $message, $notice_type = 'success' ) {
  $messages = [$message];
  include(BUT_TEMPLATE . "notices/{$notice_type}.php");
}

/**
 * Returns all queued notices, optionally filtered by a notice type.
 *
 * @param string $notice_type The singular name of the notice type - either error, success or notice. [optional]
 * @return array|mixed
 */
function but_get_notices( $notice_type = '' ) {
  $all_notices = isset($_SESSION['but_notices']) ? $_SESSION['but_notices'] : [];

	if ( empty( $notice_type ) ) {
		$notices = $all_notices;
	} elseif ( isset( $all_notices[ $notice_type ] ) ) {
		$notices = $all_notices[ $notice_type ];
	} else {
		$notices = array();
	}

	return $notices;
}

/**
 * Add notices for WP Errors.
 * @param  WP_Error $errors
 */
function but_add_wp_error_notices( $errors ) {
	if ( is_wp_error( $errors ) && $errors->get_error_messages() ) {
		foreach ( $errors->get_error_messages() as $error ) {
			but_add_notice( $error, 'error' );
		}
	}
}
