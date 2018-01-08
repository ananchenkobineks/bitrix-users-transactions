<?php
/**
 * Single Order page
 *
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="single-order">
    <div class="row">

    <?php but_print_notices(); ?>

    <?php if( !empty($wp_order) && $wp_order->post_type == 'bitrix_orders' 
    && $wp_order->post_author == get_current_user_id() ): ?>

        <?php
            $user = wp_get_current_user();
            $btx_order_id = get_post_meta( $order_id, '_btx_order_id', true );
            $btx_order = Btx_Api::get_order( $btx_order_id );
            $btx_order_products = Btx_Api::get_order_products( $btx_order_id );
            $btx_order_date = new DateTime( $btx_order['DATE_CREATE'] );
        ?>
        
        <div class="col-xs-12">
            <h2 class="order-title"><?php _e('Заказ', 'bitrix-ut'); echo " № ".$btx_order_id; ?></h2>    
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12 order-info">
            <h4><?php _e('Данные заказа:', 'bitrix-ut'); ?></h4>
            <p>
                <strong><?php _e('Статус:', 'bitrix-ut'); ?></strong><br>
                <span><?php echo but_get_order_status( $btx_order['STAGE_ID'] ); ?></span>
            </p>
            <p>
                <strong><?php _e('Создан:', 'bitrix-ut'); ?></strong><br>
                <span><?php echo $btx_order_date->format('d-m-Y'); ?></span>
            </p>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12 billing-info">
            <h4><?php _e('Данные клиента:', 'bitrix-ut'); ?></h4>
            <p>
                <strong><?php _e('Имя и Фамилия:', 'bitrix-ut'); ?></strong><br>
                <span><?php echo $user->display_name; ?></span>
            </p>
            <p>
                <strong><?php _e('Город:', 'bitrix-ut'); ?></strong><br>
                <span><?php echo $btx_order['UF_CRM_59AE8A94525B3']; //City ?></span>
            </p>
            <p>
                <strong><?php _e('Адрес или склад Новой Почты:', 'bitrix-ut'); ?></strong><br>
                <span><?php echo $btx_order['UF_CRM_59A42C22BECDE']; //Adress ?></span>
            </p>
            <p>
                <strong><?php _e('Email:', 'bitrix-ut'); ?></strong><br>
                <span><?php echo $user->user_email; ?></span>
            </p>
            <p>
                <strong><?php _e('Телефон:', 'bitrix-ut'); ?></strong><br>
                <span><?php echo get_user_meta($user->ID, 'phone', true); ?></span>
            </p>
        </div>

        <div class="col-xs-12 order-action-wrap">

            <?php $stage_id = $btx_order['STAGE_ID']; ?>

            <?php if( $stage_id == '3' ): ?>
                <a class="btn btn-default" id="conferm-order" href="<?php echo but_transaction_nonce_url( 'confirm', $order_id ); ?>">Подтвердить Заказ</a>
                <a class="btn btn-default" id="edit-order" href="">Редактировать Заказ</a>
            <?php endif; ?>

            <?php if( $stage_id == 'NEW' || $stage_id == '3' || $stage_id == '1' || $stage_id == 'PREPARATION' ): ?>
                <a class="btn btn-default" id="conferm-reject" href="<?php echo but_transaction_nonce_url( 'reject', $order_id ); ?>">Отменить Заказ</a>
            <?php endif; ?>

        </div>

        <div class="col-xs-12 products-wrap">
            <legend><?php _e('Список товаров:', 'bitrix-ut'); ?></legend>
            <form method="post" action="<?php echo but_transaction_nonce_url( 'edit', $order_id ); ?>" id="edit-products-form">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th class="product-name"><?php _e('Наименование', 'bitrix-ut'); ?></th>
                            <th class="product-code"><?php _e('Артикул', 'bitrix-ut'); ?></th>
                            <th class="product-size"><?php _e('Размер', 'bitrix-ut'); ?></th>
                            <th class="product-color"><?php _e('Цвет', 'bitrix-ut'); ?></th>
                            <th class="product-price hide-edit"><?php _e('Цена', 'bitrix-ut'); ?></th>
                            <th class="product-qty hide-edit"><?php _e('Количество', 'bitrix-ut'); ?></th>

                            <th class="product-commission hide-edit"><?php _e('Комиссия %', 'bitrix-ut'); ?></th>
                            <th class="product-weight-pred hide-edit"><?php _e('Вес предварительный', 'bitrix-ut'); ?></th>
                            <th class="product-weight-fact hide-edit"><?php _e('Вес фактический', 'bitrix-ut'); ?></th>
                            <th class="product-delivery-price hide-edit"><?php _e('Стоимость доставки', 'bitrix-ut'); ?></th>
                            <th class="product-custom-delivery hide-edit"><?php _e('Тамож. сбор', 'bitrix-ut'); ?></th>

                            <th class="hide-edit"><span class="dashicons dashicons-admin-comments"></span></th>

                            <th class="remove-product show-edit"><?php _e('Удалить', 'bitrix-ut'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 0; foreach ($btx_order_products as $order_product): ?>

                            <?php
                                $btx_product = Btx_Api::get_product( $order_product['PRODUCT_ID'] );

                                $product_url = $btx_product['PROPERTY_110']['value'];
                                $product_code = $btx_product['PROPERTY_111']['value'];
                                $product_size = $btx_product['PROPERTY_112']['value'];
                                $product_color = $btx_product['PROPERTY_113']['value'];
                                $product_qty = $btx_product['PROPERTY_143']['value'];
                                $product_price = $btx_product['PRICE'];

                                $commission = $btx_product['PROPERTY_142']['value'];
                                $weight_pred = $btx_product['PROPERTY_104']['value'];
                                $weight_fact = $btx_product['PROPERTY_105']['value'];
                                $customs_delivery = $btx_product['PROPERTY_145']['value'];

                                $product_comment = $btx_product['DESCRIPTION'];
                            ?>
                        <tr>
                            <td class="product-name">
                                <a href="<?php echo $product_url; ?>" target="_blank">
                                    <?php echo $btx_product['NAME']; ?>
                                </a>    
                            </td>
                            <td class="product-code">
                                <span class="hide-edit"><?php echo $product_code; ?></span>
                                <input type="text" name="product[<?php echo $i; ?>][code]" value="<?php echo $product_code; ?>">
                                <input type="hidden" name="product[<?php echo $i; ?>][id]" value="<?php echo $btx_product['ID']; ?>">
                            </td>
                            <td class="product-size">
                                <span class="hide-edit"><?php echo $product_size; ?></span>
                                <input type="text" name="product[<?php echo $i; ?>][size]" value="<?php echo $product_size; ?>">
                            </td>
                            <td class="product-color">
                                <span class="hide-edit"><?php echo $product_color; ?></span>
                                <input type="text" name="product[<?php echo $i; ?>][color]" value="<?php echo $product_color; ?>">
                            </td>
                            <td class="product-price hide-edit"><?php echo $product_price; ?></td>
                            <td class="product-qty hide-edit"><?php echo $product_qty; ?></td>

                            <td class="product-commission hide-edit"></td>
                            <td class="product-weight-pred hide-edit"></td>
                            <td class="product-weight-fact hide-edit"></td>
                            <td class="product-delivery-price hide-edit"></td>
                            <td class="product-custom-delivery hide-edit"></td>

                            <td class="hide-edit">
                                <?php if( empty($product_comment) || $product_comment == 'no comment'): ?>
                                    -
                                <?php else: ?>
                                    <a class="tooltip-wrap"><?php _e('Показать', 'bitrix-ut'); ?>
                                        <span class="tooltiptext"><?php echo $product_comment; ?></span>
                                    </a>
                                <?php endif; ?>
                            </td>

                            <td class="remove-product show-edit"><span class="dashicons dashicons-no-alt"></span></td>
                        </tr>
                        <?php $i++; endforeach; ?>
                    </tbody>
                </table>
            </form>

            <div class="products-total"></div>

            <div class="transactions-wrap">

                <form id="add-transaction-form" action="" method="post">
                    <h2><?php _e( 'Добавить новую транзакцию:', 'bitrix-ut' ); ?></h2>
                    
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

        </div>

    <?php else: ?>
        <p><?php but_print_notice( __('Извините, такого заказа не существует.'), 'error' ); ?></p>
    <?php endif; ?>

    </div>
</div>