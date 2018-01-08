<?php
/**
 * Orderinfo page
 */

if (!defined('ABSPATH')) {
  exit;
}
?>

<?php if( !empty($wp_order) && $wp_order->post_type == 'bitrix_orders' 
    && $wp_order->post_author == get_current_user_id() ): ?>

    <h2><?php _e('Список транзакций для заказа ', 'bitrix-ut'); echo $wp_order->post_title; ?></h2>

    <div class="transactions-wrap">
        <form id="add-transaction-form" action="" method="post">
            <h2><?php _e( 'Добавить новую:', 'bitrix-ut' ); ?></h2>
            
            <p class="form-row form-row-wide">
                <label for="transaction_goal"><?php _e( 'Назначение *', 'bitrix-ut' ); ?></label>
                <select name="transaction[type]" id="transaction_goal" required>
                    <option value="2"><?php _e( 'Предоплата за заказ', 'bitrix-ut' ); ?></option>
                    <option value="3"><?php _e( 'Доплата за комиссию и вес', 'bitrix-ut' ); ?></option>
                </select>
            </p>
            <p class="form-row form-row-wide">
                <label for="transaction_sum"><?php _e( 'Сумма *', 'bitrix-ut' ); ?></label>
                <input type="number" class="input-text" name="transaction[amount]" id="transaction_sum" value="0.00" min="0" step="0.01" required/>
            </p>
            <p class="form-row form-row-wide">
                <label for="transaction_comment"><?php _e( 'Комментарий', 'bitrix-ut' ); ?></label>
                <textarea rows="4" name="transaction[comment]"></textarea>
            </p>

            <button class="btn btn-default" id="create"><?php _e( 'Создать', 'bitrix-ut' ); ?></button>

            <input type="hidden" name="save_transaction" value="1">
            <?php wp_nonce_field( 'save_transaction', 'transaction_form' ); ?>
        </form>

        <div class="transaction-lising">

            <?php if( !empty($transactions) ): ?>
                <?php foreach( $transactions as $info ): ?>
                    <ul class="<?php echo get_transaction_type_class( $info->type ).' '. get_transaction_status_class( $info->status ); ?>">
                        <li class="date"><?php echo date( 'd-m-Y H:i',  strtotime( $info->date ) ); ?></li>
                        <li class="assignment">

                            <?php if( $info->type != 4): ?>
                                <span><?php echo get_transaction_type_name( $info->type ); ?></span>
                            <?php endif; ?>

                            <?php if( !empty($info->comment) ): ?>
                                <div class="comment">
                                    <strong><?php _e('Комментарий:', 'bitrix-ut'); ?></strong>
                                    <?php echo $info->comment; ?>
                                </div>  
                            <?php endif; ?>
                        </li>

                        <?php if( $info->type != 4): ?>
                            <li class="amount"><?php echo $info->amount; ?></li>
                        <?php endif; ?>

                        <?php if( $info->type != 4 && $info->type > 1 ): ?>
                            <li class="status"><?php echo get_transaction_status_name( $info->status ); ?></li>
                        <?php endif; ?>             

                    </ul>
                <?php endforeach; ?>

            <?php else: ?>

                <p><?php _e('Список транзакций пуст.', 'bitrix-ut'); ?></p>

            <?php endif; ?>

        </div>
    </div>

<?php else: ?>
    <p><?php but_print_notice( __('Извините, такого заказа не существует.'), 'error' ); ?></p>
<?php endif; ?>