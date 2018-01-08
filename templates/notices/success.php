<?php
/**
 * Show success messages
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $messages ) {
	return;
}

?>

<?php foreach ( $messages as $message ) : ?>
	<div class="alert alert-success"><?php echo wp_kses_post( $message ); ?></div>
<?php endforeach; ?>
