<?php
/**
 * Functions
 *
 * @package Woo - Kuroneko Atobarai Services
 * @since 1.0.0
 */

/**
 * Inittial transaction code.
 *
 * @param int $digit
 *
 * @return string
 */
function kuatowc_init_transaction_code( $digit = 12 ) {
	$transaction_code = apply_filters( 'kuatowc_init_transaction_code', str_repeat( '9', $digit ) );

	return $transaction_code;
}

/**
 * Get order property with compatibility for WC lt 3.0.
 *
 * @param WC_Order $order Order object.
 * @param string $key Order property.
 *
 * @return mixed Value of order property.
 */
function kuatowc_get_order_prop( $order, $key ) {
	$getter = array( $order, 'get_' . $key );

	return is_callable( $getter ) ? call_user_func( $getter ) : $order->{$key};
}

if ( ! function_exists( 'is_edit_cardmember_page' ) ) {

	/**
	 * Checks if is an edit card member page.
	 *
	 * @return bool
	 */
	function is_edit_cardmember_page() {
		global $wp;

		return ( is_page( wc_get_page_id( 'myaccount' ) ) && isset( $wp->query_vars['edit-cardmember'] ) );
	}
}


/**
 * Get Order's ItemName
 *
 * @param WC_Order $order
 * @param $param_list
 * @param $fee
 * @param string $fee
 *
 * @return array
 */

function kuatowc_get_settlement_item_names( $order, $param_list, $fee, $fee_label='手数料（支払手数料含む）' ) {

	$items = $order->get_items();


	$subtotal_amount = 0;
	$count           = 1;
	$other_price     = null;
	$other_item_name = '';

	$number_args = apply_filters(
		'wc_price_args',
		array(
			'ex_tax_label'       => false,
			'currency'           => '',
			'decimal_separator'  => wc_get_price_decimal_separator(),
			'thousand_separator' => wc_get_price_thousand_separator(),
			'decimals'           => wc_get_price_decimals(),
			'price_format'       => get_woocommerce_price_format(),
		)
	);


	foreach ( $items as $i => $cart_row ) {
		$itemName      = $cart_row['name'];
		$quantity      = (int) $cart_row['quantity'];
		$item_subtotal = number_format( (float) $cart_row['subtotal'], $number_args['decimals'], '.', '' );
		$subtotal      = $item_subtotal - $order->get_total_refunded_for_item( $i );

		if ( count( $items ) <= 5 ) {
			$param_list = array_merge( array(
				'itemName' . $count  => kuatowc_formatItemName( $itemName, 30 ),
				'itemCount' . $count => $quantity,
				'unitPrice' . $count => number_format( (float) $subtotal / $quantity, $number_args['decimals'], '.', '' ),
				'subTotal' . $count  => $subtotal,
			), $param_list
			);
		} else {
			if ( $count <= 4 ) {
				$param_list = array_merge( array(
					'itemName' . $count  => kuatowc_formatItemName( $itemName, 30 ),
					'itemCount' . $count => $quantity,
					'unitPrice' . $count => number_format( (float) $subtotal / $quantity, $number_args['decimals'], '.', '' ),
					'subTotal' . $count  => $subtotal,
				), $param_list
				);
			} else {
				if ( $count === 5 ) {
					$other_item_name = kuatowc_formatItemName( $itemName, 24 );
				}
				$other_price += (int) $subtotal;
			}
		}
		$count ++;
		$subtotal_amount += (int) $subtotal;
	}


	$count = count( $items ) <= 5 ? count( $items ) + 1 : 5;

	// その他 6件以上は合算
	if ( $other_price ) {
		$param_list = array_merge( array(
			'itemName' . $count => $other_item_name . '（その他）',
			'subTotal' . $count => (int) $other_price,
		), $param_list
		);
		$count ++;
	}


	$tax             = $order->get_total_tax() - $order->get_total_tax_refunded();
	$used_discount   = $order->get_discount_total();
	$shipping_charge = $order->get_shipping_total() - $order->get_total_shipping_refunded();


	// クーポン使用
	if ( isset( $used_discount ) && is_numeric( $used_discount ) && (int) $used_discount !== 0 ) {
		$param_list = array_merge( array(
			'itemName' . $count => 'クーポン使用',
			'subTotal' . $count => (int) $used_discount * - 1,
		), $param_list
		);
		$count ++;
	}


	// 手数料
	if ( isset( $fee ) && is_numeric( $fee ) && (int) $fee !== 0 ) {
		$param_list = array_merge( array(
			'itemName' . $count => $fee_label,
			'subTotal' . $count => (int) $fee,
		), $param_list
		);
		$count ++;
	}

	// 送料
	if ( isset( $shipping_charge ) && is_numeric( $shipping_charge ) && (int) $shipping_charge !== 0 ) {
		$param_list = array_merge( array(
			'itemName' . $count => '送料',
			'subTotal' . $count => (int) $shipping_charge,
		), $param_list
		);
		$count ++;
	}

	// 消費税
	if ( isset( $tax ) && is_numeric( $tax ) && (int) $tax !== 0 ) {
		$param_list = array_merge( array(
			'itemName' . $count => '消費税',
			'subTotal' . $count => (int) $tax,
		), $param_list
		);
		$count ++;
	}


	return $param_list;

}

/**
 * Get settlement fee.
 *
 */
function get_settlement_fee( $amount ) {
	$fee             = 0;
	$settlement_fees = get_option( 'kuatowc_settlement_fees', array() );
	foreach ( $settlement_fees as $fees ) {
		if ( (float) $fees['amount_from'] <= (float) $amount ) {
			$fee = (int) $fees['fee'];
			if ( empty( $fees['amount_to'] ) || (float) $amount <= (float) $fees['amount_to'] ) {
				break;
			} else {
				$fee = 0;
			}
		}
	}

	return $fee;
}

/**
 * 名前のフォーマット
 *
 * @param string $name Name
 *
 * @return string
 */
function kuatowc_formatName( $name, $len = 30 ) {
	$name = kuatowc_formatTilda( $name );
	return mb_substr( mb_convert_kana( $name, 'KAS', 'UTF-8' ), 0, $len );
}

/**
 * 商品名のフォーマット
 *
 * @param string $name Name
 *
 * @return string
 */
function kuatowc_formatItemName( $name, $len = 30 ) {
	$name = kuatowc_formatTilda( $name );
	return mb_substr( mb_convert_kana( $name, 'KAS', 'UTF-8' ), 0, $len );
}

/**
 * 日付のフォーマット
 *
 * @param $format
 *
 * @return false|string
 */
function kuatowc_date_format( $format ) {
	return gmdate( $format, ( time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) );
}


/**
 * 住所のフォーマット
 *
 * @param string $address 住所
 *
 * @return array
 */
function kuatowc_formatAddress( $address ) {
	$address = kuatowc_formatTilda( $address );
	$address = mb_convert_kana( $address, 'KAS', 'UTF-8' );

	if ( mb_strlen( $address ) <= 25 ) {
		$address_array = array(
			'address1' => $address,
			'address2' => '',
		);
	} else {
		$address_array = array(
			'address1' => mb_substr( $address, 0, 25 ),
			'address2' => mb_substr( $address, 25, 25 ),
		);
	}

	return $address_array;
}

/**
 * ~や〜など、チルダを統一
 *
 * @param string $str value
 *
 * @return string|string[]
 */
function kuatowc_formatTilda( $str ) {
	$str = str_replace( '~', '～', $str );
	$str = str_replace( '～', '～', $str ); // チルダ
	$str = str_replace( '〜', '～', $str ); // 波ダッシュ

	return $str;
}

/**
 * @param $delivery_date
 * @param $shipment_deadline
 *
 * @return false|mixed|string
 */
function shipYmd( $delivery_date, $shipment_deadline ) {

	$preg = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/';
	if ( preg_match( $preg, $delivery_date ) ) {
		return str_replace( '-', '', $delivery_date );
	}
	$limit = $shipment_deadline;

	$limit = $limit ? (int) $limit : 10;

	return gmdate( 'Ymd', strtotime( '+' . $limit . ' day', ( time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) ) );
}


$ids = array(
	'flat_rate',
	'free_shipping',
);

foreach ( $ids as $id ) {
	add_filter( 'woocommerce_shipping_instance_form_fields_' . $id, 'my_woocommerce_shipping_instance_form_field', 100, 1 );
}

function my_woocommerce_shipping_instance_form_field( $array ) {
	if ( ! is_admin() ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	if ( true === is_plugin_active( 'woo-kuronekopayment/kuroneko-pay.php' ) ) {
		return $array;
	}

	return array_merge( $array, array(
			'kuroneko_delivery_check' => array(
				'title'       => __( 'クロネコヤマト配送', 'kuatowc' ),
				'type'        => 'checkbox',
				'label'       => __( '利用する', 'kuatowc' ),
				'description' => 'クロネコヤマト配送を利用します。',
				'default'     => 'no',
				'desc_tip'    => true,
				//'sanitize_callback' => array( $this, 'sanitize_cost' ),
			),
		)
	);
}

add_filter( 'wc_order_statuses', 'kuatowc_order_statuses', 100, 1 );
function kuatowc_order_statuses( $array ) {
	return array_merge( $array, array(
		'wc-sms-processing' => _x( 'SMS認証中', 'Order status', 'kuatowc' ),
	) );
}

add_filter( 'woocommerce_valid_order_statuses_for_payment_complete', 'kuatowc_valid_order_statuses_for_payment_complete', 100, 1 );
function kuatowc_valid_order_statuses_for_payment_complete( $array ) {
	return array_merge( $array, array(
		'sms-processing'
	) );
}

add_filter( 'woocommerce_register_shop_order_post_statuses', 'kuatowc_register_approve_waiting_order_status', 10, 1 );
function kuatowc_register_approve_waiting_order_status( $array ) {
	return array_merge( $array, array(
		'wc-sms-processing' => array(
			'label'                     => _x( 'SMS認証中', 'Order status', 'kuatowc' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: %s: number of orders */
			'label_count'               => _n_noop( 'SMS認証中 <span class="count">(%s)</span>', 'SMS認証中 <span class="count">(%s)</span>', 'kuatowc' ),
		)
	) );
}

add_filter( 'woocommerce_order_has_status', 'kuatowc_order_has_status', 100, 3 );

function kuatowc_order_has_status( $checked_status, $order, $status ) {
	if ( 'sms-processing' === $order->get_status() && isset( $_GET['wc-api'] ) && 'wc_kuroneko_sms' === $_GET['wc-api'] ) {
		return true;
	}

	return $checked_status;
}


