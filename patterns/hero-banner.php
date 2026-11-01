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
<!-- wp:paragraph {"className":"coco-hero-pill-badge"} -->
<p class="coco-hero-pill-badge">📦 PAGO CONTRA ENTREGA EN TODO PARAGUAY</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"coco-hero-headline"} -->
<h1 class="wp-block-heading coco-hero-headline">Productos que<br>hacen tu día mejor</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"coco-hero-subhead"} -->
<p class="coco-hero-subhead">Moda, belleza y hogar, todo en un solo lugar. Comprá fácil, con envío y pago contra entrega.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"coco-hero-cta-wrap"} -->
<div class="wp-block-buttons coco-hero-cta-wrap">
<!-- wp:button {"className":"coco-hero-btn-catalog"} -->
<div class="wp-block-button coco-hero-btn-catalog"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">🛍️ EXPLORAR CATÁLOGO &rarr;</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"coco-hero-luxury-right"} -->
<div class="wp-block-column coco-hero-luxury-right">
<!-- wp:group {"className":"coco-hero-spotlight-box"} -->
<div class="wp-block-group coco-hero-spotlight-box">
<!-- wp:paragraph {"className":"coco-hero-featured-tag"} -->
<p class="coco-hero-featured-tag">PRODUCTO DESTACADO</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"coco-serif-sub"} -->
<p class="coco-serif-sub">Protector Solar</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"coco-serif-title"} -->
<h3 class="wp-block-heading coco-serif-title">Oro 24K &bull; FPS 90+</h3>
<!-- /wp:heading -->

<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"coco-hero-stage-img-block"} -->
<figure class="wp-block-image size-large coco-hero-stage-img-block"><a href="<?php echo esc_url( home_url( '/shop/?s=Protector+Solar' ) ); ?>"><img src="<?php echo esc_url( $theme_uri . '/assets/images/hero-sunscreen-beach.jpg' ); ?>" alt="Protector Solar Oro 24K FPS 90+ - Coco Cherry Store"/></a></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
