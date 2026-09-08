/**
 * Product page behaviour: gallery, tabs and the quantity stepper.
 *
 * All three are enhancements. Without JavaScript the main image still shows,
 * every tab panel is reachable, and the quantity field is an ordinary number
 * input that submits correctly.
 */
( function () {
	'use strict';

	function $( sel, root ) {
		return ( root || document ).querySelector( sel );
	}

	function $$( sel, root ) {
		return Array.prototype.slice.call( ( root || document ).querySelectorAll( sel ) );
	}

	/**
	 * Gallery thumbnails swap the main image.
	 */
	function initGallery() {
		var main = $( '#galleryMain' );
		var thumbs = $( '#galleryThumbs' );
		if ( ! main || ! thumbs ) {
			return;
		}

		thumbs.addEventListener( 'click', function ( e ) {
			var button = e.target.closest ? e.target.closest( '[data-full]' ) : null;
			if ( ! button ) {
				return;
			}
			e.preventDefault();

			main.src = button.getAttribute( 'data-full' );

			$$( '[data-full]', thumbs ).forEach( function ( el ) {
				var on = el === button;
				el.setAttribute( 'aria-selected', on ? 'true' : 'false' );
				el.classList.toggle( 'is-active', on );
			} );
		} );
	}

	/**
	 * The information tabs.
	 */
	function initTabs() {
		$$( '[data-tabs]' ).forEach( function ( wrap ) {
			var tabs = $$( '[role="tab"]', wrap );
			if ( ! tabs.length ) {
				return;
			}

			function select( tab ) {
				tabs.forEach( function ( t ) {
					var on = t === tab;
					t.setAttribute( 'aria-selected', on ? 'true' : 'false' );
					var panel = document.getElementById( t.getAttribute( 'aria-controls' ) );
					if ( panel ) {
						panel.hidden = ! on;
					}
				} );
			}

			wrap.addEventListener( 'click', function ( e ) {
				var tab = e.target.closest ? e.target.closest( '[role="tab"]' ) : null;
				if ( tab ) {
					e.preventDefault();
					select( tab );
				}
			} );

			wrap.addEventListener( 'keydown', function ( e ) {
				var i = tabs.indexOf( document.activeElement );
				if ( i < 0 ) {
					return;
				}
				var next = null;
				if ( 'ArrowRight' === e.key ) {
					next = tabs[ ( i + 1 ) % tabs.length ];
				} else if ( 'ArrowLeft' === e.key ) {
					next = tabs[ ( i - 1 + tabs.length ) % tabs.length ];
				}
				if ( next ) {
					e.preventDefault();
					next.focus();
					select( next );
				}
			} );
		} );
	}

	/**
	 * The quantity stepper.
	 */
	function initQty() {
		document.addEventListener( 'click', function ( e ) {
			var button = e.target.closest ? e.target.closest( '[data-qty]' ) : null;
			if ( ! button ) {
				return;
			}
			e.preventDefault();

			var box = button.parentNode;
			var input = $( 'input', box );
			if ( ! input ) {
				return;
			}

			var min = parseInt( input.getAttribute( 'min' ), 10 );
			var value = parseInt( input.value, 10 );
			if ( isNaN( value ) ) {
				value = isNaN( min ) ? 1 : min;
			}

			value += ( 'up' === button.getAttribute( 'data-qty' ) ) ? 1 : -1;
			if ( ! isNaN( min ) && value < min ) {
				value = min;
			}

			input.value = value;
			input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		initGallery();
		initTabs();
		initQty();
	} );
}() );
