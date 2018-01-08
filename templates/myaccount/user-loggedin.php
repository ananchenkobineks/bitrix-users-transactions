<?php
/**
 * Account user logged buttons
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="logged-actions">
	<a href="<?php echo but_get_account_page_link(); ?>" class="btn"><?php _e('Учетная запись', 'bitrix-ut'); ?></a>
	<a href="<?php echo wp_logout_url( get_permalink( get_page_by_path( 'order' ) ) ); ?>" class="btn"><?php _e('Выйти', 'bitrix-ut'); ?></a>	
</div>