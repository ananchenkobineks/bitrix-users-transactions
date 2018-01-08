<?php
/**
 * All Transaction List
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
		
$transaction_list_table = new BUT_Transaction_List_Table();
$transaction_list_table->prepare_items();

?>
<div class="wrap">
	
	<h2><?php _e('Список всех транзакций', 'bitrix-ut'); ?></h2>

	<form id="all-transaction-list" method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $transaction_list_table->display() ?>
	</form>

</div>