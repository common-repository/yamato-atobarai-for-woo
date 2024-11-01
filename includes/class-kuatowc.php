<?php

class Woo_KuronekoAtobarai {
	/**
	 * Instance of this class.
	 */
	protected static $instance = null;

	/**
	 * Invoice Number.
	 */
	public $kuroneko_order_invoice_number = null;

	/**
	 * WP_Error object.
	 */
	public $error;

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->error = new WP_Error();

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ) );

		register_activation_hook( KUATOWC_PLUGIN_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( KUATOWC_PLUGIN_FILE, array( $this, 'deactivate' ) );

		do_action( 'kuatowc_loaded' );
		add_action( 'woocommerce_account_Kuroneko-sms_endpoint', array( $this, 'kuroneko_sms_endpoint_content' ), 0 );
		add_action( 'woocommerce_email_customer_details', array( $this, 'kuatowc_email_customer_details' ), 10, 4 );
	}

	/**
	 * Return an instance of this class.
	 *
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * This function is hooked into admin_init to affect admin only.
	 *
	 */
	public function admin_init() {
		if ( ! is_plugin_active( KUATOWC_PLUGIN_BASENAME ) ) {
			return;
		}

		include_once( KUATOWC_PLUGIN_DIR . '/includes/class-kuatowc-admin-order.php' );
	}

	/**
	 * Initial processing.
	 *
	 */
	public function init() {
		if ( ! defined( 'WC_VERSION' ) ) {
			return;
		}

		load_plugin_textdomain( 'kuatowc', false, plugin_basename( dirname( KUATOWC_PLUGIN_FILE ) ) . '/languages' );

		require_once( KUATOWC_PLUGIN_DIR . '/includes/class-kuatowc-logger.php' );
		include_once( KUATOWC_PLUGIN_DIR . '/includes/kuatowc-functions.php' );
		require_once( KUATOWC_PLUGIN_DIR . '/includes/class-kuatowc-sln-connection.php' );
		require_once( KUATOWC_PLUGIN_DIR . '/includes/class-kuatowc-payment-logger.php' );
		require_once( KUATOWC_PLUGIN_DIR . '/includes/class-kuatowc-payment-response-handler.php' );
		require_once( KUATOWC_PLUGIN_DIR . '/includes/class-kuatowc-payment-gateway.php' );
		require_once( KUATOWC_PLUGIN_DIR . '/includes/class-kuatowc-exception.php' );
		require_once( KUATOWC_PLUGIN_DIR . '/includes/class-kuatowc-codes.php' );

		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateways' ) );
		add_filter( 'plugin_action_links_' . KUATOWC_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );
		if ( version_compare( WC_VERSION, '3.4', '<' ) ) {
			add_filter( 'woocommerce_get_sections_checkout', array( $this, 'get_sections_checkout' ) );
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_print_styles', array( $this, 'admin_styles' ) );

		add_action( 'init', array( $this, 'add_endpoints' ), 100 );
		add_filter( 'query_vars', array( $this, 'kuroneko_query_vars' ), 0 );

		add_action( 'wp_enqueue_scripts', array( $this, 'kuatowc_sms_scripts' ) );

		// クーロンでSMS認証中を消す
		register_deactivation_hook( __FILE__, array( $this, 'atobarai_sms_event_clear' ) );
		add_action( 'init', array( $this, 'atobarai_sms_schedule' ) );
		add_filter( 'cron_schedules', array( $this, 'kuroneko_atobarai_add_intervals' ) );
		add_action( 'atobarai_sms_event', array( $this, 'do_atobarai_sms_event' ) );
//	    add_filter( 'woocommerce_email_classes', array( $this, 'email_classes' ) );
	}

	function kuatowc_sms_scripts() {
		wp_enqueue_script( 'kuronekoatobarai_sms_scripts', plugins_url( 'assets/js/kuatowc-sms.js', KUATOWC_PLUGIN_FILE ), array(), KUATOWC_VERSION, true );
	}


	/**
	 * エンドポイント追加
	 */
	public function add_endpoints() {
		add_rewrite_endpoint( 'Kuroneko-sms', EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}


	/**
	 * カスタムクエリの登録
	 * マイアカウントの承認ページと承認詳細ページのためにWordPressに登録
	 *
	 * @param array $vars query_vars
	 *
	 * @return array
	 */
	public function kuroneko_query_vars( $vars ) {
		$vars[] = 'Kuroneko-sms';

		return $vars;
	}


	public function kuroneko_sms_endpoint_content() {
		require_once( KUATOWC_PLUGIN_DIR . '/templates/kuroneko-sms.php' );
	}


	/**
	 * Enqueue admin scripts.
	 *
	 */
	public function admin_scripts() {
		global $current_section;
		$screen    = get_current_screen();
		$screen_id = ( $screen ) ? $screen->id : '';

		switch ( $screen_id ) {
			case 'woocommerce_page_wc-settings':
				if ( ! empty( $current_section ) && 'kuronekoatobarai' == $current_section ) {
					wp_register_script( 'kuronekoatobarai_admin_scripts', plugins_url( 'assets/js/kuatowc-admin.js', KUATOWC_PLUGIN_FILE ), array(), KUATOWC_VERSION, true );
					$kuronekoatobarai_admin_params          = array();
					$kuronekoatobarai_admin_params['label'] = array(
						'amount_from' => __( 'Total Amount (From:)', 'kuatowc' ),
						'amount_to'   => __( 'Total Amount (To:)', 'kuatowc' ),
						'fee'         => __( 'Settlement fee (tax included)', 'kuatowc' ),
						'insert_row'  => __( 'Insert row', 'woocommerce' ),
						'remove_row'  => __( 'Remove selected row(s)', 'woocommerce' ),
					);
					$settings                               = get_option( 'woocommerce_kuronekoatobarai_settings', array() );
					if ( isset( $settings['settlement_fee'] ) && 'yes' === $settings['settlement_fee'] ) {
						$kuronekoatobarai_admin_params['fees'] = get_option( 'kuatowc_settlement_fees', array() );
					}
					wp_localize_script( 'kuronekoatobarai_admin_scripts', 'kuronekoatobarai_admin_params', apply_filters( 'kuronekoatobarai_admin_params', $kuronekoatobarai_admin_params ) );
					wp_enqueue_script( 'kuronekoatobarai_admin_scripts' );
				}
				break;
			case 'shop_order':
			case 'woocommerce_page_wc-orders':
				wp_enqueue_script( 'jquery-ui-dialog' );
				break;
		}
	}

	/**
	 * Enqueue admin styles.
	 *
	 */
	function admin_styles() {
		$screen    = get_current_screen();
		$screen_id = ( $screen ) ? $screen->id : '';

		wp_enqueue_style( 'kuronekoatobarai_admin_styles', plugins_url( 'assets/css/kuatowc-admin.css', KUATOWC_PLUGIN_FILE ), array(), KUATOWC_VERSION );

		if ( 'shop_order' === $screen_id || 'woocommerce_page_wc-orders' === $screen_id) {
			global $wp_scripts;
			$ui = $wp_scripts->query( 'jquery-ui-core' );
			$ui_themes = apply_filters( 'kupaywc_jquery_ui_themes', 'smoothness' );
			wp_enqueue_style( 'jquery-ui-kuroneko', "//code.jquery.com/ui/{$ui->ver}/themes/{$ui_themes}/jquery-ui.css" );
		}
	}

	/**
	 * Run when plugin is activated.
	 *
	 */
	function activate() {
		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && ! is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
			deactivate_plugins( KUATOWC_PLUGIN_BASENAME );
		} else {
			require_once( KUATOWC_PLUGIN_DIR . '/includes/class-kuatowc-install.php' );
			KUATOWC_Install::create_tables();
		}
	}

	/**
	 * Run when plugin is deactivated.
	 *
	 */
	function deactivate() {

	}

	/**
	 * Add the gateways to WooCommerce.
	 *
	 */
	public function add_gateways( $methods ) {
		$methods[] = 'WC_Gateway_KuronekoAtobarai';

		return $methods;
	}

	/**
	 * Adds plugin action links.
	 *
	 */
	public function plugin_action_links( $links ) {
		$plugin_links = array(
			'<a href="admin.php?page=wc-settings&tab=checkout&section=kuronekoatobarai">' . esc_html__( 'Settings', 'kuatowc' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Modifies the order of the gateways displayed in admin.
	 *
	 */
	public function get_sections_checkout( $sections ) {
		unset( $sections['kuronekoatobarai'] );
		$sections['kuronekoatobarai'] = __( 'KuronoekoAtobarai', 'kuatowc' );

		return $sections;
	}

	/**
	 * wp_schedule_event
	 */
	public function atobarai_sms_schedule() {
		if ( wp_next_scheduled( 'atobarai_sms_event' ) ) {
			return;
		}
		wp_schedule_event( time(), 'every_30min', 'atobarai_sms_event' );
	}

	/**
	 * cron_schedules
	 *
	 * @param $schedules
	 *
	 * @return mixed
	 */
	public function kuroneko_atobarai_add_intervals( $schedules ) {
		$schedules['every_30min'] = array( // 「every_30min」という名前でスケジュール登録
			'interval' => 1800, // 実行間隔 この場合は30分なので、60(秒) * 30(分) = 1800(秒)
			'display'  => __( 'Every 30 minutes' ) // 30分おきに実行
		);

		return $schedules;
	}

	/**
	 * do atobarai_sms_event
	 */
	public function do_atobarai_sms_event() {
		global $wpdb;
		$query  = new WC_Order_Query( array(
			'status'        => 'sms-processing',
			'post_modified' => '<' . gmdate( 'Y-m-d H:i:s', strtotime( '-10 minute', ( time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) ) )
		) );
		$orders = $query->get_orders();
//        $mailer = ( WooCommerce::instance() )->mailer();
//        $mailer_object = $mailer->emails[ 'Kuatowc_Email_Sms_Cancelled_Order' ];
		foreach ( $orders as $order ) {
			/** @var WC_Order $order */
			$order->update_status( 'cancelled' );
			$order->save();
//            $mailer_object->trigger( $order->get_order_number(), $order );
		}
	}

	public function atobarai_sms_event_clear() {
		wp_clear_scheduled_hook( 'atobarai_sms_event' );
	}

	public function email_classes( $emails ) {
		$emails['Kuatowc_Email_Sms_Cancelled_Order'] = include 'class-kuatowc-email-sms-cancelled-order.php';

		return $emails;
	}

	/**
	 * Payment detail in emails.
	 *
	 * @param WC_Order $order
	 * @param $sent_to_admin
	 * @param $plain_text
	 * @param $email
	 */
	public function kuatowc_email_customer_details( $order, $sent_to_admin, $plain_text, $email ) {

		if ( 'kuronekoatobarai' !== $order->get_payment_method() ) {
			return;
		}

		$is_delivery_kuroneko = false;
		foreach ( $order->get_shipping_methods() as $shipping ) {
			/** @var WC_Order_Item_Shipping $shipping */
			$woocommerce_settings = get_option( 'woocommerce_' . $shipping->get_method_id() . '_' . $shipping->get_instance_id() . '_settings' );
			if ( isset( $woocommerce_settings['kuroneko_delivery_check'] ) && 'yes' === $woocommerce_settings['kuroneko_delivery_check'] ) {
				$is_delivery_kuroneko = true;
			}
		}

		$ship_no = $is_delivery_kuroneko ? $order->get_meta( 'kuroneko_order_invoice_number', true ) : '';
		//ポストメタに送り状番号が無い場合はグローバル変数から取得する
		global $kuatowc;
		if ( $is_delivery_kuroneko && ! $ship_no ) {
			$ship_no = $kuatowc->kuroneko_order_invoice_number;
		}
		if ( ! $ship_no ) {
			return;
		}

		if ( $plain_text ) {
			echo esc_html__( 'Kuroneko Invoice Number', 'kuatowc' ) . "\n\n";
			echo esc_html( $ship_no ) . "\n\n";
		} else {
			$text_align = ( is_rtl() ) ? 'right' : 'left';
			$email      = '<table style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;border-spacing: 0;" border="0">
				<tr>
					<th scope="row" colspan="2" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">' . esc_html__( 'Kuroneko Invoice Number', 'kuatowc' ) . ':</th>
					<td style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left"><span>' . esc_html( $ship_no ) . '</span></td>
				</tr>
			</table>';
			echo $email;
		}
	}
}
