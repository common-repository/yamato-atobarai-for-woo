<?php
/**
 * KUATOWC_SLN_Connection class.
 *
 * Connection with SLN.
 * @package Woo - Kuroneko Atobarai Services
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KUATOWC_SLN_Connection {

	const SEND_SETTLEMENT        = 'https://atobarai.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAARA0010APIAction_execute.action';
	const SEND_SETTLEMENT_RESULT = 'https://atobarai.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAARS0010APIAction_execute.action';
	const SEND_SETTLEMENT_CANCEL = 'https://atobarai.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAACL0010APIAction_execute.action';
	const SEND_SHIP_INFO         = 'https://atobarai.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAASL0010APIAction_execute.action';
	const GET_STATUS_INFO        = 'https://atobarai.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAAST0010APIAction_execute.action';
	const GET_INVOICE_INFO       = 'https://atobarai.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAASD0010APIAction_execute.action';
	const SEND_AMOUNT_CHANGE     = 'https://atobarai.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAAKK0010APIAction_execute.action';
	const SEND_INVOICE_REISSUE   = 'https://atobarai.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAARR0010APIAction_execute.action';
	const SEND_SMS_AUTH          = 'https://atobarai.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAASA0020APIAction_execute.action';


	const TEST_SEND_SETTLEMENT        = 'https://atobarai-test.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAARA0010APIAction_execute.action';
	const TEST_SEND_SETTLEMENT_RESULT = 'https://atobarai-test.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAARS0010APIAction_execute.action';
	const TEST_SEND_SETTLEMENT_CANCEL = 'https://atobarai-test.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAACL0010APIAction_execute.action';
	const TEST_SEND_SHIP_INFO         = 'https://atobarai-test.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAASL0010APIAction_execute.action';
	const TEST_GET_STATUS_INFO        = 'https://atobarai-test.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAAST0010APIAction_execute.action';
	const TEST_GET_INVOICE_INFO       = 'https://atobarai-test.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAASD0010APIAction_execute.action';
	const TEST_SEND_AMOUNT_CHANGE     = 'https://atobarai-test.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAAKK0010APIAction_execute.action';
	const TEST_SEND_INVOICE_REISSUE   = 'https://atobarai-test.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAARR0010APIAction_execute.action';
	const TEST_SEND_SMS_AUTH          = 'https://atobarai-test.kuronekoyamato.co.jp/kuroneko-atobarai-api/KAASA0020APIAction_execute.action';


	const CIPHER_METHOD = 'aes-128-cbc';
	const KEY_AES       = '7Nvpiw5gdB5Z73Pe';
	const KEY_IV        = '4SMIwMoxm7VVhTEC';

	private $testmode;
	private $connection_url;
	private $connection_timeout;

	public function __construct() {
		$settings                 = get_option( 'woocommerce_kuronekoatobarai_settings', array() );
		$this->testmode           = ( ! empty( $settings['testmode'] ) && 'yes' === $settings['testmode'] ) ? true : false;
		$this->connection_url     = '';
		$this->connection_timeout = 60;
	}

	//決済依頼 接続先URL
	public function send_settlement() {
		$url = ( $this->testmode ) ? self::TEST_SEND_SETTLEMENT : self::SEND_SETTLEMENT;

		return $url;
	}

	//決済結果照会 接続先URL
	public function send_settlement_result() {
		$url = ( $this->testmode ) ? self::TEST_SEND_SETTLEMENT_RESULT : self::SEND_SETTLEMENT_RESULT;

		return $url;
	}

	//決済取消依頼 接続先 URL
	public function send_settlement_cancel() {
		$url = ( $this->testmode ) ? self::TEST_SEND_SETTLEMENT_CANCEL : self::SEND_SETTLEMENT_CANCEL;

		return $url;
	}

	//出荷情報依頼 接続先URL
	public function send_ship_info() {
		$url = ( $this->testmode ) ? self::TEST_SEND_SHIP_INFO : self::SEND_SHIP_INFO;

		return $url;
	}

	//取引状況照会 接続先URL
	public function get_status_info() {
		$url = ( $this->testmode ) ? self::TEST_GET_STATUS_INFO : self::GET_STATUS_INFO;

		return $url;
	}

	//請求書印字情報取得 接続先URL
	public function get_invoice_info() {
		$url = ( $this->testmode ) ? self::TEST_GET_INVOICE_INFO : self::GET_INVOICE_INFO;

		return $url;
	}

	//請求金額変更(減額) 接続先URL
	public function send_amount_change() {
		$url = ( $this->testmode ) ? self::TEST_SEND_AMOUNT_CHANGE : self::SEND_AMOUNT_CHANGE;

		return $url;
	}

	//請求書再発行依頼 接続先URL
	public function send_invoice_reissue() {
		$url = ( $this->testmode ) ? self::TEST_SEND_INVOICE_REISSUE : self::SEND_INVOICE_REISSUE;

		return $url;
	}

	//SMS 認証番号判定 接続先 URL
	public function send_sms_auth() {
		$url = ( $this->testmode ) ? self::TEST_SEND_SMS_AUTH : self::SEND_SMS_AUTH;

		return $url;
	}

	public function set_connection_url( $connection_url ) {
		$this->connection_url = $connection_url;
	}


	/**
	 * Connection.
	 *
	 * @param  $params
	 *
	 * @return array
	 */
	public function connection( $params ) {

		$this->set_connection_url( $params['send_url'] );
		//$this->set_connection_timeout( 60 );
		$rValue = $this->send_request( $params['param_list'] );


		return $rValue;
	}

	/**
	 * Request connection.
	 *
	 * @param array Request parameters.
	 *
	 * @return array Response parameters.
	 */
	function send_request( &$param_list = array() ) {

		$rValue = array();

		// Parameter check
		if ( empty( $param_list ) === false ) {

			$url = $this->connection_url;

			$response = $this->wp_post( $url, $param_list );

			$rValue = array_merge( array( 'send_url' => $url ), $this->parseXml2Array( $response ) );
		}

		return $rValue;
	}

	/**
	 * Xmlを連想配列に変換
	 *
	 * @param string $xml
	 *
	 * @return array
	 * @link https://qiita.com/ka_to/items/54fe4d5bb655841f85d8
	 *
	 */
	private function parseXml2Array( $xml ) {
		$xml   = simplexml_load_string( $xml );
		$json  = json_encode( $xml );
		$array = json_decode( $json, true );

		return $array;
	}

	/**
	 * 通信用のベース関数.
	 *
	 * @param string $url URL.
	 * @param mixed $body post data.
	 *
	 * @return string
	 */
	private function wp_post( $url, $body ) {
		$args     = array(
			'body'    => $body,
			'timeout' => '30',
			'headers' => array( 'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8' ),
		);
		$response = wp_remote_post( $url, $args );

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Encrypt value.
	 *
	 * @param array
	 *
	 * @return string Encrypt value.
	 */
	public static function get_encrypt_value( $data ) {
		$data_query    = http_build_query( $data );
		$encrypt_value = openssl_encrypt( $data_query, self::CIPHER_METHOD, self::KEY_AES, false, self::KEY_IV );

		return $encrypt_value;
	}

	/**
	 * Decrypt value.
	 *
	 * @param string Encrypt value.
	 *
	 * @return array
	 */
	public static function get_decrypt_value( $data ) {
		$decrypt_value = openssl_decrypt( $data, self::CIPHER_METHOD, self::KEY_AES, false, self::KEY_IV );

		return $decrypt_value;
	}

	/**
	 * Encrypt value. ( For 3d Secure )
	 *
	 * @param array
	 *
	 * @return string Encrypt value.
	 */
	public static function get_encrypt_value_3dsecure( $data ) {
		$data_query    = http_build_query( $data );
		$encrypt_value = openssl_encrypt( $data_query, self::CIPHER_METHOD, self::KEY_AES, OPENSSL_RAW_DATA, self::KEY_IV );
		$encrypt_value = base64_encode( $encrypt_value );

		return $encrypt_value;
	}

	/**
	 * Decrypt value. ( For 3d Secure )
	 *
	 * @param string Encrypt value.
	 *
	 * @return array
	 */
	public static function get_decrypt_value_3dsecure( $data ) {
		$data          = base64_decode( $data );
		$decrypt_value = openssl_decrypt( $data, self::CIPHER_METHOD, self::KEY_AES, OPENSSL_RAW_DATA, self::KEY_IV );

		return $decrypt_value;
	}
}

