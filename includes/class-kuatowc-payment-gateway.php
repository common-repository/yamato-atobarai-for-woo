<?php
/**
 * WC_Gateway_KuronekoAtobarai class.
 *
 * @extends WC_Payment_Gateway
 * @package Woo - Kuroneko Atobarai Services
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Gateway_KuronekoAtobarai extends WC_Payment_Gateway {

	public static $SEND_DIV_BETUSOU = '0';

	/**
	 * Constructor for the gateway.
	 *
	 */
	public function __construct() {
		$this->id                 = 'kuronekoatobarai';
		$this->has_fields         = true;
		$this->method_title       = __( 'Kuroneko Atobarai', 'kuatowc' );
		$this->method_description = __( 'Pay with Atobarai via Kuroneko Atobarai.', 'kuatowc' );
		$this->supports           = array(
			'products',
		);

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->enabled               = $this->get_option( 'enabled' );
		$this->title                 = $this->get_option( 'title' );
		$this->description           = $this->get_option( 'description' );
		$this->testmode              = 'yes' === $this->get_option( 'testmode' );
		$this->trader_code           = $this->get_option( 'trader_code' );
		$this->password              = $this->get_option( 'password' );
		$this->other_delivery_option = 'yes' === $this->get_option( 'other_delivery_option', 'yes' );
		$this->smartphone_type       = 'yes' === $this->get_option( 'smartphone_type', 'yes' );
		$this->shipment_deadline     = $this->get_option( 'shipment_deadline' );
		$this->settlement_fee        = 'yes' === $this->get_option( 'settlement_fee' );
		$this->logging               = 'yes' === $this->get_option( 'logging', 'yes' );
		$this->token_code            = $this->get_option( 'token_code' );

		// Hooks.
		add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
			$this,
			'process_admin_options'
		) );
		wp_register_script( 'kuronekoatobarai_payment_scripts', plugins_url( 'assets/js/kuatowc-payment.js', KUATOWC_PLUGIN_FILE ), array(), KUATOWC_VERSION, true );
		wp_enqueue_script( 'kuronekoatobarai_payment_scripts' );
	}

	/**
	 * endpoint_order_url
	 *
	 * @param string $order_id order_id
	 *
	 * @return string endpoint_order_url
	 */
	public function endpoint_order_url( $order_id ) {
		return wc_get_endpoint_url( 'Kuroneko-sms', $order_id, wc_get_page_permalink( 'checkout' ) );
	}


	/**
	 * Check if this gateway is enabled and available in the user's country.
	 *
	 * @return bool
	 */
	public function is_valid_for_use() {

		return in_array( get_woocommerce_currency(), apply_filters( 'kuatowc_supported_currencies', array( 'JPY' ) ) );
	}

	/**
	 * Admin save options.
	 *
	 */
	public function admin_options() {

		if ( $this->is_valid_for_use() ) {
			parent::admin_options();
		} else {
			?>
            <div class="inline error"><p>
                    <strong><?php echo esc_html__( 'Gateway disabled', 'kuatowc' ); ?></strong>: <?php echo esc_html__( 'Kuroneko Atobarai does not support your store currency.', 'kuatowc' ); ?>
                </p></div>
			<?php
		}
	}

	/**
	 * Save settings.
	 *
	 */
	public function process_admin_options() {
		global $current_section;

		if ( $this->id == $current_section ) {
			parent::process_admin_options();

			$settings = get_option( 'woocommerce_kuronekoatobarai_settings', array() );
			if ( ( isset( $settings['settlement_fee'] ) && 'yes' === $settings['settlement_fee'] ) &&
			     isset( $_POST['amount_from'] ) && isset( $_POST['amount_to'] ) && isset( $_POST['fee'] ) ) {

				$amount_from = wc_clean( wp_unslash( $_POST['amount_from'] ) );
				$amount_to   = wc_clean( wp_unslash( $_POST['amount_to'] ) );
				$fee         = wc_clean( wp_unslash( $_POST['fee'] ) );

				$settlement_fees = array();
				foreach ( (array) $fee as $i => $value ) {
					$settlement_fees[ $i ] = array(
						'amount_from' => $amount_from[ $i ],
						'amount_to'   => $amount_to[ $i ],
						'fee'         => $fee[ $i ],
					);
				}
				update_option( 'kuatowc_settlement_fees', $settlement_fees );
			}
		}
	}

	/**
	 * Checks if required items are set.
	 *
	 * @return bool
	 */
	public function is_valid_setting() {
		if ( empty( $this->trader_code ) || empty( $this->password ) || 'no' === $this->enabled ) {
			return false;
		}

		return true;
	}


	/**
	 * Check if the gateway is available for use.
	 *
	 * @return bool
	 */
	public function is_available() {
		if ( ! is_admin() ) {
			if ( ! WC()->cart ) {
				WC()->initialize_cart();
			}
			if ( ! $this->other_delivery_option && WC()->cart ) {
				foreach ( WC()->cart->calculate_shipping() as $shipping ) {
					/** @var WC_Order_Item_Shipping $shipping */
					$woocommerce_settings = get_option( 'woocommerce_' . $shipping->get_method_id() . '_' . $shipping->get_instance_id() . '_settings' );
					if ( ! isset( $woocommerce_settings['kuroneko_delivery_check'] ) || 'no' === $woocommerce_settings['kuroneko_delivery_check'] ) {
						return false;
					}
				}
			}

			if ( ! WC()->session ) {
				WC()->initialize_session();
			}
			$cart_totals = WC()->session->get( 'cart_totals' );
			if ( isset( $cart_totals['total'] ) && isset( $cart_totals['total_tax'] ) ) {
				if ( $cart_totals['total'] - $cart_totals['total_tax'] > 50000 ) {
					return false;
				}
			}
		}

		if ( ! $this->is_valid_setting() ) {
			return false;
		}

		return parent::is_available();
	}

	/**
	 * Initialise gateway settings form fields.
	 *
	 */
	public function init_form_fields() {

		$this->form_fields = apply_filters( 'kuatowc_gateway_settings',
			array(
				'enabled'               => array(
					'title'   => __( 'Enable/Disable', 'kuatowc' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Kuroneko Atobarai.', 'kuatowc' ),
					'default' => 'no',
				),
				'title'                 => array(
					'title'       => __( 'Title', 'kuatowc' ),
					'type'        => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'kuatowc' ),
					'default'     => __( 'Kuroneko Atobarai', 'kuatowc' ),
				),
				'description'           => array(
					'title'       => __( 'Description', 'kuatowc' ),
					'type'        => 'text',
					'description' => __( 'This controls the description which the user sees during checkout.', 'kuatowc' ),
					'default'     => __( 'Pay with Atobarai via Kuroneko Atobarai.', 'kuatowc' ),
				),
				'testmode'              => array(
					'title'       => __( 'Test mode', 'kuatowc' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable Test mode', 'kuatowc' ),
					'default'     => 'yes',
					'description' => __( 'Connect to test environment and run in test mode.', 'kuatowc' ),
				),
				'trader_code'           => array(
					'title'       => __( 'Trader Code', 'kuatowc' ),
					'type'        => 'text',
					'description' => __( '11 digits on contract sheet with Kuroneko Atobarai.', 'kuatowc' ),
				),
				'password'              => array(
					'title'       => __( 'PassWord', 'kuatowc' ),
					'type'        => 'text',
					'description' => __( 'PassWord Number on contract sheet with Kuroneko Atobarai.', 'kuatowc' ),
					'default'     => '',
				),
				'other_delivery_option' => array(
					'title'       => __( 'Other Delivery Option', 'kuatowc' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable Other Delivery Option', 'kuatowc' ),
					'default'     => 'no',
					'description' => __( 'Apply other option when use Other Delivery Option.', 'kuatowc' ),
				),
				'smartphone_type'       => array(
					'title'       => __( 'SMS Certification', 'kuatowc' ),
					'type'        => 'checkbox',
					'label'       => __( 'Use SMS Certification.', 'kuatowc' ),
					'default'     => 'no',
					'description' => __( 'Apply other option when use SMS Certification.', 'kuatowc' ),
				),
				'shipment_deadline'     => array(
					'title'       => __( 'Due to Shipping', 'kuatowc' ),
					'type'        => 'text',
					'description' => __( 'Set the maximum value (reference) of the number of days it takes from ordering to shipping.', 'kuatowc' ),
					'default'     => '10',
				),
				'settlement_fee'        => array(
					'title'       => __( 'Settlement fee', 'kuatowc' ),
					'type'        => 'checkbox',
					'label'       => __( 'Set the settlement fee', 'kuatowc' ),
					'desc_tip'    => true,
					'description' => __( 'It will be added to the total amount as a settlement fee.', 'kuatowc' ),
					'default'     => 'no',
				),
				'settlement_fee_table'  => array(
					'type'        => 'hidden',
					'default'     => '',
					'description' => __( 'Please set the settlement fee by the amount including tax.', 'kuatowc' ),
				),
				'logging'               => array(
					'title'       => __( 'Save the log', 'kuatowc' ),
					'label'       => __( 'Save the log of payment results', 'kuatowc' ),
					'type'        => 'checkbox',
					'description' => __( 'Save the log of payment results to WooCommerce System Status log.', 'kuatowc' ),
					'default'     => 'yes',
				),
			)
		);
	}


	/**
	 * Payment form on checkout page.
	 *
	 */
	public function payment_fields() {

		$description = $this->get_description() ? $this->get_description() : '';

		ob_start();
		echo '<div id="kuronekoatobarai-data">';
		if ( $description ) {
			if ( $this->testmode ) {
				$description .= ' ' . __( 'TESTMODE RUNNING.', 'kuatowc' );
				$description = trim( $description );
			}
			echo apply_filters( 'kuatowc_payment_description', wpautop( wp_kses_post( $description ) ), $this->id );
		}
		if ( $this->settlement_fee ) {
			$cart_totals = WC()->session->get( 'cart_totals' );
			if ( isset( $cart_totals['total'] ) ) {
				$fee = get_settlement_fee( $cart_totals['total'] );
				if ( 0 < $fee ) {
					echo '<div class="kuronekoatobarai-settlement-fee">' . sprintf( __( '* A settlement fee of <strong>%s</strong> will be added at the time of settlement.', 'kuatowc' ), wc_price( $fee ) ) . '</div>';
				}
			}
		}
		if ( $this->smartphone_type ) {
			echo '</div>';
			echo '<div id="kuronekoatobarai-sms-notify">';
			echo '<input type="checkbox" name="sms-notify"  id="sms-notify"/>' . __( 'Use SMS Certification.', 'kuatowc' );
			echo '<div class="form-row form-row-wide" id="kuatowc-phone-number">';
			echo '<label for="phone-number">' . esc_html__( 'Phone number', 'kuatowc' ) . ' <span class="required">*</span></label>
			    <input type="text" name="phone-number" id="phone-number" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" placeholder="08012345678"/>';
			echo '</div>';
			echo '</div>';
		}

		ob_end_flush();
	}


	/**
	 * Outputs scripts.
	 *
	 */
	public function payment_scripts() {
		if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) && ! is_add_payment_method_page() && ! isset( $_GET['change_payment_method'] ) ) {
			return;
		}
		if ( 'no' === $this->enabled ) {
			return;
		}
		wp_register_style( 'kuronekoatobarai_styles', plugins_url( 'assets/css/kuatowc.css', KUATOWC_PLUGIN_FILE ), array(), KUATOWC_VERSION );
		wp_enqueue_style( 'kuronekoatobarai_styles' );
	}


	/**
	 * Process the payment.
	 *決済処理
	 *
	 * @param int $order_no
	 *
	 * @return array
	 */
	public function process_payment( $order_no, $retry = true ) {
		$order = wc_get_order( $order_no );

		try {
			$fee = null;
			if ( $this->settlement_fee ) {
				// Add settlement fee.
				$fee = get_settlement_fee( $order->get_total() );
				if ( $fee ) {
					$fee      = floatval( $fee );
					$item_fee = new WC_Order_Item_Fee();
					$item_fee->set_amount( $fee );
					$item_fee->set_total( $fee );
					$item_fee->set_tax_status( 'none' );
					$item_fee->set_total_tax( '0' );
					$item_fee->set_name( __( 'Kuroneko Atobarai Settlement fee', 'kuatowc' ) );
					$order->add_item( $item_fee );
					$order->calculate_totals( false );
					$order->save();
				}

			}


			$country    = $order->get_billing_country();
			$state      = $order->get_billing_state();
			$name       = kuatowc_formatName( $order->get_billing_last_name() . $order->get_billing_first_name() );
			$address    = kuatowc_formatAddress( WC()->countries->get_states( $country )[ $state ] . $order->get_billing_city() . $order->get_billing_address_1() . $order->get_billing_address_2() );
			$tel_number = isset( $_POST['sms-notify'] ) && 'on' === wc_clean( wp_unslash( $_POST['sms-notify'] ) ) && isset( $_POST['phone-number'] ) ? wc_clean( wp_unslash( $_POST['phone-number'] ) ) : $order->get_billing_phone();
			$send_div   = isset( $_POST['sms-notify'] ) && 'on' === wc_clean( wp_unslash( $_POST['sms-notify'] ) ) ? 3 : 0;

			$SLN                             = new KUATOWC_SLN_Connection();
			$params                          = array();
			$param_list                      = array();
			$param_list['ycfStrCode']        = $this->trader_code;
			$param_list['orderNo']           = $order_no;
			$param_list['orderYmd']          = kuatowc_date_format( 'Ymd' );
			$param_list['shipYmd']           = shipYmd( 'Ymd', $this->shipment_deadline );
			$param_list['name']              = $name;
			$param_list['postCode']          = $order->get_billing_postcode();
			$param_list['address1']          = $address['address1'];
			$param_list['address2']          = $address['address2'];
			$param_list['telNum']            = $tel_number;
			$param_list['email']             = $order->get_billing_email();
			$param_list['totalAmount']       = $order->get_total();
			$param_list['sendDiv']           = $send_div;
			$param_list['requestDate']       = kuatowc_date_format( 'YmdHis' );
			$param_list['password']          = $this->password;
			$param_list['headerInformation'] = null;
			$param_list['fraudbuster']       = null;
			$param_list['cartCode']          = 'woocommerce';
			$param_list                      = kuatowc_get_settlement_item_names( $order, $param_list, $fee );
			$params['param_list']            = $param_list;
			$params['send_url']              = $SLN->send_settlement();
			$response_data                   = $SLN->connection( $params );

			if ( '1' === $response_data['returnCode'] ) {
				//エラーが電話番号不備だった場合
				if ( 'kaara040E' === $response_data['errorCode'] ) {
					$error_message = KUATOWCCodes::errorLabel( $response_data['errorCode'] );
					$order->add_order_note( $error_message );
					throw new KUATOWC_Exception( print_r( $response_data, true ), $error_message );
				} else {
					// エラー時
					$localized_message = '';
					if ( isset( $response_data['errorCode'] ) ) {
						$error_message = KUATOWCCodes::errorLabel( $response_data['errorCode'] );
						if ( ! empty( $error_message ) ) {
							$localized_message .= $error_message . '<br />';
						}
						$response_data['errorCode'] = KUATOWCCodes::errorLabel( $response_data['errorCode'] );
					}

					if ( empty( $localized_message ) ) {
						$localized_message .= __( 'Payment processing failed. Please retry.', 'kuatowc' );
					}
					throw new KUATOWC_Exception( print_r( $response_data, true ), $localized_message );
				}
			} else {
				//結果コードが正常で審査結果が異常だった場合
				if ( '1' === $response_data['result'] || '2' === $response_data['result'] ) {
					$localized_message = '';
					if ( '1' === $response_data['result'] ) {
						$error_message = KUATOWCCodes::$ResultErrorAdmin['1'];
						if ( ! empty( $error_message ) ) {
							$localized_message .= $error_message . '<br />';
						}
						$response_data['result'] = KUATOWCCodes::$ResultErrorAdmin['1'];

					} else if ( '2' === $response_data['result'] ) {
						$error_message = KUATOWCCodes::$ResultErrorAdmin['2'];
						if ( ! empty( $error_message ) ) {
							$localized_message .= $error_message . '<br />';
						}
						$response_data['result'] = KUATOWCCodes::$ResultErrorAdmin['2'];
					}
					throw new KUATOWC_Exception( print_r( $response_data, true ), $localized_message );
				}
				//SMS認証の場合
				if ( '3' === $response_data['result'] ) {
					$order->update_status( 'sms-processing' );
					$message = __( 'Processing SMS Certification.', 'kuatowc' );
					$order->add_order_note( $message );
					WC()->session->set( 'order_no', $order_no );

					return array(
						'result'   => 'success',
						'redirect' => str_replace( 'http://', 'https://', home_url( '/?wc-api=wc_kuroneko_sms' ) )
					,
					);
				}
			}

			$flg = false;
			foreach ( $order->get_shipping_methods() as $shipping ) {
				/** @var WC_Order_Item_Shipping $shipping */
				$woocommerce_settings = get_option( 'woocommerce_' . $shipping->get_method_id() . '_' . $shipping->get_instance_id() . '_settings' );
				if ( isset( $woocommerce_settings['kuroneko_delivery_check'] ) && 'yes' === $woocommerce_settings['kuroneko_delivery_check'] ) {
					$flg = true;
				}
			}

			if ( $order->get_total() > 0 ) {
				do_action( 'kuatowc_process_payment', $response_data, $order );
				$this->process_response( $response_data, $order );

			} else {
				$order->payment_complete();
			}

			// Remove cart.
			WC()->cart->empty_cart();

			// Return thank you page redirect.
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);

		} catch ( KUATOWC_Exception $e ) {
			wc_add_notice( $e->getLocalizedMessage(), 'error' );
			KUATOWC_Logger::add_log( 'Error: ' . $e->getMessage() );

			do_action( 'kuatowc_process_payment_error', $e, $order );

			$order->update_status( 'failed' );

			return array(
				'result'   => 'fail',
				'redirect' => '',
			);
		}
	}

	/**
	 * Store extra meta data for an order.
	 *
	 */
	public function process_response( $response_data, $order ) {

		if ( '0' === $response_data['result'] && ! empty( $response_data['requestDate'] ) ) {
			$order_no   = $order->get_id();
			$trans_code = $response_data['requestDate'];
			$order->update_meta_data( '_kuatowc_trans_code', $trans_code );
			$order->save();

			$SLN                            = new KUATOWC_SLN_Connection();
			$params                         = array();
			$param_list                     = array();
			$param_list['ycfStrCode']       = $this->trader_code;
			$param_list['orderNo']          = $order_no;
			$param_list['requestDate']      = kuatowc_date_format( 'YmdHis' );
			$param_list['password']         = $this->password;
			$params['send_url']             = $SLN->get_status_info();
			$params['param_list']           = $param_list;
			$response_info                  = $SLN->connection( $params );
			$response_data['resultStatus']  = $response_info['result'];
			$response_data['warningStatus'] = $response_info['warning'];

			KUATOWC_Payment_Logger::add_log( $response_data, $order_no, $trans_code, $order->get_total() );

			$order->payment_complete( $trans_code );

			$message = __( 'Credit is completed.', 'kuatowc' );
			$order->add_order_note( $message );
		}

		if ( is_callable( array( $order, 'save' ) ) ) {
			$order->save();
		}

		do_action( 'kuatowc_process_response', $response_data, $order );
	}


}

