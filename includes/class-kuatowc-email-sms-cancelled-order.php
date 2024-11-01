<?php
/**
 * Class Kuatowc_Email_Sms_Cancelled_Order file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Kuatowc_Email_Sms_Cancelled_Order', false ) ) :

	/**
	 * Cancelled Order Email.
	 *
	 * An email sent to the admin when an order is cancelled.
	 *
	 * @class       WC_Email_Cancelled_Order
	 * @version     2.2.7
	 * @package     WooCommerce/Classes/Emails
	 * @extends     WC_Email
	 */
	class Kuatowc_Email_Sms_Cancelled_Order extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'kuatowc_sms_cancelled_order';
			$this->title          = __( 'クロネコ代金後払いSMSキャンセル', 'kuatowc' );
			$this->customer_email = true;
			$this->description    = __( 'クロネコ代金後払いでSMSキャンセルの時に通知します', 'kuatowc' );
			$this->template_plain = 'emails/plain/kuatowc-sms-cancelled-order.php';
			$this->placeholders   = array(
				'{site_title}'              => $this->get_blogname(),
				'{order_date}'              => '',
				'{order_number}'            => '',
				'{order_billing_full_name}' => '',
			);

			// Call parent constructor.
			parent::__construct();

			// Other settings.
			$this->template_base = KUATOWC_PLUGIN_DIR . '/';
		}

		/**
		 * Get email subject.
		 *
		 * @return string
		 * @since  3.1.0
		 */
		public function get_default_subject() {
			$message = __( '[{site_title}]: {order_billing_full_name}様 #{order_number} ご注文キャンセルのお知らせ', 'kuatowc' );

			return apply_filters( 'kuatowc_sms_cancel_subject_message', $message );
		}

		/**
		 * Get email heading.
		 *
		 * @return string
		 * @since  3.1.0
		 */
		public function get_default_heading() {
			$message = __( 'ご利用誠に有難うございます。SMSでの本人認証が出来なかったため、以下のご注文はキャンセルされました。' . "\r\n"
			               . 'ご不明な点がございましたら、メールにてお問い合わせください。', 'woocommerce' );

			return apply_filters( 'kuatowc_sms_cancel_heading_message', $message );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int $order_id The order ID.
		 * @param WC_Order|false $order Order object.
		 */
		public function trigger( $order_id, $order = false ) {
			$this->setup_locale();

			if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
				$order = wc_get_order( $order_id );
			}

			if ( is_a( $order, 'WC_Order' ) ) {
				$this->object                                    = $order;
				$this->placeholders['{order_date}']              = wc_format_datetime( $this->object->get_date_created() );
				$this->placeholders['{order_number}']            = $this->object->get_order_number();
				$this->placeholders['{order_billing_full_name}'] = $this->object->get_formatted_billing_full_name();
				$this->recipient                                 = $this->object->get_billing_email();
			}
			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}
			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_plain, array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => true,
				'plain_text'    => false,
				'email'         => $this,
			),
				'',
				$this->template_base
			);
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			ob_start();
			wc_get_template(
				$this->template_plain, array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => true,
				'plain_text'    => true,
				'email'         => $this,
			),
				'',
				$this->template_base
			);

			return ob_get_clean();
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'woocommerce' ),
					'default' => 'yes',
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'    => array(
					'title'       => __( 'Email heading', 'woocommerce' ),
					'type'        => 'textarea',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'woocommerce' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
					'default'     => 'plain',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => array( 'plain' => __( 'Plain text', 'woocommerce' ) ),
					'desc_tip'    => true,
				),
			);
		}
	}

endif;

return new Kuatowc_Email_Sms_Cancelled_Order();
