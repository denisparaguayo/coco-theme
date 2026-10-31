<?php
/**
 * Title: Los Favoritos de Coco Cherry (Pago Contra Entrega)
 * Slug: coco-theme/star-products
 * Categories: coco-cherry, featured
 */

$theme_uri = get_template_directory_uri();
$p1_name = 'Protector Solar Oro 24K FPS 90+';
$p1_link = home_url( '/shop/?s=Protector+Solar' );
$p1_img  = $theme_uri . '/assets/images/fav-protector.jpg';

$p2_name = 'Rizador de Pestañas Térmico y Perfilador de Cejas 2 en 1 Recargable USB';
$p2_link = home_url( '/shop/?s=Rizador' );
$p2_img  = $theme_uri . '/assets/images/fav-rizador.jpg';

if ( function_exists( 'wc_get_products' ) ) {
	$q1 = wc_get_products(
		array(
			'limit'  => 1,
			'status' => 'publish',
			's'      => 'Protector Solar',
		)
	);
	if ( ! empty( $q1 ) ) {
		$prod1 = $q1[0];
		$p1_name = $prod1->get_name();
		$p1_link = get_permalink( $prod1->get_id() );
		if ( $prod1->get_image_id() ) {
			$img_src = wp_get_attachment_image_url( $prod1->get_image_id(), 'large' );
			if ( $img_src ) {
				$p1_img = $img_src;
			}
		}
	}

	$q2 = wc_get_products(
		array(
			'limit'  => 1,
			'status' => 'publish',
			's'      => 'Rizador',
		)
	);
	if ( ! empty( $q2 ) ) {
		$prod2 = $q2[0];
		$p2_name = $prod2->get_name();
		$p2_link = get_permalink( $prod2->get_id() );
		if ( $prod2->get_image_id() ) {
			$img_src2 = wp_get_attachment_image_url( $prod2->get_image_id(), 'large' );
			if ( $img_src2 ) {
				$p2_img = $img_src2;
			}
		}
	}
}
?>
<!-- wp:group {"className":"coco-star-products-section","style":{"spacing":{"padding":{"top":"3.5rem","bottom":"2.5rem","left":"1.25rem","right":"1.25rem"}}},"backgroundColor":"white","layout":{"type":"constrained"}} -->
<div class="wp-block-group coco-star-products-section has-white-background-color has-background" style="padding-top:3.5rem;padding-right:1.25rem;padding-bottom:2.5rem;padding-left:1.25rem">
	<div class="coco-flanked-header alignwide">
		<span class="coco-flank-line"></span>
		<h2 class="coco-flank-title">LOS FAVORITOS DE COCO CHERRY</h2>
		<span class="coco-flank-line"></span>
	</div>

	<div class="coco-fav-cards-grid alignwide">
		<!-- Card 1: Protector Solar -->
		<div class="coco-fav-card">
			<div class="coco-fav-card-img-wrap">
				<span class="coco-fav-badge coco-fav-badge-gold">&#9733; MÁS VENDIDO</span>
				<a href="<?php echo esc_url( $p1_link ); ?>" tabindex="-1">
					<img src="<?php echo esc_url( $p1_img ); ?>" alt="<?php echo esc_attr( $p1_name ); ?>" loading="lazy" />
				</a>
			</div>
			<div class="coco-fav-card-content">
				<h3 class="coco-fav-title">
					<a href="<?php echo esc_url( $p1_link ); ?>"><?php echo esc_html( $p1_name ); ?></a>
				</h3>
				<div class="coco-fav-gold-line"></div>
				<a href="<?php echo esc_url( $p1_link ); ?>" class="coco-fav-btn">
					<span>VER PRODUCTO</span>
					<span class="coco-btn-arrow">&rarr;</span>
				</a>
			</div>
		</div>

		<!-- Card 2: Rizador de Pestañas -->
		<div class="coco-fav-card">
			<div class="coco-fav-card-img-wrap">
				<span class="coco-fav-badge coco-fav-badge-pink">&#127800; BELLEZA</span>
				<a href="<?php echo esc_url( $p2_link ); ?>" tabindex="-1">
					<img src="<?php echo esc_url( $p2_img ); ?>" alt="<?php echo esc_attr( $p2_name ); ?>" loading="lazy" />
				</a>
			</div>
			<div class="coco-fav-card-content">
				<h3 class="coco-fav-title">
					<a href="<?php echo esc_url( $p2_link ); ?>"><?php echo esc_html( $p2_name ); ?></a>
				</h3>
				<div class="coco-fav-gold-line"></div>
				<a href="<?php echo esc_url( $p2_link ); ?>" class="coco-fav-btn">
					<span>VER PRODUCTO</span>
					<span class="coco-btn-arrow">&rarr;</span>
				</a>
			</div>
		</div>
	</div>
</div>
<!-- /wp:group -->
