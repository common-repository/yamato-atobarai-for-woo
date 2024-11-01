<?php
/**
 * KUATOWC_Install class.
 *
 * @package Woo - Kuroneko Atobarai Services
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class KUATOWC_Install {

	private static $db_updates = array();

	/**
	 * Create tables.
	 *
	 */
	public static function create_tables() {

		global $wpdb;

		$wpdb->hide_errors();


		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}


		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}kuatowc_log';" ) != $wpdb->prefix . 'kuatowc_log' ) {
			$sql = "CREATE TABLE {$wpdb->prefix}kuatowc_log (
        
        log_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        timestamp datetime NOT NULL,
        trans_code char(20) NOT NULL,
        response longtext NOT NULL,
        order_no BIGINT UNSIGNED NULL,
        payment_type char(20) NULL,
        log_type char(20) NULL,
        amount int(20) NULL,
        invoice_status char(15) NULL,
        PRIMARY  KEY  (log_id),
        KEY trans_code (trans_code),
        KEY order_trans (order_no, trans_code),
        KEY order_no (order_no)
        ) $collate;";


			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
//        update_option( 'kuatowc_db_updates', db_updates );
	}
}

