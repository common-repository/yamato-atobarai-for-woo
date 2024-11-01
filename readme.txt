=== Kuroneko Daikin Atobarai Service for Woo ===
Contributors: collne
Tags: pay later, payment, woocommerce, kuroneko atobarai
Donate link: https://www.welcart.com/
Requires at least: 5.5
Tested up to: 6.6.2
Requires PHP: 7.4 - 8.1
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==
Kuroneko Daikin Atobarai Service for Woo plugin allows you to accept pay later system via Kuroneko Atobarai system Powered by YAMATO TRANSPORT CO., LTD..
This plugin acts as an addon to add a payment method on WooCommerce checkout page.
On the checkout page, our plugin connects to Kuroneko Atobarai system.

The payment methods provided are as follows.
* Pay later system.
(Available currency is only JPY.)

= Mandatory requirements =
* Account for "Kuroneko Atobarai system".

= About Kuroneko Atobarai system =
"Kuroneko Atobarai system" is a payment service that allows the purchaser to pay the price after confirming the product ordered by the EC/mail order site, and can be used by both individuals and corporations.

* Since the Yamato Group guarantees the risk of uncollected payments, a stable cash flow can be realized.
* There are payment slip type and smartphone type (* separate option contract required) for invoices sent to purchasers.

* Service:
  [https://business.kuronekoyamato.co.jp/service/lineup/payment_atobarai/](https://business.kuronekoyamato.co.jp/service/lineup/payment_atobarai/)
* Privacy Policy:
  [https://www.kuronekoyamato.co.jp/ytc/privacy/](https://www.kuronekoyamato.co.jp/ytc/privacy/)

= User Manual =
https://www.collne.com/dl/woo/kuroneko-daikin-atobarai-service-for-woo.pdf

= About the use of this service =
In order to use this service, you need to apply for it from the below link.
[https://entry-form.kuronekoyamato.co.jp/form/order_entrance.php](https://entry-form.kuronekoyamato.co.jp/form/order_entrance.php)
* The information sent to the dedicated form is YAMATO TRANSPORT CO., LTD., and after the application is completed, the "Kuroneko Atobarai system" representative will contact you.
* To use the "Kuroneko Atobarai system", a review by YAMATO TRANSPORT CO., LTD. is required. Depending on the result of the examination, we may not be able to meet your request.

== Installation ==

= Minimum Requirements =

* WooCommerce 3.5 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of Kuroneko Daikin Atobarai Service for Woo, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type Kuroneko Daikin Atobarai Service for Woo and click Search Plugins. Once you've found our plugin you can view details about it such as the the rating and description. Most importantly, of course, you can install it by simply clicking Install Now.

= Manual installation =

Download page
https://ja.wordpress.org/plugins/yamato-atobarai-for-woo/

1.Go to WordPress Plugins > Add New
2.Click Upload Plugin Zip File
3.Upload the zipped Kuroneko Daikin Atobarai Service for Woo file and click "Upload Now"
4.Go to Installed Plugins
5.Activate the "Kuroneko Daikin Atobarai Service for Woo"


== Frequently Asked Questions ==


== Changelog ==
= 2.0.0 - 2024-9-30 =
* Fixed - Added support for blocking certain payment methods.
* Fixed - Added support for high-performance order storage.

= 1.1.3 - 2024-3-19 =
* Fixed - Fixed an error when deleting orders on the management screen.
* Update - Updated readme.

= 1.1.2 - 2023-10-13 =
* Tested - Tested up to WordPress 6.3.
* Update - Updated readme.

= 1.1.1 - 2023-5-31 =
* Update - Added permission check to management screen form submission.
* Fixed - Payment handling fee issue in the PHP8 environment has been fixed.

= 1.1.0 - 2023-1-30 =
* Fixed - Fixed the bug that an error related to unit price of purchased product.

= 1.0.9 - 2023-1-25 =
* Fixed - Fixed the bug that an error occurs when pay after changing quantity in cart.

= 1.0.8 - 2021-7-26 =
* Tested - Tested up to WordPress 5.8.

= 1.0.7 - 2021-7-1 =
* Fixed - Fixed the bug that an error occurs when changing the amount.

= 1.0.6 - 2020-12-11 =
* Corrected - Corrected the description of "Merchant Code" in the payment settings page.

= 1.0.5 - 2020-12-10 =
* Fixed - Fixed the bug that the close button of the payment dialog in the admin screen is broken when you update to WordPress 5.6.

= 1.0.4 - 2020-11-12 =
* Fixed - Fixed the bug that an error happens if the single-byte tilde contains in the address.

= 1.0.3 - 2020-10-05 =
* Fixed - Fixed the bug that a function error happens when you open Appearance - menus page.

= 1.0.2 - 2020-09-09 =
* Fixed - Fixed the bug that a completion email is delivered to the customer even though the order status cannot be updated in the event of an incorrect voucher number etc.
* Fixed - Fixed the bug that the order status of the Kuroneko delivery can be updated to "completed" from the order list screen.
* Fixed - Fixed the bug that the invoice number was not set in the mail depends on the specific environment.
* Fixed - Fixed the bug that the thank you mail and the order confirmation mail don't sent when the order type is SMS verification.

= 1.0.1 - 2020-08-19 =
* Changed - Changed the plugin name

= 1.0.0 - 2020-04-13 =
* Feature - Yamato Atobarai Payment
