<?php
/**
* My Account orders page
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

global $wp;

$args = [
    'author'            => get_current_user_id(),
    'orderby'           => 'post_date',
    'order'             => 'DESC',
    'post_type'         => 'bitrix_orders',
    'posts_per_page'    => -1
];

$orders_page = home_url( $wp->request );

$the_query = new WP_Query( $args ); ?>

<?php if ( $the_query->have_posts() ) : ?>

    <div class="account-orders">
        <p><?php _e( 'Здесь Вы можете видеть список Ваших заказов и их текущий статус.', 'bitrix-ut' ); ?></p>

        <table class="orders-table my_account_orders">
            <thead>
                <tr>
                    <th class="number"><span><?php _e( 'Заказ', 'bitrix-ut' ); ?></span></th>
                    <th class="create-date"><span><?php _e( 'Дата', 'bitrix-ut' ); ?></span></th>
                    <th class="status"><span><?php _e( 'Статус', 'bitrix-ut' ); ?></span></th>
                    <th class="qty"><span><?php _e( 'Кол-во', 'bitrix-ut' ); ?></span></th>
                </tr>
            </thead>
            <tbody>
            <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

                <?php

                    $wp_order_id = get_the_ID();
                    $btx_order_id = get_post_meta( $wp_order_id, '_btx_order_id', true );

                    $btx_order = Btx_Api::get_order( $btx_order_id );
                    $btx_order_products = Btx_Api::get_order_products( $btx_order_id );
                    $btx_order_date = new DateTime( $btx_order['DATE_CREATE'] );

                    $btx_qty = 0;

                    foreach( $btx_order_products as $product ) {
                        $btx_qty += $product['QUANTITY'];
                    }
                ?>

                <tr>
                    <td class="number">
                        <a href="<?php echo $orders_page.'/'.get_the_ID(); ?>"><?php echo '№ '.$btx_order_id ; ?></a>
                    </td>
                    <td class="create-date">
                        <time><?php echo $btx_order_date->format('d-m-Y'); ?></time>
                    </td>
                    <td class="status">
                        <?php echo but_get_order_status( $btx_order['STAGE_ID'] ); ?>
                    </td>
                    <td class="qty">
                        <?php echo $btx_qty; ?>
                    </td>
                </tr>
                
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php wp_reset_postdata(); ?>
<?php else : ?>
    <p><?php but_print_notice( __('Извините, у Вас ещё нет ни одного заказа.'), 'error' ); ?></p>
<?php endif; ?>