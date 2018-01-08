<?php
/**
 * Show error messages
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!$messages) {
	return;
}

?>
<div class="alert alert-danger">
	<ul>
		<?php foreach ($messages as $message) : ?>
			<li><?php echo wp_kses_post($message); ?></li>
		<?php endforeach; ?>
	</ul>
</div>