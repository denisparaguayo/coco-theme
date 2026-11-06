<?php
/**
 * Title: Pie de Página Principal (Footer)
 * Slug: coco-theme/footer
 * Categories: footer, coco-cherry
 * Block Types: core/template-part/footer
 * Description: Pie de página 100% nativo con columnas de categorías, enlaces de ayuda, atención por WhatsApp y color de fondo editable.
 */

$footer_wa_url  = function_exists( 'coco_theme_get_whatsapp_url' ) ? coco_theme_get_whatsapp_url( 'general' ) : 'https://wa.me/595981531503';
$footer_wa_disp = function_exists( 'coco_theme_get_whatsapp_display' ) ? coco_theme_get_whatsapp_display() : '0981 531503';
$store_address  = function_exists( 'coco_theme_get_store_address' ) ? coco_theme_get_store_address() : 'Guaraníes 4779, Asunción, Paraguay';
$site_name      = get_bloginfo( 'name' );
$current_year   = date( 'Y' );
?>
<!-- wp:group {"tagName":"footer","className":"coco-site-footer","style":{"spacing":{"padding":{"top":"4rem","bottom":"2.5rem","left":"1.25rem","right":"1.25rem"}}},"backgroundColor":"dark","textColor":"white","layout":{"type":"constrained"}} -->
<footer class="wp-block-group coco-site-footer has-white-color has-dark-background-color has-text-color has-background" style="padding-top:4rem;padding-right:1.25rem;padding-bottom:2.5rem;padding-left:1.25rem">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"2.5rem","left":"2.5rem"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"34%"} -->
		<div class="wp-block-column" style="flex-basis:34%">
			<!-- wp:group {"className":"coco-footer-logo","style":{"spacing":{"blockGap":"0.5rem"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
			<div class="wp-block-group coco-footer-logo">
				<!-- wp:site-logo {"width":120,"shouldSyncIcon":false} /-->
				<!-- wp:site-title {"level":3,"style":{"typography":{"fontWeight":"800","fontSize":"1.35rem"}},"textColor":"white"} /-->
			</div>
			<!-- /wp:group -->
			<!-- wp:paragraph {"style":{"color":{"text":"#cbd5e1"}},"fontSize":"sm"} -->
			<p class="has-text-color has-sm-font-size" style="color:#cbd5e1"><strong>Tendencias &bull; Novedades &bull; Entrega en Puerta</strong><br>Hacé tu pedido en 1 solo clic y pagá en efectivo o QR recién cuando recibas tus productos en tu domicilio.</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"style":{"color":{"text":"#4ade80"}},"fontSize":"sm"} -->
			<p class="has-text-color has-sm-font-size" style="color:#4ade80">🛡️ <strong>100% Compra Segura:</strong> Efectivo o QR contra entrega.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"22%"} -->
		<div class="wp-block-column" style="flex-basis:22%">
			<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"1.05rem","fontWeight":"700"}},"textColor":"white"} -->
			<h4 class="wp-block-heading has-white-color has-text-color" style="font-size:1.05rem;font-weight:700">Categorías</h4>
			<!-- /wp:heading -->
			<!-- wp:list {"style":{"typography":{"lineHeight":"2"},"color":{"text":"#cbd5e1"}},"fontSize":"sm"} -->
			<ul style="color:#cbd5e1;line-height:2" class="wp-block-list has-text-color has-sm-font-size">
				<?php
				$has_cats = false;
				if ( taxonomy_exists( 'product_cat' ) ) {
					$footer_cats = get_terms(
						array(
							'taxonomy'   => 'product_cat',
							'hide_empty' => true,
							'number'     => 4,
							'parent'     => 0,
							'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
						)
					);
					if ( ! empty( $footer_cats ) && ! is_wp_error( $footer_cats ) ) {
						$has_cats = true;
						foreach ( $footer_cats as $fcat ) {
							echo '<!-- wp:list-item --><li><a href="' . esc_url( get_term_link( $fcat ) ) . '">' . esc_html( $fcat->name ) . '</a></li><!-- /wp:list-item -->';
						}
					}
				}
				if ( ! $has_cats ) {
					echo '<!-- wp:list-item --><li><a href="' . esc_url( home_url( '/shop/' ) ) . '">Destacados</a></li><!-- /wp:list-item -->';
					echo '<!-- wp:list-item --><li><a href="' . esc_url( home_url( '/shop/' ) ) . '">Novedades</a></li><!-- /wp:list-item -->';
				}
				?>
				<!-- wp:list-item -->
				<li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Todo el Catálogo</a></li>
				<!-- /wp:list-item -->
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"20%"} -->
		<div class="wp-block-column" style="flex-basis:20%">
			<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"1.05rem","fontWeight":"700"}},"textColor":"white"} -->
			<h4 class="wp-block-heading has-white-color has-text-color" style="font-size:1.05rem;font-weight:700">Tienda y Ayuda</h4>
			<!-- /wp:heading -->
			<!-- wp:list {"style":{"typography":{"lineHeight":"2"},"color":{"text":"#cbd5e1"}},"fontSize":"sm"} -->
			<ul style="color:#cbd5e1;line-height:2" class="wp-block-list has-text-color has-sm-font-size">
				<!-- wp:list-item -->
				<li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Catálogo Completo</a></li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">Preguntas Frecuentes</a></li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li><a href="<?php echo esc_url( home_url( '/politica-de-privacidad/' ) ); ?>">Política de Privacidad</a></li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li><a href="<?php echo esc_url( $footer_wa_url ); ?>" target="_blank" rel="noreferrer noopener">Contacto por WhatsApp</a></li>
				<!-- /wp:list-item -->
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"24%"} -->
		<div class="wp-block-column" style="flex-basis:24%">
			<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"1.05rem","fontWeight":"700"}},"textColor":"white"} -->
			<h4 class="wp-block-heading has-white-color has-text-color" style="font-size:1.05rem;font-weight:700">Atención y Entregas</h4>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"style":{"color":{"text":"#cbd5e1"},"typography":{"lineHeight":"1.7"}},"fontSize":"sm"} -->
			<p class="has-text-color has-sm-font-size" style="color:#cbd5e1;line-height:1.7">📍 <strong>Ubicación:</strong> <?php echo esc_html( $store_address ); ?><br>📲 <strong>WhatsApp Pedidos:</strong> <a href="<?php echo esc_url( $footer_wa_url ); ?>" target="_blank" rel="noreferrer noopener" class="coco-footer-wa-link"><?php echo esc_html( $footer_wa_disp ); ?></a><br>🚚 <strong>Cobertura:</strong> Delivery en Asunción, Gran Asunción y envíos a todo Paraguay.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:separator {"align":"wide","style":{"spacing":{"margin":{"top":"2.5rem","bottom":"1.5rem"}},"color":{"background":"#334155"}}} -->
	<hr class="wp-block-separator alignwide has-text-color has-alpha-channel-opacity has-background" style="margin-top:2.5rem;margin-bottom:1.5rem;background-color:#334155;color:#334155"/>
	<!-- /wp:separator -->

	<!-- wp:group {"align":"wide","className":"coco-footer-bottom","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide coco-footer-bottom">
		<!-- wp:paragraph {"style":{"color":{"text":"#94a3b8"}},"fontSize":"xs"} -->
		<p class="has-text-color has-xs-font-size" style="color:#94a3b8">&copy; <?php echo esc_html( $current_year ); ?> <?php echo esc_html( $site_name ); ?> Paraguay. Todos los derechos reservados.</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"style":{"color":{"text":"#94a3b8"}},"fontSize":"xs"} -->
		<p class="has-text-color has-xs-font-size" style="color:#94a3b8">Pago Contra Entrega, transferencia, efectivo &bull; Envíos a todo Paraguay</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</footer>
<!-- /wp:group -->

