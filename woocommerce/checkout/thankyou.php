<?php
/**
 * Plantilla de agradecimiento / confirmación del pedido en Coco Theme.
 *
 * @package Coco_Theme
 * @var \WC_Order|false $order
 */

defined( 'ABSPATH' ) || exit;

// Si Coco COD está activo y tiene su plantilla, la cargamos para sincronización perfecta
if ( defined( 'COCO_COD_PATH' ) && file_exists( COCO_COD_PATH . 'templates/thankyou.php' ) ) {
	include COCO_COD_PATH . 'templates/thankyou.php';
	return;
}

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

// Datos del pedido de respaldo
$order_id   = $order->get_id();
$first_name = $order->get_billing_first_name();
$full_name  = trim( $first_name . ' ' . $order->get_billing_last_name() );
$phone      = $order->get_billing_phone();
$address_1  = $order->get_shipping_address_1() ? $order->get_shipping_address_1() : $order->get_billing_address_1();
$city       = $order->get_shipping_city() ? $order->get_shipping_city() : $order->get_billing_city();
$state      = $order->get_shipping_state() ? $order->get_shipping_state() : $order->get_billing_state();

$total_formatted    = wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) );
$subtotal_formatted = wc_price( $order->get_subtotal(), array( 'currency' => $order->get_currency() ) );
$shipping_cost      = (float) $order->get_shipping_total();
$shipping_formatted = $shipping_cost > 0 ? wc_price( $shipping_cost, array( 'currency' => $order->get_currency() ) ) : esc_html__( 'Gratis', 'coco-theme' );

$wa_phone = function_exists( 'coco_theme_get_whatsapp_number' ) ? coco_theme_get_whatsapp_number() : '595981531503';
$wa_msg   = sprintf( '¡Hola! Acabo de registrar el pedido #%s por %s. Mi nombre es %s.', $order_id, wp_strip_all_tags( $total_formatted ), $full_name );
$wa_url   = 'https://wa.me/' . $wa_phone . '?text=' . rawurlencode( $wa_msg );
$shop_url = wc_get_page_permalink( 'shop' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>

<div class="coco-thankyou-wrapper" id="coco-receipt-area">
	<div class="coco-thankyou-card">
		<div class="coco-thankyou-hero">
			<div class="coco-success-circle">
				<svg class="coco-checkmark-icon" viewBox="0 0 52 52" aria-hidden="true">
					<circle class="coco-checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
					<path class="coco-checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
				</svg>
			</div>

			<div class="coco-order-badge-pill">
				<span class="coco-pulsing-dot" aria-hidden="true"></span>
				<strong><?php echo esc_html( sprintf( __( 'Pedido #%s Registrado con Éxito', 'coco-theme' ), $order_id ) ); ?></strong>
			</div>

			<h1 class="coco-thankyou-title">
				<?php echo esc_html( sprintf( __( '¡Muchas gracias por tu compra, %s!', 'coco-theme' ), $first_name ? $first_name : '' ) ); ?>
			</h1>

			<p class="coco-thankyou-lead">
				<?php esc_html_e( 'Tu pedido está listo para ser coordinado. En breve nuestro equipo se pondrá en contacto contigo por WhatsApp o llamada para confirmar el día de entrega en tu puerta.', 'coco-theme' ); ?>
			</p>
		</div>

		<!-- AVISO DESTACADO DE PAGO EN EFECTIVO -->
		<div class="coco-payment-notice-box">
			<div class="coco-notice-icon-svg" aria-hidden="true">
				<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<rect x="2" y="6" width="20" height="12" rx="2"></rect>
					<circle cx="12" cy="12" r="3"></circle>
					<path d="M6 12h.01M18 12h.01"></path>
				</svg>
			</div>
			<div class="coco-notice-content">
				<span class="coco-notice-tag"><?php esc_html_e( 'MODALIDAD: PAGO CONTRA ENTREGA (EN EFECTIVO)', 'coco-theme' ); ?></span>
				<div class="coco-notice-total">
					<span><?php esc_html_e( 'Monto a pagar al recibir en tu puerta:', 'coco-theme' ); ?></span>
					<strong class="coco-total-highlight"><?php echo wp_kses_post( $total_formatted ); ?></strong>
				</div>
				<p class="coco-notice-tip">
					<?php esc_html_e( 'No pagás nada por adelantado. Por favor tené listo el importe en efectivo cuando el repartidor llegue a tu dirección.', 'coco-theme' ); ?>
				</p>
			</div>
		</div>

		<!-- GRID DE DETALLES -->
		<div class="coco-thankyou-grid">
			<div class="coco-section-card">
				<h3 class="coco-card-heading">
					<span class="coco-heading-icon-svg" aria-hidden="true">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
							<circle cx="12" cy="10" r="3"></circle>
						</svg>
					</span>
					<?php esc_html_e( 'Datos de Entrega', 'coco-theme' ); ?>
				</h3>
				<div class="coco-info-list">
					<div class="coco-info-item">
						<span class="coco-info-label"><?php esc_html_e( 'Destinatario:', 'coco-theme' ); ?></span>
						<strong class="coco-info-val"><?php echo esc_html( $full_name ); ?></strong>
					</div>
					<div class="coco-info-item">
						<span class="coco-info-label"><?php esc_html_e( 'Teléfono / WhatsApp:', 'coco-theme' ); ?></span>
						<strong class="coco-info-val"><?php echo esc_html( $phone ); ?></strong>
					</div>
					<div class="coco-info-item">
						<span class="coco-info-label"><?php esc_html_e( 'Ciudad / Ubicación:', 'coco-theme' ); ?></span>
						<span class="coco-info-val"><?php echo esc_html( $city . ', ' . $state ); ?></span>
					</div>
					<div class="coco-info-item">
						<span class="coco-info-label"><?php esc_html_e( 'Dirección:', 'coco-theme' ); ?></span>
						<span class="coco-info-val"><?php echo esc_html( $address_1 ); ?></span>
					</div>
				</div>
			</div>

			<div class="coco-section-card">
				<h3 class="coco-card-heading">
					<span class="coco-heading-icon-svg" aria-hidden="true">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
							<line x1="3" y1="6" x2="21" y2="6"></line>
							<path d="M16 10a4 4 0 0 1-8 0"></path>
						</svg>
					</span>
					<?php esc_html_e( 'Resumen de Compra', 'coco-theme' ); ?>
				</h3>
				<div class="coco-items-list">
					<?php foreach ( $order->get_items() as $item_id => $item ) : ?>
						<?php 
							$product = $item->get_product(); 
							$image   = $product ? $product->get_image( array( 60, 60 ) ) : '';
						?>
						<div class="coco-thankyou-item">
							<?php if ( $image ) : ?>
								<div class="coco-item-thumb">
									<?php echo wp_kses_post( $image ); ?>
									<span class="coco-item-qty-pill"><?php echo esc_html( $item->get_quantity() ); ?></span>
								</div>
							<?php endif; ?>
							<div class="coco-item-details">
								<h4 class="coco-item-title"><?php echo esc_html( $item->get_name() ); ?></h4>
								<span class="coco-item-meta"><?php echo esc_html( sprintf( __( 'Cantidad: %s', 'coco-theme' ), $item->get_quantity() ) ); ?></span>
							</div>
							<div class="coco-item-price">
								<?php echo wp_kses_post( wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) ) ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<div class="coco-totals-breakdown">
					<div class="coco-breakdown-row">
						<span><?php esc_html_e( 'Subtotal:', 'coco-theme' ); ?></span>
						<span><?php echo wp_kses_post( $subtotal_formatted ); ?></span>
					</div>
					<div class="coco-breakdown-row">
						<span><?php esc_html_e( 'Envío:', 'coco-theme' ); ?></span>
						<span class="<?php echo ( 0 === (int) $shipping_cost ) ? 'coco-shipping-free-tag' : ''; ?>"><?php echo wp_kses_post( $shipping_formatted ); ?></span>
					</div>
					<div class="coco-breakdown-row coco-grand-total">
						<span><?php esc_html_e( 'Total a pagar:', 'coco-theme' ); ?></span>
						<strong class="coco-total-price"><?php echo wp_kses_post( $total_formatted ); ?></strong>
					</div>
				</div>
			</div>
		</div>

		<!-- ACCIONES -->
		<div class="coco-thankyou-actions">
			<a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener" class="coco-btn-whatsapp">
				<svg class="coco-btn-wa-svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
					<path d="M17.5 12c0-.3-.1-.5-.3-.6l-2-1c-.2-.1-.5-.1-.7.1l-.8 1c-.1.1-.3.2-.5.1-1.1-.5-2-1.4-2.5-2.5-.1-.2 0-.4.1-.5l1-.8c.2-.2.2-.5.1-.7l-1-2c-.1-.2-.4-.3-.7-.3h-.5c-.6 0-1.1.5-1.1 1.1 0 1.9 1 3.8 2.6 5.4 1.6 1.6 3.5 2.6 5.4 2.6.6 0 1.1-.5 1.1-1.1v-.5c0-.1 0-.2-.1-.3-.1-.1-.2-.1-.3-.1zm-5.5-9.8C6.6 2.2 2.2 6.6 2.2 12c0 1.9.5 3.6 1.5 5.1L2 22l5-1.6c1.4.9 3.1 1.4 5 1.4 5.4 0 9.8-4.4 9.8-9.8S17.4 2.2 12 2.2zm0 17.6c-1.6 0-3.1-.5-4.4-1.3l-.3-.2-3.3 1.1 1.1-3.2-.2-.3c-.9-1.3-1.4-2.9-1.4-4.5 0-4.6 3.7-8.3 8.3-8.3 4.6 0 8.3 3.7 8.3 8.3s-3.7 8.4-8.1 8.4z"/>
				</svg>
				<?php esc_html_e( 'Confirmar pedido por WhatsApp', 'coco-theme' ); ?>
			</a>

			<div class="coco-secondary-actions">
				<button type="button" class="coco-btn-print" onclick="window.print();">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<polyline points="6 9 6 2 18 2 18 9"></polyline>
						<path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
						<rect x="6" y="14" width="12" height="8"></rect>
					</svg>
					<?php esc_html_e( 'Guardar o imprimir comprobante', 'coco-theme' ); ?>
				</button>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="coco-btn-return">&larr; <?php esc_html_e( 'Volver a la tienda', 'coco-theme' ); ?></a>
			</div>
		</div>

		<div class="coco-guarantee-footer">
			<div class="coco-guarantee-icon" aria-hidden="true">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
					<path d="M9 12l2 2 4-4"></path>
				</svg>
			</div>
			<div class="coco-guarantee-text">
				<strong><?php esc_html_e( 'Garantía Coco Cherry Store', 'coco-theme' ); ?></strong>
				<span><?php esc_html_e( 'Tu compra es 100% segura. Pagás únicamente al recibir tu paquete en mano.', 'coco-theme' ); ?></span>
			</div>
		</div>
	</div>
</div>

<?php
remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
do_action( 'woocommerce_thankyou', $order->get_id() );
?>

