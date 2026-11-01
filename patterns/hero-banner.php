<?php
/**
 * Title: Hero Principal Coco Cherry (Diseño Boutique Alta Conversión)
 * Slug: coco-theme/hero-banner
 * Categories: coco-cherry, banner
 */
$theme_uri = get_template_directory_uri();
?>
<!-- wp:group {"className":"coco-hero-luxury-section","style":{"spacing":{"padding":{"top":"3.5rem","bottom":"3.5rem","left":"1.25rem","right":"1.25rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group coco-hero-luxury-section" style="padding-top:3.5rem;padding-right:1.25rem;padding-bottom:3.5rem;padding-left:1.25rem">
<!-- wp:html -->
	<div class="coco-hero-luxury-inner alignwide">
		<!-- Columna Izquierda: Mensaje y Llamado a la Acción -->
		<div class="coco-hero-luxury-left">
			<div class="coco-hero-pill-badge">
				<span class="coco-hero-pill-icon">📦</span>
				<span class="coco-hero-pill-text">PAGO CONTRA ENTREGA EN TODO PARAGUAY</span>
			</div>

			<h1 class="coco-hero-headline">Productos que<br>hacen tu día mejor</h1>

			<p class="coco-hero-subhead">
				Moda, belleza y hogar, todo en un solo lugar. Comprá fácil, con envío y pago contra entrega.
			</p>

			<div class="coco-hero-cta-wrap">
				<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="coco-hero-btn-catalog">
					<svg class="coco-cta-bag-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
						<line x1="3" y1="6" x2="21" y2="6"></line>
						<path d="M16 10a4 4 0 0 1-8 0"></path>
					</svg>
					<span>EXPLORAR CATÁLOGO</span>
					<span class="coco-cta-arrow">&rarr;</span>
				</a>

				<!-- Doodle trazo de corazón dorado sutil -->
				<svg class="coco-hero-heart-doodle" width="140" height="70" viewBox="0 0 140 70" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M10 45 C 35 40, 55 25, 75 16 C 82 8, 98 8, 105 22 C 112 36, 90 56, 75 62 C 60 66, 54 50, 64 38 C 74 26, 90 32, 84 46 C 76 60, 105 52, 130 40" stroke="#E2B268" stroke-width="2.2" stroke-linecap="round" fill="none"/>
				</svg>
			</div>
		</div>

		<!-- Columna Derecha: Producto Destacado (Protector Solar Oro 24K FPS 90+) -->
		<div class="coco-hero-luxury-right">
			<div class="coco-hero-spotlight-box">
				<div class="coco-hero-featured-tag">
					<span>PRODUCTO DESTACADO</span>
				</div>

				<div class="coco-hero-serif-header">
					<span class="coco-serif-sub">Protector Solar</span>
					<span class="coco-serif-title">Oro 24K</span>
					<span class="coco-serif-fps">FPS 90+</span>
				</div>

				<a href="<?php echo esc_url( home_url( '/shop/?s=Protector+Solar' ) ); ?>" class="coco-hero-stage-link" title="Protector Solar Oro 24K FPS 90+">
					<img src="<?php echo esc_url( $theme_uri . '/assets/images/hero-sunscreen-beach.jpg' ); ?>" alt="Protector Solar Oro 24K FPS 90+ - Coco Cherry Store" class="coco-hero-stage-img" loading="eager" />
				</a>
			</div>
		</div>
	</div>
<!-- /wp:html -->
</div>
<!-- /wp:group -->
