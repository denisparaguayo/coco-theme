<?php
/**
 * Funciones y configuración principal del tema Coco Cherry Store (FSE).
 *
 * @package Coco_Theme
 */

defined( 'ABSPATH' ) || exit;

define( 'COCO_THEME_VERSION', wp_get_theme()->get( 'Version' ) ? wp_get_theme()->get( 'Version' ) : '1.0.0' );
define( 'COCO_THEME_DIR', get_template_directory() );
define( 'COCO_THEME_URI', get_template_directory_uri() );

// Cargar el actualizador continuo desde GitHub.
require_once COCO_THEME_DIR . '/inc/class-updater.php';
new \Coco_Theme\Updater();

/**
 * Configuración base del tema y soporte para FSE y WooCommerce.
 */
function coco_theme_setup() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );

	// Soporte completo para WooCommerce.
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 500,
			'single_image_width'    => 800,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 4,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'coco_theme_setup' );

/**
 * Encolar hoja de estilos principal (style.css) y fuentes optimizadas.
 */
function coco_theme_enqueue_assets() {
	wp_enqueue_style(
		'coco-theme-fonts',
		'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'coco-theme-style',
		get_stylesheet_uri(),
		array( 'coco-theme-fonts' ),
		COCO_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'coco_theme_enqueue_assets' );

/**
 * Registrar categoría de patrones propios de Coco Cherry Store.
 */
function coco_theme_register_pattern_categories() {
	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category(
			'coco-cherry',
			array(
				'label'       => __( 'Coco Cherry Store', 'coco-theme' ),
				'description' => __( 'Secciones de alta conversión para Coco Cherry Store con Pago Contra Entrega.', 'coco-theme' ),
			)
		);
	}
}
add_action( 'init', 'coco_theme_register_pattern_categories' );

/**
 * Sello de confianza debajo del botón de Coco COD en la ficha de producto.
 */
function coco_theme_product_cod_guarantees() {
	?>
	<div class="coco-product-cod-guarantees">
		<p style="margin:0 0 6px 0; font-weight:700; color:#15803d; font-size:0.92rem;">
			✓ Compra 100% Segura con Pago Contra Entrega
		</p>
		<p style="margin:0; font-size:0.85rem; color:#3e4c59; line-height:1.45;">
			Recibís tu pedido en la comodidad de tu casa u oficina y abonás en <strong>efectivo o QR</strong> recién al tenerlo en tus manos. Envíos a Asunción, Gran Asunción y todo Paraguay.
		</p>
	</div>
	<?php
}
add_action( 'woocommerce_after_add_to_cart_form', 'coco_theme_product_cod_guarantees', 25 );

/**
 * Botón flotante de WhatsApp con mensaje dinámico según la página o producto actual.
 */
function coco_theme_render_floating_whatsapp() {
	$phone = '595981531503';
	$text  = 'Hola Coco Cherry Store! Quiero hacer una consulta sobre sus productos con pago al recibir.';

	if ( function_exists( 'is_product' ) && is_product() ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product instanceof \WC_Product ) {
			$text = sprintf(
				'Hola Coco Cherry Store! Quiero consultar sobre el producto: %s (%s)',
				$product->get_name(),
				get_permalink( $product->get_id() )
			);
		}
	}

	$wa_url = 'https://wa.me/' . $phone . '?text=' . rawurlencode( $text );
	?>
	<a href="<?php echo esc_url( $wa_url ); ?>" class="coco-floating-wa" target="_blank" rel="noopener noreferrer" aria-label="Consultar por WhatsApp">
		<svg viewBox="0 0 24 24" aria-hidden="true">
			<path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.625.846 5.059 2.284 7.033l-1.492 5.451 5.603-1.469c1.911 1.196 4.169 1.889 6.605 1.889 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/>
		</svg>
		<span>WhatsApp 0981 531503</span>
	</a>
	<?php
}
add_action( 'wp_footer', 'coco_theme_render_floating_whatsapp', 30 );

