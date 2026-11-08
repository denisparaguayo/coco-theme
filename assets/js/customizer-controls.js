/**
 * Personalizador: mostrar u ocultar controles según los interruptores
 * y navegar la vista previa a la página que corresponde a cada sección.
 */
( function ( api, data ) {
	'use strict';

	if ( ! api ) {
		return;
	}

	// Control dependiente => ajustes que deben estar activos para mostrarlo.
	var dependencies = {
		coco_theme_sales_pop_mobile: [ 'coco_theme_sales_pop_enabled' ],
		coco_theme_sales_pop_mobile_pos: [ 'coco_theme_sales_pop_enabled', 'coco_theme_sales_pop_mobile' ],
		coco_theme_sales_pop_interval: [ 'coco_theme_sales_pop_enabled' ],
		coco_theme_sp_bg_color: [ 'coco_theme_sales_pop_enabled' ],
		coco_theme_sp_border_color: [ 'coco_theme_sales_pop_enabled' ],
		coco_theme_sp_title_color: [ 'coco_theme_sales_pop_enabled' ],
		coco_theme_sp_text_color: [ 'coco_theme_sales_pop_enabled' ],
		coco_theme_sp_badge_color: [ 'coco_theme_sales_pop_enabled' ],
		coco_theme_mobile_sticky_mode: [ 'coco_theme_mobile_sticky_buy_enabled' ],
		coco_theme_mobile_sticky_anim: [ 'coco_theme_mobile_sticky_buy_enabled' ],
		coco_theme_mobile_sticky_text: [ 'coco_theme_mobile_sticky_buy_enabled' ],
		coco_theme_safe_checkout_title: [ 'coco_theme_safe_checkout_enabled' ],
		coco_theme_safe_checkout_badges: [ 'coco_theme_safe_checkout_enabled' ]
	};

	var masters = {};

	Object.keys( dependencies ).forEach( function ( controlId ) {
		dependencies[ controlId ].forEach( function ( masterId ) {
			masters[ masterId ] = true;
		} );
	} );

	function isVisible( controlId ) {
		return dependencies[ controlId ].every( function ( masterId ) {
			var setting = api( masterId );
			return setting ? !! setting.get() : true;
		} );
	}

	function refreshControls() {
		Object.keys( dependencies ).forEach( function ( controlId ) {
			var control = api.control( controlId );
			if ( control ) {
				control.active.set( isVisible( controlId ) );
			}
		} );
	}

	api.bind( 'ready', function () {
		Object.keys( masters ).forEach( function ( masterId ) {
			var setting = api( masterId );
			if ( setting ) {
				setting.bind( refreshControls );
			}
		} );

		refreshControls();

		// Al abrir una sección, llevar la vista previa a la página donde se ve el cambio.
		if ( data && data.sectionUrls ) {
			Object.keys( data.sectionUrls ).forEach( function ( sectionId ) {
				var section = api.section( sectionId );
				if ( ! section || ! data.sectionUrls[ sectionId ] ) {
					return;
				}
				var navigate = function ( expanded ) {
					if ( expanded ) {
						api.previewer.previewUrl.set( data.sectionUrls[ sectionId ] );
					}
				};

				section.expanded.bind( navigate );
				navigate( section.expanded.get() );
			} );
		}
	} );
} )( window.wp && window.wp.customize, window.cocoCustomizer || {} );
