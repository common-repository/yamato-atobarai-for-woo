<?php
/**
 * kuatowc_Payment_Logger class.
 *
 * @package Woo - Kuroneko Atobarai Services
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KUATOWC_Payment_Logger {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		add_action( 'woocommerce_delete_order_item', array( $this, 'clear_log' ) );
		add_action( 'woocommerce_deleted_order_items', array( $this, 'clear_log' ) );
	}

	/**
	 * Add log data.
	 *
	 * @param array $response Log message.
	 * @param int $order_no
	 * @param string $trans_code
	 * @param int $amount
	 */
	public static function add_log( $response, $order_no, $trans_code, $amount ) {
		global $wpdb;

		if ( empty( $timestamp ) ) {
			$timestamp = current_time( 'mysql' );
		}

		$query = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}kuatowc_log ( `timestamp`, `trans_code`,  `response`, `order_no`,`amount` ) VALUES ( %s, %s, %s, %s ,%d)",
			$timestamp,
			$trans_code,
			json_encode( $response ),
			$order_no,
			$amount
		);
		$res   = $wpdb->query( $query );
	}

	/**
	 * Get log data.
	 *
	 * @param int $order_no
	 * @param string $trans_code
	 */
	public static function get_log( $order_no, $trans_code ) {
		global $wpdb;

		$query    = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}kuatowc_log WHERE `order_no` = %d AND `trans_code` = %s ORDER BY `timestamp` DESC",
			$order_no,
			$trans_code
		);
		$log_data = $wpdb->get_results( $query, ARRAY_A );

		return $log_data;
	}

	/**
	 * Get the latest log data.
	 *
	 * @param int $order_no
	 * @param string $trans_code
	 */
	public static function get_latest_log( $order_no, $trans_code, $all = false ) {
		global $wpdb;

		$latest_log = array();
		$log_data   = self::get_log( $order_no, $trans_code );

		if ( $log_data ) {
			if ( $all ) {
				$latest_log = $log_data[0];
			} else {
				foreach ( (array) $log_data as $data ) {
					$response = json_decode( $data['response'], true );
					if ( isset( $response['returnCode'] ) ) {
						if ( '0' === $response['returnCode'] ) {
							$latest_log = $data;
							break;
						}
					}
				}
			}
		}

		return $latest_log;
	}

	/**
	 * 決済のステータスラベルを返す
	 * 金額変更の時はその前のステータスを返す
	 *
	 * @param $order_id
	 * @param $trans_code
	 *
	 * @return string
	 */
	public static function get_label_status( $order_id, $trans_code ) {
		global $wpdb;

		$logs         = self::get_log( $order_id, $trans_code );
		$status_label = '';
		$flg_change_ok = false;
		if ( $logs ) {
			foreach ( (array) $logs as $log ) {
				$response = json_decode( $log['response'], true );
				if ( isset( $response['resultStatus'] ) ) {
					$statusInfo = $response['resultStatus'];
					if ( '33' !== $statusInfo ) {
						if($flg_change_ok && '34' === $statusInfo ){
							continue;
						}
						$status_label = isset( KUATOWCCodes::$StatusInfos[ $statusInfo ] ) ? KUATOWCCodes::$StatusInfos[ $statusInfo ] : '';
						if ( $status_label ) {
							break;
						}
					} else {
						$flg_change_ok = true;
					}
				}
			}
		}

		return $status_label;
	}

	/**
	 * 決済のステータスの数字を返す
	 * 金額変更の時はその前のステータスを返す
	 *
	 * @param $order_id
	 * @param $trans_code
	 *
	 * @return string
	 */
	public static function get_result_status_number( $order_id, $trans_code ) {
		global $wpdb;

		$logs          = self::get_log( $order_id, $trans_code );
		$status_number = '';
		$flg_change_ok = false;
		if ( $logs ) {
			foreach ( (array) $logs as $log ) {
				$response = json_decode( $log['response'], true );
				if ( isset( $response['resultStatus'] ) ) {
					$status_number = $response['resultStatus'];
					if ( '33' !== $status_number ) {
						if($flg_change_ok && '34' === $status_number ){
							continue;
						}
						if ( $status_number ) {
							break;
						}
					} else {
						$flg_change_ok = true;
					}
				}
			}
		}

		return $status_number;
	}

	/**
	 * Clear log data.
	 *
	 * @param int $order_id
	 */
	public function clear_log( $order_id ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}kuatowc_log WHERE `order_id` = %d", $order_id ) );
	}
}

new KUATOWC_Payment_Logger();
