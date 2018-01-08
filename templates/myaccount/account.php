<?php
/**
 * Account page
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="row">

	<div class="col-lg-3">
		<?php do_action( 'but_account_navigation' ); ?>
	</div>

	<div class="col-lg-9 account-content">
		<div class="account-border">

			<?php
				$btx_user_id = get_user_meta( get_current_user_id() , '_btx_user_id', true );
				$btx_user = Btx_Api::get_user( $btx_user_id );
				$balance = $btx_user['UF_CRM_1505307806'];
			?>

			<div class="user-balance">
				<strong><?php _e('Ваш баланс:'); ?> </strong><?php echo ( !empty($balance) ? $balance : 0 ); ?>	
			</div>

			<?php do_action( 'but_account_content' ); ?>
		</div>
	</div>

</div>