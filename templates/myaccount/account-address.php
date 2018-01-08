<?php
/**
* Edit Account Addresses
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
?>

<div class="account-address">
    <div class="row">
        
        <?php but_print_notices(); ?>
        
        <div class="col-sm-6">
            <form method="post" action="" id="add-new-address">
                <input type="text" name="address" value="">
                <input type="hidden" name="add-user-address" value="1">
                <button class="btn btn-default" type="submit"><?php _e('Добавить'); ?></button>
            </form>
        </div>

        <div class="col-sm-6">

            <?php if( !empty($user_addresses) ): ?>

                <form method="post" action="" id="edit-addresses-list">
                    <?php foreach ($user_addresses as $key => $address ): ?>
                        <input type="text" name="address[<?php echo $key; ?>]" value="<?php echo $address; ?>" disabled>
                    <?php endforeach; ?>
                    <input type="hidden" name="edit-user-address" value="1">
                    <button type="button" class="btn btn-default" id="edit-address"><?php _e('Редактировать'); ?></button>
                    <button type="submit" class="btn btn-default" id="save-address"><?php _e('Сохранить'); ?></button>
                </form>

            <?php endif; ?>

        </div>

    </div>
</div>