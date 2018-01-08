<?php
/**
 * Login Form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="but-login-form">
<h2><?php _e( 'Войти', 'bitrix-ut' ); ?></h2>


<?php but_print_notices(); ?>

	<form id="but-login-form" method="post">

		<p>
			<label for="user_login"><?php _e('Email', 'bitrix-ut'); ?> <span
					class="required">*</span></label>
			<input type="text" name="user_login" id="user_login"
						 value="<?php echo (!empty($_POST['user_login'])) ? esc_attr($_POST['user_login']) : ''; ?>" required />
		</p>
		<p>
			<label for="password"><?php _e('Пароль', 'bitrix-ut'); ?> <span
					class="required">*</span></label>
			<input type="password" name="password" id="password" required />
		</p>

		<p class="mb-5"><label class="check">
				<input name="rememberme" type="checkbox" id="rememberme"
							 value="forever"/>
				<span><?php _e('Запомнить меня', 'bitrix-ut'); ?></span>
			</label>
			<?php wp_nonce_field('but-login', 'but-login-nonce'); ?>
			<input type="submit" class="btn btn-default" name="login"
						 value="<?php esc_attr_e('Вход', 'bitrix-ut'); ?>"/>
		</p>
		<a href="<?php echo esc_url(wp_lostpassword_url()); ?>">
			<?php _e('Забыли свой пароль?', 'bitrix-ut'); ?></a>

	</form>

</div>
