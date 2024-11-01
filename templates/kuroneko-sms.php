<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $kuatowc;
$order_no = WC()->session->get( 'order_no' );

get_header();
?>

<form id="kuroneko_atobarai_sms" action="" method="post" >
	<?php wp_nonce_field( 'settlement_sms', 'kuroneko_sms_nonce_field' ); ?>
	<input type="hidden" name="order_no" value="<?php echo $order_no ?>">
	<input type="hidden" name="settlement_page" value="kuroneko_sms">

	<?php if(!$kuatowc->error->get_error_message()) : ?>
	<div class="kuroneko_sms_fields">
		<p><?php echo apply_filters('kuroneko_atobarai_sms_notice', 'SMSで届いた認証コードを入力してください'); ?></p>
		<label for="kuroneko_sms_1">
			<input id="kuroneko_sms_1" class="kuroneko_sms_field" type="text" name="kuroneko_sms[1]" value="" required pattern="[0-9]{1}" maxlength="1">
		</label>
		<label for="kuroneko_sms_2">
			<input id="kuroneko_sms_2" class="kuroneko_sms_field" type="text" name="kuroneko_sms[2]" value="" required pattern="[0-9]{1}" maxlength="1">
		</label>
		<label for="kuroneko_sms_3">
			<input id="kuroneko_sms_3" class="kuroneko_sms_field" type="text" name="kuroneko_sms[3]" value="" required pattern="[0-9]{1}" maxlength="1">
		</label>
		<label for="kuroneko_sms_4">
			<input id="kuroneko_sms_4" class="kuroneko_sms_field" type="text" name="kuroneko_sms[4]" value="" required pattern="[0-9]{1}" maxlength="1">
		</label>
	</div>
	<?php else : ?>
		<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">
			<ul class="woocommerce-error" role="alert">
				<li>
					<?php  echo $kuatowc->error->get_error_message(); ?>
				</li>
			</ul>
		</div>
	<?php endif; ?>
	<div class="send">
		<input name="sms_back" type="submit" id="back_button" class="back_to_delivery_button" value="戻る">
		<?php if(!$kuatowc->error->get_error_message()) : ?>
		<input name="sms_send" type="submit" id="purchase_button" class="checkout_button" value="確定する">
		<?php endif; ?>
	</div>
</form>
<style>
	.kuroneko_sms_fields{
		text-align: center;
		margin-top: 1em;
	}
	.kuroneko_sms_fields p{
		margin-bottom: 1em;
	}
	.kuroneko_sms_fields label input{
		width: 2em;
		font-size: 2em;
		text-align: center;
		border-radius: .25em;
	}
</style>
<?php
get_footer();