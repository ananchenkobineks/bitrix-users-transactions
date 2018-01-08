<?php
/**
 * Order Info
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="btx-order-info">

	<ul>
		<li>
			<strong><?php _e('Сделка №:', 'bitrix-ut'); ?> </strong>
			<span><?php echo $btx_order['ID']; ?></span>
		</li>
		<li>
			<strong><?php _e('Статус:', 'bitrix-ut'); ?> </strong>
			<span><?php echo but_get_order_status( $btx_order['STAGE_ID'] ); ?></span>
		</li>
		<li>
			<strong><?php _e('Пользователь:', 'bitrix-ut'); ?> </strong><br>
			<a href="<?php echo get_edit_user_link( $user->ID ); ?>" target="_blank"><?php echo $user->display_name; ?></a>
		</li>
	</ul>

</div>