<?php

/**
 * Email Form Handler
 */
function email_form_handler() {
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
  
		$email = sanitize_text_field($_POST['email']);
		$woo_coupon_prefix = 'welcome-';

		$woo_past_orders = has_woo_past_orders($email);
		$woo_user_coupon = has_user_generated_woo_coupon($email, $woo_coupon_prefix);

		if (empty($email)) {
			wp_die(json_encode('email_required'));
		}

		elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strpos($email, '+') !== false) {
			wp_die(json_encode('email_invalid'));
		}

		elseif ($woo_past_orders === true || $woo_user_coupon === true) {
			wp_die(json_encode('not_allowed'));
		}

		else {
			$woo_coupon_code = strtoupper(create_woo_coupon($email, $woo_coupon_prefix));
			send_user_email($email, $woo_coupon_code);
			wp_die(json_encode('success'));
		}
	}
}
add_action('wp_ajax_email_form_handler', 'email_form_handler');
add_action('wp_ajax_nopriv_email_form_handler', 'email_form_handler'); // for non-logged in users


/**
 * Woo-Commerce: Check if User has Past Order
 */
function has_woo_past_orders($email) {
	$args = array(
		'limit'    => -1,
		'return'   => 'ids',
		'customer' => $email
	);

	$orders = wc_get_orders($args);

	return !empty($orders) ? true : false;
}


/**
 * Woo-Commerce: Check if User has Already Generated a Coupon Code
 */
function has_user_generated_woo_coupon($email, $coupon_prefix) {
	$coupons = get_posts(array(
		'post_type'  => 'shop_coupon',
		'posts_per_page' => -1,
		'post_status' => 'publish'
	));

	foreach ($coupons as $coupon) {
		$coupon_id = $coupon->ID;
		$coupon_title = $coupon->post_title;

		if (str_starts_with($coupon_title, $coupon_prefix)) {
			$customer_email = get_post_meta($coupon_id, 'customer_email', true);

			if(in_array($email, $customer_email)) {
				return true;
			}
		}
	}

	return false;
}


/**
 * Generate Woo-Commerce Coupon Code 
 */
function create_woo_coupon($email, $coupon_prefix) {

	$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$characters_length = strlen($characters);
	$unique_coupon_code_found = false;

	$coupon_description = get_field('coupon_description', 'option');
	$coupon_amount = get_field('coupon_amount', 'option');
	$coupon_type = get_field('coupon_type', 'option');

	while (!$unique_coupon_code_found) {
		$coupon_code = '';
		
		for ($i = 0; $i < 6; $i++) {
			$coupon_code .= $characters[rand(0, $characters_length - 1)];
		}

		$coupon_code = $coupon_prefix . $coupon_code;

		if (!wc_get_coupon_id_by_code($coupon_code)) {
			$unique_coupon_code_found = true;
		}
	}

	$coupon = new WC_Coupon();
	$coupon->set_code($coupon_code);
	$coupon->set_amount($coupon_amount);
	$coupon->set_discount_type($coupon_type);
	$coupon->set_description($coupon_description);
	$coupon->set_usage_limit(1);
	$coupon->set_email_restrictions($email);
	$coupon->save();

	return $coupon_code;
}


/**
 * Send Coupon Code to User
 */
function send_user_email($email, $woo_coupon_code){
	ob_start();
	include(locate_template('lib/emails/woo-discount-popup-email-template.php'));
	$message = ob_get_clean();
	$email_subject = get_field('email_subject', 'option');
	$sender_name = get_field('wp_mail_sender_name', 'option');
	$sender_email_address = get_field('wp_mail_sender_email_address', 'option');

	wp_mail (
		$email,
		$email_subject,
		$message,
		array(
			'Content-Type: text/html; charset=UTF-8',
			'From: {$sender_name} <{$sender_email_address}>',
		)
	);
}