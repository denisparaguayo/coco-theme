/**
 * Vista previa en vivo del Personalizador: colores, ancho del logo y
 * visibilidad del título sin recargar el iframe.
 */
( function ( api ) {
	'use strict';

	if ( ! api ) {
		return;
	}

	function setVariable( name, value ) {
		if ( ! value ) {
			return;
		}
		document.documentElement.style.setProperty( name, value );
	}

	var colorBindings = {
		coco_theme_btn_cod_bg_color: [ '--coco-cod-green', '--coco-btn-cod-bg' ],
		coco_theme_btn_cod_hover_color: [ '--coco-cod-green-dark', '--coco-btn-cod-hover' ],
		coco_theme_btn_cod_text_color: [ '--coco-btn-cod-text' ],
		coco_theme_sp_bg_color: [ '--coco-sp-bg' ],
		coco_theme_sp_border_color: [ '--coco-sp-border' ],
		coco_theme_sp_title_color: [ '--coco-sp-title' ],
		coco_theme_sp_text_color: [ '--coco-sp-text' ],
		coco_theme_sp_badge_color: [ '--coco-sp-badge' ]
	};

	Object.keys( colorBindings ).forEach( function ( settingId ) {
		api( settingId, function ( setting ) {
			setting.bind( function ( value ) {
				colorBindings[ settingId ].forEach( function ( variable ) {
					setVariable( variable, value );
				} );
			} );
		} );
	} );

	api( 'coco_theme_logo_width', function ( setting ) {
		setting.bind( function ( value ) {
			var width = parseInt( value, 10 ) || 125;

			if ( width < 60 ) {
				width = 125;
			}
			if ( width > 300 ) {
				width = 300;
			}

			var height = Math.round( width * 0.49 );

			document.documentElement.style.setProperty( '--coco-logo-width', width + 'px' );
			document.documentElement.style.setProperty( '--coco-logo-height', height + 'px' );
		} );
	} );

	api( 'coco_theme_show_title_tagline', function ( setting ) {
		setting.bind( function ( value ) {
			document.body.classList.toggle( 'coco-hide-title', ! value );
		} );
	} );
} )( window.wp && window.wp.customize );
