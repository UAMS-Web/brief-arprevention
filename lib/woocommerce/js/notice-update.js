/**
 * Trigger AJAX request to save state when the WooCommerce notice is dismissed.
 *
 * @version 2.3.0
 *
 * @author Minimal Genesis
 * @license GPL-2.0-or-later
 * @package Brief
 */

jQuery( document ).on(
	'click', '.brief-woocommerce-notice .notice-dismiss', function() {

		jQuery.ajax(
			{
				url: ajaxurl,
				data: {
					action: 'brief_dismiss_woocommerce_notice'
				}
			}
		);

	}
);
