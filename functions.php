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
	add_theme_support( 'title-tag' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );

	// Soporte completo para logotipo personalizado y flexible.
	add_theme_support(
		'custom-logo',
		array(
			'height'               => 100,
			'width'                => 320,
			'flex-height'          => true,
			'flex-width'           => true,
			'unlink-homepage-logo' => false,
		)
	);

	// Habilitar gestión de Menús clásicos y ubicaciones en Apariencia > Menús y Personalizador.
	add_theme_support( 'menus' );
	register_nav_menus(
		array(
			'primary' => __( 'Menú Principal (Cabecera)', 'coco-theme' ),
			'footer'  => __( 'Menú de Pie de Página (Footer)', 'coco-theme' ),
		)
	);

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
	// Desactivamos zoom agresivo para mantener una navegación limpia y estilizada.
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'coco_theme_setup' );

/**
 * Habilitar soporte para subir y previsualizar imágenes SVG en la biblioteca de medios.
 */
function coco_theme_mime_types( $mimes ) {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'coco_theme_mime_types' );

function coco_theme_fix_svg_filetype( $data, $file, $filename, $mimes ) {
	$ext = isset( $data['ext'] ) ? $data['ext'] : '';
	if ( strlen( $ext ) < 1 ) {
		$exploded = explode( '.', $filename );
		$ext      = strtolower( end( $exploded ) );
	}
	if ( 'svg' === $ext ) {
		$data['type'] = 'image/svg+xml';
		$data['ext']  = 'svg';
	} elseif ( 'svgz' === $ext ) {
		$data['type'] = 'image/svg+xml';
		$data['ext']  = 'svgz';
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'coco_theme_fix_svg_filetype', 10, 4 );

/**
 * Asignar dimensiones intrínsecas a archivos SVG en la biblioteca de medios
 * para que WordPress, el Personalizador y Gutenberg los rendericen correctamente.
 */
function coco_theme_fix_svg_size_attributes( $image, $attachment_id, $size, $icon ) {
	if ( is_array( $image ) && ( empty( $image[1] ) || empty( $image[2] ) || $image[1] <= 1 || $image[2] <= 1 ) ) {
		$mime = get_post_mime_type( $attachment_id );
		if ( 'image/svg+xml' === $mime || ( ! empty( $image[0] ) && false !== strpos( strtolower( $image[0] ), '.svg' ) ) ) {
			$image[1] = 1046;
			$image[2] = 515;
		}
	}
	return $image;
}
add_filter( 'wp_get_attachment_image_src', 'coco_theme_fix_svg_size_attributes', 10, 4 );

/**
 * Auto-detectar y sincronizar el logo de Coco Cherry Store (incluyendo COCO-NEW-LOGO.svg)
 * para que nunca quede vacío tras activar el tema.
 */
function coco_theme_ensure_site_logo() {
	$current_logo = get_theme_mod( 'custom_logo' );
	$site_logo    = get_option( 'site_logo' );

	if ( ! empty( $current_logo ) && ! empty( $site_logo ) ) {
		return;
	}

	if ( empty( $current_logo ) && ! empty( $site_logo ) ) {
		set_theme_mod( 'custom_logo', $site_logo );
		return;
	}

	if ( ! empty( $current_logo ) && empty( $site_logo ) ) {
		update_option( 'site_logo', $current_logo );
		return;
	}

	// 1. Buscar en mods de temas anteriores (ej. Kadence, Astra, Storefront).
	global $wpdb;
	$prev_mods = $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'theme_mods_%' AND option_name != 'theme_mods_coco-theme' LIMIT 15" );
	if ( ! empty( $prev_mods ) ) {
		foreach ( $prev_mods as $row ) {
			$data = maybe_unserialize( $row->option_value );
			if ( is_array( $data ) && ! empty( $data['custom_logo'] ) ) {
				$logo_id = intval( $data['custom_logo'] );
				if ( $logo_id > 0 && wp_get_attachment_url( $logo_id ) ) {
					set_theme_mod( 'custom_logo', $logo_id );
					update_option( 'site_logo', $logo_id );
					return;
				}
			}
		}
	}

	// 2. Buscar por nombre de archivo oficial COCO-NEW-LOGO en biblioteca de medios.
	$query = new \WP_Query(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			's'              => 'COCO-NEW-LOGO',
		)
	);

	if ( $query->have_posts() ) {
		$logo_id = $query->posts[0]->ID;
		set_theme_mod( 'custom_logo', $logo_id );
		update_option( 'site_logo', $logo_id );
		return;
	}

	// 3. Búsqueda de respaldo por 'logo'.
	$query_alt = new \WP_Query(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			's'              => 'logo',
		)
	);

	if ( $query_alt->have_posts() ) {
		$logo_id = $query_alt->posts[0]->ID;
		set_theme_mod( 'custom_logo', $logo_id );
		update_option( 'site_logo', $logo_id );
	}
}
add_action( 'after_setup_theme', 'coco_theme_ensure_site_logo', 20 );

/**
 * Asegurar que el bloque Gutenberg core/site-logo renderice siempre con dimensiones explícitas
 * (especialmente para archivos SVG con width="100%" que sin ancho fijo en px colapsan a 0x0).
 */
function coco_theme_fallback_site_logo_render( $block_content, $block ) {
	if ( 'core/site-logo' !== $block['blockName'] ) {
		return $block_content;
	}

	$logo_id = get_theme_mod( 'custom_logo' );
	if ( ! $logo_id ) {
		$logo_id = get_option( 'site_logo' );
	}

	if ( $logo_id ) {
		$logo_url = wp_get_attachment_url( $logo_id );
		if ( $logo_url ) {
			$alt = get_post_meta( $logo_id, '_wp_attachment_image_alt', true );
			$alt = $alt ? $alt : get_bloginfo( 'name' );
			return sprintf(
				'<div class="wp-block-site-logo"><a href="%s" class="custom-logo-link" rel="home"><img src="%s" class="custom-logo" width="115" height="56" alt="%s" style="width:115px;height:56px;max-height:56px;object-fit:contain;display:block;" /></a></div>',
				esc_url( home_url( '/' ) ),
				esc_url( $logo_url ),
				esc_attr( $alt )
			);
		}
	}

	// Fallback garantizado: si aún no se configuró en medios, servir el logo oficial del tema (COCO-NEW-LOGO.svg).
	$theme_logo = get_template_directory_uri() . '/assets/images/coco-logo.svg';
	return sprintf(
		'<div class="wp-block-site-logo"><a href="%s" class="custom-logo-link" rel="home"><img src="%s" class="custom-logo" width="115" height="56" alt="%s" style="width:115px;height:56px;max-height:56px;object-fit:contain;display:block;" /></a></div>',
		esc_url( home_url( '/' ) ),
		esc_url( $theme_logo ),
		esc_attr( get_bloginfo( 'name' ) )
	);
}
add_filter( 'render_block', 'coco_theme_fallback_site_logo_render', 10, 2 );

/**
 * Filtro get_custom_logo para garantizar la visualización en llamadas clásicas y en el Personalizador.
 */
function coco_theme_fallback_custom_logo_html( $html ) {
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( ! $logo_id ) {
		$logo_id = get_option( 'site_logo' );
	}

	if ( $logo_id ) {
		$logo_url = wp_get_attachment_url( $logo_id );
		if ( $logo_url ) {
			$alt = get_post_meta( $logo_id, '_wp_attachment_image_alt', true );
			$alt = $alt ? $alt : get_bloginfo( 'name' );
			return sprintf(
				'<a href="%s" class="custom-logo-link" rel="home"><img src="%s" class="custom-logo" width="115" height="56" alt="%s" style="width:115px;height:56px;max-height:56px;object-fit:contain;display:block;" /></a>',
				esc_url( home_url( '/' ) ),
				esc_url( $logo_url ),
				esc_attr( $alt )
			);
		}
	}

	if ( empty( $html ) ) {
		$theme_logo = get_template_directory_uri() . '/assets/images/coco-logo.svg';
		return sprintf(
			'<a href="%s" class="custom-logo-link" rel="home"><img src="%s" class="custom-logo" width="115" height="56" alt="%s" style="width:115px;height:56px;max-height:56px;object-fit:contain;display:block;" /></a>',
			esc_url( home_url( '/' ) ),
			esc_url( $theme_logo ),
			esc_attr( get_bloginfo( 'name' ) )
		);
	}
	return $html;
}
add_filter( 'get_custom_logo', 'coco_theme_fallback_custom_logo_html' );

/**
 * Conectar el bloque de navegación de la cabecera con los menús de WordPress
 * seleccionados en Apariencia > Menús o en Personalizar > Menús / Opciones Coco Cherry.
 */
function coco_theme_render_header_navigation( $block_content, $block ) {
	if ( 'core/navigation' !== $block['blockName'] ) {
		return $block_content;
	}

	$selected_menu = intval( get_theme_mod( 'coco_theme_header_menu', 0 ) );
	$locations     = get_nav_menu_locations();
	$primary_loc   = ( ! empty( $locations['primary'] ) ) ? intval( $locations['primary'] ) : 0;

	$menu_id = $selected_menu ? $selected_menu : $primary_loc;

	if ( $menu_id > 0 && is_nav_menu( $menu_id ) ) {
		$menu_html = wp_nav_menu(
			array(
				'menu'        => $menu_id,
				'container'   => false,
				'menu_class'  => 'coco-classic-menu-list',
				'echo'        => false,
				'fallback_cb' => false,
				'depth'       => 2,
			)
		);

		if ( ! empty( $menu_html ) ) {
			return sprintf(
				'<nav class="coco-main-nav coco-classic-nav" aria-label="Menú Principal"><button type="button" class="coco-mobile-menu-btn" aria-label="Menú" onclick="this.parentElement.classList.toggle(\'is-open\')">☰</button>%s</nav>',
				$menu_html
			);
		}
	}

	return $block_content;
}
add_filter( 'render_block', 'coco_theme_render_header_navigation', 10, 2 );

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
 * Barra de urgencia y demanda estilo Ecomus bajo el precio en la ficha de producto.
 */
function coco_theme_single_product_urgency_bar() {
	?>
	<div class="coco-stock-urgency-bar">
		<div class="coco-urgency-text">
			<span class="coco-urgency-flame">⚡</span>
			<span>¡Alta demanda! Quedan <strong>pocas unidades en stock</strong> para entrega inmediata</span>
		</div>
		<div class="coco-urgency-progress">
			<div class="coco-urgency-fill"></div>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'coco_theme_single_product_urgency_bar', 12 );

/**
 * Caja de garantías de compra estilo Ecomus debajo del botón de Coco COD en la ficha de producto.
 */
function coco_theme_product_cod_guarantees() {
	?>
	<div class="coco-ecomus-trust-box">
		<div class="coco-trust-item">
			<div class="coco-trust-item-icon">💵</div>
			<div class="coco-trust-item-content">
				<strong>Pago Contra Entrega</strong>
				<span>Abonás en efectivo o transferencia recién al recibir tu paquete en puerta</span>
			</div>
		</div>
		<div class="coco-trust-item">
			<div class="coco-trust-item-icon">🚚</div>
			<div class="coco-trust-item-content">
				<strong>Envíos a Todo Paraguay</strong>
				<span>Delivery express en Asunción, Gran Asunción e interior del país</span>
			</div>
		</div>
		<div class="coco-trust-item">
			<div class="coco-trust-item-icon">🛡️</div>
			<div class="coco-trust-item-content">
				<strong>100% Compra Segura</strong>
				<span>Artículos verificados, nuevos y con garantía directa</span>
			</div>
		</div>
		<div class="coco-trust-item">
			<div class="coco-trust-item-icon">📲</div>
			<div class="coco-trust-item-content">
				<strong>Atención por WhatsApp</strong>
				<span>Consultas y seguimiento directo al 0981 531503</span>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_after_add_to_cart_form', 'coco_theme_product_cod_guarantees', 25 );

/**
 * Añadir insignia de producto estrella (Más Vendido) en las tarjetas del catálogo WooCommerce.
 */
function coco_theme_loop_star_badge() {
	global $product;
	if ( ! $product instanceof \WC_Product ) {
		return;
	}
	$name    = mb_strtolower( $product->get_name(), 'UTF-8' );
	$is_star = $product->is_featured() || false !== strpos( $name, 'protector solar' ) || false !== strpos( $name, 'rizador' );

	if ( $is_star ) {
		echo '<span class="coco-star-product-badge">⭐ MÁS VENDIDO</span>';
	}
}
add_action( 'woocommerce_before_shop_loop_item_title', 'coco_theme_loop_star_badge', 5 );

/**
 * Añadir etiquetas universales de pago y entrega en las tarjetas del catálogo WooCommerce.
 */
function coco_theme_loop_cod_tag() {
	echo '<div class="coco-product-cod-badge"><span class="coco-cod-pill">💵 Pago Contra Entrega, transferencia, efectivo</span><span class="coco-delivery-pill">🚚 Envíos a todo Paraguay</span></div>';
}
add_action( 'woocommerce_after_shop_loop_item_title', 'coco_theme_loop_cod_tag', 15 );

/**
 * Shortcode [coco_star_products]: Muestra los 2 productos estrella destacados con diseño de alta conversión.
 */
function coco_theme_star_products_shortcode() {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return '';
	}

	$products = array();

	// 1. Protector Solar Oro 24K
	$query_sun = wc_get_products(
		array(
			'limit'  => 1,
			'status' => 'publish',
			's'      => 'Protector Solar',
		)
	);
	if ( ! empty( $query_sun ) ) {
		$products[] = array(
			'product' => $query_sun[0],
			'badge'   => '🔥 #1 MÁS VENDIDO EN BELLEZA',
			'rating'  => '★★★★★ 4.9 (Más de 350 clientas)',
			'bullets' => array(
				'Máxima protección solar FPS 90+ de amplio espectro',
				'Fórmula antiedad enriquecida con partículas de Oro 24K',
				'Textura ultraligera, acabado mate sin brillo ni efecto graso',
			),
		);
	}

	// 2. Rizador de Pestañas Térmico y Perfilador de Cejas 2 en 1
	$query_riz = wc_get_products(
		array(
			'limit'  => 1,
			'status' => 'publish',
			's'      => 'Rizador',
		)
	);
	if ( ! empty( $query_riz ) ) {
		$products[] = array(
			'product' => $query_riz[0],
			'badge'   => '✨ TENDENCIA VIRAL 2 EN 1',
			'rating'  => '★★★★★ 5.0 (Novedad destacada)',
			'bullets' => array(
				'Rizado térmico rápido, natural y de larga duración sin quebrar',
				'Perfilador eléctrico de alta precisión integrado para cejas perfectas',
				'Batería recargable USB de larga duración para llevar a cualquier parte',
			),
		);
	}

	// Respaldo dinámico si no coinciden por nombre exacto
	if ( count( $products ) < 2 ) {
		$fallback = wc_get_products(
			array(
				'limit'   => 2 - count( $products ),
				'status'  => 'publish',
				'orderby' => 'popularity',
			)
		);
		foreach ( $fallback as $fb_prod ) {
			$products[] = array(
				'product' => $fb_prod,
				'badge'   => '⭐ PRODUCTO DESTACADO',
				'rating'  => '★★★★★ 4.9',
				'bullets' => array(
					'Calidad premium seleccionada por Coco Cherry Store',
					'Abonás en efectivo o QR recién al recibir tu paquete',
					'Envíos rápidos y seguros a todo Paraguay',
				),
			);
		}
	}

	if ( empty( $products ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="coco-star-spotlight-grid">
		<?php foreach ( $products as $item ) :
			$p = $item['product'];
			$img_id = $p->get_image_id();
			$img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : wc_placeholder_img_src();
			$link = get_permalink( $p->get_id() );
			?>
			<div class="coco-star-spotlight-card">
				<div class="coco-spotlight-header">
					<span class="coco-spotlight-badge"><?php echo esc_html( $item['badge'] ); ?></span>
					<span class="coco-spotlight-rating"><?php echo esc_html( $item['rating'] ); ?></span>
				</div>
				<a href="<?php echo esc_url( $link ); ?>" class="coco-spotlight-img-link" title="<?php echo esc_attr( $p->get_name() ); ?>">
					<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $p->get_name() ); ?>" loading="lazy" />
				</a>
				<h3 class="coco-spotlight-title">
					<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $p->get_name() ); ?></a>
				</h3>
				<ul class="coco-spotlight-features">
					<?php foreach ( $item['bullets'] as $b ) : ?>
						<li>✓ <?php echo esc_html( $b ); ?></li>
					<?php endforeach; ?>
				</ul>
				<div class="coco-spotlight-price-row">
					<span class="coco-spotlight-price"><?php echo $p->get_price_html(); ?></span>
					<span class="coco-spotlight-cod">💵 Pago al Recibir en Puerta</span>
				</div>
				<div class="coco-spotlight-action">
					<a href="<?php echo esc_url( $link ); ?>" class="coco-spotlight-btn">
						Pedir con Pago Contra Entrega &rarr;
					</a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'coco_star_products', 'coco_theme_star_products_shortcode' );

/**
 * Botón flotante de WhatsApp: desactivado por defecto para no obstruir pantalla móvil ni fichas de producto.
 * Puede encenderse o apagarse cuando se desee desde Apariencia > Personalizar > Opciones Coco Cherry.
 */
function coco_theme_render_floating_whatsapp() {
	if ( 'yes' !== get_option( 'coco_theme_show_floating_wa', 'no' ) ) {
		return;
	}

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

/**
 * Panel de Personalización para elegir Menú Principal y prender o apagar el botón flotante de WhatsApp.
 */
function coco_theme_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'coco_theme_options',
		array(
			'title'    => __( 'Opciones Coco Cherry Store', 'coco-theme' ),
			'priority' => 30,
		)
	);

	// Selector de Menú Principal para la cabecera.
	$menus        = wp_get_nav_menus();
	$menu_choices = array(
		0 => __( '— Usar ubicación Menú Principal o por defecto —', 'coco-theme' ),
	);
	if ( ! empty( $menus ) && ! is_wp_error( $menus ) ) {
		foreach ( $menus as $menu_obj ) {
			$menu_choices[ $menu_obj->term_id ] = $menu_obj->name;
		}
	}

	$wp_customize->add_setting(
		'coco_theme_header_menu',
		array(
			'default'           => 0,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_header_menu',
		array(
			'label'       => __( 'Seleccionar Menú de Cabecera', 'coco-theme' ),
			'description' => __( 'Elige cuál de tus menús creados en WordPress deseas mostrar en la barra superior.', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'select',
			'choices'     => $menu_choices,
		)
	);

	$wp_customize->add_setting(
		'coco_theme_show_floating_wa',
		array(
			'default'   => 'no',
			'type'      => 'option',
			'transport' => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_show_floating_wa',
		array(
			'label'       => __( 'Activar botón flotante sticky de WhatsApp', 'coco-theme' ),
			'description' => __( 'Por defecto está apagado para no tapar los productos en móviles. Marque esta casilla si desea encenderlo.', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'coco_theme_customize_register' );

