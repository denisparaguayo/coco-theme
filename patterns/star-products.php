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
<!-- wp:group {"className":"coco-star-products-section"} -->
<div class="wp-block-group coco-star-products-section">
	<!-- wp:heading {"className":"coco-flank-title"} -->
	<h2 class="wp-block-heading coco-flank-title">Los Favoritos de Coco Cherry</h2>
	<!-- /wp:heading -->

	<!-- wp:columns {"className":"coco-fav-cards-grid"} -->
	<div class="wp-block-columns coco-fav-cards-grid">
		<!-- wp:column {"className":"coco-fav-card"} -->
		<div class="wp-block-column coco-fav-card">
			<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"coco-fav-card-img-wrap"} -->
			<figure class="wp-block-image size-large coco-fav-card-img-wrap">
				<a href="<?php echo esc_url( $p1_link ); ?>">
					<img src="<?php echo esc_url( $p1_img ); ?>" alt="<?php echo esc_attr( $p1_name ); ?>"/>
				</a>
			</figure>
			<!-- /wp:image -->

			<!-- wp:group {"className":"coco-fav-card-content"} -->
			<div class="wp-block-group coco-fav-card-content">
				<span class="coco-fav-badge coco-fav-badge-gold">Más vendido</span>

				<!-- wp:heading {"level":3,"className":"coco-fav-title"} -->
				<h3 class="wp-block-heading coco-fav-title">
					<a href="<?php echo esc_url( $p1_link ); ?>"><?php echo esc_html( $p1_name ); ?></a>
				</h3>
				<!-- /wp:heading -->

				<!-- wp:buttons {"className":"coco-fav-btn-wrap"} -->
				<div class="wp-block-buttons coco-fav-btn-wrap">
					<!-- wp:button {"className":"coco-fav-btn-block"} -->
					<div class="wp-block-button coco-fav-btn-block">
						<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $p1_link ); ?>">Ver producto</a>
					</div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"className":"coco-fav-card"} -->
		<div class="wp-block-column coco-fav-card">
			<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"coco-fav-card-img-wrap"} -->
			<figure class="wp-block-image size-large coco-fav-card-img-wrap">
				<a href="<?php echo esc_url( $p2_link ); ?>">
					<img src="<?php echo esc_url( $p2_img ); ?>" alt="<?php echo esc_attr( $p2_name ); ?>"/>
				</a>
			</figure>
			<!-- /wp:image -->

			<!-- wp:group {"className":"coco-fav-card-content"} -->
			<div class="wp-block-group coco-fav-card-content">
				<span class="coco-fav-badge coco-fav-badge-pink">Belleza</span>

				<!-- wp:heading {"level":3,"className":"coco-fav-title"} -->
				<h3 class="wp-block-heading coco-fav-title">
					<a href="<?php echo esc_url( $p2_link ); ?>"><?php echo esc_html( $p2_name ); ?></a>
				</h3>
				<!-- /wp:heading -->

				<!-- wp:buttons {"className":"coco-fav-btn-wrap"} -->
				<div class="wp-block-buttons coco-fav-btn-wrap">
					<!-- wp:button {"className":"coco-fav-btn-block"} -->
					<div class="wp-block-button coco-fav-btn-block">
						<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $p2_link ); ?>">Ver producto</a>
					</div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->