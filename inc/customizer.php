<?php
/**
 * Personalizador del tema: panel "Coco Store", secciones, dependencias y vista previa en vivo.
 *
 * @package Coco_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sanear el ancho del logotipo (60-300 px). Por debajo del mínimo vuelve al valor por defecto.
 *
 * @param mixed $value Valor enviado desde el Personalizador.
 * @return int
 */
function coco_theme_sanitize_logo_width( $value ) {
	$value = absint( $value );

	if ( $value < 60 ) {
		return 125;
	}

	return min( 300, $value );
}

/**
 * Sanear el intervalo entre avisos de compra (8-90 segundos).
 *
 * @param mixed $value Valor enviado desde el Personalizador.
 * @return int
 */
function coco_theme_sanitize_sales_pop_interval( $value ) {
	$value = absint( $value );

	return max( 8, min( 90, $value ) );
}

/**
 * ¿Están activos los avisos de compra?
 *
 * @return bool
 */
function coco_theme_customizer_sales_pop_is_on() {
	return (bool) get_theme_mod( 'coco_theme_sales_pop_enabled', true );
}

/**
 * ¿Están activos los avisos de compra en celulares?
 *
 * @return bool
 */
function coco_theme_customizer_sales_pop_mobile_is_on() {
	return coco_theme_customizer_sales_pop_is_on() && (bool) get_theme_mod( 'coco_theme_sales_pop_mobile', true );
}

/**
 * ¿Está activo el botón fijo de compra en celulares?
 *
 * @return bool
 */
function coco_theme_customizer_sticky_is_on() {
	return (bool) get_theme_mod( 'coco_theme_mobile_sticky_buy_enabled', true );
}

/**
 * ¿Está activa la fila de pago seguro?
 *
 * @return bool
 */
function coco_theme_customizer_safe_checkout_is_on() {
	return (bool) get_theme_mod( 'coco_theme_safe_checkout_enabled', true );
}

/**
 * Callback de visibilidad para las opciones dependientes de los avisos de compra.
 *
 * @param WP_Customize_Control $control Control evaluado.
 * @return bool
 */
function coco_theme_customizer_active_sales_pop( $control ) {
	return coco_theme_customizer_sales_pop_is_on();
}

/**
 * Callback de visibilidad para la posición de los avisos en celulares.
 *
 * @param WP_Customize_Control $control Control evaluado.
 * @return bool
 */
function coco_theme_customizer_active_sales_pop_mobile_pos( $control ) {
	return coco_theme_customizer_sales_pop_mobile_is_on();
}

/**
 * Callback de visibilidad para las opciones del botón fijo.
 *
 * @param WP_Customize_Control $control Control evaluado.
 * @return bool
 */
function coco_theme_customizer_active_sticky( $control ) {
	return coco_theme_customizer_sticky_is_on();
}

/**
 * Callback de visibilidad para las opciones de pago seguro.
 *
 * @param WP_Customize_Control $control Control evaluado.
 * @return bool
 */
function coco_theme_customizer_active_safe_checkout( $control ) {
	return coco_theme_customizer_safe_checkout_is_on();
}

/**
 * Registrar panel, secciones y controles del Personalizador.
 *
 * @param WP_Customize_Manager $wp_customize Gestor del Personalizador.
 */
function coco_theme_customize_register( $wp_customize ) {
	/* ---------------------------------------------------------------------
	 * Identidad del sitio
	 * ------------------------------------------------------------------ */
	$wp_customize->add_setting(
		'coco_theme_show_title_tagline',
		array(
			'default'           => false,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'coco_theme_show_title_tagline',
		array(
			'label'       => __( 'Mostrar el título y la descripción corta del sitio', 'coco-theme' ),
			'description' => __( 'Si lo desactivás, la cabecera muestra solo el logotipo.', 'coco-theme' ),
			'section'     => 'title_tagline',
			'type'        => 'checkbox',
			'priority'    => 45,
		)
	);

	// Quitar la casilla nativa duplicada: el tema usa su propio ajuste.
	$wp_customize->remove_control( 'header_text' );

	$wp_customize->add_setting(
		'coco_theme_logo_width',
		array(
			'default'           => 125,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'coco_theme_sanitize_logo_width',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'coco_theme_logo_width',
		array(
			'label'       => __( 'Ancho del logotipo (px)', 'coco-theme' ),
			'description' => __( 'Ajusta el tamaño del logotipo en la cabecera y el pie de página (por defecto: 125 px).', 'coco-theme' ),
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

	/* ---------------------------------------------------------------------
	 * Panel Coco Store
	 * ------------------------------------------------------------------ */
	$wp_customize->add_panel(
		'coco_store',
		array(
			'title'       => __( 'Coco Store', 'coco-theme' ),
			'description' => __( 'Apariencia y comportamiento de la tienda. Cada sección agrupa ajustes relacionados.', 'coco-theme' ),
			'priority'    => 25,
		)
	);

	$sections = array(
		'coco_header'           => array(
			'title'       => __( 'Cabecera y menú', 'coco-theme' ),
			'description' => __( 'Barra de navegación superior del sitio.', 'coco-theme' ),
			'priority'    => 10,
		),
		'coco_hero'             => array(
			'title'       => __( 'Portada de la tienda', 'coco-theme' ),
			'description' => __( 'Banner de la página /shop/ y de cada categoría. En las categorías se muestra automáticamente el nombre y la descripción de la categoría.', 'coco-theme' ),
			'priority'    => 20,
		),
		'coco_commerce'         => array(
			'title'       => __( 'Botones y compra', 'coco-theme' ),
			'description' => __( 'Colores del botón de compra y comportamiento del botón fijo en celulares.', 'coco-theme' ),
			'priority'    => 30,
		),
		'coco_product'          => array(
			'title'       => __( 'Ficha de producto', 'coco-theme' ),
			'description' => __( 'Pago seguro, garantías y plazos de entrega que se muestran debajo de las opciones de compra.', 'coco-theme' ),
			'priority'    => 40,
		),
		'coco_sales_pop'        => array(
			'title'       => __( 'Avisos de compra', 'coco-theme' ),
			'description' => __( 'Notificaciones flotantes con compras recientes de la tienda.', 'coco-theme' ),
			'priority'    => 50,
		),
		'coco_whatsapp_options' => array(
			'title'       => __( 'Contacto y WhatsApp', 'coco-theme' ),
			'description' => __( 'Número de atención, mensajes predeterminados y botón flotante.', 'coco-theme' ),
			'priority'    => 60,
		),
	);

	foreach ( $sections as $section_id => $section_config ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title'       => $section_config['title'],
				'description' => $section_config['description'],
				'panel'       => 'coco_store',
				'priority'    => $section_config['priority'],
			)
		);
	}

	/* ---------------------------------------------------------------------
	 * Cabecera y menú
	 * ------------------------------------------------------------------ */
	$menus        = wp_get_nav_menus();
	$menu_choices = array(
		0 => __( '— Usar el menú de la ubicación principal —', 'coco-theme' ),
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
			'label'       => __( 'Menú de la cabecera', 'coco-theme' ),
			'description' => __( 'Elegí cuál de tus menús creados en WordPress se muestra en la barra superior.', 'coco-theme' ),
			'section'     => 'coco_header',
			'type'        => 'select',
			'choices'     => $menu_choices,
			'priority'    => 10,
		)
	);

	/* ---------------------------------------------------------------------
	 * Portada de la tienda
	 * ------------------------------------------------------------------ */
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
			'label'       => __( 'Etiqueta superior', 'coco-theme' ),
			'description' => __( 'Texto pequeño que aparece sobre el título de la portada.', 'coco-theme' ),
			'section'     => 'coco_hero',
			'type'        => 'text',
			'priority'    => 10,
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
			'label'       => __( 'Título principal', 'coco-theme' ),
			'description' => __( 'Título general de /shop/. En cada categoría se muestra el nombre de esa categoría.', 'coco-theme' ),
			'section'     => 'coco_hero',
			'type'        => 'text',
			'priority'    => 20,
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
			'label'       => __( 'Descripción', 'coco-theme' ),
			'description' => __( 'Texto descriptivo general de /shop/. Para una categoría, editá su descripción en Productos > Categorías.', 'coco-theme' ),
			'section'     => 'coco_hero',
			'type'        => 'textarea',
			'priority'    => 30,
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
					'label'       => __( 'Imagen de fondo', 'coco-theme' ),
					'description' => __( 'Foto de fondo del banner. Si se deja vacía se usa la imagen boutique por defecto.', 'coco-theme' ),
					'section'     => 'coco_hero',
					'priority'    => 40,
				)
			)
		);
	}

	/* ---------------------------------------------------------------------
	 * Botones y compra
	 * ------------------------------------------------------------------ */
	$button_colors = array(
		'coco_theme_btn_cod_bg_color'    => array(
			'label'       => __( 'Botón de compra: color de fondo', 'coco-theme' ),
			'description' => __( 'Color principal del botón "Comprar ahora", la barra fija móvil y los destacados.', 'coco-theme' ),
			'default'     => '#16A34A',
			'priority'    => 10,
		),
		'coco_theme_btn_cod_text_color'  => array(
			'label'       => __( 'Botón de compra: color del texto', 'coco-theme' ),
			'description' => __( 'Color del texto dentro del botón de compra.', 'coco-theme' ),
			'default'     => '#FFFFFF',
			'priority'    => 20,
		),
		'coco_theme_btn_cod_hover_color' => array(
			'label'       => __( 'Botón de compra: color al pasar el cursor', 'coco-theme' ),
			'description' => __( 'Tonalidad del botón cuando el cliente pasa el cursor por encima.', 'coco-theme' ),
			'default'     => '#15803D',
			'priority'    => 30,
		),
	);

	foreach ( $button_colors as $setting_id => $color_config ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $color_config['default'],
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'       => $color_config['label'],
					'description' => $color_config['description'],
					'section'     => 'coco_commerce',
					'priority'    => $color_config['priority'],
				)
			)
		);
	}

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
			'label'       => __( 'Activar botón fijo de compra en celulares', 'coco-theme' ),
			'description' => __( 'Muestra un botón flotante inferior en la ficha de producto cuando se navega desde un teléfono.', 'coco-theme' ),
			'section'     => 'coco_commerce',
			'type'        => 'checkbox',
			'priority'    => 40,
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
			'label'           => __( 'Cuándo mostrar el botón fijo', 'coco-theme' ),
			'description'     => __( 'Elegí en qué momento aparece el botón en la pantalla del celular.', 'coco-theme' ),
			'section'         => 'coco_commerce',
			'type'            => 'select',
			'choices'         => array(
				'scroll' => __( 'Inteligente: al hacer scroll, después del botón principal', 'coco-theme' ),
				'always' => __( 'Siempre visible, fijo abajo', 'coco-theme' ),
			),
			'priority'        => 50,
			'active_callback' => 'coco_theme_customizer_active_sticky',
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
			'label'           => __( 'Animación del botón fijo', 'coco-theme' ),
			'description'     => __( 'Efecto de atención visual en celulares para aumentar los clics.', 'coco-theme' ),
			'section'         => 'coco_commerce',
			'type'            => 'select',
			'choices'         => array(
				'pulse' => __( 'Latido con resplandor (pulse glow)', 'coco-theme' ),
				'shine' => __( 'Destello de luz (shine effect)', 'coco-theme' ),
				'shake' => __( 'Sacudida de atención (wiggle)', 'coco-theme' ),
				'none'  => __( 'Sin animación (estático)', 'coco-theme' ),
			),
			'priority'        => 60,
			'active_callback' => 'coco_theme_customizer_active_sticky',
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
			'label'           => __( 'Texto del botón fijo (opcional)', 'coco-theme' ),
			'description'     => __( 'Dejalo vacío para usar el mismo texto del botón principal (por ejemplo, "Pagar contra entrega").', 'coco-theme' ),
			'section'         => 'coco_commerce',
			'type'            => 'text',
			'priority'        => 70,
			'active_callback' => 'coco_theme_customizer_active_sticky',
		)
	);

	/* ---------------------------------------------------------------------
	 * Ficha de producto
	 * ------------------------------------------------------------------ */
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
			'label'       => __( 'Mostrar fila "Garantizamos un pago seguro"', 'coco-theme' ),
			'description' => __( 'Muestra el escudo de seguridad y los distintivos de bancos y métodos de pago debajo de las opciones de compra.', 'coco-theme' ),
			'section'     => 'coco_product',
			'type'        => 'checkbox',
			'priority'    => 10,
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
			'label'           => __( 'Texto junto al escudo', 'coco-theme' ),
			'description'     => __( 'Texto descriptivo junto al icono del escudo. Acepta saltos de línea.', 'coco-theme' ),
			'section'         => 'coco_product',
			'type'            => 'textarea',
			'priority'        => 20,
			'active_callback' => 'coco_theme_customizer_active_safe_checkout',
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
			'label'           => __( 'Bancos y métodos de pago', 'coco-theme' ),
			'description'     => __( 'Un método por línea o separados por comas. Colorea automáticamente Itaú, ueno bank, Giros Tigo, Efectivo, QR / Transf., Continental, Familiar, Basa, Sudameris, BNF, Tarjetas, Visa y Mastercard.', 'coco-theme' ),
			'section'         => 'coco_product',
			'type'            => 'textarea',
			'priority'        => 30,
			'active_callback' => 'coco_theme_customizer_active_safe_checkout',
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
			'label'       => __( 'Plazos de entrega', 'coco-theme' ),
			'description' => __( 'Primera caja informativa de la ficha de producto. Acepta HTML básico.', 'coco-theme' ),
			'section'     => 'coco_product',
			'type'        => 'textarea',
			'priority'    => 40,
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
			'label'       => __( 'Garantía y pago contra entrega', 'coco-theme' ),
			'description' => __( 'Segunda caja informativa de la ficha de producto. Acepta HTML básico.', 'coco-theme' ),
			'section'     => 'coco_product',
			'type'        => 'textarea',
			'priority'    => 50,
		)
	);

	/* ---------------------------------------------------------------------
	 * Avisos de compra (Sales Pop)
	 * ------------------------------------------------------------------ */
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
			'label'       => __( 'Activar avisos de compra en vivo', 'coco-theme' ),
			'description' => __( 'Muestra el aviso flotante "Fulanito en Ciudad compró…" con productos reales de la tienda.', 'coco-theme' ),
			'section'     => 'coco_sales_pop',
			'type'        => 'checkbox',
			'priority'    => 10,
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
			'label'           => __( 'Mostrar también en celulares', 'coco-theme' ),
			'description'     => __( 'Adapta el aviso en formato compacto para pantallas móviles.', 'coco-theme' ),
			'section'         => 'coco_sales_pop',
			'type'            => 'checkbox',
			'priority'        => 20,
			'active_callback' => 'coco_theme_customizer_active_sales_pop',
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
			'label'           => __( 'Posición en celulares', 'coco-theme' ),
			'description'     => __( 'Arriba se recomienda para no tapar los botones de compra.', 'coco-theme' ),
			'section'         => 'coco_sales_pop',
			'type'            => 'select',
			'choices'         => array(
				'top'    => __( 'Arriba (recomendado)', 'coco-theme' ),
				'bottom' => __( 'Abajo', 'coco-theme' ),
			),
			'priority'        => 30,
			'active_callback' => 'coco_theme_customizer_active_sales_pop_mobile_pos',
		)
	);

	$wp_customize->add_setting(
		'coco_theme_sales_pop_interval',
		array(
			'default'           => 16,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'coco_theme_sanitize_sales_pop_interval',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'coco_theme_sales_pop_interval',
		array(
			'label'           => __( 'Intervalo entre avisos (segundos)', 'coco-theme' ),
			'description'     => __( 'Tiempo de espera entre un aviso y el siguiente (por defecto: 16).', 'coco-theme' ),
			'section'         => 'coco_sales_pop',
			'type'            => 'number',
			'input_attrs'     => array(
				'min'  => 8,
				'max'  => 90,
				'step' => 2,
			),
			'priority'        => 40,
			'active_callback' => 'coco_theme_customizer_active_sales_pop',
		)
	);

	$sales_pop_colors = array(
		'coco_theme_sp_bg_color'     => array(
			'label'    => __( 'Color de fondo', 'coco-theme' ),
			'default'  => '#FFFFFF',
			'priority' => 50,
		),
		'coco_theme_sp_border_color' => array(
			'label'    => __( 'Color del borde', 'coco-theme' ),
			'default'  => '#E4E7EB',
			'priority' => 60,
		),
		'coco_theme_sp_title_color'  => array(
			'label'    => __( 'Color del nombre del producto', 'coco-theme' ),
			'default'  => '#1F2933',
			'priority' => 70,
		),
		'coco_theme_sp_text_color'   => array(
			'label'    => __( 'Color del texto secundario', 'coco-theme' ),
			'default'  => '#52606D',
			'priority' => 80,
		),
		'coco_theme_sp_badge_color'  => array(
			'label'    => __( 'Color del sello "Verificada"', 'coco-theme' ),
			'default'  => '#16A34A',
			'priority' => 90,
		),
	);

	foreach ( $sales_pop_colors as $setting_id => $color_config ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $color_config['default'],
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'           => $color_config['label'],
					'section'         => 'coco_sales_pop',
					'priority'        => $color_config['priority'],
					'active_callback' => 'coco_theme_customizer_active_sales_pop',
				)
			)
		);
	}

	/* ---------------------------------------------------------------------
	 * Contacto y WhatsApp
	 * ------------------------------------------------------------------ */
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
			'description' => __( 'Ejemplo: 595981531503, sin "+" ni guiones. A este número llegan los pedidos y consultas.', 'coco-theme' ),
			'section'     => 'coco_whatsapp_options',
			'type'        => 'text',
			'priority'    => 10,
		)
	);

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
			'description' => __( 'Así se muestra en textos, botones y la marquesina. Ejemplo: 0981 531503.', 'coco-theme' ),
			'section'     => 'coco_whatsapp_options',
			'type'        => 'text',
			'priority'    => 20,
		)
	);

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
			'label'       => __( 'Mensaje de consultas generales', 'coco-theme' ),
			'description' => __( 'Usá {sitio} para insertar automáticamente el nombre de la tienda.', 'coco-theme' ),
			'section'     => 'coco_whatsapp_options',
			'type'        => 'textarea',
			'priority'    => 30,
		)
	);

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
			'label'       => __( 'Mensaje desde una ficha de producto', 'coco-theme' ),
			'description' => __( 'Variables disponibles: {sitio} (nombre de la tienda), {producto} (nombre del artículo) y {url} (enlace al producto).', 'coco-theme' ),
			'section'     => 'coco_whatsapp_options',
			'type'        => 'textarea',
			'priority'    => 40,
		)
	);

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
			'label'       => __( 'Dirección de la tienda (pie de página)', 'coco-theme' ),
			'description' => __( 'Aparece en la columna de atención del pie de página.', 'coco-theme' ),
			'section'     => 'coco_whatsapp_options',
			'type'        => 'text',
			'priority'    => 50,
		)
	);

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
			'label'       => __( 'Botón flotante de WhatsApp', 'coco-theme' ),
			'description' => __( 'Muestra el botón en la esquina inferior de la pantalla para atención directa.', 'coco-theme' ),
			'section'     => 'coco_whatsapp_options',
			'type'        => 'checkbox',
			'priority'    => 60,
		)
	);
}
add_action( 'customize_register', 'coco_theme_customize_register', 20 );

/**
 * Marcar el cuerpo cuando el título y la descripción del sitio están ocultos.
 *
 * @param array $classes Clases del body.
 * @return array
 */
function coco_theme_customizer_body_class( $classes ) {
	if ( ! get_theme_mod( 'coco_theme_show_title_tagline', false ) ) {
		$classes[] = 'coco-hide-title';
	}

	return $classes;
}
add_filter( 'body_class', 'coco_theme_customizer_body_class' );

/**
 * Imprimir las variables de color, logo y visibilidad del título.
 */
function coco_theme_customizer_dynamic_css() {
	$logo_width  = coco_theme_sanitize_logo_width( get_theme_mod( 'coco_theme_logo_width', 125 ) );
	$logo_height = (int) round( $logo_width * 0.49 );

	$sp_bg     = sanitize_hex_color( get_theme_mod( 'coco_theme_sp_bg_color', '#FFFFFF' ) ) ?: '#FFFFFF';
	$sp_border = sanitize_hex_color( get_theme_mod( 'coco_theme_sp_border_color', '#E4E7EB' ) ) ?: '#E4E7EB';
	$sp_title  = sanitize_hex_color( get_theme_mod( 'coco_theme_sp_title_color', '#1F2933' ) ) ?: '#1F2933';
	$sp_text   = sanitize_hex_color( get_theme_mod( 'coco_theme_sp_text_color', '#52606D' ) ) ?: '#52606D';
	$sp_badge  = sanitize_hex_color( get_theme_mod( 'coco_theme_sp_badge_color', '#16A34A' ) ) ?: '#16A34A';

	$btn_bg    = sanitize_hex_color( get_theme_mod( 'coco_theme_btn_cod_bg_color', '#16A34A' ) ) ?: '#16A34A';
	$btn_text  = sanitize_hex_color( get_theme_mod( 'coco_theme_btn_cod_text_color', '#FFFFFF' ) ) ?: '#FFFFFF';
	$btn_hover = sanitize_hex_color( get_theme_mod( 'coco_theme_btn_cod_hover_color', '#15803D' ) ) ?: '#15803D';

	// Extraer valores RGB para que el resplandor de la animación Pulse use exactamente el color elegido.
	$r = 22;
	$g = 163;
	$b = 74;
	if ( preg_match( '/^#?([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})$/i', $btn_bg, $m ) ) {
		$r = hexdec( $m[1] );
		$g = hexdec( $m[2] );
		$b = hexdec( $m[3] );
	}
	?>
	<style id="coco-customizer-dynamic-css">
		:root {
			--coco-logo-width: <?php echo (int) $logo_width; ?>px;
			--coco-logo-height: <?php echo (int) $logo_height; ?>px;
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
			width: var(--coco-logo-width) !important;
			height: var(--coco-logo-height) !important;
			max-height: calc(var(--coco-logo-height) + 6px) !important;
			object-fit: contain !important;
		}

		body.coco-hide-title .wp-block-site-title,
		body.coco-hide-title .wp-block-site-tagline,
		body.coco-hide-title .site-title,
		body.coco-hide-title .site-description {
			display: none !important;
		}

		body.coco-hide-title .coco-header-logo .wp-block-site-title,
		body.coco-hide-title .coco-footer-logo .wp-block-site-title {
			display: block !important;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'coco_theme_customizer_dynamic_css', 99 );

/**
 * Encolar el JS que muestra u oculta controles dependientes en el panel del Personalizador.
 */
function coco_theme_customize_controls_assets() {
	$script_path = COCO_THEME_DIR . '/assets/js/customizer-controls.js';

	wp_enqueue_script(
		'coco-customizer-controls',
		COCO_THEME_URI . '/assets/js/customizer-controls.js',
		array( 'customize-controls', 'jquery' ),
		file_exists( $script_path ) ? filemtime( $script_path ) : COCO_THEME_VERSION,
		true
	);

	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	if ( ! $shop_url ) {
		$shop_url = home_url( '/shop/' );
	}

	$product_url = $shop_url;
	if ( function_exists( 'wc_get_products' ) ) {
		$products = wc_get_products(
			array(
				'limit'   => 1,
				'status'  => 'publish',
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);

		if ( ! empty( $products ) ) {
			$product_url = get_permalink( $products[0]->get_id() );
		}
	}

	wp_localize_script(
		'coco-customizer-controls',
		'cocoCustomizer',
		array(
			'sectionUrls' => array(
				'coco_header'           => home_url( '/' ),
				'coco_hero'             => $shop_url,
				'coco_commerce'         => $product_url,
				'coco_product'          => $product_url,
				'coco_sales_pop'        => home_url( '/' ),
				'coco_whatsapp_options' => home_url( '/' ),
			),
		)
	);
}
add_action( 'customize_controls_enqueue_scripts', 'coco_theme_customize_controls_assets' );

/**
 * Encolar el JS de vista previa en vivo (colores, ancho de logo y visibilidad del título).
 */
function coco_theme_customize_preview_assets() {
	$script_path = COCO_THEME_DIR . '/assets/js/customizer-preview.js';

	wp_enqueue_script(
		'coco-customizer-preview',
		COCO_THEME_URI . '/assets/js/customizer-preview.js',
		array( 'customize-preview' ),
		file_exists( $script_path ) ? filemtime( $script_path ) : COCO_THEME_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'coco_theme_customize_preview_assets' );
