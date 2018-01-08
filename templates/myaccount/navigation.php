<?php
/**
 * My Account navigation
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pathes = array(
    array(
        'title'     => __('Учетная запись', 'bitrix-ut'),
        'icon'      => 'admin-users',
        'endpoint'  => ''
    ),
    array(
        'title'     => __('Учетные данные', 'bitrix-ut'),
        'icon'      => 'welcome-write-blog',
        'endpoint'  => 'edit-account'
    ),
    array(
        'title'     => __('Адреса', 'bitrix-ut'),
        'icon'      => 'location-alt',
        'endpoint'  => 'edit-address'
    ),
    array(
        'title'     => __('Заказы', 'bitrix-ut'),
        'icon'      => 'cart',
        'endpoint'  => 'orders'
    ),
    array(
        'title'     => __('Транзакции', 'bitrix-ut'),
        'icon'      => 'email-alt',
        'endpoint'  => 'transactions',
        'count'     => but_get_user_waiting_transaction_count()
    ),
);

$current_endpoint = but_get_current_endpoint();
?>

<div class="buttons-wrap">
    <a class="btn btn-default" href="/order"><?php _e( 'Сделать заказ', 'bitrix-ut' ); ?></a>
    <a href="<?php echo wp_logout_url( get_permalink( get_page_by_path( 'order' ) ) ); ?>" class="btn btn-default">
        <?php _e( 'Выйти', 'bitrix-ut' ); ?>
    </a>
</div>

<nav class="but-account-navigation">
    <ul>
        <?php foreach($pathes as $key => $el): ?>
            <li class="<?php echo ($current_endpoint == $el['endpoint']) ? 'active' : ''; ?> ">
              <a href="<?php echo but_get_page_link($el['endpoint']); ?>">
                <span class="dashicons dashicons-<?php print $el['icon']; ?>"></span>
                <span class="nav-text"><?php _e($el['title'], 'bitrix-ut'); ?></span>

                <?php if( isset( $el['count'] ) ): ?>
                    <span class="count"><?php echo $el['count']; ?></span>
                <?php endif; ?>
              </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>