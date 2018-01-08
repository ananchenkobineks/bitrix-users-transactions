<?php
/**
 * Transaction Form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="transaction-form">

	<p>
		<label for="transaction-type"><?php _e('Назначение:', 'bitrix-ut'); ?></label><br>
		<select name="transaction[type]" id="transaction-type">

			<?php foreach( get_transaction_types() as $key => $type_name ): ?>
				<option value="<?php echo $key; ?>"><?php echo $type_name; ?></option>
			<?php endforeach; ?>

		</select>
	</p>

	<p>
		<label for="transaction-price"><?php _e('Сумма:', 'bitrix-ut'); ?></label><br>
		<input type="number" id="transaction-price" name="transaction[amount]" value="0.00" min="0" step="0.01">
	</p>

	<p>
		<label for="transaction-status"><?php _e('Статус:', 'bitrix-ut'); ?></label><br>
		<select name="transaction[status]" id="transaction-status" disabled>

		<?php foreach( get_transaction_statuses() as $key => $status_name ): ?>
			<option value="<?php echo $key; ?>"><?php echo $status_name; ?></option>
		<?php endforeach; ?>

		</select>
	</p>

	<p>
		<label for="transaction-comment"><?php _e('Комментарий:', 'bitrix-ut'); ?></label><br>
		<textarea rows="5" id="transaction-comment" name="transaction[comment]"></textarea>
	</p>

	<p class="transaction-submit">
		<input name="save" type="submit" class="button button-primary button-large" value="Добавить">
	</p>
</div>