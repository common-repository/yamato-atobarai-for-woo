<?php
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Automattic\WooCommerce\Blocks\Payments\PaymentResult;
use Automattic\WooCommerce\Blocks\Payments\PaymentContext;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Stripe_Blocks_Support class.
 *
 * @extends AbstractPaymentMethodType
 */
final class Woo_KuronekoAtobarai_Blocks_Support extends AbstractPaymentMethodType {
	/**
	 * The gateway instance.
	 *
	 * @var WC_Gateway_Dummy
	 */
	private $gateway;

	/**
	 * Payment method name/id/slug.
	 *
	 * @var string
	 */
	protected $name = 'kuronekoatobarai';

	/**
	 * Initializes the payment method type.
	 */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_kuronekoatobarai_settings', [] );
		$gateways       = WC()->payment_gateways->payment_gateways();
		$this->gateway  = $gateways[ $this->name ];
	}

	/**
	 * Returns if this payment method should be active. If false, the scripts will not be enqueued.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return $this->gateway->is_available();
	}

	/**
	 * Returns an array of scripts/handles to be registered for this payment method.
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles() {

		$script_path       = '/build/index.js';
		$script_asset_path = KUATOWC_PLUGIN_DIR . '/build/index.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require( $script_asset_path )
			: array(
				'dependencies' => array(),
				'version'      => '1.2.0'
			);
		$script_url        = KUATOWC_PLUGIN_URL . $script_path;

		wp_register_script(
			'wc-kuronekoatobarai-payments-blocks',
			$script_url,
			$script_asset[ 'dependencies' ],
			$script_asset[ 'version' ],
			true
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
//			wp_set_script_translations( 'wc-kuronekopayment-payments-blocks', 'kuatowc', KUPAYWC_PLUGIN_DIR . '/languages');
			wp_set_script_translations( 'wc-kuronekopayment-payments-blocks', 'kuatowc');
		}

		return [ 'wc-kuronekoatobarai-payments-blocks' ];
	}

	/**
	 * Returns an array of key=>value pairs of data made available to the payment methods script.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {

		$description     = $this->get_setting( 'description' );
		$test_mode       = 'yes' === $this->gateway->get_option( 'testmode' );
		$settlement_fee  = 'yes' === $this->gateway->get_option( 'settlement_fee' );
		$smartphone_type = 'yes' === $this->gateway->get_option( 'smartphone_type' );
		$fee_text        = '';
		if ( $description ) {
			if ( $test_mode ) {
				$description .= ' ' . __( 'TESTMODE RUNNING.', 'kuatowc' );
				$description = trim( $description );
			}
			$description = apply_filters( 'kuatowc_payment_description', wpautop( wp_kses_post( $description ) ), $this->name );
		}
		$checkout_page_id = get_option( 'woocommerce_checkout_page_id' );
		$language = array(
			'use_sms_certification' => __( 'Use SMS Certification.', 'kuatowc' ),
			'phone_number'          => __( 'Phone number', 'kuatowc' ),
		);
		if ( is_page( $checkout_page_id ) ) {
			if ( $settlement_fee ) {
				$cart_totals = WC()->session->get( 'cart_totals' );
				if ( isset( $cart_totals['total'] ) ) {
					$fee = get_settlement_fee( $cart_totals['total'] );
					if ( 0 < $fee ) {
						$fee_text = '<div class="kuronekoatobarai-settlement-fee">' . sprintf( __( '* A settlement fee of <strong>%s</strong> will be added at the time of settlement.', 'kuatowc' ), wc_price( $fee ) ) . '</div>';
					}
				}
			}
		}

		$data = array(
			'title'           => $this->get_setting( 'title' ),
			'description'     => $description,
			'supports'        => array_filter( $this->gateway->supports, [ $this->gateway, 'supports' ] ),
			'testmode'        => $test_mode,
			'settlement_fee'  => $settlement_fee,
			'smartphone_type' => $smartphone_type,
			'fee_text'        => $fee_text,
			'language'        => $language
		);

		return $data;
	}
}
