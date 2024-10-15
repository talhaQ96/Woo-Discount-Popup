# Woo Discount Popup
This custom popup integrates with WooCommerce to offer users a discount. The popup contains a form that collects the user's email, and upon submission, a coupon is generated in WooCommerce and sent to the user via email for use on WooCommerce products.

## Features :sparkles:
1. **Email Collection:** The popup includes a form with an email field to collect user emails.
2. **Coupon Generation:** Upon submission, a WooCommerce coupon is generated for use on WooCommerce products only.
3. **Targeted Display:** The popup will be displayed only on WooCommerce product-related pages.
4. **First-Time User Limitation:** The popup is only visible to first-time users who have no past orders and have not previously generated a coupon. Only one coupon is allowed per new user.

## Development Approach :hammer_and_wrench:
I created this popup using custom HTML and processed the form with PHP and JavaScript, utilizing the Fetch API and the `wp_ajax` hook.

I leveraged WooCommerce default functions, such as `wc_get_orders` and `wc_get_coupon_id_by_code`, to build custom functions for generating unique coupon codes and identifying whether a user is new or returning.

Additionally, since the website uses Advanced Custom Fields (ACF) for its content foundation, I developed custom ACF fields to manage coupon settings in the backend, including coupon discount amount and coupon type.

## Technology Used :computer:
1. **HTML/CSS:** For form structure and styling.
2. **PHP:** For backend processing of the form and handling AJAX requests.
3. **JavaScript (with jQuery):** For client-side form handling and using the Fetch API.
4. **Advanced Custom Fields (ACF):** Used to create dynamic fields for managing coupon settings, including discount amount, type, and description, directly from the admin panel.

