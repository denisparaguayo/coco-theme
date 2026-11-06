<?php
/**
 * Title: Cabecera Principal (Header)
 * Slug: coco-theme/header
 * Categories: header, coco-cherry
 * Block Types: core/template-part/header
 * Description: Cabecera Boutique con marquesina superior, logo, navegación con iconos, buscador y acciones con color de fondo configurable.
 */

$header_wa_url  = function_exists( 'coco_theme_get_whatsapp_url' ) ? coco_theme_get_whatsapp_url( 'general' ) : 'https://wa.me/595981531503';
$header_wa_disp = function_exists( 'coco_theme_get_whatsapp_display' ) ? coco_theme_get_whatsapp_display() : '0981 531503';
$site_name      = get_bloginfo( 'name' );
?>
<!-- wp:group {"tagName":"div","className":"coco-sticky-top-marquee","backgroundColor":"dark","textColor":"white","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
<div id="coco-sticky-marquee-top" class="wp-block-group coco-sticky-top-marquee has-white-color has-dark-background-color has-text-color has-background">
	<div class="coco-top-marquee-container">
		<div class="coco-top-marquee-track">
			<span>💵 Pago Contra Entrega en efectivo o QR</span>
			<span class="coco-marquee-dot">•</span>
			<span>🚚 Envíos a todo Paraguay</span>
			<span class="coco-marquee-dot">•</span>
			<span>📲 WhatsApp: <a href="<?php echo esc_url( $header_wa_url ); ?>" target="_blank" rel="noreferrer noopener"><?php echo esc_html( $header_wa_disp ); ?></a></span>
			<span class="coco-marquee-dot">•</span>
			<span>🛡️ 100% Compra Segura</span>
			<span class="coco-marquee-dot">•</span>
			<span>💵 Pago Contra Entrega en efectivo o QR</span>
			<span class="coco-marquee-dot">•</span>
			<span>🚚 Envíos a todo Paraguay</span>
			<span class="coco-marquee-dot">•</span>
			<span>📲 WhatsApp: <a href="<?php echo esc_url( $header_wa_url ); ?>" target="_blank" rel="noreferrer noopener"><?php echo esc_html( $header_wa_disp ); ?></a></span>
			<span class="coco-marquee-dot">•</span>
			<span>🛡️ 100% Compra Segura</span>
		</div>
	</div>
	<button type="button" class="coco-top-marquee-close" aria-label="Cerrar aviso" onclick="cocoDismissTopBar()">✕</button>
</div>
<!-- /wp:group -->

<!-- wp:group {"tagName":"header","className":"coco-site-header coco-boutique-header","backgroundColor":"white","layout":{"type":"constrained"}} -->
<header class="wp-block-group coco-site-header coco-boutique-header has-white-background-color has-background">
	<!-- wp:group {"align":"wide","className":"coco-header-container","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group alignwide coco-header-container">
		<!-- wp:group {"className":"coco-header-logo-area","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
		<div class="wp-block-group coco-header-logo-area">
			<!-- wp:site-logo {"width":140,"shouldSyncIcon":false} /-->
			<!-- wp:site-title {"level":0,"style":{"typography":{"fontWeight":"800","fontSize":"1.35rem"}}} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:html -->
		<nav class="coco-header-nav-center" aria-label="Navegación Principal">
			<ul class="coco-boutique-nav-list">
				<li class="coco-nav-item">
					<a href="/shop/" class="coco-nav-link">
						<svg class="coco-nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
							<polyline points="9 22 9 12 15 12 15 22"></polyline>
						</svg>
						<span class="coco-nav-label">Tienda</span>
					</a>
				</li>
				<li class="coco-nav-item">
					<a href="/categoria-producto/bijouterie/" class="coco-nav-link">
						<svg class="coco-nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<path d="M6 3h12l4 6-10 13L2 9z"></path>
							<path d="M11 3 8 9l4 13 4-13-3-6"></path>
							<path d="M2 9h20"></path>
						</svg>
						<span class="coco-nav-label">Bijouterie & Accesorios</span>
					</a>
				</li>
				<li class="coco-nav-item">
					<a href="/categoria-producto/hogar/" class="coco-nav-link">
						<svg class="coco-nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<path d="M12 2a4 4 0 0 0-4 4c0 1.5.8 2.8 2 3.5L2 14v2h20v-2l-8-4.5c1.2-.7 2-2 2-3.5a4 4 0 0 0-4-4z"></path>
							<path d="M12 6a2 2 0 1 1-4 0"></path>
						</svg>
						<span class="coco-nav-label">Hogar</span>
					</a>
				</li>
				<li class="coco-nav-item">
					<a href="/categoria-producto/moda/" class="coco-nav-link">
						<svg class="coco-nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 12 20 22 4 22 4 12"></polyline>
							<rect x="2" y="7" width="20" height="5"></rect>
							<line x1="12" y1="22" x2="12" y2="7"></line>
							<path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
							<path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
						</svg>
						<span class="coco-nav-label">Moda & Indumentaria</span>
					</a>
				</li>
				<li class="coco-nav-item">
					<a href="/faq/" class="coco-nav-link">
						<svg class="coco-nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
						</svg>
						<span class="coco-nav-label">Preguntas Frecuentes</span>
					</a>
				</li>
				<li class="coco-nav-item">
					<a href="<?php echo esc_url( $header_wa_url ); ?>" target="_blank" rel="noreferrer noopener" class="coco-nav-link">
						<svg class="coco-nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
							<polyline points="22,6 12,13 2,6"></polyline>
						</svg>
						<span class="coco-nav-label">Contacto</span>
					</a>
				</li>
			</ul>
		</nav>
		<!-- /wp:html -->

		<!-- wp:html -->
		<div class="coco-header-actions-right">
			<button type="button" class="coco-header-action-btn coco-btn-search-toggle" aria-label="Abrir buscador" onclick="cocoToggleSearch()">
				<svg style="pointer-events:none;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
			</button>
			<a href="<?php echo esc_url( $header_wa_url ); ?>" class="coco-header-action-btn" aria-label="Atención y Pedidos por WhatsApp" target="_blank" rel="noreferrer noopener" title="Atención y Pedidos por WhatsApp">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
			</a>
			<button type="button" class="coco-header-mobile-toggle" aria-label="Abrir menú de navegación" onclick="cocoToggleMobileNav(true)">
				<svg style="pointer-events:none;" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
			</button>
		</div>
		<!-- /wp:html -->
	</div>
	<!-- /wp:group -->

	<!-- wp:html -->
	<div id="coco-header-search-bar" class="coco-header-search-drawer" style="display:none;">
		<div class="coco-header-search-inner">
			<form role="search" method="get" class="coco-header-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<svg class="coco-search-input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
				<input type="search" class="coco-header-search-input" placeholder="Buscar productos..." name="s" autocomplete="off" />
				<input type="hidden" name="post_type" value="product" />
				<button type="submit" class="coco-header-search-submit">Buscar</button>
				<button type="button" class="coco-header-search-close" onclick="cocoToggleSearch(false)">✕</button>
			</form>
		</div>
	</div>
	<!-- /wp:html -->
</header>
<!-- /wp:group -->

<!-- wp:html -->
<div id="coco-mobile-drawer" class="coco-mobile-nav-drawer" aria-hidden="true" style="display:none;">
	<div class="coco-mobile-drawer-backdrop" onclick="cocoToggleMobileNav(false)"></div>
	<div class="coco-mobile-drawer-content">
		<div class="coco-mobile-drawer-header">
			<span class="coco-mobile-drawer-title">Menú <?php echo esc_html( $site_name ); ?></span>
			<button type="button" class="coco-mobile-drawer-close" aria-label="Cerrar menú" onclick="cocoToggleMobileNav(false)">✕</button>
		</div>
		<ul class="coco-mobile-nav-list">
			<li><a href="/shop/"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg> Tienda (Catálogo)</a></li>
			<li><a href="/faq/"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg> Preguntas Frecuentes</a></li>
			<li><a href="<?php echo esc_url( $header_wa_url ); ?>" target="_blank" rel="noreferrer noopener"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path></svg> Contacto (WhatsApp)</a></li>
		</ul>
	</div>
</div>
<!-- /wp:html -->
