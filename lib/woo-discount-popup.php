<?php 
$logo_header_light_id = get_field( 'logo_header_single_light', 'option' ); 
$shop_popup_message = get_field('shop_popup_message', 'option');
$shop_popup_success_message = get_field('shop_popup_success_message', 'option');
$shop_popup_not_eligible_message = get_field('shop_popup_not_eligible_message', 'option');
$shop_hide_popup = get_field('shop_hide_popup', 'option');

$shop_popup_background_image = get_field('shop_popup_background_image', 'option');
if( $shop_popup_background_image ):
    $shop_popup_bg_alt = $shop_popup_background_image['alt'];
    $shop_popup_bg_id = $shop_popup_background_image['ID'];
endif;

$email = wp_get_current_user()->user_email;
$woo_past_orders = has_woo_past_orders($email);
?>

<?php 
	if(!$shop_hide_popup): 
		if( (is_shop() || is_product()) && ($woo_past_orders === false) ):
			if($shop_popup_message):
?>
				<section id="woo-discount-popup" class="woo-discount-popup">
			
					<div class="woo-discount-popup__overlay"></div>
					
					<div class="woo-discount-popup__wrapper">

						<?php if($shop_popup_background_image): ?>
							<div class="popup-bg">
								<picture>
									<source srcset="<?= esc_html( wp_get_attachment_image_url( $shop_popup_bg_id, 'card-image-hige-desktop' ) ) ?>" media="(max-width: 660px)">
								    <source srcset="<?= esc_html( wp_get_attachment_image_url( $shop_popup_bg_id, 'section-background-tablet' ) ) ?>" media="(max-width: 800px)">
								    <source srcset="<?= esc_html( wp_get_attachment_image_url( $shop_popup_bg_id, 'section-background-desktop' ) ) ?>" media="(max-width: 991px)">

								    <img src="<?= esc_html( wp_get_attachment_image_url( $shop_popup_bg_id, 'section-background-tablet' ) ) ?>" alt="<?= esc_attr( hmt_get_attachment_image_alt( $shop_popup_bg_alt ) ) ?>">
								</picture>
							</div>
						<?php endif; ?>

						<div class="close-icon"></div>

						<div class="popup-logo">
							<?= hmt_get_svg_inline( wp_get_attachment_url( $logo_header_light_id ) ); ?>
						</div>

						<div class="popup-content">
							<div class="popup-content__wrapper">
								<?php if( $shop_popup_message ): ?>
							    	<h2><?php echo $shop_popup_message ?></h2>
								<?php endif; ?>
												
								<form id="woo-discount-popup__form" method="post">
									<input class="email" type="text" name="email" placeholder="Email*">
									<button id="woo-discount-popup__submit" class="button yes" type="submit">Submit</button>
								</form> 	
							</div>
						</div>

					</div>
				</section>
<?php 
			endif; 
		endif;
	endif;
?>