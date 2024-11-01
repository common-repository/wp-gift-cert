=== Wordpress Printable Gift Certificate Plugin (WP Gift Cert) ===
Contributors: SuburbanMedia 
Donate link: http://www.suburbanmedia.net/wordpress-plugins/wp-gift-cert/
Tags: gift certificates, paypal, IPN, printable gift certificates
Requires at least: 2.8
Tested up to: 3.1.2
Stable tag: 1.1.1

A plugin that allows you to sell printable gift certificates for your service based business. Requires Paypal/Paypal IPN.

== Description ==
A plugin that allows you to sell printable gift certificates for your service based business through your Wordpress Blog or website. 
Fully integrated with the PayPal IPN framework printable gift certificates are e-mailed to your customers upons successful payment. 
Each certificate includes a QR code allowing you to validate the certificates in the field.

Use of this plugin requires that you have a PayPal account capable of processing IPN transactions. All standard Business accounts include
this functionality.

*******Now with International Currency Support and support for international addresses or no address at all.***********

= Comments and Feedback =
If you have any comments, ideas, or other feedback for this plugin please contact me directly via the [Suburban Media Contact Page](http://www.suburbanmedia.net/contact)</a>

== Installation ==

* 1) Extract the zip file and just drop the contents in the `wp-content/plugins/` directory of your WordPress installation 
* 2) Activate the plugin from Plugins page.
* 3) Edit the Settings to meet your needs.
* 4) Create a certificate to sell, 0 value certificates allow the buyer to specify the amount
* 5) Simply add the shortcode to any page or post to create the option to sell a certificate

== Frequently Asked Questions ==

See our FAQ on the [WP Gift Cert page](http://www.suburbanmedia.net/wordpress-plugins/wp-gift-cert/)

== Screenshots ==

You can see screenshots on the [WP Gift Cert page](http://www.suburbanmedia.net/wordpress-plugins/wp-gift-cert/)

== Changelog ==
= 1.1.1 = 
* Fixed conflict with the Facebook Comments for Wordpress Plugin.

= 1.1.0 = 
* Added Option for Different business names per certificate
* Fixed bug where address defaulted to Australia every time
* Fixed email so it now says from "Business Name" instead of from "Wordpress"
* Fixed default sandbox Paypal URL to use HTTPS

= 1.0.0 =
* Added option to Make Address Optional
* Support for international Currencies (CAD, AUD, EUR, GBP, NZD)
* Added option to export sold certificate data to CSV
* Added option to disable included CSS
* Added option for custom return page
* Restructured the display of the certificate buttons to use definition lists instead of a table
* Added shortcode option to display only the button (no description or amount). [wpgft id=1 button_only="TRUE"]
* Various bugfixes

= 0.9.4 =
* Added additional CSS tags to the button tables to allow for customizations
* e-mails are now sent using wp_mail() allowing you to set smtp settings using wp-mail-smtp or configure-smtp

= 0.9.3 =
* Bugfix - Verify Page now displays correctly when main site page is an index or archive page

= 0.9.2 =
* Bugfix - Return Page now displays correctly when main site page is an index or archive page
* Bugfix - Removed hardcoded database prefix from ipn processing and verify functions

= 0.9.1 = 
* Documentation Update

= 0.9.0 =
* Initial Release

== Upgrade Notice ==
= 1.1.0 =
Upgrade adds functionality and corrects some bugs.

= 1.0.0 =
* Please note, upgrading to version 1.0 will change how your certificate buttons are displayed. They will now utilize a definition list which will slightly change how they show up.

= 0.9.4 = 
* Upgrade may help with users who have had problems sending/receiving the certificate e-mails. If you were having problems upgrade and check, if still experiencing issued install wp-mail-smtp or configure-smtp plugins and setup/verify.

= 0.9.1 =
* Documentation Update Only, Upgrade not necessary.

= 0.9.0 =
Initial Release