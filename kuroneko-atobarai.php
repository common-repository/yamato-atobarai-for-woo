<?php
/**
 * Plugin Name: Kuroneko Daikin Atobarai Service for Woo
 * Plugin URI: https://www.collne.com/yamato-atobarai-for-woo/
 * Description: Pay with Atobarai via Kuroneko Atobarai.
 * Author: Welcart Inc., yamatofinancial
 * Author URI: https://www.welcart.com/
 * Version: 2.0.0
 * WC requires at least: 3.5
 * WC tested up to: 9.3.3
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kuatowc
 * Domain Path: /languages
 */

if( !defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KUATOWC_VERSION', '2.0.0' );
define( 'KUATOWC_PLUGIN_FILE', __FILE__ );
define( 'KUATOWC_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'KUATOWC_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'KUATOWC_PLUGIN_BASENAME', untrailingslashit( plugin_basename( __FILE__ ) ) );

if( !class_exists( 'Woo_KuronekoAtobarai' ) ) {
	include_once( KUATOWC_PLUGIN_DIR.'/includes/class-kuatowc.php' );
}

global $kuatowc;
$kuatowc = Woo_KuronekoAtobarai::get_instance();

add_action( 'woocommerce_blocks_loaded', 'woocommerce_gateway_kuroneko_atobarai_woocommerce_block_support' );
/**
 * @return void
 */
function woocommerce_gateway_kuroneko_atobarai_woocommerce_block_support() {
	if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
		require_once dirname( __FILE__ ) . '/includes/class-kuatowc-blocks-support.php';
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new Woo_KuronekoAtobarai_Blocks_Support() );
			}
		);
	}
}

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );
