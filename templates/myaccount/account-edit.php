<?php
/**
* User edit account page
*/
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
if(empty($current_user)){
  return;
}
?>
  <form id="edit-account-form" class="edit-account"  method="post">

    <p class="form-row form-row-first">
      <label for="account_first_name"><?php _e( 'Имя', 'bitrix-ut' ); ?></label>
      <input type="text" disabled="disabled" class="input-text" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $current_user->first_name ); ?>" />
    </p>
    <p class="form-row form-row-last">
      <label for="account_last_name"><?php _e( 'Фамилия', 'bitrix-ut' ); ?></label>
      <input type="text" disabled="disabled" class="input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $current_user->last_name ); ?>" />
    </p>
    <div class="clear"></div>

    <p class="form-row form-row-wide">
      <label for="account_email"><?php _e( 'Email', 'bitrix-ut' ); ?></label>
      <input type="email" disabled="disabled" class="input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" />
    </p>

    <fieldset>
      <?php but_print_notices(); ?>
      <legend><?php _e( 'Изменение пароля', 'bitrix-ut' ); ?></legend>
      <p class="form-row form-row-wide">
        <label for="password_current"><?php _e( 'Текущий пароль', 'bitrix-ut' ); ?></label>
        <input type="password" class="input-text" name="password_current" id="password_current" />
      </p>
      <p class="form-row form-row-wide">
        <label for="password_1"><?php _e( 'Новый пароль', 'bitrix-ut' ); ?></label>
        <input type="password" class="input-text" name="password_1" id="password_1" />
      </p>
      <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="password_2"><?php _e( 'Подтвердите пароль', 'bitrix-ut' ); ?></label>
        <input type="password" class="input-text" name="password_2" id="password_2" />
      </p>
    </fieldset>
    <div class="clear"></div>

    <p>
      <?php wp_nonce_field( 'save_account_details' ); ?>
      <input type="submit" class="btn btn-default" name="save_account_details" value="<?php _e( 'Сохранить', 'bitrix-ut' ); ?>" />
      <input type="hidden" name="action" value="save_account_details" />
    </p>

  </form>
