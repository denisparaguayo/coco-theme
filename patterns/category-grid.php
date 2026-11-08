<?php
/**
 * Title: Explorá Nuestro Catálogo (Categorías Visuales)
 * Slug: coco-theme/category-grid
 * Categories: coco-cherry
 */
$theme_uri = get_template_directory_uri();
?>
<!-- wp:group {"className":"coco-category-showcase-section"} -->
<div class="wp-block-group coco-category-showcase-section">
	<!-- wp:heading {"className":"coco-flank-title"} -->
	<h2 class="wp-block-heading coco-flank-title">Explorá nuestro catálogo</h2>
	<!-- /wp:heading -->

	<!-- wp:columns {"className":"coco-catalog-grid"} -->
	<div class="wp-block-columns coco-catalog-grid">
		<!-- wp:column {"className":"coco-catalog-card"} -->
		<div class="wp-block-column coco-catalog-card">
			<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"coco-catalog-img-container"} -->
			<figure class="wp-block-image size-large coco-catalog-img-container">
				<a href="<?php echo esc_url( home_url( '/categoria-producto/belleza/' ) ); ?>">
					<img src="<?php echo esc_url( $theme_uri . '/assets/images/cat-belleza.jpg' ); ?>" alt="Categoría Belleza - Coco Cherry Store"/>
				</a>
			</figure>
			<!-- /wp:image -->

			<!-- wp:buttons {"className":"coco-catalog-pill-wrap"} -->
			<div class="wp-block-buttons coco-catalog-pill-wrap">
				<!-- wp:button {"className":"coco-catalog-pill-btn"} -->
				<div class="wp-block-button coco-catalog-pill-btn">
					<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/categoria-producto/belleza/' ) ); ?>">Belleza</a>
				</div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"className":"coco-catalog-card"} -->
		<div class="wp-block-column coco-catalog-card">
			<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"coco-catalog-img-container"} -->
			<figure class="wp-block-image size-large coco-catalog-img-container">
				<a href="<?php echo esc_url( home_url( '/categoria-producto/moda/' ) ); ?>">
					<img src="<?php echo esc_url( $theme_uri . '/assets/images/cat-moda.jpg' ); ?>" alt="Categoría Moda - Coco Cherry Store"/>
				</a>
			</figure>
			<!-- /wp:image -->

			<!-- wp:buttons {"className":"coco-catalog-pill-wrap"} -->
			<div class="wp-block-buttons coco-catalog-pill-wrap">
				<!-- wp:button {"className":"coco-catalog-pill-btn"} -->
				<div class="wp-block-button coco-catalog-pill-btn">
					<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/categoria-producto/moda/' ) ); ?>">Moda</a>
				</div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"className":"coco-catalog-card"} -->
		<div class="wp-block-column coco-catalog-card">
			<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"coco-catalog-img-container"} -->
			<figure class="wp-block-image size-large coco-catalog-img-container">
				<a href="<?php echo esc_url( home_url( '/categoria-producto/hogar/' ) ); ?>">
					<img src="<?php echo esc_url( $theme_uri . '/assets/images/cat-hogar.jpg' ); ?>" alt="Categoría Hogar - Coco Cherry Store"/>
				</a>
			</figure>
			<!-- /wp:image -->

			<!-- wp:buttons {"className":"coco-catalog-pill-wrap"} -->
			<div class="wp-block-buttons coco-catalog-pill-wrap">
				<!-- wp:button {"className":"coco-catalog-pill-btn"} -->
				<div class="wp-block-button coco-catalog-pill-btn">
					<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/categoria-producto/hogar/' ) ); ?>">Hogar</a>
				</div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"className":"coco-catalog-card"} -->
		<div class="wp-block-column coco-catalog-card">
			<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"coco-catalog-img-container"} -->
			<figure class="wp-block-image size-large coco-catalog-img-container">
				<a href="<?php echo esc_url( home_url( '/categoria-producto/bijouterie/' ) ); ?>">
					<img src="<?php echo esc_url( $theme_uri . '/assets/images/cat-accesorios.jpg' ); ?>" alt="Categoría Accesorios - Coco Cherry Store"/>
				</a>
			</figure>
			<!-- /wp:image -->

			<!-- wp:buttons {"className":"coco-catalog-pill-wrap"} -->
			<div class="wp-block-buttons coco-catalog-pill-wrap">
				<!-- wp:button {"className":"coco-catalog-pill-btn"} -->
				<div class="wp-block-button coco-catalog-pill-btn">
					<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/categoria-producto/bijouterie/' ) ); ?>">Accesorios</a>
				</div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->