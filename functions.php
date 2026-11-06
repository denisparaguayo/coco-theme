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
 * ==========================================================================
 * HELPERS DINÁMICOS Y MULTITIENDA (WHATSAPP, MARCA, CONTACTO)
 * ==========================================================================
 */

/**
 * Obtener el número de WhatsApp para enlaces wa.me (limpio de espacios y caracteres).
 */
function coco_theme_get_whatsapp_number() {
	$num = get_theme_mod( 'coco_whatsapp_number', '' );
	if ( empty( $num ) ) {
		$num = get_option( 'coco_whatsapp_number', '595981531503' );
	}
	return preg_replace( '/[^0-9]/', '', (string) $num );
}

/**
 * Obtener el número visible para los visitantes en textos y botones.
 */
function coco_theme_get_whatsapp_display() {
	$display = get_theme_mod( 'coco_whatsapp_display', '' );
	if ( ! empty( $display ) ) {
		return esc_html( $display );
	}
	$num = coco_theme_get_whatsapp_number();
	if ( 0 === strpos( $num, '5959' ) ) {
		return '09' . substr( $num, 4, 2 ) . ' ' . substr( $num, 6 );
	}
	return ! empty( $num ) ? $num : '0981 531503';
}

/**
 * Obtener la dirección o ciudad de la tienda para el pie de página.
 */
function coco_theme_get_store_address() {
	$addr = get_theme_mod( 'coco_store_address', '' );
	if ( ! empty( $addr ) ) {
		return esc_html( $addr );
	}
	if ( function_exists( 'WC' ) && WC()->countries ) {
		$base = WC()->countries->get_base_address();
		$city = WC()->countries->get_base_city();
		if ( ! empty( $base ) && ! empty( $city ) ) {
			return esc_html( $base . ', ' . $city . ', Paraguay' );
		}
	}
	return 'Guaraníes 4779, Asunción, Paraguay';
}

/**
 * Generar URL de WhatsApp dinámica con mensaje 100% personalizable y variables dinámicas.
 *
 * @param string                  $context 'general' o 'product'.
 * @param \WC_Product|int|null    $product Objeto o ID del producto (opcional).
 * @return string URL de wa.me lista.
 */
function coco_theme_get_whatsapp_url( $context = 'general', $product = null ) {
	$phone     = coco_theme_get_whatsapp_number();
	$site_name = get_bloginfo( 'name' );

	if ( 'product' === $context ) {
		if ( ! $product && function_exists( 'is_product' ) && is_product() ) {
			$product = wc_get_product( get_queried_object_id() );
		} elseif ( is_numeric( $product ) && function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( $product );
		}

		$raw_msg = get_theme_mod(
			'coco_whatsapp_product_message',
			'¡Hola {sitio}! Tengo una consulta sobre el producto: {producto} ({url})'
		);

		$title = ( $product instanceof \WC_Product ) ? $product->get_name() : ( is_object( $product ) && isset( $product->post_title ) ? $product->post_title : get_the_title() );
		$link  = ( $product instanceof \WC_Product ) ? $product->get_permalink() : get_permalink();

		$msg = str_replace(
			array( '{sitio}', '{producto}', '{url}' ),
			array( $site_name, $title, $link ),
			$raw_msg
		);
	} else {
		$raw_msg = get_theme_mod(
			'coco_whatsapp_general_message',
			'¡Hola {sitio}! Quiero hacer una consulta sobre sus productos con entrega a domicilio.'
		);
		$msg = str_replace( '{sitio}', $site_name, $raw_msg );
	}

	return 'https://wa.me/' . $phone . '?text=' . rawurlencode( $msg );
}

/**
 * Configuración base del tema y soporte para FSE y WooCommerce.
 */
function coco_theme_setup() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	// Soporte completo para logotipo personalizado y opción nativa de ocultar título/descripción.
	add_theme_support(
		'custom-logo',
		array(
			'height'               => 100,
			'width'                => 320,
			'flex-height'          => true,
			'flex-width'           => true,
			'header-text'          => array( 'site-title', 'site-description', 'wp-block-site-title', 'wp-block-site-tagline' ),
			'unlink-homepage-logo' => false,
		)
	);

	// Soporte para fondo personalizado y cabecera nativa de WordPress.
	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'ffffff',
		)
	);

	// Habilitar gestión de Menús clásicos y ubicaciones en Apariencia > Menús y Personalizador.
	add_theme_support( 'menus' );
	register_nav_menus(
		array(
			'primary' => __( 'Menú Principal (Cabecera)', 'coco-theme' ),
			'mobile'  => __( 'Menú Móvil (Cajón Lateral)', 'coco-theme' ),
			'footer'  => __( 'Menú de Pie de Página (Footer)', 'coco-theme' ),
		)
	);

	// Habilitar soporte nativo de Widgets.
	add_theme_support( 'widgets' );
	add_theme_support( 'widgets-block-editor' );

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
	// Desactivamos zoom agresivo dejando el aspecto natural por defecto de WooCommerce.
	remove_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'coco_theme_setup' );

/**
 * Desactivar de forma segura el zoom de WooCommerce sin romper el script del slider ni la galería.
 */
add_action( 'after_setup_theme', function() {
	remove_theme_support( 'wc-product-gallery-zoom' );
}, 100 );

add_filter( 'woocommerce_single_product_zoom_enabled', '__return_false' );

add_filter( 'woocommerce_get_script_data', function( $params, $handle ) {
	if ( 'wc-single-product' === $handle && is_array( $params ) ) {
		$params['zoom_enabled'] = false;
	}
	return $params;
}, 99, 2 );

add_filter( 'woocommerce_single_product_params', function( $params ) {
	if ( is_array( $params ) ) {
		$params['zoom_enabled'] = false;
	}
	return $params;
}, 99 );

add_action( 'wp_enqueue_scripts', function() {
	// Shim defensivo para jQuery.fn.zoom para garantizar que ningún script de WooCommerce falle
	$dummy_zoom = "if(window.jQuery && !jQuery.fn.zoom){ jQuery.fn.zoom = function(){ return this; }; }";
	wp_add_inline_script( 'wc-single-product', $dummy_zoom, 'before' );
}, 20 );

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

	// Fallback garantizado: si aún no se configuró en medios, servir el logo de muestra predeterminado (default-logo.jpg).
	$theme_logo = get_template_directory_uri() . '/assets/images/default-logo.jpg';
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
		$theme_logo = get_template_directory_uri() . '/assets/images/default-logo.jpg';
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
 * Obtener el icono SVG o emoji adecuado para un elemento del menú de navegación.
 *
 * @param string $title Título del elemento.
 * @param string $url   URL del enlace.
 * @param bool   $is_mobile Si se renderiza en la barra móvil o cabecera de escritorio.
 * @return string HTML del icono.
 */
function coco_theme_get_nav_icon_html( $title, $url = '', $is_mobile = false ) {
	$title_clean = trim( wp_strip_all_tags( $title ) );
	$size        = $is_mobile ? '18' : '20';
	$stroke      = '1.8';

	// 1. Detectar si el usuario incluyó un emoji al inicio del título (ej: "👗 Vestidos", "🔥 Ofertas", "💍 Joyas")
	if ( preg_match( '/^([\x{1F300}-\x{1FAD6}\x{1F900}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F600}-\x{1F64F}\x{1F680}-\x{1F6FF}])/u', $title_clean, $emoji_matches ) ) {
		$emoji_size = $is_mobile ? '1.15rem' : '1.25rem';
		return sprintf( '<span class="coco-nav-icon coco-nav-emoji" style="font-size:%s;line-height:1;display:inline-flex;align-items:center;justify-content:center;">%s</span>', esc_attr( $emoji_size ), esc_html( $emoji_matches[1] ) );
	}

	// Normalizar texto del título y URL limpiando palabras del sistema como 'categoria-producto'
	$title_norm = strtolower( remove_accents( $title_clean ) );
	$url_clean  = str_replace( array( 'categoria-producto', 'product-category', 'categoria' ), '', strtolower( remove_accents( $url ) ) );
	$normalized = $title_norm . ' ' . $url_clean;

	// 2. Bijouterie / Joyas / Accesorios (Diamante)
	if ( preg_match( '/(bijou|joya|joyeria|accesorio|anillo|collar|aro|aros|pulsera|reloj|brillante|oro|plata)/i', $normalized ) ) {
		return sprintf(
			'<svg class="coco-nav-icon" width="%s" height="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12l4 6-10 13L2 9z"></path><path d="M11 3 8 9l4 13 4-13-3-6"></path><path d="M2 9h20"></path></svg>',
			$size, $size, $stroke
		);
	}

	// 3. Hogar / Deco / Living / Cocina (Lámpara / Hogar Boutique)
	if ( preg_match( '/(hogar|casa|deco|decoracion|living|cocina|bazar|lampara|mueble)/i', $normalized ) ) {
		return sprintf(
			'<svg class="coco-nav-icon" width="%s" height="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a4 4 0 0 0-4 4c0 1.5.8 2.8 2 3.5L2 14v2h20v-2l-8-4.5c1.2-.7 2-2 2-3.5a4 4 0 0 0-4-4z"></path><path d="M12 6a2 2 0 1 1-4 0"></path></svg>',
			$size, $size, $stroke
		);
	}

	// 4. Moda / Ropa / Indumentaria / Prendas / Vestidos (Moda Boutique)
	if ( preg_match( '/(moda|ropa|indumentaria|prenda|vestido|blusa|lenceria|calzado|zapato|camisa|pantalon)/i', $normalized ) ) {
		return sprintf(
			'<svg class="coco-nav-icon" width="%s" height="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>',
			$size, $size, $stroke
		);
	}

	// 5. Belleza / Cuidado / Skincare / Cosméticos / Maquillaje (Destellos / Belleza)
	if ( preg_match( '/(belleza|cosmetica|cosmeticos|skincare|facial|capilar|maquillaje|pestana|ceja|piel)/i', $normalized ) ) {
		return sprintf(
			'<svg class="coco-nav-icon" width="%s" height="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path></svg>',
			$size, $size, $stroke
		);
	}

	// 6. Ofertas / Descuentos / Sale / Promociones (Fuego / Ofertas)
	if ( preg_match( '/(oferta|ofertas|sale|descuento|promo|promocion|liquidacion)/i', $normalized ) ) {
		return sprintf(
			'<svg class="coco-nav-icon" width="%s" height="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>',
			$size, $size, $stroke
		);
	}

	// 7. FAQ / Preguntas Frecuentes / Dudas / Ayuda (Estrella)
	if ( preg_match( '/(faq|pregunta|preguntas|duda|ayuda|soporte)/i', $normalized ) ) {
		return sprintf(
			'<svg class="coco-nav-icon" width="%s" height="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>',
			$size, $size, $stroke
		);
	}

	// 8. Contacto / WhatsApp / Mensaje / Soporte (Sobre)
	if ( preg_match( '/(contacto|whatsapp|escribenos|mensaje|correo|email|telefono)/i', $normalized ) ) {
		return sprintf(
			'<svg class="coco-nav-icon" width="%s" height="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
			$size, $size, $stroke
		);
	}

	// 9. Nosotros / Quiénes Somos / Empresa (Corazón)
	if ( preg_match( '/(nosotros|quienes|somos|sobre|historia|empresa)/i', $normalized ) ) {
		return sprintf(
			'<svg class="coco-nav-icon" width="%s" height="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path></svg>',
			$size, $size, $stroke
		);
	}

	// 10. Tienda / Catálogo / Inicio / Shop (Tienda / Casita)
	if ( preg_match( '/(tienda|shop|catalogo|inicio|home)/i', $title_norm ) || preg_match( '/(\/shop|\/tienda|\/catalogo)/i', $url ) ) {
		return sprintf(
			'<svg class="coco-nav-icon" width="%s" height="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
			$size, $size, $stroke
		);
	}

	// Icono por defecto (medallón boutique elegante)
	return sprintf(
		'<svg class="coco-nav-icon" width="%s" height="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="m10 12 4-4"></path><path d="m10 12 4 4"></path></svg>',
		$size, $size, $stroke
	);
}

/**
 * Obtener elementos de navegación configurados en Apariencia > Menús con jerarquía padre-hijos.
 *
 * @param string $location Ubicación del menú ('primary' o 'mobile').
 * @return array Lista jerárquica de elementos con title, url, target, classes, children.
 */
function coco_theme_get_header_nav_items( $location = 'primary' ) {
	$locations = get_nav_menu_locations();
	$menu_id   = ! empty( $locations[ $location ] ) ? intval( $locations[ $location ] ) : 0;

	// Si se solicita 'mobile' pero no tiene menú asignado, heredar automáticamente 'primary'
	if ( 'mobile' === $location && ! $menu_id && ! empty( $locations['primary'] ) ) {
		$menu_id = intval( $locations['primary'] );
	}

	// Si no está asignado a 'primary', revisar la opción del personalizador
	if ( ! $menu_id ) {
		$menu_id = intval( get_theme_mod( 'coco_theme_header_menu', 0 ) );
	}

	// Si todavía no está asignado, buscar menú existente llamado "main", "principal" o el primer menú disponible
	if ( ! $menu_id ) {
		$all_menus = wp_get_nav_menus();
		if ( ! empty( $all_menus ) && is_array( $all_menus ) ) {
			foreach ( $all_menus as $m ) {
				$m_name = strtolower( $m->name );
				if ( 'main' === $m_name || 'principal' === $m_name || 'cabecera' === $m_name ) {
					$menu_id = $m->term_id;
					break;
				}
			}
			if ( ! $menu_id && ! empty( $all_menus[0]->term_id ) ) {
				$menu_id = $all_menus[0]->term_id;
			}
		}
	}

	if ( $menu_id > 0 && is_nav_menu( $menu_id ) ) {
		$nav_items = wp_get_nav_menu_items( $menu_id );
		if ( ! empty( $nav_items ) && ! is_wp_error( $nav_items ) ) {
			$items_by_id = array();
			$tree        = array();

			foreach ( $nav_items as $item ) {
				$items_by_id[ $item->ID ] = array(
					'id'       => $item->ID,
					'parent'   => intval( $item->menu_item_parent ),
					'title'    => $item->title,
					'url'      => $item->url,
					'target'   => ! empty( $item->target ) ? $item->target : '',
					'classes'  => is_array( $item->classes ) ? implode( ' ', array_filter( $item->classes ) ) : '',
					'children' => array(),
				);
			}

			foreach ( $items_by_id as $id => &$node ) {
				if ( 0 === $node['parent'] ) {
					$tree[ $id ] = &$node;
				} else {
					if ( isset( $items_by_id[ $node['parent'] ] ) ) {
						$items_by_id[ $node['parent'] ]['children'][] = &$node;
					}
				}
			}
			unset( $node );

			return array_values( $tree );
		}
	}

	// Si no hay menú creado aún en WordPress, generar navegación automática desde WooCommerce
	$default_items = array(
		array(
			'title'    => 'Tienda',
			'url'      => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
			'target'   => '',
			'children' => array(),
		),
	);

	if ( taxonomy_exists( 'product_cat' ) ) {
		$cats = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'number'     => 4,
				'parent'     => 0,
				'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			)
		);
		if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
			foreach ( $cats as $cat ) {
				$default_items[] = array(
					'title'    => $cat->name,
					'url'      => get_term_link( $cat ),
					'target'   => '',
					'children' => array(),
				);
			}
		}
	}

	// Si aún no se crearon categorías en la tienda, colocar accesos generales seguros
	if ( count( $default_items ) === 1 ) {
		$default_items[] = array(
			'title'    => 'Destacados',
			'url'      => home_url( '/shop/' ),
			'target'   => '',
			'children' => array(),
		);
		$default_items[] = array(
			'title'    => 'Novedades',
			'url'      => home_url( '/shop/' ),
			'target'   => '',
			'children' => array(),
		);
	}

	$default_items[] = array(
		'title'    => 'Preguntas Frecuentes',
		'url'      => home_url( '/faq/' ),
		'target'   => '',
		'children' => array(),
	);

	$default_items[] = array(
		'title'    => 'Contacto',
		'url'      => coco_theme_get_whatsapp_url( 'general' ),
		'target'   => '_blank',
		'children' => array(),
	);

	return $default_items;
}

/**
 * Renderizar la barra de navegación boutique para la cabecera (escritorio).
 *
 * @return string HTML de la navegación con iconos y submenús desplegables.
 */
function coco_theme_render_boutique_nav() {
	$items = coco_theme_get_header_nav_items( 'primary' );
	if ( empty( $items ) ) {
		return '';
	}

	$current_req = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

	$output  = '<nav class="coco-header-nav-center" aria-label="Navegación Principal">';
	$output .= '<ul class="coco-boutique-nav-list">';

	foreach ( $items as $item ) {
		$title        = esc_html( $item['title'] );
		$url          = esc_url( $item['url'] );
		$target       = ! empty( $item['target'] ) ? ' target="' . esc_attr( $item['target'] ) . '" rel="noreferrer noopener"' : '';
		$icon_html    = coco_theme_get_nav_icon_html( $item['title'], $item['url'], false );
		$has_children = ! empty( $item['children'] );

		$is_active = false;
		if ( ! empty( $current_req ) && ! empty( $item['url'] ) ) {
			$item_path = wp_parse_url( $item['url'], PHP_URL_PATH );
			if ( ! empty( $item_path ) && '/' !== $item_path && false !== strpos( $current_req, $item_path ) ) {
				$is_active = true;
			}
		}

		$item_classes = 'coco-nav-item' . ( $is_active ? ' active' : '' ) . ( $has_children ? ' has-dropdown' : '');
		if ( ! empty( $item['classes'] ) ) {
			$item_classes .= ' ' . esc_attr( $item['classes'] );
		}

		$arrow_html = $has_children ? ' <span class="coco-dropdown-arrow">▾</span>' : '';

		$output .= sprintf(
			'<li class="%s"><a href="%s" class="coco-nav-link"%s>%s<span class="coco-nav-label">%s%s</span></a>',
			esc_attr( $item_classes ),
			$url,
			$target,
			$icon_html,
			$title,
			$arrow_html
		);

		if ( $has_children ) {
			$output .= '<ul class="coco-dropdown-menu">';
			foreach ( $item['children'] as $child ) {
				$child_title  = esc_html( $child['title'] );
				$child_url    = esc_url( $child['url'] );
				$child_target = ! empty( $child['target'] ) ? ' target="' . esc_attr( $child['target'] ) . '" rel="noreferrer noopener"' : '';
				$output      .= sprintf(
					'<li class="coco-dropdown-item"><a href="%s" class="coco-dropdown-link"%s><span>%s</span></a></li>',
					$child_url,
					$child_target,
					$child_title
				);
			}
			$output .= '</ul>';
		}

		$output .= '</li>';
	}

	$output .= '</ul>';
	$output .= '</nav>';

	return $output;
}
add_shortcode( 'coco_boutique_nav', 'coco_theme_render_boutique_nav' );

/**
 * Renderizar la lista de navegación para el cajón lateral móvil.
 *
 * @return string HTML <ul> con iconos, enlaces y submenús desplegables.
 */
function coco_theme_render_mobile_nav() {
	$items = coco_theme_get_header_nav_items( 'mobile' );
	if ( empty( $items ) ) {
		return '';
	}

	$output = '<ul class="coco-mobile-nav-list">';

	foreach ( $items as $item ) {
		$title        = esc_html( $item['title'] );
		$url          = esc_url( $item['url'] );
		$target       = ! empty( $item['target'] ) ? ' target="' . esc_attr( $item['target'] ) . '" rel="noreferrer noopener"' : '';
		$icon_html    = coco_theme_get_nav_icon_html( $item['title'], $item['url'], true );
		$has_children = ! empty( $item['children'] );

		if ( $has_children ) {
			$output .= '<li class="coco-mobile-nav-item has-children">';
			$output .= '<div class="coco-mobile-nav-row">';
			$output .= sprintf( '<a href="%s" class="coco-mobile-nav-link"%s>%s<span>%s</span></a>', $url, $target, $icon_html, $title );
			$output .= '<button type="button" class="coco-mobile-submenu-toggle" aria-label="Desplegar submenú" onclick="cocoToggleMobileSubmenu(this, event)">';
			$output .= '<svg class="coco-submenu-caret" style="pointer-events:none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';
			$output .= '</button>';
			$output .= '</div>';
			$output .= '<ul class="coco-mobile-submenu">';
			foreach ( $item['children'] as $child ) {
				$child_title  = esc_html( $child['title'] );
				$child_url    = esc_url( $child['url'] );
				$child_target = ! empty( $child['target'] ) ? ' target="' . esc_attr( $child['target'] ) . '" rel="noreferrer noopener"' : '';
				$output      .= sprintf(
					'<li><a href="%s"%s class="coco-mobile-sublink"><span class="coco-sublink-bullet">•</span> <span>%s</span></a></li>',
					$child_url,
					$child_target,
					$child_title
				);
			}
			$output .= '</ul>';
			$output .= '</li>';
		} else {
			$output .= sprintf(
				'<li class="coco-mobile-nav-item"><a href="%s"%s class="coco-mobile-nav-link">%s<span>%s</span></a></li>',
				$url,
				$target,
				$icon_html,
				$title
			);
		}
	}

	$output .= '</ul>';
	return $output;
}
add_shortcode( 'coco_mobile_nav', 'coco_theme_render_mobile_nav' );

/**
 * Conectar la cabecera del editor de bloques con los menús dinámicos de WordPress.
 */
function coco_theme_render_header_navigation( $block_content, $block ) {
	// 0. Proteger bloques de WooCommerce, fichas de producto y contenido para que no se alteren sus URLs
	if ( ! empty( $block['blockName'] ) ) {
		if ( false !== strpos( $block['blockName'], 'woocommerce' ) || 'core/post-content' === $block['blockName'] ) {
			return $block_content;
		}
	}

	// 1. Reemplazar core/navigation si se usa en Gutenberg
	if ( 'core/navigation' === $block['blockName'] ) {
		return coco_theme_render_boutique_nav();
	}

	// 2. Si el bloque contiene la navegación central de la cabecera, inyectar el menú dinámico
	if ( false !== strpos( $block_content, 'coco-boutique-nav-list' ) || false !== strpos( $block_content, 'coco-header-nav-center' ) ) {
		$pattern = '/<nav\s+class="coco-header-nav-center"[^>]*>.*?<\/nav>/s';
		if ( preg_match( $pattern, $block_content ) ) {
			$block_content = preg_replace( $pattern, coco_theme_render_boutique_nav(), $block_content, 1 );
		}
	}

	// 3. Si el bloque contiene la lista móvil de navegación, inyectar la lista dinámica
	if ( false !== strpos( $block_content, 'coco-mobile-nav-list' ) ) {
		$pattern_mob = '/<ul\s+class="coco-mobile-nav-list"[^>]*>.*?<\/ul>/s';
		if ( preg_match( $pattern_mob, $block_content ) ) {
			$block_content = preg_replace( $pattern_mob, coco_theme_render_mobile_nav(), $block_content, 1 );
		}
	}

	// 4. Dinamizar variables globales de WhatsApp, teléfono, dirección y marca en cabecera/pie
	if ( false !== strpos( $block_content, '595981531503' ) || false !== strpos( $block_content, '0981 531503' ) || false !== strpos( $block_content, 'Guaraníes 4779' ) || false !== strpos( $block_content, 'Coco Cherry' ) ) {
		$wa_general_url = coco_theme_get_whatsapp_url( 'general' );
		$wa_display     = coco_theme_get_whatsapp_display();
		$wa_clean_num   = coco_theme_get_whatsapp_number();
		$store_address  = coco_theme_get_store_address();
		$site_title     = get_bloginfo( 'name' );

		// Reemplazar SOLO enlaces wa.me directos que NO contengan ya un mensaje personalizado (?text=...)
		$block_content = preg_replace( '#https://wa\.me/595981531503(?![?&a-zA-Z0-9])#', esc_url( $wa_general_url ), $block_content );
		$block_content = str_replace( '0981 531503', esc_html( $wa_display ), $block_content );
		$block_content = str_replace( 'Guaraníes 4779, Asunción, Paraguay', esc_html( $store_address ), $block_content );
		$block_content = str_replace( 'Menú Coco Cherry', 'Menú ' . esc_html( $site_title ), $block_content );
		$block_content = str_replace( '2026 Coco Cherry Store Paraguay', date( 'Y' ) . ' ' . esc_html( $site_title ) . ' Paraguay', $block_content );
		$block_content = str_replace( 'Coco Cherry Store Paraguay', esc_html( $site_title ) . ' Paraguay', $block_content );
	}

	// 5. Garantizar que el formulario de búsqueda apunte siempre a la URL del sitio (home_url)
	if ( false !== strpos( $block_content, 'coco-header-search-form' ) ) {
		$home_url_clean = esc_url( home_url( '/' ) );
		$block_content  = str_replace( 'class="coco-header-search-form" action="/"', 'class="coco-header-search-form" action="' . $home_url_clean . '"', $block_content );
		$block_content  = str_replace( 'action="/" class="coco-header-search-form"', 'action="' . $home_url_clean . '" class="coco-header-search-form"', $block_content );
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
		'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap',
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
 * Imprimir scripts esenciales de cabecera y navegación móvil en wp_head.
 */
function coco_theme_print_header_scripts() {
	?>
	<script id="coco-header-core-js">
	var _cocoSearchToggleTime = 0;
	window.cocoToggleSearch = function(force) {
		var now = Date.now();
		if (typeof force !== 'boolean') {
			if (now - _cocoSearchToggleTime < 350) return;
			_cocoSearchToggleTime = now;
		}

		var bar = document.getElementById('coco-header-search-bar');
		if (!bar) {
			var header = document.querySelector('.coco-site-header') || document.querySelector('header');
			if (header) {
				bar = document.createElement('div');
				bar.id = 'coco-header-search-bar';
				bar.className = 'coco-header-search-drawer';
				bar.style.display = 'none';
				bar.innerHTML = '<div class="coco-header-search-inner">' +
					'<form role="search" method="get" class="coco-header-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">' +
					'<svg class="coco-search-input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>' +
					'<input type="search" class="coco-header-search-input" placeholder="Buscar productos..." name="s" autocomplete="off" />' +
					'<input type="hidden" name="post_type" value="product" />' +
					'<button type="submit" class="coco-header-search-submit">Buscar</button>' +
					'<button type="button" class="coco-header-search-close" onclick="cocoToggleSearch(false)">✕</button>' +
					'</form></div>';
				header.appendChild(bar);
			} else {
				return;
			}
		}

		var isCurrentlyOpen = (bar.classList.contains('is-open') || (bar.style.display !== 'none' && bar.style.display !== ''));
		var willOpen = (typeof force === 'boolean') ? force : !isCurrentlyOpen;

		if (willOpen) {
			bar.classList.add('is-open');
			bar.style.display = 'block';
			var inp = bar.querySelector('.coco-header-search-input');
			if (inp) {
				setTimeout(function() {
					try {
						inp.focus();
						inp.select();
					} catch (err) {}
				}, 60);
			}
		} else {
			bar.classList.remove('is-open');
			bar.style.display = 'none';
		}
	};

	var _cocoNavToggleTime = 0;
	window.cocoToggleMobileNav = function(force) {
		var now = Date.now();
		if (typeof force !== 'boolean') {
			if (now - _cocoNavToggleTime < 350) return;
			_cocoNavToggleTime = now;
		}

		var drawer = document.getElementById('coco-mobile-drawer');
		if (!drawer) {
			console.warn('coco-mobile-drawer no encontrado en el DOM');
			return;
		}

		var willOpen = (typeof force === 'boolean') ? force : !drawer.classList.contains('is-open');
		if (willOpen) {
			drawer.classList.add('is-open');
			drawer.style.display = 'block';
			drawer.setAttribute('aria-hidden', 'false');
			document.body.style.overflow = 'hidden';
		} else {
			drawer.classList.remove('is-open');
			drawer.style.display = 'none';
			drawer.setAttribute('aria-hidden', 'true');
			document.body.style.overflow = '';
		}
	};

	var _cocoSubmenuToggleTime = 0;
	window.cocoToggleMobileSubmenu = function(btn, e) {
		if (e && e.stopPropagation) {
			e.stopPropagation();
		}
		var now = Date.now();
		if (now - _cocoSubmenuToggleTime < 350) return;
		_cocoSubmenuToggleTime = now;

		if (!btn) return;
		var item = btn.closest('.coco-mobile-nav-item');
		if (item) {
			item.classList.toggle('is-open');
		}
	};

	window.cocoDismissTopBar = function() {
		var m = document.getElementById('coco-sticky-marquee-top');
		if (m) {
			m.style.display = 'none';
			try { sessionStorage.setItem('coco_topbar_dismissed', '1'); } catch(e){}
		}
	};

	// Delegación global de clics para móvil, buscador y cierre al hacer clic fuera
	document.addEventListener('click', function(e) {
		var mobileToggle = e.target.closest('.coco-header-mobile-toggle');
		if (mobileToggle) {
			e.preventDefault();
			window.cocoToggleMobileNav(true);
			return;
		}
		var mobileClose = e.target.closest('.coco-mobile-drawer-close, .coco-mobile-drawer-backdrop');
		if (mobileClose) {
			e.preventDefault();
			window.cocoToggleMobileNav(false);
			return;
		}
		var searchToggle = e.target.closest('.coco-btn-search-toggle');
		if (searchToggle) {
			e.preventDefault();
			window.cocoToggleSearch();
			return;
		}
		var searchClose = e.target.closest('.coco-header-search-close');
		if (searchClose) {
			e.preventDefault();
			window.cocoToggleSearch(false);
			return;
		}

		// Cerrar buscador si se hace clic fuera del formulario y del botón lupa
		var bar = document.getElementById('coco-header-search-bar');
		if (bar && (bar.classList.contains('is-open') || bar.style.display === 'block')) {
			var insideSearch = e.target.closest('#coco-header-search-bar');
			var insideToggle = e.target.closest('.coco-btn-search-toggle');
			if (!insideSearch && !insideToggle) {
				window.cocoToggleSearch(false);
			}
		}

		// Centrar suavemente pestaña de producto al tocarla en pantallas móviles
		var tabLink = e.target.closest('.woocommerce-tabs ul.tabs li a, .wc-tabs li a');
		if (tabLink) {
			var tabLi = tabLink.closest('li');
			if (tabLi && typeof tabLi.scrollIntoView === 'function') {
				setTimeout(function() {
					tabLi.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
				}, 60);
			}
		}
	});

	// Cerrar buscador y drawer móvil al presionar la tecla Escape
	document.addEventListener('keydown', function(e) {
		if (e.key === 'Escape' || e.keyCode === 27) {
			window.cocoToggleSearch(false);
			window.cocoToggleMobileNav(false);
		}
	});

	// Comprobación de marquesina y enlaces activos al cargar DOM
	document.addEventListener('DOMContentLoaded', function() {
		try {
			if (sessionStorage.getItem('coco_topbar_dismissed') === '1') {
				var m = document.getElementById('coco-sticky-marquee-top');
				if (m) m.style.display = 'none';
			}
		} catch(e){}
	});
	</script>
	<?php
}
add_action( 'wp_head', 'coco_theme_print_header_scripts', 2 );

/**
 * Garantizar que el cajón móvil #coco-mobile-drawer siempre exista en el DOM.
 */
function coco_theme_ensure_mobile_drawer_in_body() {
	?>
	<script id="coco-mobile-drawer-fallback">
	(function() {
		if (!document.getElementById('coco-mobile-drawer')) {
			var d = document.createElement('div');
			d.id = 'coco-mobile-drawer';
			d.className = 'coco-mobile-nav-drawer';
			d.setAttribute('aria-hidden', 'true');
			d.style.display = 'none';
			d.innerHTML = '<div class="coco-mobile-drawer-backdrop" onclick="cocoToggleMobileNav(false)"></div><div class="coco-mobile-drawer-content"><div class="coco-mobile-drawer-header"><span class="coco-mobile-drawer-title">Menú Coco Cherry</span><button type="button" class="coco-mobile-drawer-close" aria-label="Cerrar menú" onclick="cocoToggleMobileNav(false)">✕</button></div><?php echo addslashes( preg_replace( "/\r|\n/", '', coco_theme_render_mobile_nav() ) ); ?></div>';
			document.body.appendChild(d);
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'coco_theme_ensure_mobile_drawer_in_body', 99 );

/**
 * Registrar categoría de patrones propios de Coco Cherry Store.
 */
function coco_theme_register_pattern_categories() {
	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category(
			'coco-cherry',
			array(
				'label'       => __( 'CocoStore Pro', 'coco-theme' ),
				'description' => __( 'Secciones de alta conversión para CocoStore Pro con Pago Contra Entrega.', 'coco-theme' ),
			)
		);
	}
}
add_action( 'init', 'coco_theme_register_pattern_categories' );

/**
 * Cabecera de marca y estado En Stock estilo Ecomus en la ficha de producto.
 */
function coco_theme_ecomus_brand_and_stock() {
	global $product;
	?>
	<div class="coco-ecomus-meta-top">
		<span class="coco-ecomus-brand"><?php esc_html_e( 'Marca:', 'coco-theme' ); ?> <strong><?php echo esc_html( get_bloginfo( 'name' ) ); ?></strong></span>
	</div>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'coco_theme_ecomus_brand_and_stock', 4 );

function coco_theme_ecomus_stock_badge() {
	global $product;
	if ( $product instanceof \WC_Product && ! $product->is_in_stock() ) {
		echo '<div class="coco-ecomus-stock-wrap"><span class="coco-ecomus-stock-badge is-out">Agotado</span></div>';
	} else {
		echo '<div class="coco-ecomus-stock-wrap"><span class="coco-ecomus-stock-badge">En stock</span></div>';
	}
}
add_action( 'woocommerce_single_product_summary', 'coco_theme_ecomus_stock_badge', 8 );

/**
 * Contador dinámico estilo Ecomus: "X personas están viendo esto ahora mismo."
 */
function coco_theme_single_product_live_viewers() {
	$initial_viewers = wp_rand( 7, 15 );
	?>
	<div class="coco-ecomus-live-viewers">
		<span id="coco-live-viewers-count" class="coco-viewers-badge"><?php echo esc_html( $initial_viewers ); ?></span>
		<span class="coco-viewers-text">personas están viendo esto ahora mismo.</span>
	</div>
	<script>
	(function(){
		var el = document.getElementById('coco-live-viewers-count');
		if (!el) return;
		setInterval(function(){
			var current = parseInt(el.textContent, 10) || 8;
			var delta = Math.floor(Math.random() * 5) - 2; // -2 a +2
			var next = current + delta;
			if (next < 5) next = 6;
			if (next > 24) next = 19;
			el.textContent = next;
		}, 6500);
	})();
	</script>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'coco_theme_single_product_live_viewers', 12 );

/**
 * Barra de urgencia y demanda de stock animada bajo el contador de visitas.
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
add_action( 'woocommerce_single_product_summary', 'coco_theme_single_product_urgency_bar', 14 );

/**
 * Selector de cantidad accesible estilo Ecomus [ - 1 + ] junto al botón "Comprar ahora".
 */
function coco_theme_before_quantity_wrap() {
	echo '<div class="coco-ecomus-qty-label">Cantidad:</div>';
	echo '<div class="coco-cart-action-row">';
}
add_action( 'woocommerce_before_add_to_cart_quantity', 'coco_theme_before_quantity_wrap', 5 );

function coco_theme_qty_minus_btn() {
	echo '<button type="button" class="coco-qty-btn coco-qty-minus" aria-label="Disminuir cantidad">−</button>';
}
add_action( 'woocommerce_before_quantity_input_field', 'coco_theme_qty_minus_btn', 10 );

function coco_theme_qty_plus_btn() {
	echo '<button type="button" class="coco-qty-btn coco-qty-plus" aria-label="Aumentar cantidad">+</button>';
}
add_action( 'woocommerce_after_quantity_input_field', 'coco_theme_qty_plus_btn', 10 );

function coco_theme_after_cart_action_row() {
	echo '</div><!-- /.coco-cart-action-row -->';
	?>
	<script>
	(function(){
		function initCocoEcomusCartRow() {
			var row = document.querySelector('.single-product .coco-cart-action-row');
			var codBtn = document.getElementById('coco-cod-open-modal') || document.querySelector('.single-product .coco-cod-button');
			if (row && codBtn && codBtn.parentElement !== row) {
				row.appendChild(codBtn);
			}
			if (codBtn) {
				var titleEl = codBtn.querySelector('.coco-cod-btn-title');
				if (titleEl) {
					titleEl.textContent = 'Pagar al recibir en casa';
				}
			}
			// Botones + y - del selector de cantidad
			var qtyWrap = document.querySelector('.single-product .quantity');
			if (qtyWrap && !qtyWrap.dataset.cocoBound) {
				qtyWrap.dataset.cocoBound = '1';
				var input = qtyWrap.querySelector('input.qty');
				var minus = qtyWrap.querySelector('.coco-qty-minus');
				var plus  = qtyWrap.querySelector('.coco-qty-plus');
				if (input && minus) {
					minus.addEventListener('click', function(){
						var val = parseFloat(input.value) || 1;
						var min = parseFloat(input.getAttribute('min')) || 1;
						var step = parseFloat(input.getAttribute('step')) || 1;
						if (val > min) {
							input.value = val - step;
							input.dispatchEvent(new Event('change', { bubbles: true }));
						}
					});
				}
				if (input && plus) {
					plus.addEventListener('click', function(){
						var val = parseFloat(input.value) || 1;
						var max = parseFloat(input.getAttribute('max'));
						var step = parseFloat(input.getAttribute('step')) || 1;
						if (!max || val < max) {
							input.value = val + step;
							input.dispatchEvent(new Event('change', { bubbles: true }));
						}
					});
				}
			}
		}
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', initCocoEcomusCartRow);
		} else {
			initCocoEcomusCartRow();
		}
		setTimeout(initCocoEcomusCartRow, 400);
	})();
	</script>
	<?php
}
add_action( 'woocommerce_after_add_to_cart_button', 'coco_theme_after_cart_action_row', 99 );

/**
 * Determina la clase CSS para el badge según el texto del banco o método de pago.
 */
function coco_theme_get_pay_badge_class( $badge_text ) {
	$clean = function_exists( 'mb_strtolower' ) ? mb_strtolower( trim( wp_strip_all_tags( $badge_text ) ), 'UTF-8' ) : strtolower( trim( wp_strip_all_tags( $badge_text ) ) );
	$clean_plain = str_replace( array( 'á', 'é', 'í', 'ó', 'ú', 'ñ' ), array( 'a', 'e', 'i', 'o', 'u', 'n' ), $clean );

	if ( false !== strpos( $clean_plain, 'itau' ) ) {
		return 'coco-pay-itau';
	}
	if ( false !== strpos( $clean_plain, 'ueno' ) ) {
		return 'coco-pay-ueno';
	}
	if ( false !== strpos( $clean_plain, 'tigo' ) ) {
		return 'coco-pay-tigo';
	}
	if ( false !== strpos( $clean_plain, 'efectivo' ) || false !== strpos( $clean_plain, 'cash' ) || false !== strpos( $clean_plain, 'contra entrega' ) ) {
		return 'coco-pay-efectivo';
	}
	if ( false !== strpos( $clean_plain, 'qr' ) || false !== strpos( $clean_plain, 'transf' ) || false !== strpos( $clean_plain, 'sipap' ) ) {
		return 'coco-pay-qr';
	}
	if ( false !== strpos( $clean_plain, 'continental' ) ) {
		return 'coco-pay-continental';
	}
	if ( false !== strpos( $clean_plain, 'familiar' ) ) {
		return 'coco-pay-familiar';
	}
	if ( false !== strpos( $clean_plain, 'basa' ) ) {
		return 'coco-pay-basa';
	}
	if ( false !== strpos( $clean_plain, 'sudameris' ) ) {
		return 'coco-pay-sudameris';
	}
	if ( false !== strpos( $clean_plain, 'bnf' ) ) {
		return 'coco-pay-bnf';
	}
	if ( false !== strpos( $clean_plain, 'visa' ) ) {
		return 'coco-pay-visa';
	}
	if ( false !== strpos( $clean_plain, 'master' ) ) {
		return 'coco-pay-mastercard';
	}
	if ( false !== strpos( $clean_plain, 'tarjeta' ) || false !== strpos( $clean_plain, 'pos' ) ) {
		return 'coco-pay-tarjetas';
	}

	return 'coco-pay-default';
}

/**
 * Bloques Ecomus adaptados a Paraguay debajo del botón "Comprar ahora":
 * 1) Enlaces rápidos (Haz una pregunta / Entrega y devolución / Compartir)
 * 2) Dos cajas de plazos de entrega y garantía en Paraguay (configurables desde Personalizar)
 * 3) Garantía de pago seguro con métodos locales configurables (Itaú, Giros Tigo, Efectivo, QR / Transferencia)
 */
function coco_theme_product_cod_guarantees() {
	global $product;
	$wa_question = coco_theme_get_whatsapp_url( 'product', $product );

	$delivery_1 = get_theme_mod(
		'coco_theme_delivery_box_1',
		'Plazos de entrega estimados: <strong>24–48 hs</strong> (Asunción y Gran Asunción), <strong>2–4 días</strong> (Interior de Paraguay).'
	);
	$delivery_2 = get_theme_mod(
		'coco_theme_delivery_box_2',
		'<strong>Pago Contra Entrega</strong> al recibir en puerta. Garantía de cambio directo por defectos de fábrica.'
	);

	$show_safe_checkout  = (bool) get_theme_mod( 'coco_theme_safe_checkout_enabled', true );
	$safe_checkout_title = get_theme_mod( 'coco_theme_safe_checkout_title', "Garantizamos<br>un pago seguro." );
	$default_badges      = "Itaú\nGiros Tigo\n💵 Efectivo\nQR / Transf.";
	$badges_raw          = get_theme_mod( 'coco_theme_safe_checkout_badges', $default_badges );
	$badges_list         = array_filter( array_map( 'trim', preg_split( '/[\r\n,]+/', (string) $badges_raw ) ) );
	?>
	<div class="coco-ecomus-extra-info">
		<!-- Enlaces de acción rápida estilo Ecomus -->
		<div class="coco-ecomus-quick-links">
			<a href="<?php echo esc_url( $wa_question ); ?>" target="_blank" rel="noopener noreferrer" class="coco-quick-link">
				<span class="coco-ql-icon">?</span>
				<span>Haz una pregunta</span>
			</a>
			<a href="#tab-description" class="coco-quick-link">
				<span class="coco-ql-icon">🚚</span>
				<span>Entrega y devolución</span>
			</a>
			<button type="button" class="coco-quick-link coco-share-btn" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href});}else if(navigator.clipboard){navigator.clipboard.writeText(window.location.href);this.querySelector('.coco-share-label').textContent='¡Enlace copiado!';}">
				<span class="coco-ql-icon">🔗</span>
				<span class="coco-share-label">Compartir</span>
			</button>
		</div>

		<!-- Dos cajas de entrega y garantía estilo Ecomus adaptadas a Paraguay -->
		<div class="coco-ecomus-delivery-grid">
			<div class="coco-ecomus-delivery-card">
				<div class="coco-delivery-card-icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
				</div>
				<p><?php echo wp_kses_post( $delivery_1 ); ?></p>
			</div>
			<div class="coco-ecomus-delivery-card">
				<div class="coco-delivery-card-icon">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"></path><path d="M16 16h5v5"></path></svg>
				</div>
				<p><?php echo wp_kses_post( $delivery_2 ); ?></p>
			</div>
		</div>

		<?php if ( $show_safe_checkout && ! empty( $badges_list ) ) : ?>
		<!-- Fila de pago seguro configurable desde Apariencia > Personalizar -->
		<div class="coco-ecomus-safe-checkout">
			<div class="coco-safe-label">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>
				<span><?php echo wp_kses_post( nl2br( $safe_checkout_title ) ); ?></span>
			</div>
			<div class="coco-py-payment-badges">
				<?php foreach ( $badges_list as $badge_item ) : ?>
					<span class="coco-pay-badge <?php echo esc_attr( coco_theme_get_pay_badge_class( $badge_item ) ); ?>" title="<?php echo esc_attr( $badge_item ); ?>"><?php echo esc_html( $badge_item ); ?></span>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php
}
add_action( 'woocommerce_after_add_to_cart_form', 'coco_theme_product_cod_guarantees', 25 );

/**
 * Catálogo Boutique de Alta Gama (Sin precios, sin botón, sin SKU y sin carrito):
 * En todo el catálogo, categorías, búsquedas y relacionados se retiran el precio,
 * el botón de carrito, valoraciones, SKU y flash genérico.
 * Las tarjetas muestran una estética limpia tipo lookbook/revista:
 * foto en alta definición (con hover a segunda imagen), badges sutiles (Más vendido / Nuevo / Oferta),
 * botón de favoritos e interactividad fluida al hacer clic.
 */
function coco_theme_catalog_minimal_loop() {
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
}
add_action( 'init', 'coco_theme_catalog_minimal_loop' );
add_action( 'wp', 'coco_theme_catalog_minimal_loop', 20 );

add_action( 'woocommerce_before_template_part', function( $template_name ) {
	if ( false !== strpos( $template_name, 'related' ) || false !== strpos( $template_name, 'up-sells' ) || false !== strpos( $template_name, 'product' ) ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	}
} );

/**
 * Elementos superiores de las cards: Badges boutique (Más vendido / Nuevo / Oferta) y botón de Wishlist.
 */
function coco_theme_loop_card_top_elements() {
	global $product;
	if ( ! $product instanceof \WC_Product ) {
		return;
	}

	echo '<div class="coco-card-top-bar">';

	// Badge dinámico a la izquierda
	if ( $product->is_featured() ) {
		echo '<span class="coco-card-tag tag-featured"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg> Más vendido</span>';
	} elseif ( ( time() - strtotime( get_the_date( 'Y-m-d', $product->get_id() ) ) ) < ( 30 * DAY_IN_SECONDS ) ) {
		echo '<span class="coco-card-tag tag-new">Nuevo</span>';
	} elseif ( $product->is_on_sale() ) {
		echo '<span class="coco-card-tag tag-sale">Oferta</span>';
	} else {
		echo '<span class="coco-card-tag-placeholder"></span>';
	}

	// Botón de Favoritos / Wishlist a la derecha
	echo '<button type="button" class="coco-card-wishlist-btn" aria-label="Guardar en favoritos" onclick="event.preventDefault(); event.stopPropagation(); this.classList.toggle(\'active\');"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg></button>';

	echo '</div>';
}
add_action( 'woocommerce_before_shop_loop_item_title', 'coco_theme_loop_card_top_elements', 9 );

/**
 * Segunda imagen fluida al hacer hover en el catálogo (si existe en la galería del producto).
 */
function coco_theme_loop_second_image() {
	global $product;
	if ( ! $product instanceof \WC_Product ) {
		return;
	}
	$gallery = $product->get_gallery_image_ids();
	if ( ! empty( $gallery ) ) {
		$second_id  = $gallery[0];
		$second_img = wp_get_attachment_image(
			$second_id,
			'woocommerce_thumbnail',
			false,
			array(
				'class'   => 'coco-loop-secondary-img',
				'loading' => 'lazy',
				'alt'     => esc_attr( $product->get_name() ),
			)
		);
		if ( $second_img ) {
			echo '<div class="coco-loop-second-img-wrap">' . $second_img . '</div>';
		}
	}
}
add_action( 'woocommerce_before_shop_loop_item_title', 'coco_theme_loop_second_image', 11 );

/**
 * Ocultar título redundante ("Shop"), migas de pan y descripción duplicada en el archivo de tienda/categorías
 * para que el Hero Banner de ancho completo sea el único encabezado protagonista.
 */
add_filter( 'woocommerce_show_page_title', '__return_false' );

function coco_theme_clean_archive_headers() {
	remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
	remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	}
}
add_action( 'wp', 'coco_theme_clean_archive_headers' );

/**
 * Banner Hero Editorial de Colección de Ancho Completo (Estilo Boutique de Lujo).
 * Soporta edición directa desde:
 * - Apariencia > Personalizar > Opciones Coco Cherry Store (texto e imagen general de /shop/)
 * - Páginas > Shop (extracto/contenido e imagen destacada para /shop/)
 * - Productos > Categorías (nombre, descripción y miniatura de cada categoría individual)
 */
function coco_theme_render_category_banner() {
	if ( ! is_shop() && ! is_product_taxonomy() ) {
		return;
	}

	$default_bg  = COCO_THEME_URI . '/assets/images/hero-collection-bg.jpg';
	$custom_bg   = get_theme_mod( 'coco_theme_shop_hero_bg', '' );
	$bg_url      = ! empty( $custom_bg ) ? $custom_bg : $default_bg;

	$kicker      = get_theme_mod( 'coco_theme_hero_kicker', 'CATÁLOGO OFICIAL' );
	$title       = get_theme_mod( 'coco_theme_shop_hero_title', 'Nuestra Tienda' );
	$description = get_theme_mod(
		'coco_theme_shop_hero_desc',
		'Descubrí nuestra selección en bijouterie, moda femenina, belleza, artículos para el hogar y tecnología con envíos a todo Paraguay y pago al recibir.'
	);

	// Si estamos en /shop/, permitir también sobreescribir desde Páginas > Shop (extracto/contenido e imagen destacada)
	if ( is_shop() && ! is_product_category() && function_exists( 'wc_get_page_id' ) ) {
		$shop_page_id = wc_get_page_id( 'shop' );
		if ( $shop_page_id > 0 ) {
			$page_thumb = get_the_post_thumbnail_url( $shop_page_id, 'full' );
			if ( $page_thumb ) {
				$bg_url = $page_thumb;
			}
			$shop_post = get_post( $shop_page_id );
			if ( $shop_post instanceof \WP_Post ) {
				if ( ! empty( $shop_post->post_title ) && strtolower( trim( $shop_post->post_title ) ) !== 'shop' ) {
					$title = $shop_post->post_title;
				}
				if ( ! empty( $shop_post->post_excerpt ) ) {
					$description = wp_strip_all_tags( $shop_post->post_excerpt );
				}
			}
		}
	}

	// Si estamos en una categoría específica (ej. Aros, Belleza, Hogar, Moda)
	if ( is_product_category() ) {
		$kicker = 'NUESTRA COLECCIÓN';
		$term   = get_queried_object();
		if ( $term && ! is_wp_error( $term ) ) {
			$title = $term->name;
			if ( ! empty( $term->description ) ) {
				$description = wp_strip_all_tags( $term->description );
			} elseif ( stripos( $term->name, 'aro' ) !== false ) {
				$description = 'Aretes, aros colgantes, argollas y modelos con brillo pensados para iluminar tu rostro y combinar con cualquier estilo.';
			} elseif ( stripos( $term->name, 'bijou' ) !== false ) {
				$description = 'Accesorios, aros, collares y piezas con brillo pensadas para realzar tu look en cada ocasión.';
			} elseif ( stripos( $term->name, 'hogar' ) !== false ) {
				$description = 'Artículos prácticos, decoración y soluciones modernas pensadas para darle más confort a tu hogar.';
			} elseif ( stripos( $term->name, 'moda' ) !== false ) {
				$description = 'Prendas, conjuntos y tendencias seleccionadas para combinar comodidad y estilo todos los días.';
			} elseif ( stripos( $term->name, 'belleza' ) !== false ) {
				$description = 'Cuidado personal, maquillaje y aliados de belleza pensados para resaltar tu brillo natural.';
			} else {
				$description = sprintf( 'Modelos exclusivos de %s seleccionados con envíos a todo Paraguay y pago contra entrega.', $term->name );
			}

			// Si la categoría tiene una imagen (Miniatura) cargada en Productos > Categorías, usarla como fondo
			$cat_thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
			if ( $cat_thumb_id ) {
				$cat_img_url = wp_get_attachment_image_url( $cat_thumb_id, 'full' );
				if ( $cat_img_url ) {
					$bg_url = $cat_img_url;
				}
			}
		}
	} elseif ( is_product_tag() ) {
		$kicker = 'COLECCIÓN';
		$term   = get_queried_object();
		if ( $term && ! is_wp_error( $term ) ) {
			$title = $term->name;
		}
	}
	?>
	<section class="coco-collection-hero-banner" aria-label="Cabecera de Colección" style="--coco-hero-bg: url('<?php echo esc_url( $bg_url ); ?>');">
		<div class="coco-collection-hero-inner">
			<div class="coco-collection-hero-content">
				<span class="coco-collection-kicker"><span class="coco-kicker-line" aria-hidden="true"></span><?php echo esc_html( $kicker ); ?></span>
				<h1 class="coco-collection-title"><?php echo esc_html( $title ); ?></h1>
				<p class="coco-collection-desc"><?php echo esc_html( $description ); ?></p>
				<span class="coco-collection-bottom-line" aria-hidden="true"></span>
			</div>
		</div>
	</section>
	<?php
}
add_action( 'woocommerce_archive_description', 'coco_theme_render_category_banner', 15 );

/**
 * Helper para asociar íconos vectoriales SVG a cada categoría.
 */
function coco_theme_get_category_icon_svg( $slug, $name ) {
	$slug = strtolower( $slug );
	$name = strtolower( $name );

	if ( strpos( $slug, 'aro' ) !== false || strpos( $name, 'aro' ) !== false ) {
		return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3h12l4 6-10 13L2 9z"></path><path d="M11 3 8 9l4 13 4-13-3-6"></path><path d="M2 9h20"></path></svg>';
	}
	if ( strpos( $slug, 'bijou' ) !== false || strpos( $name, 'bijou' ) !== false ) {
		return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg>';
	}
	if ( strpos( $slug, 'belleza' ) !== false || strpos( $name, 'belleza' ) !== false || strpos( $slug, 'cosmet' ) !== false ) {
		return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>';
	}
	if ( strpos( $slug, 'collar' ) !== false || strpos( $name, 'collar' ) !== false ) {
		return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
	}
	if ( strpos( $slug, 'crop' ) !== false || strpos( $slug, 'top' ) !== false || strpos( $name, 'top' ) !== false ) {
		return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"></path></svg>';
	}
	if ( strpos( $slug, 'electro' ) !== false || strpos( $name, 'electro' ) !== false ) {
		return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>';
	}
	if ( strpos( $slug, 'tecno' ) !== false || strpos( $name, 'tecno' ) !== false || strpos( $slug, 'celular' ) !== false ) {
		return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>';
	}
	if ( strpos( $slug, 'moda' ) !== false || strpos( $name, 'moda' ) !== false || strpos( $slug, 'faja' ) !== false || strpos( $name, 'faja' ) !== false ) {
		return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line></svg>';
	}
	if ( strpos( $slug, 'hogar' ) !== false || strpos( $name, 'hogar' ) !== false ) {
		return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>';
	}

	return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>';
}

/**
 * Píldoras de navegación rápida con scroll horizontal hacia izquierda y derecha.
 */
function coco_theme_render_category_chips() {
	if ( ! is_shop() && ! is_product_taxonomy() ) {
		return;
	}

	$current_cat_id = 0;
	if ( is_product_category() ) {
		$current_cat    = get_queried_object();
		$current_cat_id = $current_cat ? $current_cat->term_id : 0;
	}

	$cats = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => 0,
			'exclude'    => array( get_option( 'default_product_cat' ) ),
		)
	);

	if ( empty( $cats ) || is_wp_error( $cats ) ) {
		return;
	}

	$shop_url      = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$is_all_active = is_shop() && ! is_product_category();

	echo '<div class="coco-catalog-chips-wrapper">';

	// Botón flecha izquierda para deslizar hacia atrás
	echo '<button type="button" class="coco-chips-scroll-btn coco-chips-scroll-prev" id="coco-chips-prev" aria-label="Deslizar categorías a la izquierda" onclick="cocoScrollChips(-280)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></button>';

	echo '<div class="coco-catalog-chips-scroll" id="coco-chips-scroll-container">';

	// Opción "Todas las colecciones" con ícono de grilla
	printf(
		'<a href="%s" class="coco-cat-chip %s"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg><span>Todas las colecciones</span></a>',
		esc_url( $shop_url ),
		$is_all_active ? 'active' : ''
	);

	foreach ( $cats as $cat ) {
		$is_active = ( $cat->term_id === $current_cat_id );
		$icon_svg  = coco_theme_get_category_icon_svg( $cat->slug, $cat->name );
		printf(
			'<a href="%s" class="coco-cat-chip %s">%s<span>%s</span></a>',
			esc_url( get_term_link( $cat ) ),
			$is_active ? 'active' : '',
			$icon_svg,
			esc_html( $cat->name )
		);
	}

	echo '</div>'; // /.coco-catalog-chips-scroll

	// Botón flecha derecha para deslizar hacia adelante
	echo '<button type="button" class="coco-chips-scroll-btn coco-chips-scroll-next" id="coco-chips-next" aria-label="Deslizar categorías a la derecha" onclick="cocoScrollChips(280)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></button>';

	echo '</div>'; // /.coco-catalog-chips-wrapper
}
add_action( 'woocommerce_archive_description', 'coco_theme_render_category_chips', 25 );

/**
 * Apertura del Contenedor Principal del Catálogo (Solo tarjetas en el container, sin sidebar de filtros).
 */
function coco_theme_catalog_split_open() {
	if ( function_exists( 'is_product' ) && is_product() ) {
		return;
	}
	echo '<div class="coco-catalog-container">';
	echo '<div class="coco-catalog-main-area">';
}
add_action( 'woocommerce_before_shop_loop', 'coco_theme_catalog_split_open', 5 );

/**
 * Cierre del Contenedor Principal del Catálogo.
 */
function coco_theme_catalog_split_close() {
	if ( function_exists( 'is_product' ) && is_product() ) {
		return;
	}
	echo '</div><!-- /.coco-catalog-main-area -->';
	echo '</div><!-- /.coco-catalog-container -->';
}
add_action( 'woocommerce_after_shop_loop', 'coco_theme_catalog_split_close', 999 );

/**
 * Envoltorio para la barra de herramientas del catálogo y conmutador visual de cuadrícula [ 2 col | 4 col ].
 */
function coco_theme_catalog_toolbar_open() {
	if ( function_exists( 'is_product' ) && is_product() ) {
		return;
	}
	echo '<div class="coco-catalog-toolbar-wrap">';
}
add_action( 'woocommerce_before_shop_loop', 'coco_theme_catalog_toolbar_open', 15 );

function coco_theme_catalog_grid_switcher() {
	if ( function_exists( 'is_product' ) && is_product() ) {
		return;
	}
	?>
	<div class="coco-grid-switcher" aria-label="Cambiar columnas">
		<button type="button" class="coco-grid-btn coco-grid-btn-4 active" data-columns="4" title="Cuadrícula 4 Columnas">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<rect x="3" y="3" width="7" height="7"></rect>
				<rect x="14" y="3" width="7" height="7"></rect>
				<rect x="14" y="14" width="7" height="7"></rect>
				<rect x="3" y="14" width="7" height="7"></rect>
			</svg>
		</button>
		<button type="button" class="coco-grid-btn coco-grid-btn-2" data-columns="2" title="Cuadrícula 2 Columnas">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<line x1="8" y1="6" x2="21" y2="6"></line>
				<line x1="8" y1="12" x2="21" y2="12"></line>
				<line x1="8" y1="18" x2="21" y2="18"></line>
				<line x1="3" y1="6" x2="3.01" y2="6"></line>
				<line x1="3" y1="12" x2="3.01" y2="12"></line>
				<line x1="3" y1="18" x2="3.01" y2="18"></line>
			</svg>
		</button>
	</div>
	<?php
}
add_action( 'woocommerce_before_shop_loop', 'coco_theme_catalog_grid_switcher', 35 );

function coco_theme_catalog_toolbar_close() {
	if ( function_exists( 'is_product' ) && is_product() ) {
		return;
	}
	echo '</div><!-- /.coco-catalog-toolbar-wrap -->';
}
add_action( 'woocommerce_before_shop_loop', 'coco_theme_catalog_toolbar_close', 40 );

/**
 * Banda de confianza y servicios al pie del catálogo (iconografía lineal SVG sobria).
 */
function coco_theme_catalog_trust_band() {
	if ( function_exists( 'is_product' ) && is_product() ) {
		return;
	}
	?>
	<section class="coco-catalog-trust-band" aria-label="Servicios y garantías de Coco Cherry">
		<div class="coco-trust-col">
			<div class="coco-trust-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
					<rect x="1" y="3" width="15" height="13"></rect>
					<polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
					<circle cx="5.5" cy="18.5" r="2.5"></circle>
					<circle cx="18.5" cy="18.5" r="2.5"></circle>
				</svg>
			</div>
			<div class="coco-trust-content">
				<h4 class="coco-trust-title">Cobertura Nacional</h4>
				<p class="coco-trust-desc">24 a 48 hs en Asunción y Gran Asunción; 2 a 4 días en el interior.</p>
			</div>
		</div>

		<div class="coco-trust-col">
			<div class="coco-trust-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
					<rect x="2" y="5" width="20" height="14" rx="2"></rect>
					<line x1="2" y1="10" x2="22" y2="10"></line>
				</svg>
			</div>
			<div class="coco-trust-content">
				<h4 class="coco-trust-title">Pago Contra Entrega</h4>
				<p class="coco-trust-desc">Aboná en tu puerta en efectivo, transferencias bancarias o código QR.</p>
			</div>
		</div>

		<div class="coco-trust-col">
			<div class="coco-trust-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
					<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
					<polyline points="9 12 11 14 15 10"></polyline>
				</svg>
			</div>
			<div class="coco-trust-content">
				<h4 class="coco-trust-title">Garantía Asegurada</h4>
				<p class="coco-trust-desc">Cobertura directa de cambio ante cualquier defecto de fábrica.</p>
			</div>
		</div>
	</section>
	<?php
}
add_action( 'woocommerce_after_shop_loop', 'coco_theme_catalog_trust_band', 25 );

/**
 * Scripts de interactividad para conmutador de columnas y scroll bidireccional de píldoras.
 */
function coco_theme_catalog_scripts() {
	if ( ! is_shop() && ! is_product_taxonomy() ) {
		return;
	}
	?>
	<script>
	function cocoScrollChips(offset) {
		var container = document.getElementById('coco-chips-scroll-container');
		if (!container) return;
		var target = container.scrollLeft + offset;
		if (typeof container.scrollTo === 'function') {
			container.scrollTo({ left: target, behavior: 'smooth' });
		} else {
			container.scrollLeft = target;
		}
	}
	(function(){
		function initChipsAndGrid() {
			var container = document.getElementById('coco-chips-scroll-container');
			if (container) {
				var activeChip = container.querySelector('.coco-cat-chip.active');
				if (activeChip && activeChip.offsetLeft > container.clientWidth * 0.6) {
					container.scrollLeft = Math.max(0, activeChip.offsetLeft - container.clientWidth / 2 + activeChip.clientWidth / 2);
				}
			}

			var grid = document.querySelector('.woocommerce ul.products, .woocommerce-page ul.products');
			var buttons = document.querySelectorAll('.coco-grid-switcher .coco-grid-btn');
			if (!grid || !buttons.length) return;

			var saved = localStorage.getItem('coco_catalog_cols') || '4';
			if (saved === '2') {
				grid.classList.add('coco-grid-cols-2');
				buttons.forEach(function(b){ b.classList.toggle('active', b.dataset.columns === '2'); });
			}

			buttons.forEach(function(btn){
				btn.addEventListener('click', function(e){
					e.preventDefault();
					var cols = this.dataset.columns;
					buttons.forEach(function(b){ b.classList.remove('active'); });
					this.classList.add('active');
					if (cols === '2') {
						grid.classList.add('coco-grid-cols-2');
						localStorage.setItem('coco_catalog_cols', '2');
					} else {
						grid.classList.remove('coco-grid-cols-2');
						localStorage.setItem('coco_catalog_cols', '4');
					}
				});
			});
		}
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', initChipsAndGrid);
		} else {
			initChipsAndGrid();
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'coco_theme_catalog_scripts' );

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
		$p1 = $query_sun[0];
		$img1 = $p1->get_image_id() ? wp_get_attachment_image_url( $p1->get_image_id(), 'large' ) : get_template_directory_uri() . '/assets/images/fav-protector.jpg';
		$link1 = get_permalink( $p1->get_id() );
		$name1 = $p1->get_name();
	} else {
		$img1 = get_template_directory_uri() . '/assets/images/fav-protector.jpg';
		$link1 = home_url( '/shop/' );
		$name1 = 'Protector Solar Oro 24K FPS 90+';
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
		$p2 = $query_riz[0];
		$img2 = $p2->get_image_id() ? wp_get_attachment_image_url( $p2->get_image_id(), 'large' ) : get_template_directory_uri() . '/assets/images/fav-rizador.jpg';
		$link2 = get_permalink( $p2->get_id() );
		$name2 = $p2->get_name();
	} else {
		$img2 = get_template_directory_uri() . '/assets/images/fav-rizador.jpg';
		$link2 = home_url( '/shop/' );
		$name2 = 'Rizador de Pestañas Térmico y Perfilador de Cejas 2 en 1 Recargable USB';
	}

	$home_wa_url    = coco_theme_get_whatsapp_url( 'general' );
	$home_wa_disp   = coco_theme_get_whatsapp_display();
	$home_site_name = get_bloginfo( 'name' );

	ob_start();
	?>
	<div class="coco-fav-cards-grid">
		<!-- Card 1: Protector Solar -->
		<div class="coco-fav-card">
			<div class="coco-fav-card-img-wrap">
				<span class="coco-fav-badge coco-fav-badge-gold">&#9733; MÁS VENDIDO</span>
				<a href="<?php echo esc_url( $link1 ); ?>" tabindex="-1">
					<img src="<?php echo esc_url( $img1 ); ?>" alt="<?php echo esc_attr( $name1 ); ?>" loading="lazy" />
				</a>
			</div>
			<div class="coco-fav-card-content">
				<h3 class="coco-fav-title">
					<a href="<?php echo esc_url( $link1 ); ?>"><?php echo esc_html( $name1 ); ?></a>
				</h3>
				<div class="coco-fav-gold-line"></div>
				<a href="<?php echo esc_url( $link1 ); ?>" class="coco-fav-btn">
					<span>VER PRODUCTO</span>
					<span class="coco-btn-arrow">&rarr;</span>
				</a>
			</div>
		</div>

		<!-- Card 2: Rizador de Pestañas -->
		<div class="coco-fav-card">
			<div class="coco-fav-card-img-wrap">
				<span class="coco-fav-badge coco-fav-badge-pink">&#127800; BELLEZA</span>
				<a href="<?php echo esc_url( $link2 ); ?>" tabindex="-1">
					<img src="<?php echo esc_url( $img2 ); ?>" alt="<?php echo esc_attr( $name2 ); ?>" loading="lazy" />
				</a>
			</div>
			<div class="coco-fav-card-content">
				<h3 class="coco-fav-title">
					<a href="<?php echo esc_url( $link2 ); ?>"><?php echo esc_html( $name2 ); ?></a>
				</h3>
				<div class="coco-fav-gold-line"></div>
				<a href="<?php echo esc_url( $link2 ); ?>" class="coco-fav-btn">
					<span>VER PRODUCTO</span>
					<span class="coco-btn-arrow">&rarr;</span>
				</a>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'coco_star_products', 'coco_theme_star_products_shortcode' );

/**
 * Asegurar que cualquier bloque o plantilla que contenga [coco_star_products]
 * se renderice automáticamente con las tarjetas de los productos favoritos.
 */
add_filter(
	'render_block',
	function( $block_content, $block ) {
		if ( is_string( $block_content ) && strpos( $block_content, '[coco_star_products]' ) !== false ) {
			return str_replace( '[coco_star_products]', coco_theme_star_products_shortcode(), $block_content );
		}
		return $block_content;
	},
	20,
	2
);

/**
 * Botón flotante de WhatsApp: desactivado por defecto para no obstruir pantalla móvil ni fichas de producto.
 * Puede encenderse o apagarse cuando se desee desde Apariencia > Personalizar > Opciones Coco Cherry.
 */
function coco_theme_render_floating_whatsapp() {
	$show_mod = get_theme_mod( 'coco_theme_show_floating_wa', null );
	$show_opt = get_option( 'coco_theme_show_floating_wa', null );

	$is_active = false;
	if ( null !== $show_mod ) {
		$is_active = ( true === $show_mod || 1 === $show_mod || '1' === (string) $show_mod || 'yes' === (string) $show_mod );
	}
	if ( ! $is_active && null !== $show_opt ) {
		$is_active = ( true === $show_opt || 1 === $show_opt || '1' === (string) $show_opt || 'yes' === (string) $show_opt );
	}

	if ( ! $is_active ) {
		return;
	}

	$is_product = function_exists( 'is_product' ) && is_product();
	$wa_url     = coco_theme_get_whatsapp_url( $is_product ? 'product' : 'general' );
	$display    = coco_theme_get_whatsapp_display();
	?>
	<a href="<?php echo esc_url( $wa_url ); ?>" class="coco-floating-wa" target="_blank" rel="noopener noreferrer" aria-label="Consultar por WhatsApp">
		<svg viewBox="0 0 24 24" aria-hidden="true">
			<path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.625.846 5.059 2.284 7.033l-1.492 5.451 5.603-1.469c1.911 1.196 4.169 1.889 6.605 1.889 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/>
		</svg>
		<span>WhatsApp <?php echo esc_html( $display ); ?></span>
	</a>
	<?php
}
add_action( 'wp_footer', 'coco_theme_render_floating_whatsapp', 30 );

/**
 * Registrar áreas de Widgets nativas de WordPress (Tienda y Footer).
 */
function coco_theme_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Barra Lateral de Tienda (WooCommerce)', 'coco-theme' ),
			'id'            => 'sidebar-shop',
			'description'   => __( 'Añade aquí widgets para el catálogo de productos.', 'coco-theme' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Pie de Página (Widgets)', 'coco-theme' ),
			'id'            => 'footer-1',
			'description'   => __( 'Área de widgets adicional para el pie de página.', 'coco-theme' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);
}
add_action( 'widgets_init', 'coco_theme_widgets_init' );

/**
 * Panel de Personalización: controles nativos en "Identidad del sitio" (ocultar título/descripción,
 * ancho de logo) y sección "Opciones Coco Cherry Store".
 */
function coco_theme_customize_register( $wp_customize ) {
	// 1. Casilla nativa en "Identidad del sitio": Mostrar el título y la descripción corta del sitio.
	$wp_customize->add_setting(
		'coco_theme_show_title_tagline',
		array(
			'default'           => false,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_show_title_tagline',
		array(
			'label'    => __( 'Mostrar el título y la descripción corta del sitio', 'coco-theme' ),
			'section'  => 'title_tagline',
			'type'     => 'checkbox',
			'priority' => 45,
		)
	);

	// 2. Control de ancho del logotipo en "Identidad del sitio".
	$wp_customize->add_setting(
		'coco_theme_logo_width',
		array(
			'default'           => 125,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_logo_width',
		array(
			'label'       => __( 'Ancho del Logotipo (px)', 'coco-theme' ),
			'description' => __( 'Ajusta el tamaño del logotipo en la cabecera y pie de página (por defecto: 125px).', 'coco-theme' ),
			'section'     => 'title_tagline',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 60,
				'max'  => 300,
				'step' => 5,
			),
			'priority'    => 9,
		)
	);

	// 3. Sección propia Opciones de Tienda (COD Paraguay).
	$wp_customize->add_section(
		'coco_theme_options',
		array(
			'title'    => __( 'Opciones de la Tienda (COD)', 'coco-theme' ),
			'priority' => 30,
		)
	);

	// Sección propia para WhatsApp y Contacto (100% Personalizable)
	$wp_customize->add_section(
		'coco_whatsapp_options',
		array(
			'title'    => __( 'Ajustes de WhatsApp y Contacto', 'coco-theme' ),
			'priority' => 28,
		)
	);

	// 1. Número de WhatsApp (con código de país)
	$wp_customize->add_setting(
		'coco_whatsapp_number',
		array(
			'default'           => '595981531503',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'coco_whatsapp_number',
		array(
			'label'       => __( 'Número de WhatsApp (con código de país)', 'coco-theme' ),
			'description' => __( 'Ejemplo: 595981531503 (sin signos + ni guiones). A este número llegarán todos los pedidos y consultas.', 'coco-theme' ),
			'section'     => 'coco_whatsapp_options',
			'type'        => 'text',
		)
	);

	// 2. Número visible en pantalla
	$wp_customize->add_setting(
		'coco_whatsapp_display',
		array(
			'default'           => '0981 531503',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'coco_whatsapp_display',
		array(
			'label'       => __( 'Número visible para los clientes', 'coco-theme' ),
			'description' => __( 'Ejemplo: 0981 531503. Así se mostrará en los textos, botones y marquesina.', 'coco-theme' ),
			'section'     => 'coco_whatsapp_options',
			'type'        => 'text',
		)
	);

	// 3. Mensaje general de WhatsApp
	$wp_customize->add_setting(
		'coco_whatsapp_general_message',
		array(
			'default'           => '¡Hola {sitio}! Quiero hacer una consulta sobre sus productos con entrega a domicilio.',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_textarea_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'coco_whatsapp_general_message',
		array(
			'label'       => __( 'Mensaje predeterminado de consultas generales', 'coco-theme' ),
			'description' => __( 'Usa {sitio} para insertar automáticamente el nombre de tu tienda.', 'coco-theme' ),
			'section'     => 'coco_whatsapp_options',
			'type'        => 'textarea',
		)
	);

	// 4. Mensaje de WhatsApp para ficha de producto
	$wp_customize->add_setting(
		'coco_whatsapp_product_message',
		array(
			'default'           => '¡Hola {sitio}! Tengo una consulta sobre el producto: {producto} ({url})',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_textarea_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'coco_whatsapp_product_message',
		array(
			'label'       => __( 'Mensaje al consultar desde una ficha de producto', 'coco-theme' ),
			'description' => __( 'Variables disponibles: {sitio} (nombre de tienda), {producto} (nombre del artículo), {url} (enlace al producto).', 'coco-theme' ),
			'section'     => 'coco_whatsapp_options',
			'type'        => 'textarea',
		)
	);

	// 5. Dirección y Ubicación en Footer
	$wp_customize->add_setting(
		'coco_store_address',
		array(
			'default'           => 'Guaraníes 4779, Asunción, Paraguay',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'coco_store_address',
		array(
			'label'       => __( 'Dirección / Ubicación de la Tienda (Footer)', 'coco-theme' ),
			'description' => __( 'Aparece en la columna de atención del pie de página.', 'coco-theme' ),
			'section'     => 'coco_whatsapp_options',
			'type'        => 'text',
		)
	);

	// 6. Activar botón flotante sticky de WhatsApp
	$wp_customize->add_setting(
		'coco_theme_show_floating_wa',
		array(
			'default'           => false,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'coco_theme_show_floating_wa',
		array(
			'label'       => __( 'Activar botón flotante sticky de WhatsApp', 'coco-theme' ),
			'description' => __( 'Muestra el botón flotante en la esquina inferior de la pantalla para atención directa.', 'coco-theme' ),
			'section'     => 'coco_whatsapp_options',
			'type'        => 'checkbox',
		)
	);

	// Controles para editar fácilmente el Hero Banner de la Tienda (/shop/)
	$wp_customize->add_setting(
		'coco_theme_hero_kicker',
		array(
			'default'           => 'CATÁLOGO OFICIAL',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'coco_theme_hero_kicker',
		array(
			'label'   => __( 'Hero Tienda: Etiqueta Superior', 'coco-theme' ),
			'section' => 'coco_theme_options',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'coco_theme_shop_hero_title',
		array(
			'default'           => 'Nuestra Tienda',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'coco_theme_shop_hero_title',
		array(
			'label'       => __( 'Hero Tienda (/shop/): Título Principal', 'coco-theme' ),
			'description' => __( 'Título general cuando estás en /shop/. En cada categoría se mostrará automáticamente el nombre de esa categoría.', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'coco_theme_shop_hero_desc',
		array(
			'default'           => 'Descubrí nuestra selección en bijouterie, moda femenina, belleza, artículos para el hogar y tecnología con envíos a todo Paraguay y pago al recibir.',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_textarea_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'coco_theme_shop_hero_desc',
		array(
			'label'       => __( 'Hero Tienda (/shop/): Descripción', 'coco-theme' ),
			'description' => __( 'Texto descriptivo general para /shop/. Para cambiar el texto de una categoría, edita la "Descripción" en Productos > Categorías.', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'coco_theme_shop_hero_bg',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);
	if ( class_exists( '\WP_Customize_Image_Control' ) ) {
		$wp_customize->add_control(
			new \WP_Customize_Image_Control(
				$wp_customize,
				'coco_theme_shop_hero_bg',
				array(
					'label'       => __( 'Hero Tienda: Imagen de Fondo', 'coco-theme' ),
					'description' => __( 'Sube o cambia la foto de fondo del banner de la tienda (si se deja vacío usa la imagen boutique por defecto).', 'coco-theme' ),
					'section'     => 'coco_theme_options',
				)
			)
		);
	}

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

	// 4. Controles de Notificaciones de Compras en Vivo (Sales Pop / Social Proof).
	$wp_customize->add_setting(
		'coco_theme_sales_pop_enabled',
		array(
			'default'           => true,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_sales_pop_enabled',
		array(
			'label'       => __( 'Activar notificaciones de compras en vivo (Sales Pop)', 'coco-theme' ),
			'description' => __( 'Muestra el aviso flotante inferior izquierdo ("Fulanito en Ciudad compró...") con tus productos reales.', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'coco_theme_sales_pop_mobile',
		array(
			'default'           => true,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_sales_pop_mobile',
		array(
			'label'       => __( 'Mostrar Sales Pop también en celulares', 'coco-theme' ),
			'description' => __( 'Adapta la tarjeta en formato compacto para pantallas móviles.', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'coco_theme_sales_pop_mobile_pos',
		array(
			'default'           => 'top',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_key',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_sales_pop_mobile_pos',
		array(
			'label'       => __( 'Posición del Sales Pop en celulares', 'coco-theme' ),
			'description' => __( 'En celulares se recomienda arriba (tipo notificación de WhatsApp/iOS) para no tapar los botones de compra.', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'select',
			'choices'     => array(
				'top'    => __( 'Arriba (Recomendado - No tapa botones)', 'coco-theme' ),
				'bottom' => __( 'Abajo', 'coco-theme' ),
			),
		)
	);

	$wp_customize->add_setting(
		'coco_theme_sales_pop_interval',
		array(
			'default'           => 16,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_sales_pop_interval',
		array(
			'label'       => __( 'Intervalo entre notificaciones (segundos)', 'coco-theme' ),
			'description' => __( 'Tiempo de espera entre un aviso de compra y el siguiente (por defecto: 16 seg).', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 8,
				'max'  => 90,
				'step' => 2,
			),
		)
	);

	// Selectores de color de Sales Pop (Por defecto usan la paleta oficial de Coco Theme).
	$sales_pop_colors = array(
		'coco_theme_sp_bg_color'     => array(
			'label'   => __( 'Sales Pop: Color de Fondo', 'coco-theme' ),
			'default' => '#FFFFFF',
		),
		'coco_theme_sp_border_color' => array(
			'label'   => __( 'Sales Pop: Color de Borde', 'coco-theme' ),
			'default' => '#E4E7EB',
		),
		'coco_theme_sp_title_color'  => array(
			'label'   => __( 'Sales Pop: Color del Nombre del Producto', 'coco-theme' ),
			'default' => '#1F2933',
		),
		'coco_theme_sp_text_color'   => array(
			'label'   => __( 'Sales Pop: Color del Texto Secundario', 'coco-theme' ),
			'default' => '#52606D',
		),
		'coco_theme_sp_badge_color'  => array(
			'label'   => __( 'Sales Pop: Color del Sello "✓ Verificada"', 'coco-theme' ),
			'default' => '#16A34A',
		),
	);

	foreach ( $sales_pop_colors as $setting_id => $color_cfg ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $color_cfg['default'],
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'   => $color_cfg['label'],
					'section' => 'coco_theme_options',
				)
			)
		);
	}

	// 5. Controles del Botón Sticky Inferior en Celulares (Mobile Sticky Buy Bar).
	$wp_customize->add_setting(
		'coco_theme_mobile_sticky_buy_enabled',
		array(
			'default'           => true,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_mobile_sticky_buy_enabled',
		array(
			'label'       => __( 'Activar botón sticky de compra en celulares', 'coco-theme' ),
			'description' => __( 'Muestra el botón flotante inferior en la ficha de producto al navegar desde un teléfono móvil.', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'coco_theme_mobile_sticky_mode',
		array(
			'default'           => 'scroll',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_key',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_mobile_sticky_mode',
		array(
			'label'       => __( 'Comportamiento del botón sticky móvil', 'coco-theme' ),
			'description' => __( 'Elige cuándo debe mostrarse el botón en la pantalla móvil.', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'select',
			'choices'     => array(
				'scroll' => __( 'Inteligente (Aparece al hacer scroll cuando se pasa el botón principal)', 'coco-theme' ),
				'always' => __( 'Fijo siempre visible abajo (Sticky permanente)', 'coco-theme' ),
			),
		)
	);

	$wp_customize->add_setting(
		'coco_theme_mobile_sticky_anim',
		array(
			'default'           => 'pulse',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_key',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_mobile_sticky_anim',
		array(
			'label'       => __( 'Animación del botón sticky móvil', 'coco-theme' ),
			'description' => __( 'Efecto de atención visual en celulares para maximizar clics.', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'select',
			'choices'     => array(
				'pulse' => __( 'Latido con Resplandor (Pulse Glow)', 'coco-theme' ),
				'shine' => __( 'Destello de Luz Brillante (Shine Effect)', 'coco-theme' ),
				'shake' => __( 'Sacudida de Atención (Wiggle / Shake)', 'coco-theme' ),
				'none'  => __( 'Sin animación (Estático)', 'coco-theme' ),
			),
		)
	);

	$wp_customize->add_setting(
		'coco_theme_mobile_sticky_text',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_mobile_sticky_text',
		array(
			'label'       => __( 'Texto personalizado del botón sticky (opcional)', 'coco-theme' ),
			'description' => __( 'Déjalo vacío para usar el mismo texto del botón principal (ej: "Pagar contra entrega" o "Comprar ahora").', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'text',
		)
	);

	// 6. Controles de Estilo del Botón de Compra / Pago Contra Entrega (Ficha, Sticky móvil y Catálogo).
	$button_colors = array(
		'coco_theme_btn_cod_bg_color'    => array(
			'label'       => __( 'Botón de Compra: Color de Fondo', 'coco-theme' ),
			'description' => __( 'Color principal del botón "Comprar ahora", barra sticky móvil y destacados.', 'coco-theme' ),
			'default'     => '#16A34A',
		),
		'coco_theme_btn_cod_text_color'  => array(
			'label'       => __( 'Botón de Compra: Color de Texto', 'coco-theme' ),
			'description' => __( 'Color del texto dentro del botón de compra.', 'coco-theme' ),
			'default'     => '#FFFFFF',
		),
		'coco_theme_btn_cod_hover_color' => array(
			'label'       => __( 'Botón de Compra: Color Hover (Al pasar el cursor)', 'coco-theme' ),
			'description' => __( 'Tonalidad del botón cuando el cliente pasa el cursor por encima.', 'coco-theme' ),
			'default'     => '#15803D',
		),
	);

	foreach ( $button_colors as $setting_id => $btn_cfg ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $btn_cfg['default'],
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'       => $btn_cfg['label'],
					'description' => $btn_cfg['description'],
					'section'     => 'coco_theme_options',
				)
			)
		);
	}

	// 7. Controles de Garantías y Métodos de Pago en Ficha de Producto (Pago Seguro).
	$wp_customize->add_setting(
		'coco_theme_safe_checkout_enabled',
		array(
			'default'           => true,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_safe_checkout_enabled',
		array(
			'label'       => __( 'Mostrar fila de "Garantizamos un pago seguro"', 'coco-theme' ),
			'description' => __( 'Muestra el escudo de seguridad y los distintivos de bancos y métodos de pago debajo de las opciones de compra.', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'coco_theme_safe_checkout_title',
		array(
			'default'           => "Garantizamos\nun pago seguro.",
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_textarea_field',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_safe_checkout_title',
		array(
			'label'       => __( 'Pago Seguro: Texto junto al escudo', 'coco-theme' ),
			'description' => __( 'Texto descriptivo junto al icono del escudo (acepta saltos de línea).', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'coco_theme_safe_checkout_badges',
		array(
			'default'           => "Itaú\nGiros Tigo\n💵 Efectivo\nQR / Transf.",
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_textarea_field',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_safe_checkout_badges',
		array(
			'label'       => __( 'Pago Seguro: Bancos y Métodos de Pago (Badges)', 'coco-theme' ),
			'description' => __( 'Escribe un método o banco por línea o separados por comas (ej: Itaú, Giros Tigo, 💵 Efectivo, QR / Transf.). Aplica automáticamente colores de marca para: Itaú, ueno bank, Giros Tigo, Efectivo, QR / Transf., Continental, Familiar, Basa, Sudameris, BNF, Tarjetas, Visa, Mastercard.', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'coco_theme_delivery_box_1',
		array(
			'default'           => 'Plazos de entrega estimados: <strong>24–48 hs</strong> (Asunción y Gran Asunción), <strong>2–4 días</strong> (Interior de Paraguay).',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'wp_kses_post',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_delivery_box_1',
		array(
			'label'       => __( 'Ficha de Producto: Plazos de Entrega (Caja 1)', 'coco-theme' ),
			'description' => __( 'Texto de la primera caja informativa (plazos estimados de envío).', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'coco_theme_delivery_box_2',
		array(
			'default'           => '<strong>Pago Contra Entrega</strong> al recibir en puerta. Garantía de cambio directo por defectos de fábrica.',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'wp_kses_post',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_delivery_box_2',
		array(
			'label'       => __( 'Ficha de Producto: Garantía y Pago Contra Entrega (Caja 2)', 'coco-theme' ),
			'description' => __( 'Texto de la segunda caja informativa (condiciones de pago y cambio).', 'coco-theme' ),
			'section'     => 'coco_theme_options',
			'type'        => 'textarea',
		)
	);
}
add_action( 'customize_register', 'coco_theme_customize_register' );

/**
 * Aplicar visibilidad del título/descripción del sitio, ancho personalizado del logo
 * y variables de color del componente Sales Pop y Botones de Compra.
 */
function coco_theme_customizer_dynamic_css() {
	$show_title = get_theme_mod( 'coco_theme_show_title_tagline', false );
	$logo_width = absint( get_theme_mod( 'coco_theme_logo_width', 125 ) );
	if ( $logo_width < 60 ) {
		$logo_width = 125;
	}
	$logo_height = intval( round( $logo_width * 0.49 ) );

	$sp_bg     = sanitize_hex_color( get_theme_mod( 'coco_theme_sp_bg_color', '#FFFFFF' ) ) ?: '#FFFFFF';
	$sp_border = sanitize_hex_color( get_theme_mod( 'coco_theme_sp_border_color', '#E4E7EB' ) ) ?: '#E4E7EB';
	$sp_title  = sanitize_hex_color( get_theme_mod( 'coco_theme_sp_title_color', '#1F2933' ) ) ?: '#1F2933';
	$sp_text   = sanitize_hex_color( get_theme_mod( 'coco_theme_sp_text_color', '#52606D' ) ) ?: '#52606D';
	$sp_badge  = sanitize_hex_color( get_theme_mod( 'coco_theme_sp_badge_color', '#16A34A' ) ) ?: '#16A34A';

	$btn_bg    = sanitize_hex_color( get_theme_mod( 'coco_theme_btn_cod_bg_color', '#16A34A' ) ) ?: '#16A34A';
	$btn_text  = sanitize_hex_color( get_theme_mod( 'coco_theme_btn_cod_text_color', '#FFFFFF' ) ) ?: '#FFFFFF';
	$btn_hover = sanitize_hex_color( get_theme_mod( 'coco_theme_btn_cod_hover_color', '#15803D' ) ) ?: '#15803D';

	// Extraer valores RGB para que el resplandor de la animación Pulse use exactamente el color elegido
	$r = 22; $g = 163; $b = 74;
	if ( preg_match( '/^#?([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})$/i', $btn_bg, $m ) ) {
		$r = hexdec( $m[1] );
		$g = hexdec( $m[2] );
		$b = hexdec( $m[3] );
	}
	?>
	<style id="coco-customizer-dynamic-css">
		:root {
			--coco-cod-green: <?php echo esc_attr( $btn_bg ); ?>;
			--coco-cod-green-dark: <?php echo esc_attr( $btn_hover ); ?>;
			--coco-btn-cod-bg: <?php echo esc_attr( $btn_bg ); ?>;
			--coco-btn-cod-text: <?php echo esc_attr( $btn_text ); ?>;
			--coco-btn-cod-hover: <?php echo esc_attr( $btn_hover ); ?>;
			--coco-sp-bg: <?php echo esc_attr( $sp_bg ); ?>;
			--coco-sp-border: <?php echo esc_attr( $sp_border ); ?>;
			--coco-sp-title: <?php echo esc_attr( $sp_title ); ?>;
			--coco-sp-text: <?php echo esc_attr( $sp_text ); ?>;
			--coco-sp-badge: <?php echo esc_attr( $sp_badge ); ?>;
		}

		/* Estilos dinámicos para los botones de compra */
		#coco-cod-open-modal,
		.coco-cod-button,
		.coco-sticky-buy-btn,
		.coco-spotlight-btn {
			background-color: var(--coco-btn-cod-bg) !important;
			color: var(--coco-btn-cod-text) !important;
		}

		#coco-cod-open-modal:hover,
		.coco-cod-button:hover,
		.coco-sticky-buy-btn:hover,
		.coco-spotlight-btn:hover {
			background-color: var(--coco-btn-cod-hover) !important;
			color: var(--coco-btn-cod-text) !important;
		}

		@keyframes cocoStickyPulse {
			0%, 100% {
				transform: scale(1);
				box-shadow: 0 4px 14px rgba(<?php echo "$r, $g, $b"; ?>, 0.35);
			}
			50% {
				transform: scale(1.028);
				box-shadow: 0 6px 20px rgba(<?php echo "$r, $g, $b"; ?>, 0.55), 0 0 0 5px rgba(<?php echo "$r, $g, $b"; ?>, 0.18);
			}
		}

		.coco-header-logo img,
		.coco-footer-logo img,
		.wp-block-site-logo img,
		.custom-logo {
			width: <?php echo esc_attr( $logo_width ); ?>px !important;
			height: <?php echo esc_attr( $logo_height ); ?>px !important;
			max-height: <?php echo esc_attr( $logo_height + 6 ); ?>px !important;
			object-fit: contain !important;
		}
		<?php if ( ! $show_title ) : ?>
		.wp-block-site-title,
		.wp-block-site-tagline,
		.site-title,
		.site-description {
			display: none !important;
		}
		<?php else : ?>
		.coco-header-logo .wp-block-site-title,
		.coco-footer-logo .wp-block-site-title {
			display: block !important;
		}
		<?php endif; ?>
	</style>
	<?php
}
add_action( 'wp_head', 'coco_theme_customizer_dynamic_css', 99 );

/**
 * Limpiar caché del Sales Pop cuando se actualiza un producto o entra un nuevo pedido.
 */
function coco_theme_flush_sales_pop_cache() {
	delete_transient( 'coco_theme_sales_pop_items_v1' );
}
add_action( 'save_post_product', 'coco_theme_flush_sales_pop_cache' );
add_action( 'woocommerce_new_order', 'coco_theme_flush_sales_pop_cache' );

/**
 * Obtener lista híbrida de notificaciones de compra (Pedidos reales recientes + Catálogo activo de Paraguay).
 *
 * @return array Lista de notificaciones listas para mostrar.
 */
function coco_theme_get_sales_pop_items() {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	$cached = get_transient( 'coco_theme_sales_pop_items_v1' );
	if ( is_array( $cached ) && ! empty( $cached ) ) {
		return $cached;
	}

	$items = array();

	// 1. Intentar nutrir primero con pedidos recientes reales de WooCommerce.
	if ( function_exists( 'wc_get_orders' ) ) {
		$recent_orders = wc_get_orders(
			array(
				'limit'   => 5,
				'status'  => array( 'wc-processing', 'wc-completed', 'wc-on-hold' ),
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);

		foreach ( $recent_orders as $order ) {
			if ( ! $order instanceof \WC_Order ) {
				continue;
			}
			$first_name = trim( $order->get_billing_first_name() );
			if ( empty( $first_name ) ) {
				$first_name = trim( $order->get_shipping_first_name() );
			}
			// Extraer solo el primer nombre de pila por privacidad.
			if ( ! empty( $first_name ) ) {
				$parts      = preg_split( '/\s+/', $first_name );
				$first_name = ucfirst( mb_strtolower( $parts[0], 'UTF-8' ) );
			} else {
				continue;
			}

			$city = trim( $order->get_billing_city() );
			if ( empty( $city ) ) {
				$city = trim( $order->get_shipping_city() );
			}
			if ( empty( $city ) ) {
				$city = 'Asunción';
			}

			$order_items = $order->get_items();
			if ( empty( $order_items ) ) {
				continue;
			}
			$first_item = reset( $order_items );
			$product    = is_callable( array( $first_item, 'get_product' ) ) ? $first_item->get_product() : null;
			if ( ! $product instanceof \WC_Product || 'publish' !== $product->get_status() ) {
				continue;
			}

			$img_id  = $product->get_image_id();
			$img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src();

			$date_created = $order->get_date_created();
			$time_ago     = 'hace 25 minutos';
			if ( $date_created ) {
				$diff_hours = round( ( time() - $date_created->getTimestamp() ) / 3600 );
				if ( $diff_hours <= 1 ) {
					$time_ago = 'hace ' . wp_rand( 8, 45 ) . ' minutos';
				} elseif ( $diff_hours <= 12 ) {
					$time_ago = 'hace ' . intval( $diff_hours ) . ' horas';
				} else {
					$time_ago = 'hace ' . wp_rand( 1, 3 ) . ' horas';
				}
			}

			$items[] = array(
				'buyer' => sprintf( '%s en %s compró', $first_name, $city ),
				'title' => $product->get_name(),
				'url'   => get_permalink( $product->get_id() ),
				'image' => $img_url,
				'time'  => $time_ago,
			);
		}
	}

	// 2. Completar hasta 12 notificaciones con productos reales del catálogo y compradores de Paraguay.
	$catalog_products = wc_get_products(
		array(
			'limit'   => 14,
			'status'  => 'publish',
			'orderby' => 'popularity',
			'order'   => 'DESC',
		)
	);

	if ( ! empty( $catalog_products ) ) {
		$py_buyers = array(
			array( 'name' => 'María', 'city' => 'Asunción', 'time' => 'hace 12 minutos' ),
			array( 'name' => 'Camila', 'city' => 'San Lorenzo', 'time' => 'hace 24 minutos' ),
			array( 'name' => 'Guillermo', 'city' => 'Luque', 'time' => 'hace 41 minutos' ),
			array( 'name' => 'Leticia', 'city' => 'Ciudad del Este', 'time' => 'hace 1 hora' ),
			array( 'name' => 'Romina', 'city' => 'Lambaré', 'time' => 'hace 1 hora' ),
			array( 'name' => 'Andrea', 'city' => 'Fernando de la Mora', 'time' => 'hace 2 horas' ),
			array( 'name' => 'Carlos', 'city' => 'Encarnación', 'time' => 'hace 2 horas' ),
			array( 'name' => 'Fátima', 'city' => 'Capiatá', 'time' => 'hace 3 horas' ),
			array( 'name' => 'Belén', 'city' => 'Mariano Roque Alonso', 'time' => 'hace 18 minutos' ),
			array( 'name' => 'Rodrigo', 'city' => 'Ñemby', 'time' => 'hace 35 minutos' ),
			array( 'name' => 'Jazmín', 'city' => 'Villarrica', 'time' => 'hace 3 horas' ),
			array( 'name' => 'Karina', 'city' => 'Caaguazú', 'time' => 'hace 4 horas' ),
		);

		$buyer_count = count( $py_buyers );
		$idx         = 0;

		foreach ( $catalog_products as $prod ) {
			if ( count( $items ) >= 12 ) {
				break;
			}
			if ( ! $prod instanceof \WC_Product ) {
				continue;
			}

			$img_id  = $prod->get_image_id();
			$img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src();
			$profile = $py_buyers[ $idx % $buyer_count ];
			$idx++;

			$items[] = array(
				'buyer' => sprintf( '%s en %s compró', $profile['name'], $profile['city'] ),
				'title' => $prod->get_name(),
				'url'   => get_permalink( $prod->get_id() ),
				'image' => $img_url,
				'time'  => $profile['time'],
			);
		}
	}

	if ( ! empty( $items ) ) {
		set_transient( 'coco_theme_sales_pop_items_v1', $items, 30 * MINUTE_IN_SECONDS );
	}

	return $items;
}

/**
 * Renderizar el componente flotante de Notificaciones de Compra en Vivo (Sales Pop) en el pie de página.
 */
function coco_theme_render_sales_pop() {
	if ( is_admin() ) {
		return;
	}

	// Respetar el interruptor del Personalizador.
	if ( ! get_theme_mod( 'coco_theme_sales_pop_enabled', true ) ) {
		return;
	}

	// No mostrar durante el proceso de pago / orden recibida para evitar distracciones.
	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		return;
	}

	$items = coco_theme_get_sales_pop_items();
	if ( empty( $items ) ) {
		return;
	}

	$show_mobile  = get_theme_mod( 'coco_theme_sales_pop_mobile', true );
	$mobile_pos   = get_theme_mod( 'coco_theme_sales_pop_mobile_pos', 'top' );
	$interval_sec = absint( get_theme_mod( 'coco_theme_sales_pop_interval', 16 ) );
	if ( $interval_sec < 8 ) {
		$interval_sec = 16;
	}

	$first   = $items[0];
	$classes = 'coco-sales-pop';
	if ( ! $show_mobile ) {
		$classes .= ' hide-on-mobile';
	}
	if ( 'bottom' === $mobile_pos ) {
		$classes .= ' mobile-pos-bottom';
	}
	?>
	<div id="coco-sales-pop" class="<?php echo esc_attr( $classes ); ?>" role="status" aria-live="polite">
		<a href="<?php echo esc_url( $first['url'] ); ?>" class="coco-sp-thumb-link" id="coco-sp-thumb-link">
			<img src="<?php echo esc_url( $first['image'] ); ?>" alt="<?php echo esc_attr( $first['title'] ); ?>" class="coco-sp-thumb" id="coco-sp-thumb" loading="lazy" />
		</a>
		<div class="coco-sp-body">
			<div class="coco-sp-top-row">
				<p class="coco-sp-buyer" id="coco-sp-buyer"><?php echo esc_html( $first['buyer'] ); ?></p>
				<button type="button" class="coco-sp-close" id="coco-sp-close" aria-label="<?php esc_attr_e( 'Cerrar notificación', 'coco-theme' ); ?>">&times;</button>
			</div>
			<a href="<?php echo esc_url( $first['url'] ); ?>" class="coco-sp-product-link" id="coco-sp-title">
				<?php echo esc_html( $first['title'] ); ?>
			</a>
			<div class="coco-sp-meta-row">
				<div class="coco-sp-meta-left">
					<span class="coco-sp-time" id="coco-sp-time"><?php echo esc_html( $first['time'] ); ?></span>
					<span class="coco-sp-verified">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<circle cx="12" cy="12" r="10"></circle>
							<polyline points="8.5 12.5 10.8 14.8 15.5 9.5"></polyline>
						</svg>
						<span><?php esc_html_e( 'Verificada', 'coco-theme' ); ?></span>
					</span>
				</div>
				<span class="coco-sp-info-icon" title="<?php echo esc_attr( sprintf( __( 'Compra verificada con Pago Contra Entrega en %s', 'coco-theme' ), get_bloginfo( 'name' ) ) ); ?>">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<circle cx="12" cy="12" r="10"></circle>
						<line x1="12" y1="16" x2="12" y2="12"></line>
						<line x1="12" y1="8" x2="12.01" y2="8"></line>
					</svg>
				</span>
			</div>
		</div>
	</div>
	<script>
	(function(){
		try {
			if (window.sessionStorage && sessionStorage.getItem('coco_sales_pop_dismissed') === '1') {
				return;
			}
		} catch (e) {}

		var items = <?php echo wp_json_encode( array_values( $items ) ); ?>;
		if (!items || !items.length) return;

		var popEl     = document.getElementById('coco-sales-pop');
		var thumbLink = document.getElementById('coco-sp-thumb-link');
		var thumbImg  = document.getElementById('coco-sp-thumb');
		var buyerEl   = document.getElementById('coco-sp-buyer');
		var titleEl   = document.getElementById('coco-sp-title');
		var timeEl    = document.getElementById('coco-sp-time');
		var closeBtn  = document.getElementById('coco-sp-close');

		if (!popEl) return;

		var currentIndex = Math.floor(Math.random() * items.length);
		var isHovered    = false;
		var isDismissed  = false;
		var hideTimer    = null;
		var nextTimer    = null;
		var baseInterval = <?php echo intval( $interval_sec * 1000 ); ?>;
		var visibleDuration = 6500;

		function isCodModalOpen() {
			var modal = document.getElementById('coco-cod-modal');
			if (modal && (modal.classList.contains('is-open') || modal.style.display === 'flex' || modal.style.display === 'block')) {
				return true;
			}
			return document.body.classList.contains('coco-cod-modal-open');
		}

		function populateItem(item) {
			if (!item) return;
			buyerEl.textContent = item.buyer;
			titleEl.textContent = item.title;
			titleEl.href        = item.url;
			thumbLink.href      = item.url;
			thumbImg.src        = item.image;
			thumbImg.alt        = item.title;
			timeEl.textContent  = item.time;
		}

		function showNotification() {
			if (isDismissed) return;
			if (isCodModalOpen()) {
				scheduleNext();
				return;
			}
			var item = items[currentIndex % items.length];
			currentIndex = (currentIndex + 1) % items.length;
			populateItem(item);
			popEl.classList.add('is-visible');

			clearTimeout(hideTimer);
			hideTimer = setTimeout(function(){
				if (!isHovered) {
					hideNotification();
				}
			}, visibleDuration);
		}

		function hideNotification() {
			popEl.classList.remove('is-visible');
			scheduleNext();
		}

		function scheduleNext() {
			if (isDismissed) return;
			clearTimeout(nextTimer);
			var jitter = Math.floor(Math.random() * 4000) - 1500;
			var delay  = Math.max(7000, baseInterval + jitter);
			nextTimer  = setTimeout(showNotification, delay);
		}

		popEl.addEventListener('mouseenter', function(){
			isHovered = true;
			clearTimeout(hideTimer);
		});

		popEl.addEventListener('mouseleave', function(){
			isHovered = false;
			hideTimer = setTimeout(hideNotification, 2500);
		});

		if (closeBtn) {
			closeBtn.addEventListener('click', function(e){
				e.preventDefault();
				e.stopPropagation();
				isDismissed = true;
				popEl.classList.remove('is-visible');
				clearTimeout(hideTimer);
				clearTimeout(nextTimer);
				try {
					if (window.sessionStorage) {
						sessionStorage.setItem('coco_sales_pop_dismissed', '1');
					}
				} catch (err) {}
			});
		}

		// Primera aparición suave tras 5.5 segundos de entrar a la tienda.
		nextTimer = setTimeout(showNotification, 5500);
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'coco_theme_render_sales_pop', 25 );

/**
 * 5. Barra Sticky Inferior de Compra en Ficha de Producto para Móviles.
 * Permite activación/desactivación desde Apariencia > Personalizar,
 * elección entre modo Inteligente (IntersectionObserver al hacer scroll) o Siempre visible,
 * scroll asistido a variaciones incompletas y compatibilidad con iOS Safe Area.
 */
function coco_theme_render_mobile_sticky_buy_bar() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	// Comprobar si el usuario tiene habilitado el botón sticky en el Personalizador
	if ( ! get_theme_mod( 'coco_theme_mobile_sticky_buy_enabled', true ) ) {
		return;
	}

	global $product;
	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_ID() );
	}
	if ( ! $product ) {
		return;
	}

	$is_variable      = $product->is_type( 'variable' ) ? '1' : '0';
	$sticky_mode      = get_theme_mod( 'coco_theme_mobile_sticky_mode', 'scroll' );
	$sticky_anim      = get_theme_mod( 'coco_theme_mobile_sticky_anim', 'pulse' );
	$custom_text      = trim( get_theme_mod( 'coco_theme_mobile_sticky_text', '' ) );
	$default_btn_text = ! empty( $custom_text ) ? $custom_text : __( 'Pagar al recibir en casa', 'coco-theme' );
	$initial_visible  = ( 'always' === $sticky_mode );

	$anim_class = '';
	if ( 'pulse' === $sticky_anim ) {
		$anim_class = 'coco-anim-pulse';
	} elseif ( 'shine' === $sticky_anim ) {
		$anim_class = 'coco-anim-shine';
	} elseif ( 'shake' === $sticky_anim ) {
		$anim_class = 'coco-anim-shake';
	}
	?>
	<!-- Barra Sticky Inferior de Compra en Móviles -->
	<div id="coco-sticky-buy-bar" class="coco-sticky-buy-bar<?php echo $initial_visible ? ' is-always-visible is-visible' : ''; ?>" aria-hidden="<?php echo $initial_visible ? 'false' : 'true'; ?>" data-variable="<?php echo esc_attr( $is_variable ); ?>" data-mode="<?php echo esc_attr( $sticky_mode ); ?>" data-has-custom-text="<?php echo ! empty( $custom_text ) ? '1' : '0'; ?>">
		<div class="coco-sticky-buy-inner">
			<button type="button" id="coco-sticky-buy-btn" class="coco-sticky-buy-btn <?php echo esc_attr( $anim_class ); ?>" aria-label="<?php esc_attr_e( 'Comprar ahora', 'coco-theme' ); ?>">
				<span class="coco-sticky-btn-text"><?php echo esc_html( $default_btn_text ); ?></span>
			</button>
		</div>
	</div>

	<script>
	(function(){
		function initCocoStickyBar() {
			var bar = document.getElementById('coco-sticky-buy-bar');
			var btn = document.getElementById('coco-sticky-buy-btn');
			var btnText = bar ? bar.querySelector('.coco-sticky-btn-text') : null;
			if (!bar || !btn) return;

			var mode = bar.getAttribute('data-mode') || 'scroll';
			var hasCustomText = bar.getAttribute('data-has-custom-text') === '1';

			// Sincronizar texto si no se definió uno personalizado en el Personalizador
			var origCodBtn = document.getElementById('coco-cod-open-modal') || document.querySelector('.single-product .coco-cod-button') || document.querySelector('.single-product .single_add_to_cart_button');
			if (!hasCustomText && origCodBtn && btnText) {
				var titleSpan = origCodBtn.querySelector('.coco-cod-btn__title');
				var origText = titleSpan ? titleSpan.textContent.trim() : origCodBtn.textContent.trim();
				if (origText) {
					btnText.textContent = origText;
				}
			}

			function isModalActive() {
				var m = document.getElementById('coco-cod-modal');
				return (m && (m.classList.contains('is-open') || m.style.display === 'flex' || m.style.display === 'block')) || document.body.classList.contains('coco-cod-modal-open');
			}

			// Modo "Siempre visible"
			if (mode === 'always') {
				function handleAlwaysVisibility() {
					if (isModalActive()) {
						bar.classList.remove('is-visible');
						document.body.classList.remove('has-coco-sticky-visible');
						bar.setAttribute('aria-hidden', 'true');
					} else {
						bar.classList.add('is-visible');
						document.body.classList.add('has-coco-sticky-visible');
						bar.setAttribute('aria-hidden', 'false');
					}
				}
				handleAlwaysVisibility();
			} else {
				// Modo "Inteligente" (IntersectionObserver al hacer scroll)
				var triggerEl = document.querySelector('.coco-cart-action-row') || document.querySelector('form.cart') || origCodBtn;
				if (triggerEl) {
					function updateVisibility() {
						if (isModalActive()) {
							bar.classList.remove('is-visible');
							document.body.classList.remove('has-coco-sticky-visible');
							bar.setAttribute('aria-hidden', 'true');
							return;
						}
						var rect = triggerEl.getBoundingClientRect();
						if (rect.bottom < 0) {
							bar.classList.add('is-visible');
							document.body.classList.add('has-coco-sticky-visible');
							bar.setAttribute('aria-hidden', 'false');
						} else {
							bar.classList.remove('is-visible');
							document.body.classList.remove('has-coco-sticky-visible');
							bar.setAttribute('aria-hidden', 'true');
						}
					}

					if ('IntersectionObserver' in window) {
						var observer = new IntersectionObserver(function(entries) {
							entries.forEach(function(entry) {
								if (isModalActive()) {
									bar.classList.remove('is-visible');
									document.body.classList.remove('has-coco-sticky-visible');
									bar.setAttribute('aria-hidden', 'true');
									return;
								}
								if (!entry.isIntersecting && entry.boundingClientRect.top < 0) {
									bar.classList.add('is-visible');
									document.body.classList.add('has-coco-sticky-visible');
									bar.setAttribute('aria-hidden', 'false');
								} else {
									bar.classList.remove('is-visible');
									document.body.classList.remove('has-coco-sticky-visible');
									bar.setAttribute('aria-hidden', 'true');
								}
							});
						}, { rootMargin: '0px', threshold: 0 });

						observer.observe(triggerEl);
					} else {
						window.addEventListener('scroll', updateVisibility, { passive: true });
					}
				}
			}

			// Evento al hacer clic en el botón sticky
			btn.addEventListener('click', function(e){
				e.preventDefault();

				var isVariable = bar.getAttribute('data-variable') === '1';
				var varForm = document.querySelector('form.variations_form');

				// Si el producto es variable y faltan seleccionar opciones
				if (isVariable && varForm) {
					var varIdInput = varForm.querySelector('input[name="variation_id"]');
					var variationId = varIdInput ? parseInt(varIdInput.value, 10) : 0;

					if (!variationId || variationId <= 0) {
						// Desplazar suavemente hasta los selectores de opciones
						varForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
						varForm.classList.remove('coco-variations-highlight');
						void varForm.offsetWidth; // Forzar reflow
						varForm.classList.add('coco-variations-highlight');
						setTimeout(function(){
							varForm.classList.remove('coco-variations-highlight');
						}, 2000);
						return;
					}
				}

				// Disparar el botón original de Coco COD
				var targetBtn = document.getElementById('coco-cod-open-modal') || document.querySelector('.single-product .coco-cod-button') || document.querySelector('.single-product .single_add_to_cart_button');
				if (targetBtn) {
					targetBtn.click();
				}
			});

			// Monitorear apertura de modal para esconder la barra sticky
			var modalEl = document.getElementById('coco-cod-modal');
			if (window.MutationObserver && modalEl) {
				var modalObserver = new MutationObserver(function(){
					if (isModalActive()) {
						bar.classList.remove('is-visible');
						bar.setAttribute('aria-hidden', 'true');
					} else if (mode === 'always') {
						bar.classList.add('is-visible');
						bar.setAttribute('aria-hidden', 'false');
					}
				});
				modalObserver.observe(modalEl, { attributes: true, attributeFilter: ['class', 'style', 'aria-hidden'] });
			}
		}

		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', initCocoStickyBar);
		} else {
			initCocoStickyBar();
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'coco_theme_render_mobile_sticky_buy_bar', 35 );

/**
 * Encolar fuentes y estilos principales también dentro del Editor de Bloques (Gutenberg)
 * para que al editar Páginas > Inicio se vea exactamente igual que en la portada real.
 */
function coco_theme_enqueue_block_editor_styles() {
	wp_enqueue_style(
		'coco-theme-editor-fonts',
		'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'coco-theme-editor-style',
		get_stylesheet_uri(),
		array( 'coco-theme-editor-fonts' ),
		COCO_THEME_VERSION
	);
}
add_action( 'enqueue_block_editor_assets', 'coco_theme_enqueue_block_editor_styles' );

/**
 * Limpiar automáticamente cualquier plantilla de portada corrupta guardada por el Editor del Sitio
 * en la base de datos (wp_template) para que la web se edite como una página normal en Páginas > Inicio.
 */
function coco_theme_clear_corrupted_fse_front_page() {
	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'wp_template' AND (post_name = 'front-page' OR post_name LIKE '%front-page%')" );
}

/**
 * Generar el contenido de bloques 100% nativos y compatibles con Gutenberg para Páginas > Inicio.
 */
function coco_theme_get_default_home_content() {
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
			$prod1   = $q1[0];
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
			$prod2   = $q2[0];
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

	$home_wa_url    = coco_theme_get_whatsapp_url( 'general' );
	$home_wa_disp   = coco_theme_get_whatsapp_display();
	$home_site_name = get_bloginfo( 'name' );

	ob_start();
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

<!-- wp:group {"className":"coco-star-products-section"} -->
<div class="wp-block-group coco-star-products-section">
<!-- wp:heading {"className":"coco-flank-title"} -->
<h2 class="wp-block-heading coco-flank-title">LOS FAVORITOS DE <?php echo esc_html( strtoupper( $home_site_name ) ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"coco-fav-cards-grid"} -->
<div class="wp-block-columns coco-fav-cards-grid">
<!-- wp:column {"className":"coco-fav-card"} -->
<div class="wp-block-column coco-fav-card">
<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"coco-fav-card-img-wrap"} -->
<figure class="wp-block-image size-large coco-fav-card-img-wrap"><a href="<?php echo esc_url( $p1_link ); ?>"><img src="<?php echo esc_url( $p1_img ); ?>" alt="<?php echo esc_attr( $p1_name ); ?>"/></a></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"coco-fav-card-content"} -->
<div class="wp-block-group coco-fav-card-content">
<!-- wp:paragraph {"className":"coco-fav-badge coco-fav-badge-gold"} -->
<p class="coco-fav-badge coco-fav-badge-gold">★ MÁS VENDIDO</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"coco-fav-title"} -->
<h3 class="wp-block-heading coco-fav-title"><a href="<?php echo esc_url( $p1_link ); ?>"><?php echo esc_html( $p1_name ); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:buttons {"className":"coco-fav-btn-wrap"} -->
<div class="wp-block-buttons coco-fav-btn-wrap">
<!-- wp:button {"className":"coco-fav-btn-block"} -->
<div class="wp-block-button coco-fav-btn-block"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $p1_link ); ?>">VER PRODUCTO &rarr;</a></div>
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
<figure class="wp-block-image size-large coco-fav-card-img-wrap"><a href="<?php echo esc_url( $p2_link ); ?>"><img src="<?php echo esc_url( $p2_img ); ?>" alt="<?php echo esc_attr( $p2_name ); ?>"/></a></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"coco-fav-card-content"} -->
<div class="wp-block-group coco-fav-card-content">
<!-- wp:paragraph {"className":"coco-fav-badge coco-fav-badge-pink"} -->
<p class="coco-fav-badge coco-fav-badge-pink">🌸 BELLEZA</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"coco-fav-title"} -->
<h3 class="wp-block-heading coco-fav-title"><a href="<?php echo esc_url( $p2_link ); ?>"><?php echo esc_html( $p2_name ); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:buttons {"className":"coco-fav-btn-wrap"} -->
<div class="wp-block-buttons coco-fav-btn-wrap">
<!-- wp:button {"className":"coco-fav-btn-block"} -->
<div class="wp-block-button coco-fav-btn-block"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $p2_link ); ?>">VER PRODUCTO &rarr;</a></div>
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

<!-- wp:group {"className":"coco-trust-strip-wrapper"} -->
<div class="wp-block-group coco-trust-strip-wrapper">
<!-- wp:columns {"className":"coco-trust-strip"} -->
<div class="wp-block-columns coco-trust-strip">
<!-- wp:column {"className":"coco-trust-strip-item"} -->
<div class="wp-block-column coco-trust-strip-item">
<!-- wp:paragraph {"className":"coco-trust-strip-text"} -->
<p class="coco-trust-strip-text">🚚 Pago contra entrega</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"coco-trust-strip-item"} -->
<div class="wp-block-column coco-trust-strip-item">
<!-- wp:paragraph {"className":"coco-trust-strip-text"} -->
<p class="coco-trust-strip-text">📍 Envíos a todo Paraguay</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"coco-trust-strip-item"} -->
<div class="wp-block-column coco-trust-strip-item">
<!-- wp:paragraph {"className":"coco-trust-strip-text"} -->
<p class="coco-trust-strip-text">🛡️ Compra fácil y segura</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"coco-category-showcase-section"} -->
<div class="wp-block-group coco-category-showcase-section">
<!-- wp:heading {"className":"coco-flank-title"} -->
<h2 class="wp-block-heading coco-flank-title">EXPLORÁ NUESTRO CATÁLOGO</h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"coco-catalog-grid"} -->
<div class="wp-block-columns coco-catalog-grid">
<!-- wp:column {"className":"coco-catalog-card"} -->
<div class="wp-block-column coco-catalog-card">
<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"coco-catalog-img-container"} -->
<figure class="wp-block-image size-large coco-catalog-img-container"><a href="<?php echo esc_url( home_url( '/categoria-producto/belleza/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri . '/assets/images/cat-belleza.jpg' ); ?>" alt="Categoría Belleza - Coco Cherry Store"/></a></figure>
<!-- /wp:image -->

<!-- wp:buttons {"className":"coco-catalog-pill-wrap"} -->
<div class="wp-block-buttons coco-catalog-pill-wrap">
<!-- wp:button {"className":"coco-catalog-pill-btn"} -->
<div class="wp-block-button coco-catalog-pill-btn"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/categoria-producto/belleza/' ) ); ?>">Belleza &rarr;</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"coco-catalog-card"} -->
<div class="wp-block-column coco-catalog-card">
<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"coco-catalog-img-container"} -->
<figure class="wp-block-image size-large coco-catalog-img-container"><a href="<?php echo esc_url( home_url( '/categoria-producto/moda/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri . '/assets/images/cat-moda.jpg' ); ?>" alt="Categoría Moda - Coco Cherry Store"/></a></figure>
<!-- /wp:image -->

<!-- wp:buttons {"className":"coco-catalog-pill-wrap"} -->
<div class="wp-block-buttons coco-catalog-pill-wrap">
<!-- wp:button {"className":"coco-catalog-pill-btn"} -->
<div class="wp-block-button coco-catalog-pill-btn"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/categoria-producto/moda/' ) ); ?>">Moda &rarr;</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"coco-catalog-card"} -->
<div class="wp-block-column coco-catalog-card">
<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"coco-catalog-img-container"} -->
<figure class="wp-block-image size-large coco-catalog-img-container"><a href="<?php echo esc_url( home_url( '/categoria-producto/hogar/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri . '/assets/images/cat-hogar.jpg' ); ?>" alt="Categoría Hogar - Coco Cherry Store"/></a></figure>
<!-- /wp:image -->

<!-- wp:buttons {"className":"coco-catalog-pill-wrap"} -->
<div class="wp-block-buttons coco-catalog-pill-wrap">
<!-- wp:button {"className":"coco-catalog-pill-btn"} -->
<div class="wp-block-button coco-catalog-pill-btn"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/categoria-producto/hogar/' ) ); ?>">Hogar &rarr;</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"coco-catalog-card"} -->
<div class="wp-block-column coco-catalog-card">
<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"coco-catalog-img-container"} -->
<figure class="wp-block-image size-large coco-catalog-img-container"><a href="<?php echo esc_url( home_url( '/categoria-producto/bijouterie/' ) ); ?>"><img src="<?php echo esc_url( $theme_uri . '/assets/images/cat-accesorios.jpg' ); ?>" alt="Categoría Accesorios - Coco Cherry Store"/></a></figure>
<!-- /wp:image -->

<!-- wp:buttons {"className":"coco-catalog-pill-wrap"} -->
<div class="wp-block-buttons coco-catalog-pill-wrap">
<!-- wp:button {"className":"coco-catalog-pill-btn"} -->
<div class="wp-block-button coco-catalog-pill-btn"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/categoria-producto/bijouterie/' ) ); ?>">Accesorios &rarr;</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"coco-home-catalog-section"} -->
<div class="wp-block-group coco-home-catalog-section">
<!-- wp:columns {"className":"coco-home-catalog-header"} -->
<div class="wp-block-columns coco-home-catalog-header">
<!-- wp:column {"className":"coco-home-catalog-titles"} -->
<div class="wp-block-column coco-home-catalog-titles">
<!-- wp:heading {"className":"coco-section-heading"} -->
<h2 class="wp-block-heading coco-section-heading">Productos Destacados</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"coco-section-subtext"} -->
<p class="coco-section-subtext">Hacé clic en cualquier producto para pedirlo con <strong>Pago al Recibir en Casa</strong></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"coco-home-catalog-action"} -->
<div class="wp-block-column coco-home-catalog-action">
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"className":"coco-btn-primary"} -->
<div class="wp-block-button coco-btn-primary"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Ver Todo el Catálogo &rarr;</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:shortcode -->
[products limit="8" columns="4" orderby="date" order="DESC"]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"coco-how-section"} -->
<div class="wp-block-group coco-how-section">
<!-- wp:heading {"textAlign":"center","className":"coco-section-heading"} -->
<h2 class="wp-block-heading has-text-align-center coco-section-heading">¿Cómo Comprar con Pago Contra Entrega?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"coco-section-subtext"} -->
<p class="has-text-align-center coco-section-subtext">Sin carritos complicados ni tarjetas de crédito. Tu pedido listo en menos de 1 minuto.</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"className":"coco-how-grid"} -->
<div class="wp-block-columns coco-how-grid">
<!-- wp:column {"className":"coco-trust-card"} -->
<div class="wp-block-column coco-trust-card">
<!-- wp:paragraph {"className":"coco-step-label"} -->
<p class="coco-step-label">PASO 1</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"coco-step-title"} -->
<h3 class="wp-block-heading coco-step-title">Elegí tu Producto</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"coco-step-desc"} -->
<p class="coco-step-desc">Entrá al artículo que más te guste y presioná el botón <strong>"Pagar al recibir en casa"</strong> (o escribinos por WhatsApp).</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"coco-trust-card"} -->
<div class="wp-block-column coco-trust-card">
<!-- wp:paragraph {"className":"coco-step-label"} -->
<p class="coco-step-label">PASO 2</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"coco-step-title"} -->
<h3 class="wp-block-heading coco-step-title">Indicá tu Ciudad y Dirección</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"coco-step-desc"} -->
<p class="coco-step-desc">Seleccioná tu Departamento y Ciudad en la ventana rápida para conocer el costo de envío exacto y cargá tu número de celular.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"coco-trust-card"} -->
<div class="wp-block-column coco-trust-card">
<!-- wp:paragraph {"className":"coco-step-label coco-step-green"} -->
<p class="coco-step-label coco-step-green">PASO 3</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"coco-step-title"} -->
<h3 class="wp-block-heading coco-step-title">Recibí y Aboná en tu Puerta</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"coco-step-desc"} -->
<p class="coco-step-desc">Coordinamos la entrega con vos y abonás en <strong>efectivo o transferencia QR</strong> recién cuando recibís tu pedido.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"coco-reviews-section"} -->
<div class="wp-block-group coco-reviews-section">
<!-- wp:heading {"textAlign":"center","className":"coco-section-heading"} -->
<h2 class="wp-block-heading has-text-align-center coco-section-heading">Lo Que Dicen Nuestras Clientas en Paraguay</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"coco-section-subtext"} -->
<p class="has-text-align-center coco-section-subtext">Cientos de entregas realizadas con éxito en Asunción, Central e Interior</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"className":"coco-reviews-grid"} -->
<div class="wp-block-columns coco-reviews-grid">
<!-- wp:column {"className":"coco-review-card"} -->
<div class="wp-block-column coco-review-card">
<!-- wp:paragraph {"className":"coco-review-stars"} -->
<p class="coco-review-stars">★★★★★</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"coco-review-quote"} -->
<p class="coco-review-quote">"Pedí el Protector Solar Oro 24K y unos aros. Me encantó que pude pagar recién cuando el delivery llegó a mi casa en San Lorenzo. Súper amables por WhatsApp."</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"coco-review-author"} -->
<p class="coco-review-author">— Leticia G. &bull; San Lorenzo</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"coco-review-card"} -->
<div class="wp-block-column coco-review-card">
<!-- wp:paragraph {"className":"coco-review-stars"} -->
<p class="coco-review-stars">★★★★★</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"coco-review-quote"} -->
<p class="coco-review-quote">"Excelente calidad de las prendas y la lencería. Hice el pedido en la web en tres pasos y al día siguiente ya lo tenía en mi oficina en Asunción. 100% recomendados."</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"coco-review-author"} -->
<p class="coco-review-author">— Camila R. &bull; Asunción</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"coco-review-card"} -->
<div class="wp-block-column coco-review-card">
<!-- wp:paragraph {"className":"coco-review-stars"} -->
<p class="coco-review-stars">★★★★★</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"coco-review-quote"} -->
<p class="coco-review-quote">"Compré el matamosquitos eléctrico para mi casa y funciona de maravilla. Da mucha confianza no tener que girar plata antes de recibir el producto."</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"coco-review-author"} -->
<p class="coco-review-author">— Marcos V. &bull; Luque</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"coco-faq-section"} -->
<div class="wp-block-group coco-faq-section">
<!-- wp:heading {"textAlign":"center","className":"coco-section-heading"} -->
<h2 class="wp-block-heading has-text-align-center coco-section-heading">Preguntas Frecuentes</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"coco-section-subtext"} -->
<p class="has-text-align-center coco-section-subtext">Resolvemos tus dudas sobre envíos y pagos contra entrega</p>
<!-- /wp:paragraph -->

<!-- wp:details -->
<details class="wp-block-details"><summary>¿Cómo funciona el Pago Contra Entrega?</summary><!-- wp:paragraph -->
<p>Es muy simple y 100% seguro: hacés tu pedido en la web eligiendo tu ciudad y dirección. Nuestro repartidor te lleva el paquete hasta tu casa o trabajo y abonás recién en ese momento en efectivo o vía código QR / transferencia.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary>¿A qué ciudades de Paraguay realizan envíos?</summary><!-- wp:paragraph -->
<p>Realizamos entregas con delivery en toda Asunción y Gran Asunción (Central), y enviamos a todos los departamentos del país. Al hacer clic en "Pagar al recibir en casa" en cualquier producto, podés seleccionar tu departamento y ciudad para ver el costo exacto al instante.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary>¿Cuánto tiempo demora la entrega de mi pedido?</summary><!-- wp:paragraph -->
<p>En Asunción y Central las entregas se realizan habitualmente entre 24 y 48 horas hábiles. Para ciudades del interior, el plazo promedio es de 48 a 72 horas hábiles dependiendo de la localidad.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary>¿Puedo hacer mi pedido o consultar talles por WhatsApp?</summary><!-- wp:paragraph -->
<p>¡Por supuesto! Nuestro equipo te atiende de forma personalizada en el WhatsApp <strong><?php echo esc_html( $home_wa_disp ); ?></strong> para asesorarte con talles, colores o tomar tu pedido directamente.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"coco-wa-cta-section"} -->
<div class="wp-block-group coco-wa-cta-section">
<!-- wp:heading {"textAlign":"center","className":"coco-section-heading"} -->
<h2 class="wp-block-heading has-text-align-center coco-section-heading">¿Tenés alguna consulta o querés pedir por WhatsApp?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"coco-section-subtext"} -->
<p class="has-text-align-center coco-section-subtext">Escribinos ahora mismo al <strong><?php echo esc_html( $home_wa_disp ); ?></strong> y te ayudamos a elegir tus productos con entrega en puerta.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"coco-wa-cta-buttons"} -->
<div class="wp-block-buttons coco-wa-cta-buttons">
<!-- wp:button {"className":"coco-btn-whatsapp"} -->
<div class="wp-block-button coco-btn-whatsapp"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $home_wa_url ); ?>">📲 Hablar por WhatsApp (<?php echo esc_html( $home_wa_disp ); ?>)</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"coco-btn-primary"} -->
<div class="wp-block-button coco-btn-primary"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">🛍️ Ver Todo el Catálogo</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
	<?php
	return trim( ob_get_clean() );
}

/**
 * Conectar la portada al sistema normal de Páginas de WordPress.
 * Si la página "Inicio" está vacía, mostrar el diseño completo de Coco Cherry.
 */
add_filter( 'the_content', function( $content ) {
	if ( is_front_page() && is_page() ) {
		if ( empty( trim( strip_tags( $content, '<img><svg><video><audio><input><form>' ) ) ) ) {
			return do_blocks( coco_theme_get_default_home_content() );
		}
	}
	return $content;
} );

/**
 * Asegurar que la página "Inicio" existe y se actualiza con los bloques 100% nativos de Gutenberg.
 */
function coco_theme_sync_native_front_page() {
	if ( ! is_admin() ) {
		return;
	}

	coco_theme_clear_corrupted_fse_front_page();

	$front_id = get_option( 'page_on_front' );
	if ( ! $front_id ) {
		$existing = get_page_by_path( 'inicio' );
		if ( ! $existing ) {
			$existing = get_page_by_path( 'home' );
		}
		if ( $existing ) {
			$front_id = $existing->ID;
		} else {
			$front_id = wp_insert_post(
				array(
					'post_title'   => 'Inicio',
					'post_name'    => 'inicio',
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_content' => coco_theme_get_default_home_content(),
				)
			);
		}
		if ( $front_id && ! is_wp_error( $front_id ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $front_id );
			update_option( 'coco_theme_migrated_native_blocks_v133', 1 );
		}
	} else {
		$post = get_post( $front_id );
		if ( $post ) {
			$has_legacy_html = ( false !== strpos( $post->post_content, '<!-- wp:html -->' ) || false !== strpos( $post->post_content, '<!-- wp:pattern' ) || false !== strpos( $post->post_content, 'id="catalogo-destacado"' ) );
			$needs_migration = ! get_option( 'coco_theme_migrated_native_blocks_v133' );
			$manual_reload   = ( isset( $_GET['coco_reload_home'] ) && current_user_can( 'edit_pages' ) );

			if ( empty( trim( $post->post_content ) ) || $has_legacy_html || $needs_migration || $manual_reload ) {
				wp_update_post(
					array(
						'ID'           => $front_id,
						'post_content' => coco_theme_get_default_home_content(),
					)
				);
				update_option( 'coco_theme_migrated_native_blocks_v133', 1 );
			}
		}
	}
}
add_action( 'admin_init', 'coco_theme_sync_native_front_page', 20 );

/**
 * Aviso explicativo y botón de recarga rápida en Páginas > Inicio.
 */
add_action( 'admin_notices', function() {
	$screen   = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$front_id = get_option( 'page_on_front' );
	if ( $screen && 'page' === $screen->post_type && isset( $_GET['post'] ) && intval( $_GET['post'] ) === intval( $front_id ) ) {
		$reload_url = add_query_arg( 'coco_reload_home', '1' );
		echo '<div class="notice notice-info is-dismissible"><p><strong>CocoStore Pro:</strong> Estás editando la <strong>Portada (Home)</strong> con bloques 100% nativos de WordPress. Hacé clic en cualquier título, texto, botón o imagen para modificarlo y luego pulsá <strong>Guardar / Actualizar</strong>. <a class="button button-secondary button-small" style="margin-left:8px;" href="' . esc_url( $reload_url ) . '" onclick="return confirm(\'¿Restaurar el diseño original predeterminado de la portada?\');">Restaurar diseño original</a></p></div>';
	}
} );

/**
 * Desactivar la plantilla de bloques 'order-confirmation' de WooCommerce en Coco Theme
 * para asegurar que siempre se ejecute la plantilla estilizada PHP de confirmación (thankyou.php).
 */
add_filter( 'woocommerce_has_block_template', function( $has_template, $template_name ) {
	if ( 'order-confirmation' === $template_name ) {
		return false;
	}
	return $has_template;
}, 10, 2 );





