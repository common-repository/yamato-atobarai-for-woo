<?php
/**
 * KUATOWC_Admin_Order class.
 *
 * @package Woo - Kuroneko Atobarai Services
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

class KUATOWC_Admin_Order {

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		add_filter( 'manage_shop_order_posts_columns', array( $this, 'define_columns' ), 20 );
		add_filter( 'manage_woocommerce_page_wc-orders_columns', array( $this, 'define_columns' ), 20 ); // HPOS
		add_filter( 'manage_shop_order_posts_custom_column', array( $this, 'render_columns' ), 20, 2 );
		add_action( 'manage_woocommerce_page_wc-orders_custom_column', array( $this, 'render_columns' ), 20, 2 ); // HPOS

		add_action( 'add_meta_boxes', array( $this, 'meta_box' ) );
		add_action( 'wp_ajax_kuatowc_settlement_actions', array( $this, 'ajax_handler' ) );
		wp_enqueue_script( 'jquery-ui-dialog' );
		add_action( 'admin_print_footer_scripts', array( $this, 'settlement_scripts' ) );
		add_filter( 'woocommerce_bulk_action_ids', array( $this, 'kuatowc_woocommerce_bulk_action_ids' ), 20, 2 );
		add_action( 'woocommerce_process_shop_order_meta', array(
			$this,
			'kuatowc_woocommerce_order_actions_end'
		), 20, 2 );
//        add_action( 'woocommerce_order_item_add_action_buttons', array($this,'kuatowc_order_item_add_action_buttons' ),100,2 );

		if ( true === is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
			function kuatowc_showNotUseNotices() {
				$html = '<div class="error notice">';
				$html .= '<p>' . sprintf( __( 'When using %s, Kuroneko Atobarai not use.', 'kuatowc' ), __( 'WooCommerce Subscriptions', 'kupaywc' ) ) . '</p>';
				$html .= '</div>';

				echo $html;
			}

			add_action( 'admin_notices', 'kuatowc_showNotUseNotices' );
		};
	}

	/**
	 * Render columm: kuatowc_status.
	 *
	 */
	public function define_columns( $columns ) {
		$columns['kuatowc_status'] = __( 'Payment Status', 'kuatowc' ) . '<br />' . __( '(KuronekoAtobarai)', 'kuatowc' );

		return $columns;
	}

	/**
	 * Render columm: kuatowc_status.
	 *
	 * @param  string  $column  Column ID to render.
	 * @param $post_or_order_object
	 */
	public function render_columns( $column, $post_or_order_object ) {

		if ( 'kuatowc_status' !== $column ) {
			return;
		}
		if ( $post_or_order_object instanceof WC_order ) {
			$order = $post_or_order_object;
		} else {
			$order = wc_get_order( $post_or_order_object );
		}
		if ( ! is_object( $order ) ) {
			return;
		}
		$order_no = $order->get_id();

		$trans_code = $order->get_meta( '_kuatowc_trans_code', true );
		if ( ! $trans_code ) {
			return;
		}
		$latest_log = KUATOWC_Payment_Logger::get_latest_log( $order_no, $trans_code );
		if ( ! $latest_log ) {
			return;
		}

		$latest_status_number = KUATOWC_Payment_Logger::get_result_status_number( $order_no, $trans_code );

		if ( '1' === $latest_status_number ) {
			$class = '';
		} else if ( '3' === $latest_status_number || '10' === $latest_status_number || '11' === $latest_status_number || '12' === $latest_status_number ) {
			$class = 'payment-complete';
		} else if ( '2' === $latest_status_number || '31' === $latest_status_number ) {
			$class = 'credit-delete';
		} else if ( '33' === $latest_status_number ) {
			$class = 'amount-chn';
		} else {
			$class = 'card-error';
		}

		printf( '<mark class="order-kuatowc-status %s"><span>%s</span></mark>', esc_attr( sanitize_html_class( $class ) ), esc_html( KUATOWC_Payment_Logger::get_label_status( $order_no, $trans_code ) ) );
	}

	/**
	 * Settlement actions metabox.
	 *
	 */
	public function meta_box() {
		$order_no = wc_get_order( absint( isset( $_GET['id'] ) ? $_GET['id'] : 0 ) );
		$order    = wc_get_order( $order_no );
		if ( ! $order ) {
			return;
		}

		$payment_method = $order->get_payment_method();
		if ( 'kuronekoatobarai' === $payment_method ) {
			$screen = class_exists( '\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' ) && wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
				? wc_get_page_screen_id( 'shop-order' )
				: 'shop_order';

			add_meta_box( 'kuatowc-settlement-actions', __( 'Kuroneko Atobarai', 'kuatowc' ), array(
				$this,
				'settlement_actions_box'
			), $screen, 'side' );
			add_meta_box( 'kuatowc-invoice-number', __( 'Kuroneko Invoice Number', 'kuatowc' ), array(
				$this,
				'invoice_number_box'
			), $screen, 'side' );
		}
	}

	/**s
	 * Settlement actions metabox content.
	 *
	 * @param $post_or_order_object
	 */
	public function settlement_actions_box( $post_or_order_object ) {
		$order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
		if ( empty( $order ) ) {
			return;
		}
		$order_no = $order->get_id();
		$payment_method = $order->get_payment_method();

		$trans_code = $order->get_meta( '_kuatowc_trans_code', true );
		if ( empty( $trans_code ) ) {
			$trans_code = kuatowc_init_transaction_code();
		}
		$latest_info = $this->settlement_latest_info( $order_no, $trans_code, $payment_method );
		?>
        <div id="kuatowc-settlement-latest">
			<?php echo $latest_info; ?>
        </div>
		<p id="kuatowc-settlement-latest-button">
			<input type="button" class="button kuatowc-settlement-info"
				   id="kuatowc-<?php echo esc_attr( $order_no ); ?>-<?php echo esc_attr( $trans_code ); ?>-1"
				   value="<?php echo esc_attr__( 'Info', 'kuatowc' ); ?>"/>
		</p>
		<?php
	}

	/**
	 * Settlement actions metabox content.
	 *
	 * @param $post_or_order_object
	 */
	public function invoice_number_box( $post_or_order_object ) {
		$order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
		if ( empty( $order ) ) {
			return;
		}
		$shipment_entry_end = $order->get_meta( '_kuatowc_shipment_entry_end', true );
		$invoice_number     = $order->get_meta( 'kuroneko_order_invoice_number', true );
		?>
        <div class="add_invoice_number">
            <p>
                <label for="add_order_invoice_number"><?php esc_html_e( '送り状番号を追加', 'kuatowc' ); ?><?php echo wc_help_tip( __( '発送完了に変更する前に指定してください。', 'kuatowc' ) ); ?></label>
                <input type="text" name="order_invoice_number" id="add_order_invoice_number" class="input-text"
                       value="<?php echo esc_attr( $invoice_number ); ?>"<?php if ( $shipment_entry_end === '1' ) : ?> readonly <?php endif; ?>>
            </p>
        </div>
		<?php

	}

	/**
	 * 管理画面一括操作の処理
	 *
	 * @param $ids
	 * @param $action
	 *
	 * @return array
	 */
	function kuatowc_woocommerce_bulk_action_ids( $ids, $action ) {
		$return_ids = array();
		foreach ( $ids as $id ) {
			$order          = wc_get_order( $id );
			$payment_method = $order->get_payment_method();
			if ( 'kuronekoatobarai' === $payment_method && 'mark_completed' === $action ) {
				if ( $this->kuatowc_order_shipment_process( $id ) ) {
					$return_ids[] = $id;
				}
			} else {
				$return_ids[] = $id;
			}
		}

		return $return_ids;
	}


	/**
	 * 管理画面の受注更新
	 *
	 * @param $order_no
	 * @param $post
	 */
	public function kuatowc_woocommerce_order_actions_end( $order_no, $post ) {
		$order          = wc_get_order( $order_no );
		$payment_method = $order->get_payment_method();
		if ( 'kuronekoatobarai' === $payment_method ) {
			$order->update_meta_data( 'kuroneko_order_invoice_number', wc_clean( wp_unslash( $_POST['order_invoice_number'] ) ) );
			$order->save();
			//グローバル変数に入れる。
			global $kuatowc;
			$kuatowc->kuroneko_order_invoice_number = wc_clean( wp_unslash( $_POST['order_invoice_number'] ) );
			if ( 'wc-completed' === wc_clean( wp_unslash( $_POST['order_status'] ) ) ) {
				$this->kuatowc_order_shipment_process( $order_no );
			}
		}
	}

	/**
	 * 出荷情報登録
	 *
	 * @param $order_no
	 *
	 * @return bool
	 */
	function kuatowc_order_shipment_process( $order_no ) {
		$order              = wc_get_order( $order_no );
		$settings           = get_option( 'woocommerce_kuronekoatobarai_settings', array() );
		$shipment_entry_end = $order->get_meta( '_kuatowc_shipment_entry_end', true );
		if ( '1' !== $shipment_entry_end ) {
			$is_delivery_kuroneko = false;
			foreach ( $order->get_shipping_methods() as $shipping ) {
				/** @var WC_Order_Item_Shipping $shipping */
				$woocommerce_settings = get_option( 'woocommerce_' . $shipping->get_method_id() . '_' . $shipping->get_instance_id() . '_settings' );
				if ( isset( $woocommerce_settings['kuroneko_delivery_check'] ) && 'yes' === $woocommerce_settings['kuroneko_delivery_check'] ) {
					$is_delivery_kuroneko = true;
				}
			}


			$SLN        = new KUATOWC_SLN_Connection();
			$params     = array();
			$param_list = array();
			// metaに送り状番号が無い場合はグローバル変数から取得する.
			global $kuatowc;
			if ( $is_delivery_kuroneko ) {
				$ship_no = $order->get_meta( 'kuroneko_order_invoice_number', true ) ? $order->get_meta( 'kuroneko_order_invoice_number', true ) : $kuatowc->kuroneko_order_invoice_number;
			} else {
				$ship_no = $order_no;
			}
			$param_list['ycfStrCode']      = $settings['trader_code'];
			$param_list['orderNo']         = $order_no;
			$param_list['paymentNo']       = $ship_no;
			$param_list['processDiv']      = $is_delivery_kuroneko ? 0 : 2;
			$param_list['shipYmd']         = null;
			$param_list['beforePaymentNo'] = null;
			$param_list['requestDate']     = kuatowc_date_format( 'YmdHis' );
			$param_list['password']        = $settings['password'];
			$params['param_list']          = $param_list;
			$params['send_url']            = $SLN->send_ship_info();
			$response                      = $SLN->connection( $params );

			// エラー時
			if ( ! isset( $response['returnCode'] ) || '1' === $response['returnCode'] ) {
				// 注文完了時エラー文が出た時にメールが飛ばないようにする。
				remove_action( 'woocommerce_process_shop_order_meta', 'WC_Meta_Box_Order_Data::save', 40 );
				remove_action( 'woocommerce_process_shop_order_meta', 'WC_Meta_Box_Order_Actions::save', 50 );
				$localized_message = '';
				if ( isset( $response['errorCode'] ) ) {
					$error_message = KUATOWCCodes::errorLabel( $response['errorCode'] );
					if ( ! empty( $error_message ) ) {
						$localized_message .= $error_message . '<br />';
					}
					$response['errorCode'] = KUATOWCCodes::errorLabel( $response['errorCode'] );
				}

				if ( empty( $localized_message ) ) {
					$localized_message .= __( 'Payment processing failed. Please retry.', 'kuatowc' );
				}
				WC_Admin_Meta_Boxes::$meta_box_errors[] = '#' . $order_no . 'クロネコの更新失敗 ' . $localized_message;
				$order->add_order_note( __( 'Error during status transition.', 'woocommerce' ) . ' クロネコの更新失敗 ' . $localized_message );

				return false;
			} else {
				// 注文照会の処理
				$order->update_meta_data( '_kuatowc_shipment_entry_end', '1' );
				$order->save();
				$SLN                       = new KUATOWC_SLN_Connection();
				$params                    = array();
				$param_list                = array();
				$param_list['ycfStrCode']  = $settings['trader_code'];
				$param_list['orderNo']     = $order_no;
				$param_list['requestDate'] = kuatowc_date_format( 'YmdHis' );
				$param_list['password']    = $settings['password'];
				$params['send_url']        = $SLN->get_status_info();
				$params['param_list']      = $param_list;
				$response_data             = $SLN->connection( $params );
				$response['resultStatus']  = $response_data['result'];
				$trans_code                = $order->get_meta( '_kuatowc_trans_code', true );
				KUATOWC_Payment_Logger::add_log( $response, $order_no, $trans_code, $order->get_total() );
				$message = __( 'Payment is completed.', 'kuatowc' );
				$order->add_order_note( $message );

				return true;
			}
		}

		return true;
	}

	/**
	 * Settlement actions latest.
	 *
	 */
	private function settlement_latest_info( $order_no, $trans_code, $payment_method ) {

		$latest     = '';
		$latest_log = KUATOWC_Payment_Logger::get_latest_log( $order_no, $trans_code, true );
		if ( $latest_log ) {
			$latest .= '<table>
				<tr><th>' . esc_html__( '決済日時', 'kuatowc' ) . ':</th><td>' . esc_html( $latest_log['timestamp'] ) . '</td></tr>
				<tr><th>' . esc_html__( '決済コード', 'kuatowc' ) . ':</th><td>' . esc_html( $latest_log['trans_code'] ) . '</td></tr>';
			$latest .= '<tr><th>' . esc_html__( 'Status', 'kuatowc' ) . ':</th><td>' . esc_html( KUATOWC_Payment_Logger::get_label_status( $order_no, $trans_code ) ) . '</td></tr>';
			$latest .= '</table>';
		}

		return $latest;
	}

	/**
	 * Settlement actions history.
	 *
	 */
	private function settlement_history( $order_no, $trans_code ) {
		$history  = '';
		$log_data = KUATOWC_Payment_Logger::get_log( $order_no, $trans_code );

		if ( $log_data ) {
			$num     = count( $log_data );
			$history = '<table class="kuatowc-settlement-history">
				<thead class="kuatowc-settlement-history-head">
					<tr><th></th><th>' . esc_html__( 'Processing date', 'kuatowc' ) . '</th><th>' . esc_html__( 'Sequence number', 'kuatowc' ) . '</th><th>' . esc_html__( 'Result', 'kuatowc' ) . '</th></tr>
				</thead>
				<tbody class="kuatowc-settlement-history-body">';
			foreach ( (array) $log_data as $data ) {
				$response   = json_decode( $data['response'], true );
				$class      = ( $response['returnCode'] != '0' ) ? 'error' : '';
				$responsecd = isset( KUATOWCCodes::$StatusInfos[ $response['resultStatus'] ] ) ? KUATOWCCodes::$StatusInfos[ $response['resultStatus'] ] : '';
				$history    .= '<tr>
					<td class="num">' . esc_html( $num ) . '</td>
					<td class="datetime">' . esc_html( $data['timestamp'] ) . '</td>
					<td class="transactionid">' . esc_html( $response['requestDate'] ) . '</td>
					<td class="responsecd' . esc_attr( $class ) . '">' . esc_html( $responsecd ) . '</td>
				</tr>';
				$num --;
			}
			$history .= '</tbody>
				</table>';
		}

		return $history;
	}


	/**
	 * AJAX handler that performs settlement actions.
	 *
	 */
	public function ajax_handler() {
		check_ajax_referer( 'kuatowc-settlement_actions', 'security' );

		if ( ! ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) || current_user_can( 'shop_manager' ) || current_user_can( 'edit_shop_orders' ) ) ) {
			wp_die( 'You do not have sufficient permissions to perform this action.', 403 );
		}

		$mode = wc_clean( wp_unslash( $_POST['mode'] ) );
		$data = array();

		switch ( $mode ) {
			// Get latest information
			//----------------------------------------------------------------------
			case 'get_latest_info':
				$order_no       = ( isset( $_POST['order_no'] ) ) ? wc_clean( wp_unslash( $_POST['order_no'] ) ) : 0;
				$order_num      = ( isset( $_POST['order_num'] ) ) ? wc_clean( wp_unslash( $_POST['order_num'] ) ) : 0;
				$trans_code     = ( isset( $_POST['trans_code'] ) ) ? wc_clean( wp_unslash( $_POST['trans_code'] ) ) : '';
				$payment_method = ( isset( $_POST['payment_method'] ) ) ? wc_clean( wp_unslash( $_POST['payment_method'] ) ) : '';
				if ( empty( $order_no ) || empty( $order_num ) || empty( $trans_code ) ) {
					$data['status'] = 'NG';
					break;
				}
				$order = wc_get_order( $order_no );
				if ( empty( $order ) ) {
					$data['status'] = 'NG';
					break;
				}
				$init_code = str_repeat( '9', strlen( $trans_code ) );
				if ( $trans_code == $init_code ) {
					$trans_code         = $order->get_meta( '_kuatowc_trans_code', true );
					$data['trans_code'] = $trans_code;
				}
				$latest_info = $this->settlement_latest_info( $order_no, $trans_code, $payment_method );
				if ( $latest_info ) {
					$data['status'] = 'OK';
					$data['latest'] = $latest_info;
				}
				break;

			// Card - Transaction reference　取引照会
			//----------------------------------------------------------------------
			case 'get_card':
				$res        = '';
				$order_no   = ( isset( $_POST['order_no'] ) ) ? wc_clean( wp_unslash( $_POST['order_no'] ) ) : 0;
				$order_num  = ( isset( $_POST['order_num'] ) ) ? wc_clean( wp_unslash( $_POST['order_num'] ) ) : 0;
				$trans_code = ( isset( $_POST['trans_code'] ) ) ? wc_clean( wp_unslash( $_POST['trans_code'] ) ) : '';

				if ( empty( $order_no ) || empty( $order_num ) || empty( $trans_code ) ) {
					$data['status'] = 'NG';
					break;
				}

				$settings                  = get_option( 'woocommerce_kuronekoatobarai_settings', array() );
				$latest_log                = KUATOWC_Payment_Logger::get_latest_log( $order_no, $trans_code, true );
				$order                     = wc_get_order( $order_no );
				$SLN                       = new KUATOWC_SLN_Connection();
				$params                    = array();
				$param_list                = array();
				$param_list['ycfStrCode']  = $settings['trader_code'];
				$param_list['orderNo']     = $order_no;
				$param_list['requestDate'] = kuatowc_date_format( 'YmdHis' );
				$param_list['password']    = $settings['password'];
				$params['send_url']        = $SLN->get_status_info();
				$params['param_list']      = $param_list;
				$response_data             = $SLN->connection( $params );

				if ( '0' === $response_data['returnCode'] ) {

					if ( '2' === $response_data['result'] ) {
						$res .= '<span class="kuatowc-settlement-admin credit-delete">取消</span>';
						$res .= '</div>';
					} else {
						$res .= '<table class="kuatowc-settlement-admin-table">';
						if ( $latest_log['amount'] ) {
							$res .= '<tr><th>' . sprintf( __( 'Spending amount (%s)', 'kuatowc' ), get_woocommerce_currency_symbol() ) . '</th>
							<td><input type="text" id="kuatowc-amount_change" value="' . esc_attr( $order->get_total() - $order->get_total_refunded() ) . '" style="text-align:right;ime-mode:disabled" size="10" /><input type="hidden" id="kuatowc-amount" value="' . esc_attr( $latest_log['amount'] ) . '" /></td>
							</tr>';
						}
						$res .= '</table>';
						$res .= '<div class="kuatowc-settlement-admin-button">';
						$res .= '<input type="button" id="kuatowc-delete-button" class="button" value="' . esc_attr__( 'Cancel', 'kuatowc' ) . '" />';
						$res .= '<input type="button" id="kuatowc-change-button" class="button" value="' . esc_attr__( 'Amount change', 'kuatowc' ) . '" />';
						$res .= '</div>';
					}
				} else {
					$res           .= '<span class="kupaywc-settlement-admin card-error">' . esc_html__( 'Error', 'kuatowc' ) . '</span>';
					$res           .= '<div class="kupaywc-settlement-admin-error">';
					$error_message = KUATOWCCodes::errorLabel( $response_data['errorCode'] );
					$res           .= '<div><span class="code">' . esc_html( $response_data['errorCode'] ) . '</span> : <span class="message">' . esc_html( $error_message ) . '</span></div>';
					$res           .= '</div>';
					KUATOWC_Logger::add_log( '[1Search] Error: ' . print_r( $response_data, true ) );
				}
				$res            .= $this->settlement_history( $order_no, $trans_code );
				$data['status'] = $response_data['result'];
				$data['result'] = $res;

				break;


			//  Cancel / Return　取引取消
			//----------------------------------------------------------------------
			case 'delete_card':
				$order_no   = ( isset( $_POST['order_no'] ) ) ? wc_clean( wp_unslash( $_POST['order_no'] ) ) : 0;
				$order_num  = ( isset( $_POST['order_num'] ) ) ? wc_clean( wp_unslash( $_POST['order_num'] ) ) : 0;
				$trans_code = ( isset( $_POST['trans_code'] ) ) ? wc_clean( wp_unslash( $_POST['trans_code'] ) ) : '';
				$amount     = ( isset( $_POST['amount'] ) ) ? wc_clean( wp_unslash( $_POST['amount'] ) ) : '';
				if ( empty( $order_no ) || empty( $order_num ) || empty( $trans_code ) ) {
					$data['status'] = 'NG';
					break;
				}
				$res                       = '';
				$settings                  = get_option( 'woocommerce_kuronekoatobarai_settings', array() );
				$SLN                       = new KUATOWC_SLN_Connection();
				$params                    = array();
				$param_list                = array();
				$param_list['ycfStrCode']  = $settings['trader_code'];
				$param_list['orderNo']     = $order_no;
				$param_list['requestDate'] = kuatowc_date_format( 'YmdHis' );
				$param_list['password']    = $settings['password'];
				$params['send_url']        = $SLN->send_settlement_cancel();
				$params['param_list']      = $param_list;
				$response_data             = $SLN->connection( $params );

				if ( '0' === $response_data['returnCode'] ) {
					$order = wc_get_order( $order_no );
					if ( is_object( $order ) ) {
						$message = sprintf( __( '%s is completed.', 'kuatowc' ), __( 'Cancel', 'kuatowc' ) );
						$order->add_order_note( $message );
					}

					$res .= '<span class="kuatowc-settlement-admin credit-delete">取消</span>';
					$res .= '</div>';

				} else {
					$res           .= '<span class="kuatowc-settlement-admin card-error">' . esc_html__( 'Error', 'kuatowc' ) . '</span>';
					$res           .= '<div class="kuatowc-settlement-admin-error">';
					$error_message = KUATOWCCodes::errorLabel( $response_data['errorCode'] );
					$res           .= '<div><span class="code">' . esc_html( $response_data['errorCode'] ) . '</span> : <span class="message">' . esc_html( $error_message ) . '</span></div>';
					$res           .= '</div>';
					KUATOWC_Logger::add_log( '[1Delete] Error: ' . print_r( $response_data, true ) );
				}

				$SLN                       = new KUATOWC_SLN_Connection();
				$params                    = array();
				$param_list                = array();
				$param_list['ycfStrCode']  = $settings['trader_code'];
				$param_list['orderNo']     = $order_no;
				$param_list['requestDate'] = kuatowc_date_format( 'YmdHis' );
				$param_list['password']    = $settings['password'];
				$params['send_url']        = $SLN->get_status_info();
				$params['param_list']      = $param_list;
				$response_info             = $SLN->connection( $params );
				if ( '0' === $response_info['returnCode'] ) {
					$response_info['resultStatus'] = $response_info['result'];
				}

				do_action( 'kuatowc_action_admin_' . $mode, $response_data, $order_no, $trans_code );
				KUATOWC_Payment_Logger::add_log( $response_info, $order_no, $trans_code, $amount );
				$res            .= $this->settlement_history( $order_no, $trans_code );
				$data['status'] = $response_info['result'];
				$data['result'] = $res;
				break;

			// Card - Amount change　金額変更
			//----------------------------------------------------------------------
			case 'change_card':
				$order_no   = ( isset( $_POST['order_no'] ) ) ? wc_clean( wp_unslash( $_POST['order_no'] ) ) : 0;
				$trans_code = ( isset( $_POST['trans_code'] ) ) ? wc_clean( wp_unslash( $_POST['trans_code'] ) ) : '';
				$amount     = ( isset( $_POST['amount'] ) ) ? wc_clean( wp_unslash( $_POST['amount'] ) ) : '';

				if ( empty( $order_no ) ) {
					$data['status'] = 'NG';
					break;
				}

				$res            = '';
				$order          = wc_get_order( $order_no );
				$settlement_fee = $order->get_items( 'fee' );
				$kuroneko_fee = 0;
				foreach ( $settlement_fee as $item ) {
					$kuroneko_fee += $item->get_amount() - $order->get_total_refunded_for_item( $item->get_id(), 'fee' );
				}
				$is_sms   = $order->get_meta( '_kuatowc_sms_notify', true );
				$send_div = $is_sms ? 2 : 0;
				$settings = get_option( 'woocommerce_kuronekoatobarai_settings', array() );
				$fee      = $kuroneko_fee;
				$country  = $order->get_billing_country();
				$state    = $order->get_billing_state();
				$address  = kuatowc_formatAddress( WC()->countries->get_states( $country )[ $state ] . $order->get_billing_city() . $order->get_billing_address_1() . $order->get_billing_address_2() );

				$SLN                       = new KUATOWC_SLN_Connection();
				$params                    = array();
				$param_list                = array();
				$param_list['ycfStrCode']  = $settings['trader_code'];
				$param_list['orderNo']     = $order_no;
				$param_list['postCode']    = $order->get_billing_postcode();
				$param_list['address1']    = $address['address1'];
				$param_list['address2']    = $address['address2'];
				$param_list['totalAmount'] = $amount;
				$param_list['sendDiv']     = $send_div;
				$param_list['password']    = $settings['password'];
				$param_list['requestDate'] = kuatowc_date_format( 'YmdHis' );
				$param_list                = kuatowc_get_settlement_item_names( $order, $param_list, $fee );
				$params['param_list']      = $param_list;
				$params['send_url']        = $SLN->send_amount_change();
				$response_data             = $SLN->connection( $params );

				if ( '0' === $response_data['returnCode'] ) {
					$order = wc_get_order( $order_no );
					if ( is_object( $order ) ) {
						$message = sprintf( __( '%s is completed.', 'kuatowc' ), __( 'Amount change', 'kuatowc' ) );
						$order->add_order_note( $message );
					}
					$res         .= '<table class="kuatowc-settlement-admin-table">';
					$res         .= '<tr><th>' . sprintf( __( 'Spending amount (%s)', 'kuatowc' ), get_woocommerce_currency_symbol() ) . '</th>
					<td><input type="text" id="kuatowc-amount_change" value="' . esc_attr( $amount ) . '" style="text-align:right;ime-mode:disabled" size="10" /><input type="hidden" id="kuatowc-amount" value="' . esc_attr( $amount ) . '" /></td>
					</tr>';
					$res         .= '</table>';
					$res         .= '<div class="kuatowc-settlement-admin-button">';
					$res         .= '<input type="button" id="kuatowc-delete-button" class="button" value="' . esc_attr__( 'Cancel', 'kuatowc' ) . '" />';
					$res         .= '<input type="button" id="kuatowc-change-button" class="button" value="' . esc_attr__( 'Amount change', 'kuatowc' ) . '" />';
					$res         .= '</div>';
					$change_info = '33';
				} else {
					$res           .= '<span class="kuatowc-settlement-admin card-error">' . esc_html__( 'Error', 'kuatowc' ) . '</span>';
					$res           .= '<div class="kuatowc-settlement-admin-error">';
					$error_message = KUATOWCCodes::errorLabel( $response_data['errorCode'] );
					$res           .= '<div><span class="code">' . esc_html( $response_data['errorCode'] ) . '</span> : <span class="message">' . esc_html( $error_message ) . '</span></div>';
					$res           .= '</div>';
					KUATOWC_Logger::add_log( '[1Change] Error: ' . print_r( $response_data, true ) );
					$change_info = '34';
					$latest_log  = KUATOWC_Payment_Logger::get_latest_log( $order_no, $trans_code, true );
					$amount      = $latest_log['amount'];
				}

				$SLN                       = new KUATOWC_SLN_Connection();
				$params                    = array();
				$param_list                = array();
				$param_list['ycfStrCode']  = $settings['trader_code'];
				$param_list['orderNo']     = $order_no;
				$param_list['requestDate'] = kuatowc_date_format( 'YmdHis' );
				$param_list['password']    = $settings['password'];
				$params['send_url']        = $SLN->get_status_info();
				$params['param_list']      = $param_list;
				$response_info             = $SLN->connection( $params );
				if ( '0' === $response_info['returnCode'] ) {
					$response_info['resultStatus'] = $change_info;
				}
				do_action( 'kuatowc_action_admin_' . $mode, $response_data, $order_no, $trans_code );
				KUATOWC_Payment_Logger::add_log( $response_info, $order_no, $trans_code, $amount );
				$res            .= $this->settlement_history( $order_no, $trans_code );
				$data['status'] = $response_info['result'];
				$data['result'] = $res;
				break;

		}
		wp_send_json( $data );
	}

	/**
	 * Outputs scripts.
	 *
	 */
	public function settlement_scripts() {
		$screen = get_current_screen();
		if ( $screen->id === 'woocommerce_page_wc-orders' ) {
			$order_no = wc_get_order( absint( isset( $_GET['id'] ) ? $_GET['id'] : 0 ) );
			$order    = wc_get_order( $order_no );
			if ( ! $order ) {
				return;
			}
			$order_no = $order->get_id();
		} else {
			global $post, $post_type, $pagenow;
			if ( ! is_object( $post ) ) {
				return;
			}
			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				return;
			}
			if ( 'post.php' !== $pagenow && 'shop_order' !== $post_type ) {
				return;
			}

			$order_no = absint( $post->ID );
			$order    = wc_get_order( $order_no );
		}

		if ( ! $order ) {
			return;
		}

		$nonce                = wp_create_nonce( 'kuatowc-settlement_actions' );
		$customer_id          = $order->get_customer_id();
		$payment_method       = $order->get_payment_method();
		$payment_method_title = $order->get_payment_method_title();
		?>
        <div id="kuatowc-settlement-dialog" title="">
            <div id="kuatowc-settlement-response-loading"></div>
            <fieldset>
                <div id="kuatowc-settlement-response"></div>
                <input type="hidden" id="kuatowc-order_no">
                <input type="hidden" id="kuatowc-order_num">
                <input type="hidden" id="kuatowc-trans_code">
                <input type="hidden" id="kuatowc-error"/>
            </fieldset>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {

                kuatowc_admin_order = {

                    loadingOn: function () {
                        $('#kuatowc-settlement-response-loading').html('<img src="<?php echo admin_url(); ?>images/loading.gif" />');
                    },

                    loadingOff: function () {
                        $('#kuatowc-settlement-response-loading').html('');
                    },

                    getSettlementLatestInfo: function (payment_method) {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            cache: false,
                            dataType: 'json',
                            data: {
                                action: 'kuatowc_settlement_actions',
                                mode: 'get_latest_info',
                                order_no: <?php echo $order_no; ?>,
                                order_num: $('#kuatowc-order_num').val(),
                                trans_code: $('#kuatowc-trans_code').val(),
                                customer_id: <?php echo $customer_id; ?>,
                                payment_method: payment_method,
                                security: '<?php echo $nonce; ?>'
                            }
                        }).done(function (retVal, dataType) {
                            $('#kuatowc-settlement-latest').html(retVal.latest);
                            if (retVal.trans_code != undefined) {
                                var init_id = '#kuatowc-<?php echo $order_no; ?>-' + $('#kuatowc-trans_code').val() + '-1';
                                var new_id = '#kuatowc-<?php echo $order_no; ?>-' + retVal.trans_code + '-1';
                                $(init_id).attr('id', new_id);
                                //$( '#kuatowc-trans_code' ).val( retVal.trans_code );
                            }
                        }).fail(function (retVal) {
                            window.console.log(retVal);
                        });
                        return false;
                    },

					<?php if( 'kuronekoatobarai' === $payment_method ) : ?>
                    getSettlementInfoCard: function () {
                        kuatowc_admin_order.loadingOn();
                        var mode = ('' != $('#kuatowc-error').val()) ? 'error_card' : 'get_card';
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            cache: false,
                            dataType: 'json',
                            data: {
                                action: 'kuatowc_settlement_actions',
                                mode: mode,
                                order_no: <?php echo $order_no; ?>,
                                order_num: $('#kuatowc-order_num').val(),
                                trans_code: $('#kuatowc-trans_code').val(),
                                customer_id: <?php echo $customer_id; ?>,
                                security: '<?php echo $nonce; ?>',
                            }
                        }).done(function (retVal, dataType) {
                            $('#kuatowc-settlement-response').html(retVal.result);
                        }).fail(function (retVal) {
                            window.console.log(retVal);
                        }).always(function (retVal) {
                            kuatowc_admin_order.loadingOff();
                        });
                        return false;
                    },

                    changeSettlementCard: function (amount) {
                        kuatowc_admin_order.loadingOn();

                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            cache: false,
                            dataType: 'json',
                            data: {
                                action: 'kuatowc_settlement_actions',
                                mode: 'change_card',
                                order_no: <?php echo $order_no; ?>,
                                order_num: $('#kuatowc-order_num').val(),
                                trans_code: $('#kuatowc-trans_code').val(),
                                customer_id: <?php echo $customer_id; ?>,
                                amount: amount,
                                security: '<?php echo $nonce; ?>'
                            }
                        }).done(function (retVal, dataType) {
                            $('#kuatowc-settlement-response').html(retVal.result);
                        }).fail(function (retVal) {
                            window.console.log(retVal);
                        }).always(function (retVal) {
                            kuatowc_admin_order.loadingOff();
                        });
                        return false;
                    },

                    deleteSettlementCard: function (amount) {
                        kuatowc_admin_order.loadingOn();

                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            cache: false,
                            dataType: 'json',
                            data: {
                                action: 'kuatowc_settlement_actions',
                                mode: 'delete_card',
                                order_no: <?php echo $order_no; ?>,
                                order_num: $('#kuatowc-order_num').val(),
                                trans_code: $('#kuatowc-trans_code').val(),
                                customer_id: <?php echo $customer_id; ?>,
                                amount: amount,
                                security: '<?php echo $nonce; ?>'
                            }
                        }).done(function (retVal, dataType) {
                            $('#kuatowc-settlement-response').html(retVal.result);
                        }).fail(function (retVal) {
                            window.console.log(retVal);
                        }).always(function (retVal) {
                            kuatowc_admin_order.loadingOff();
                        });
                        return false;
                    },
					<?php endif; ?>
                };

                $('#kuatowc-settlement-dialog').dialog({
                    bgiframe: true,
                    autoOpen: false,
                    height: 'auto',
                    width: 'auto',
                    modal: true,
                    resizable: true,
                    buttons: {
                        "<?php _e( 'Close' ); ?>": function () {
                            $(this).dialog('close');
                        }
                    },
                    open: function () {
						<?php if( 'kuronekoatobarai' === $payment_method ) : ?>
                        kuatowc_admin_order.getSettlementInfoCard();
						<?php endif; ?>
                    },
                    close: function () {
                        kuatowc_admin_order.getSettlementLatestInfo('<?php echo esc_attr( $payment_method ); ?>');
                    }
                });

                $(document).on('click', '.kuatowc-settlement-info', function () {
                    var idname = $(this).attr('id');
                    var ids = idname.split('-');
                    $('#kuatowc-trans_code').val(ids[2]);
                    $('#kuatowc-order_num').val(ids[3]);
                    $('#kuatowc-error').val('');
                    $('#kuatowc-settlement-dialog').dialog('option', 'title', '<?php echo $payment_method_title; ?>');
                    $('#kuatowc-settlement-dialog').dialog('open');
                });

				<?php if( 'kuronekoatobarai' === $payment_method ) : ?>

                $(document).on('click', '#kuatowc-delete-button', function () {
                    if (!confirm("<?php _e( 'Are you sure you want to a processing of cancellation?', 'kuatowc' ); ?>")) {
                        return;
                    }
                    kuatowc_admin_order.deleteSettlementCard($('#kuatowc-amount_change').val());
                });

                $(document).on('click', '#kuatowc-change-button', function () {
                    if ($('#kuatowc-amount_change').val() == $('#kuatowc-amount').val()) {
                        return;
                    }
                    var amount = $('#kuatowc-amount_change').val();
                    if (amount == "" || parseInt(amount) === 0 || !$.isNumeric(amount)) {
                        alert("<?php _e( 'The spending amount format is incorrect. Please enter with numeric value.', 'kuatowc' ); ?>");
                        return;
                    }
                    if (!confirm("<?php _e( 'Are you sure you want to change the spending amount?', 'kuatowc' ); ?>")) {
                        return;
                    }
                    kuatowc_admin_order.changeSettlementCard($('#kuatowc-amount_change').val());
                });
				<?php endif; ?>
            });
        </script>
		<?php
	}
}

new KUATOWC_Admin_Order();
