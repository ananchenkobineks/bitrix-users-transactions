<?php
/**
* Edit Account Transaction List
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
?>

<div class="transaction-lising">

    <?php if( !empty($transactions) ): ?>
        <?php foreach( $transactions as $info ): ?>
            <ul class="<?php echo get_transaction_type_class( $info->type ).' '. get_transaction_status_class( $info->status ); ?>">
                <li class="date">
                	<?php echo date( 'd-m-Y H:i',  strtotime( $info->date ) ); ?>
                	<a href="<?php echo but_get_page_link('orders').'/'.$info->order_id; ?>">

                		<?php echo __( 'Заказ № ', 'bitrix-ut' ).get_post_meta( $info->order_id, '_btx_order_id', true ); ?>

                	</a>
                </li>
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