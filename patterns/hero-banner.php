<?php
/**
 * Title: Hero Principal Coco Cherry (Diseño Boutique Alta Conversión)
 * Slug: coco-theme/hero-banner
 * Categories: coco-cherry, banner
 */
$theme_uri = get_template_directory_uri();
?>
<!-- wp:group {"className":"coco-hero-luxury-section"} -->
<div class="wp-block-group coco-hero-luxury-section">
	<!-- wp:columns {"className":"coco-hero-luxury-inner"} -->
	<div class="wp-block-columns coco-hero-luxury-inner">
		<!-- wp:column {"className":"coco-hero-luxury-left"} -->
		<div class="wp-block-column coco-hero-luxury-left">
			<span class="coco-hero-pill-badge">Envío y Pago Contra Entrega en todo Paraguay</span>

			<!-- wp:heading {"level":1,"className":"coco-hero-headline"} -->
			<h1 class="wp-block-heading coco-hero-headline">Tendencias en moda, belleza y hogar con entrega en puerta</h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"coco-hero-subhead"} -->
			<p class="coco-hero-subhead">Elegí online y aboná en efectivo o QR recién cuando el paquete llegue a tus manos en cualquier punto del país.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"className":"coco-hero-cta-wrap"} -->
			<div class="wp-block-buttons coco-hero-cta-wrap">
				<!-- wp:button {"className":"coco-hero-btn-catalog"} -->
				<div class="wp-block-button coco-hero-btn-catalog">
					<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Ver Catálogo</a>
				</div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"className":"coco-hero-luxury-right"} -->
		<div class="wp-block-column coco-hero-luxury-right">
			<!-- wp:group {"className":"coco-hero-spotlight-box"} -->
			<div class="wp-block-group coco-hero-spotlight-box">
				<div class="coco-hero-spotlight-header">
					<span class="coco-hero-featured-tag">Colección Destacada</span>
					<span class="coco-hero-spotlight-badge">Pago en Puerta</span>
				</div>

				<div class="coco-hero-spotlight-meta">
					<span class="coco-hero-spotlight-cat">Cuidado Facial</span>
					<h3 class="wp-block-heading coco-hero-spotlight-title">Protector Solar Oro 24K FPS 90+</h3>
					<div class="coco-hero-spotlight-price-row">
						<span class="coco-hero-spotlight-price">85.000 Gs.</span>
						<span class="coco-hero-spotlight-guarantee">Efectivo o QR al recibir</span>
					</div>
				</div>

				<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"coco-hero-stage-img-block"} -->
				<figure class="wp-block-image size-large coco-hero-stage-img-block">
					<a href="<?php echo esc_url( home_url( '/shop/?s=Protector+Solar' ) ); ?>">
						<img src="<?php echo esc_url( $theme_uri . '/assets/images/hero-sunscreen-beach.jpg' ); ?>" alt="Protector Solar Oro 24K FPS 90+ - Coco Cherry Store"/>
					</a>
				</figure>
				<!-- /wp:image -->

				<a class="coco-hero-spotlight-action" href="<?php echo esc_url( home_url( '/shop/?s=Protector+Solar' ) ); ?>">
					Pedir al recibir en casa
				</a>
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->