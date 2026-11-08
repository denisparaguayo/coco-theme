<?php
/**
 * Plantilla de agradecimiento / confirmación del pedido — Coco Theme.
 *
 * Rediseño celebratorio + informativo.
 * El tema es la fuente de verdad de la presentación.
 *
 * @package Coco_Theme
 * @var \WC_Order|false $order
 */

defined( 'ABSPATH' ) || exit;

// Recuperar el pedido si no viene inyectado.
if ( ! $order instanceof \WC_Order ) {
	global $wp;
	$order_id_fallback  = isset( $wp->query_vars['order-received'] ) ? absint( $wp->query_vars['order-received'] ) : ( isset( $_GET['order'] ) ? absint( $_GET['order'] ) : 0 );
	$order_key_fallback = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : '';
	if ( $order_id_fallback > 0 ) {
		$candidate = wc_get_order( $order_id_fallback );
		if ( $candidate && ( empty( $order_key_fallback ) || hash_equals( $candidate->get_order_key(), $order_key_fallback ) ) ) {
			$order = $candidate;
		}
	}
}

if ( ! $order instanceof \WC_Order ) {
	return;
}

// Datos del plugin (si está activo) con fallback para COD básico.
$plugin_active = defined( 'COCO_COD_PATH' ) && class_exists( '\\Coco_COD\\Plugin' );
$plugin        = $plugin_active ? \Coco_COD\Plugin::instance() : null;

$order_id   = $order->get_id();
$first_name = $order->get_billing_first_name();
$last_name  = $order->get_billing_last_name();
$full_name  = trim( $first_name . ' ' . $last_name );
$phone      = $order->get_billing_phone();
$email      = $order->get_billing_email();

$address_1  = $order->get_shipping_address_1() ? $order->get_shipping_address_1() : $order->get_billing_address_1();
$address_2  = $order->get_shipping_address_2() ? $order->get_shipping_address_2() : $order->get_billing_address_2();
$city       = $order->get_shipping_city() ? $order->get_shipping_city() : $order->get_billing_city();
$state_code = $order->get_shipping_state() ? $order->get_shipping_state() : $order->get_billing_state();

$state_name = $state_code;
if ( $plugin_active ) {
	$locations = $plugin->get_locations();
	if ( isset( $locations[ $state_code ]['name'] ) ) {
		$state_name = $locations[ $state_code ]['name'];
	}
}

$total_formatted    = wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) );
$subtotal_formatted = wc_price( $order->get_subtotal(), array( 'currency' => $order->get_currency() ) );
$shipping_cost      = (float) $order->get_shipping_total();
$shipping_formatted = $shipping_cost > 0
	? wc_price( $shipping_cost, array( 'currency' => $order->get_currency() ) )
	: esc_html__( 'Gratis', 'coco-theme' );

$payment_type = $order->get_meta( '_coco_cod_payment_type' );
if ( ! $payment_type ) {
	$payment_type = ( 'bacs' === $order->get_payment_method() ) ? 'transfer' : 'cod';
}
$is_transfer = 'transfer' === $payment_type;

$bank_name     = $plugin_active ? $plugin->get_setting( 'bank_name', 'Ueno' ) : 'Ueno';
$bank_holder   = $plugin_active ? $plugin->get_setting( 'bank_holder', 'MARIA ANASTASIA CONCEPCION PRIETO DE CANTERO' ) : 'MARIA ANASTASIA CONCEPCION PRIETO DE CANTERO';
$bank_id       = $plugin_active ? $plugin->get_setting( 'bank_id', '1064733' ) : '1064733';
$bank_account  = $plugin_active ? $plugin->get_setting( 'bank_account', '61965042' ) : '61965042';
$bank_currency = $plugin_active ? $plugin->get_setting( 'bank_currency', 'Gs' ) : 'Gs';

$whatsapp_clean = '';
$whatsapp_url   = '';
if ( $plugin_active ) {
	$whatsapp_num   = $plugin->get_setting( 'whatsapp_number', '' );
	$whatsapp_clean = preg_replace( '/\D/', '', $whatsapp_num );
}
if ( empty( $whatsapp_clean ) ) {
	$whatsapp_clean = function_exists( 'coco_theme_get_whatsapp_number' ) ? coco_theme_get_whatsapp_number() : '595981531503';
}
if ( ! empty( $whatsapp_clean ) ) {
	if ( $is_transfer ) {
		$wa_msg = sprintf(
			__( '¡Hola! Acabo de registrar el pedido #%1$s en su tienda por un total de %2$s vía Transferencia (%3$s). Mi nombre es %4$s. Adjunto mi comprobante para despachar mi paquete.', 'coco-theme' ),
			$order_id,
			wp_strip_all_tags( $total_formatted ),
			$bank_name,
			$full_name
		);
	} else {
		$wa_msg = sprintf(
			__( '¡Hola! Acabo de registrar el pedido #%1$s por un total de %2$s con Pago Contra Entrega. Mi nombre es %3$s.', 'coco-theme' ),
			$order_id,
			wp_strip_all_tags( $total_formatted ),
			$full_name
		);
	}
	$whatsapp_url = 'https://wa.me/' . $whatsapp_clean . '?text=' . rawurlencode( $wa_msg );
}

$shop_url = wc_get_page_permalink( 'shop' );
if ( ! $shop_url ) {
	$shop_url = home_url( '/' );
}

$item_count = $order->get_item_count();
?>
<div class="coco-thankyou-wrapper" id="coco-receipt-area">
	<div class="coco-thankyou-card">

		<div class="coco-ty-hero">
			<div class="coco-ty-badge">
				<span class="coco-ty-badge-dot" aria-hidden="true"></span>
				<strong><?php echo esc_html( sprintf( __( 'Pedido #%s registrado', 'coco-theme' ), $order_id ) ); ?></strong>
			</div>

			<h1 class="coco-ty-title">
				<?php
				if ( ! empty( $first_name ) ) {
					echo esc_html( sprintf( __( '¡Muchas gracias, %s!', 'coco-theme' ), $first_name ) );
				} else {
					esc_html_e( '¡Muchas gracias por tu compra!', 'coco-theme' );
				}
				?>
			</h1>

			<p class="coco-ty-lead">
				<?php if ( $is_transfer ) : ?>
					<?php esc_html_e( 'Tu pedido fue registrado con éxito. Realizá tu transferencia con los datos de abajo y envianos el comprobante por WhatsApp para despachar de inmediato.', 'coco-theme' ); ?>
				<?php else : ?>
					<?php esc_html_e( 'Tu pedido está listo para ser coordinado. En breve nuestro equipo se pondrá en contacto contigo por WhatsApp para confirmar el día de entrega en tu puerta.', 'coco-theme' ); ?>
				<?php endif; ?>
			</p>

			<div class="coco-ty-total-block">
				<span class="coco-ty-total-label"><?php echo $is_transfer ? esc_html__( 'Total a Transferir:', 'coco-theme' ) : esc_html__( 'Total a pagar al recibir:', 'coco-theme' ); ?></span>
				<strong class="coco-ty-total-amount"><?php echo wp_kses_post( $total_formatted ); ?></strong>
				<span class="coco-ty-total-items"><?php echo esc_html( sprintf( _n( '%s producto', '%s productos', $item_count, 'coco-theme' ), $item_count ) ); ?></span>
			</div>
		</div>

		<?php if ( $is_transfer ) : ?>
			<div class="coco-ty-notice coco-ty-notice--transfer">
				<div class="coco-ty-notice-icon" aria-hidden="true">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 21h18M3 10h18M5 10v11M9 10v11M15 10v11M19 10v11M12 2L2 7h20L12 2z"/>
					</svg>
				</div>
				<div class="coco-ty-notice-content">
					<span class="coco-ty-notice-tag"><?php esc_html_e( 'TRANSFERENCIA BANCARIA / PAGO QR', 'coco-theme' ); ?></span>
					<p class="coco-ty-notice-text"><?php esc_html_e( 'Realizá tu transferencia con los datos de abajo y enviá el comprobante por WhatsApp para despachar de inmediato.', 'coco-theme' ); ?></p>
				</div>
			</div>

			<div class="coco-ty-bank-box">
				<div class="coco-ty-bank-header">
					<div class="coco-ty-bank-icon" aria-hidden="true">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M3 21h18M3 10h18M5 10v11M9 10v11M15 10v11M19 10v11M12 2L2 7h20L12 2z"/>
						</svg>
					</div>
					<h3 class="coco-ty-bank-title"><?php esc_html_e( 'Datos para la transferencia', 'coco-theme' ); ?></h3>
				</div>
				<div class="coco-ty-bank-table"><?php if ( $bank_name ) : ?><div class="coco-ty-bank-row"><span class="coco-ty-bank-label"><?php esc_html_e( 'Banco', 'coco-theme' ); ?></span><strong class="coco-ty-bank-value"><?php echo esc_html( $bank_name ); ?></strong></div><?php endif; ?><?php if ( $bank_holder ) : ?><div class="coco-ty-bank-row"><span class="coco-ty-bank-label"><?php esc_html_e( 'Titular', 'coco-theme' ); ?></span><strong class="coco-ty-bank-value"><?php echo esc_html( $bank_holder ); ?></strong></div><?php endif; ?><?php if ( $bank_id ) : ?><div class="coco-ty-bank-row"><span class="coco-ty-bank-label"><?php esc_html_e( 'C.I.', 'coco-theme' ); ?></span><strong class="coco-ty-bank-value"><?php echo esc_html( $bank_id ); ?></strong></div><?php endif; ?><?php if ( $bank_account ) : ?><div class="coco-ty-bank-row coco-ty-bank-row--account"><span class="coco-ty-bank-label"><?php esc_html_e( 'Cuenta', 'coco-theme' ); ?></span><div class="coco-ty-account-block"><strong class="coco-ty-bank-value" id="coco-bank-acc"><?php echo esc_html( $bank_account ); ?></strong><button type="button" class="coco-ty-copy-btn" id="coco-btn-copy-acc" data-copy="<?php echo esc_attr( $bank_account ); ?>"><?php esc_html_e( 'Copiar', 'coco-theme' ); ?></button></div></div><?php endif; ?><div class="coco-ty-bank-row coco-ty-bank-row--total"><span class="coco-ty-bank-label"><?php esc_html_e( 'Monto exacto', 'coco-theme' ); ?></span><strong class="coco-ty-bank-amount"><?php echo wp_kses_post( $total_formatted ); ?></strong></div></div>
				<div class="coco-ty-bank-alert">
					<span aria-hidden="true">💡</span>
					<p><?php esc_html_e( 'Una vez hecha la transferencia, enviá tu comprobante por WhatsApp indicando tu número de pedido para despachar tu paquete inmediatamente.', 'coco-theme' ); ?></p>
				</div>
			</div>
		<?php else : ?>
			<div class="coco-ty-notice coco-ty-notice--cod">
				<div class="coco-ty-notice-icon" aria-hidden="true">
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<rect x="2" y="6" width="20" height="12" rx="2"></rect>
						<circle cx="12" cy="12" r="3"></circle>
						<path d="M6 12h.01M18 12h.01"></path>
					</svg>
				</div>
				<div class="coco-ty-notice-content">
					<span class="coco-ty-notice-tag"><?php esc_html_e( 'PAGO CONTRA ENTREGA', 'coco-theme' ); ?></span>
					<p class="coco-ty-notice-text"><?php esc_html_e( 'No pagás nada por adelantado. Tené listo el importe en efectivo cuando el repartidor llegue a tu dirección.', 'coco-theme' ); ?></p>
				</div>
			</div>
		<?php endif; ?>

		<div class="coco-ty-tracker" aria-label="Progreso del pedido">
			<div class="coco-ty-tracker-step is-done">
				<div class="coco-ty-tracker-dot">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
				</div>
				<span class="coco-ty-tracker-label"><?php esc_html_e( 'Pedido Recibido', 'coco-theme' ); ?></span>
			</div>
			<div class="coco-ty-tracker-line is-active"></div>
			<div class="coco-ty-tracker-step is-current">
				<div class="coco-ty-tracker-dot">
					<?php if ( $is_transfer ) : ?>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
					<?php else : ?>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
					<?php endif; ?>
				</div>
				<span class="coco-ty-tracker-label"><?php echo $is_transfer ? esc_html__( 'Verificación', 'coco-theme' ) : esc_html__( 'Coordinación', 'coco-theme' ); ?></span>
			</div>
			<div class="coco-ty-tracker-line"></div>
			<div class="coco-ty-tracker-step is-pending">
				<div class="coco-ty-tracker-dot">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
				</div>
				<span class="coco-ty-tracker-label"><?php esc_html_e( 'Entregado', 'coco-theme' ); ?></span>
			</div>
		</div>

		<div class="coco-ty-grid">
			<div class="coco-ty-card">
				<h3 class="coco-ty-card-heading">
					<span class="coco-ty-card-icon" aria-hidden="true">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
							<circle cx="12" cy="10" r="3"></circle>
						</svg>
					</span>
					<?php esc_html_e( 'Datos de Entrega', 'coco-theme' ); ?>
				</h3>
				<div class="coco-ty-info-list">
					<div class="coco-ty-info-item">
						<span class="coco-ty-info-label"><?php esc_html_e( 'Destinatario', 'coco-theme' ); ?></span>
						<strong class="coco-ty-info-value"><?php echo esc_html( $full_name ); ?></strong>
					</div>
					<div class="coco-ty-info-item">
						<span class="coco-ty-info-label"><?php esc_html_e( 'Teléfono', 'coco-theme' ); ?></span>
						<strong class="coco-ty-info-value"><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>" class="coco-ty-phone-link"><?php echo esc_html( $phone ); ?></a></strong>
					</div>
					<?php if ( $email ) : ?>
					<div class="coco-ty-info-item">
						<span class="coco-ty-info-label"><?php esc_html_e( 'Correo', 'coco-theme' ); ?></span>
						<span class="coco-ty-info-value"><?php echo esc_html( $email ); ?></span>
					</div>
					<?php endif; ?>
					<div class="coco-ty-info-item">
						<span class="coco-ty-info-label"><?php esc_html_e( 'Ciudad', 'coco-theme' ); ?></span>
						<span class="coco-ty-info-value"><?php echo esc_html( $city . ', ' . $state_name ); ?></span>
					</div>
					<div class="coco-ty-info-item">
						<span class="coco-ty-info-label"><?php esc_html_e( 'Dirección', 'coco-theme' ); ?></span>
						<span class="coco-ty-info-value"><?php echo esc_html( $address_1 ); ?></span>
					</div>
					<?php if ( ! empty( $address_2 ) ) : ?>
					<div class="coco-ty-info-item coco-ty-info-item--ref">
						<span class="coco-ty-info-label"><?php esc_html_e( 'Referencia', 'coco-theme' ); ?></span>
						<span class="coco-ty-info-value"><em><?php echo esc_html( $address_2 ); ?></em></span>
					</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="coco-ty-card">
				<h3 class="coco-ty-card-heading">
					<span class="coco-ty-card-icon" aria-hidden="true">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
							<line x1="3" y1="6" x2="21" y2="6"></line>
							<path d="M16 10a4 4 0 0 1-8 0"></path>
						</svg>
					</span>
					<?php esc_html_e( 'Tu Pedido', 'coco-theme' ); ?>
				</h3>
				<div class="coco-ty-items-list">
					<?php foreach ( $order->get_items() as $item_id => $item ) : ?>
						<?php
							$product = $item->get_product();
							$image   = $product ? $product->get_image( array( 56, 56 ) ) : '';
						?>
						<div class="coco-ty-item">
							<?php if ( $image ) : ?>
								<div class="coco-ty-item-thumb">
									<?php echo wp_kses_post( $image ); ?>
									<span class="coco-ty-item-qty"><?php echo esc_html( $item->get_quantity() ); ?></span>
								</div>
							<?php endif; ?>
							<div class="coco-ty-item-details">
								<h4 class="coco-ty-item-title"><?php echo esc_html( $item->get_name() ); ?></h4>
								<span class="coco-ty-item-meta"><?php echo esc_html( sprintf( __( 'Cantidad: %s', 'coco-theme' ), $item->get_quantity() ) ); ?></span>
							</div>
							<div class="coco-ty-item-price">
								<?php echo wp_kses_post( wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) ) ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="coco-ty-totals">
					<div class="coco-ty-total-row">
						<span><?php esc_html_e( 'Subtotal', 'coco-theme' ); ?></span>
						<span><?php echo wp_kses_post( $subtotal_formatted ); ?></span>
					</div>
					<div class="coco-ty-total-row">
						<span><?php esc_html_e( 'Envío', 'coco-theme' ); ?></span>
						<span class="<?php echo ( 0 === (int) $shipping_cost ) ? 'coco-ty-free-shipping' : ''; ?>"><?php echo wp_kses_post( $shipping_formatted ); ?></span>
					</div>
					<div class="coco-ty-total-row coco-ty-total-row--grand">
						<span><?php esc_html_e( 'Total a pagar', 'coco-theme' ); ?></span>
						<strong class="coco-ty-grand-total"><?php echo wp_kses_post( $total_formatted ); ?></strong>
					</div>
				</div>
			</div>
		</div>

		<div class="coco-ty-actions">
			<?php if ( ! empty( $whatsapp_url ) ) : ?>
				<a href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener" class="coco-ty-btn-whatsapp">
					<?php echo $is_transfer ? esc_html__( 'Enviar comprobante por WhatsApp', 'coco-theme' ) : esc_html__( 'Confirmar pedido por WhatsApp', 'coco-theme' ); ?>
				</a>
			<?php endif; ?>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="coco-ty-btn-return">
				&larr; <?php esc_html_e( 'Volver a la tienda', 'coco-theme' ); ?>
			</a>
		</div>

		<div class="coco-ty-guarantee">
			<div class="coco-ty-guarantee-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
					<path d="M9 12l2 2 4-4"></path>
				</svg>
			</div>
			<div class="coco-ty-guarantee-text">
				<strong><?php esc_html_e( 'Garantía Coco Cherry Store', 'coco-theme' ); ?></strong>
				<span><?php esc_html_e( 'Tu compra es 100% segura. Pagás únicamente al recibir tu paquete en mano.', 'coco-theme' ); ?></span>
			</div>
		</div>

	</div>
</div>

<script>
(function(){
	// Limpiar etiquetas espurias de wpautop en toda la card
	var card = document.querySelector('.coco-thankyou-card');
	if (card) {
		card.querySelectorAll('p, br').forEach(function(el){
			// Solo eliminar si esta vacio o solo contiene comentarios HTML
			var text = el.textContent.trim();
			var html = el.innerHTML.trim();
			if (text === '' || html === '' || /^<!--.*-->$/.test(html)) {
				el.remove();
			}
		});
	}
	// Copiar numero de cuenta
	var btn = document.getElementById('coco-btn-copy-acc');
	if (!btn) return;
	btn.addEventListener('click', function(e){
		e.preventDefault();
		var text = this.getAttribute('data-copy');
		if (!text) return;
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(text).then(function(){
				btn.classList.add('copied');
				btn.textContent = 'Copiado';
				setTimeout(function(){
					btn.classList.remove('copied');
					btn.textContent = 'Copiar';
				}, 2000);
			});
		}
	});
})();
</script>

<?php
remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
do_action( 'woocommerce_thankyou', $order->get_id() );
?>