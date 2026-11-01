<?php
/**
 * Title: Explorá Nuestro Catálogo (Categorías Visuales)
 * Slug: coco-theme/category-grid
 * Categories: coco-cherry
 */
$theme_uri = get_template_directory_uri();
?>
<!-- wp:group {"className":"coco-category-showcase-section","style":{"spacing":{"padding":{"top":"3.5rem","bottom":"3.5rem","left":"1.25rem","right":"1.25rem"}}},"backgroundColor":"white","layout":{"type":"constrained"}} -->
<div class="wp-block-group coco-category-showcase-section has-white-background-color has-background" style="padding-top:3.5rem;padding-right:1.25rem;padding-bottom:3.5rem;padding-left:1.25rem">
<!-- wp:html -->
	<div class="coco-flanked-header alignwide">
		<span class="coco-flank-line"></span>
		<h2 class="coco-flank-title">EXPLORÁ NUESTRO CATÁLOGO</h2>
		<span class="coco-flank-line"></span>
	</div>

	<div class="coco-catalog-grid alignwide">
		<!-- Categoría 1: Belleza -->
		<div class="coco-catalog-card">
			<a href="<?php echo esc_url( home_url( '/categoria-producto/belleza/' ) ); ?>" class="coco-catalog-card-link">
				<div class="coco-catalog-img-container">
					<img src="<?php echo esc_url( $theme_uri . '/assets/images/cat-belleza.jpg' ); ?>" alt="Categoría Belleza - Coco Cherry Store" loading="lazy" />
				</div>
				<span class="coco-catalog-pill">
					<span>Belleza</span>
					<span class="coco-pill-arrow">&rarr;</span>
				</span>
			</a>
		</div>

		<!-- Categoría 2: Moda -->
		<div class="coco-catalog-card">
			<a href="<?php echo esc_url( home_url( '/categoria-producto/moda/' ) ); ?>" class="coco-catalog-card-link">
				<div class="coco-catalog-img-container">
					<img src="<?php echo esc_url( $theme_uri . '/assets/images/cat-moda.jpg' ); ?>" alt="Categoría Moda - Coco Cherry Store" loading="lazy" />
				</div>
				<span class="coco-catalog-pill">
					<span>Moda</span>
					<span class="coco-pill-arrow">&rarr;</span>
				</span>
			</a>
		</div>

		<!-- Categoría 3: Hogar -->
		<div class="coco-catalog-card">
			<a href="<?php echo esc_url( home_url( '/categoria-producto/hogar/' ) ); ?>" class="coco-catalog-card-link">
				<div class="coco-catalog-img-container">
					<img src="<?php echo esc_url( $theme_uri . '/assets/images/cat-hogar.jpg' ); ?>" alt="Categoría Hogar - Coco Cherry Store" loading="lazy" />
				</div>
				<span class="coco-catalog-pill">
					<span>Hogar</span>
					<span class="coco-pill-arrow">&rarr;</span>
				</span>
			</a>
		</div>

		<!-- Categoría 4: Accesorios -->
		<div class="coco-catalog-card">
			<a href="<?php echo esc_url( home_url( '/categoria-producto/bijouterie/' ) ); ?>" class="coco-catalog-card-link">
				<div class="coco-catalog-img-container">
					<img src="<?php echo esc_url( $theme_uri . '/assets/images/cat-accesorios.jpg' ); ?>" alt="Categoría Accesorios - Coco Cherry Store" loading="lazy" />
				</div>
				<span class="coco-catalog-pill">
					<span>Accesorios</span>
					<span class="coco-pill-arrow">&rarr;</span>
				</span>
			</a>
		</div>
	</div>
<!-- /wp:html -->
</div>
<!-- /wp:group -->
