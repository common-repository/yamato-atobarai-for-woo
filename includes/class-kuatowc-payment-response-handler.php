<?php
/**
 * KUATOWC_Payment_Response_Handler class.
 *
 * @package Woo - KURONEKO Payment Services
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KUATOWC_Payment_Response_Handler {

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		add_action( 'woocommerce_api_wc_kuroneko_sms', array( $this, 'response_handler' ) );
	}

	/**
	 * Response handler.
	 *
	 */
	public function response_handler() {
		if ( ! isset( $_GET['wc-api'] ) || ( 'wc_kuroneko_sms' !== $_GET['wc-api'] ) ) {
			return;
		}

		global $kuatowc;

		try {
			$order_no = WC()->session->get( 'order_no' );
			if ( ! $order_no ) {
				// セッションにデータがない場合
				$localized_message = '注文に失敗しました';
				$kuatowc->error->add( 'error', $localized_message );
			}

			if ( isset( $_POST['sms_back'] ) ) {
				// 戻る処理した時のステータスをキャンセル
				if ( $order_no ) {
					$order = wc_get_order( $order_no );
					$order->update_status( 'cancelled' );
					$message = __( 'Cancel SMS Certification.', 'kuatowc' );
					$order->add_order_note( $message );
					$order->save();
					WC()->session->__unset( 'order_no' );
//		            $mailer = ( WooCommerce::instance() )->mailer();
//		            $mailer_object = $mailer->emails[ 'Kuatowc_Email_Sms_Cancelled_Order' ];
//		            $mailer_object->trigger( $order_no, $order );
				}
				wp_redirect( wc_get_checkout_url() );

				return true;
			}

			if ( isset( $_POST['sms_send'] ) && isset( $_POST['kuroneko_sms_nonce_field'] ) && wp_verify_nonce( $_POST['kuroneko_sms_nonce_field'], 'settlement_sms' )
			) {
				$nin_code = '';
				foreach ( wc_clean( wp_unslash( $_POST['kuroneko_sms'] ) ) as $val ) {
					$nin_code .= $val;
				}
				$order                     = wc_get_order( $order_no );
				$settings                  = get_option( 'woocommerce_kuronekoatobarai_settings', array() );
				$SLN                       = new KUATOWC_SLN_Connection();
				$params                    = array();
				$param_list                = array();
				$param_list['ycfStrCode']  = $settings['trader_code'];
				$param_list['orderNo']     = $order_no;
				$param_list['ninCode']     = $nin_code;
				$param_list['requestDate'] = kuatowc_date_format( 'YmdHis' );
				$param_list['password']    = $settings['password'];
				$params['send_url']        = $SLN->send_sms_auth();
				$params['param_list']      = $param_list;
				$response_data             = $SLN->connection( $params );

				if ( '0' === $response_data['returnCode'] ) {
					if ( '1' === $response_data['result'] ) {
						do_action( 'kuatowc_process_payment', $response_data, $order );
						$flg = false;
						foreach ( $order->get_shipping_methods() as $shipping ) {
							/** @var WC_Order_Item_Shipping $shipping */
							$woocommerce_settings = get_option( 'woocommerce_' . $shipping->get_method_id() . '_' . $shipping->get_instance_id() . '_settings' );
							if ( isset( $woocommerce_settings['kuroneko_delivery_check'] ) && 'yes' === $woocommerce_settings['kuroneko_delivery_check'] ) {
								$flg = true;
							}
						}

						$return_url = $order->get_checkout_order_received_url();
						$trans_code = $response_data['requestDate'];
						$order->update_meta_data( '_kuatowc_trans_code', $trans_code );
						$order->update_meta_data( '_kuatowc_sms_notify', '1' );
						$order->save();

						$SLN                           = new KUATOWC_SLN_Connection();
						$params                        = array();
						$param_list                    = array();
						$param_list['ycfStrCode']      = $settings['trader_code'];
						$param_list['orderNo']         = $order_no;
						$param_list['requestDate']     = kuatowc_date_format( 'YmdHis' );
						$param_list['password']        = $settings['password'];
						$params['send_url']            = $SLN->get_status_info();
						$params['param_list']          = $param_list;
						$response_info                 = $SLN->connection( $params );
						$response_data['resultStatus'] = $response_info['result'];
						$response_data['sms'];

						KUATOWC_Payment_Logger::add_log( $response_data, $order_no, $trans_code, $order->get_total() );

						$order->payment_complete( $trans_code );

						$message = __( 'Credit is completed.', 'kuatowc' );
						$order->add_order_note( $message );

						if ( is_callable( array( $order, 'save' ) ) ) {
							$order->save();
						}
						do_action( 'kuatowc_process_response', $response_data, $order );

						// Remove cart.
						WC()->cart->empty_cart();
						WC()->session->__unset( 'order_no' );
						$mailer        = ( WooCommerce::instance() )->mailer();
						$mailer_object = $mailer->emails['WC_Email_Customer_Processing_Order'];
						$mailer_object->trigger( $order->get_order_number(), $order );
						$admin_mailer_object = $mailer->emails['WC_Email_New_Order'];
						$admin_mailer_object->trigger( $order->get_order_number(), $order );

						// Return thank you page redirect.
						wp_redirect( $return_url );
					} else {
						$localized_message = '';
						$error_message     = KUATOWCCodes::$SmsResult[ $response_data['result'] ];
						if ( ! empty( $error_message ) ) {
							$localized_message .= $error_message . '<br />';
						}
						$response_data['result'] = KUATOWCCodes::$SmsResult[ $response_data['result'] ];
						$kuatowc->error->add( $response_data['result'], $localized_message );
					}
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

					$response_data['result'] = KUATOWCCodes::$SmsResult[ $response_data['result'] ];
					$kuatowc->error->add( $response_data['errorCode'], $localized_message );
				}
			}

			$file = 'templates/kuroneko-sms.php';
			if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . $file;
			} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
				$theme_file = get_template_directory() . '/' . $file;
			} else {
				$theme_file = KUATOWC_PLUGIN_DIR . '/' . $file;
			}
			require_once( $theme_file );
			exit();

		} catch ( Exception $e ) {
			wc_add_notice( $e->getLocalizedMessage(), 'error' );
			KUATOWC_Logger::add_log( 'Error: ' . $e->getMessage() );

			wp_die(
				$e->getMessage(),
				'Bad request',
				array(
					'response' => 400,
				)
			);
		}
	}
}

new KUATOWC_Payment_Response_Handler();
