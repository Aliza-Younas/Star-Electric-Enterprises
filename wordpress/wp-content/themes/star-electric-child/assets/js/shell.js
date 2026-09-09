/**
 * Global shell behaviour: the mobile drawer, the sticky header shadow and the
 * back-to-top control.
 *
 * Ported from the approved storefront's js/main.js. The mega menu is not here
 * because it never was - it opens on :hover and :focus-within in CSS, which
 * keeps it working without JavaScript.
 */
( function () {
	'use strict';

	function $( sel, root ) {
		return ( root || document ).querySelector( sel );
	}

	function closest( el, sel ) {
		return el && el.closest ? el.closest( sel ) : null;
	}

	/**
	 * The mobile drawer.
	 */
	function initDrawer() {
		var drawer = $( '#mobileNav' );
		var toggle = $( '#navToggle' );
		if ( ! drawer ) {
			return;
		}

		function open() {
			drawer.hidden = false;
			document.body.classList.add( 'no-scroll' );
			window.requestAnimationFrame( function () {
				drawer.classList.add( 'is-open' );
			} );
			if ( toggle ) {
				toggle.setAttribute( 'aria-expanded', 'true' );
			}
			var first = $( '.drawer__nav a', drawer );
			if ( first ) {
				first.focus();
			}
		}

		function close() {
			if ( drawer.hidden ) {
				return;
			}
			drawer.classList.remove( 'is-open' );
			document.body.classList.remove( 'no-scroll' );
			window.setTimeout( function () {
				drawer.hidden = true;
			}, 250 );
			if ( toggle ) {
				toggle.setAttribute( 'aria-expanded', 'false' );
				toggle.focus();
			}
		}

		document.addEventListener( 'click', function ( e ) {
			if ( closest( e.target, '#navToggle' ) ) {
				e.preventDefault();
				open();
				return;
			}
			if ( closest( e.target, '[data-drawer-close]' ) ) {
				e.preventDefault();
				close();
			}
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && ! drawer.hidden ) {
				close();
			}
		} );

		window.addEventListener( 'resize', function () {
			if ( window.innerWidth > 900 && ! drawer.hidden ) {
				close();
			}
		} );
	}

	/**
	 * Sticky header shadow and back to top.
	 */
	function initScroll() {
		var header = $( '#siteHeaderBar' );
		var top = $( '#toTop' );
		var ticking = false;

		function run() {
			var y = window.pageYOffset || document.documentElement.scrollTop;
			if ( header ) {
				header.classList.toggle( 'is-stuck', y > 8 );
			}
			if ( top ) {
				top.hidden = y < 700;
			}
			ticking = false;
		}

		window.addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				window.requestAnimationFrame( run );
				ticking = true;
			}
		}, { passive: true } );

		run();

		if ( top ) {
			top.addEventListener( 'click', function () {
				window.scrollTo( { top: 0, behavior: 'smooth' } );
			} );
		}
	}

	/**
	 * Collapsible panels.
	 *
	 * The FAQ accordion and any other [data-toggle-panel] control on the site.
	 * The shop's filter form is skipped because shop.js owns the toggles inside
	 * it - two handlers on the same button would open and close it in the same
	 * click, which reads as a control that does nothing.
	 */
	function initPanels() {
		document.addEventListener( 'click', function ( e ) {
			var btn = closest( e.target, '[data-toggle-panel]' );
			if ( ! btn || closest( btn, '#shopFilters' ) ) {
				return;
			}
			e.preventDefault();
			var panel = document.getElementById( btn.getAttribute( 'aria-controls' ) );
			if ( ! panel ) {
				return;
			}
			var open = 'true' === btn.getAttribute( 'aria-expanded' );
			btn.setAttribute( 'aria-expanded', open ? 'false' : 'true' );
			panel.hidden = open;
		} );
	}

	/**
	 * The FAQ topic filter.
	 *
	 * Hides whole groups rather than filtering questions, which is what the
	 * approved page does, and leaves every answer in the DOM so a browser find
	 * still reaches it.
	 */
	function initFaq() {
		var bar = $( '#faqCats' );
		if ( ! bar ) {
			return;
		}
		bar.addEventListener( 'click', function ( e ) {
			var btn = closest( e.target, '[data-faq-cat]' );
			if ( ! btn ) {
				return;
			}
			var key = btn.getAttribute( 'data-faq-cat' );
			Array.prototype.forEach.call( bar.querySelectorAll( '[data-faq-cat]' ), function ( o ) {
				o.setAttribute( 'aria-pressed', o === btn ? 'true' : 'false' );
			} );
			Array.prototype.forEach.call( document.querySelectorAll( '[data-faq-group]' ), function ( g ) {
				g.hidden = ! ( 'all' === key || g.getAttribute( 'data-faq-group' ) === key );
			} );
		} );
	}

	/**
	 * Variant swatches on a product with no published price.
	 *
	 * There is nothing to recalculate - the product is quoted on enquiry - so
	 * the swatches do exactly what the approved page's do: name which variant
	 * the shopper means, and carry it into the quotation request.
	 */
	function initSwatches() {
		var wrap = $( '#pdpVariations' );
		if ( ! wrap || ! wrap.querySelector( '.swatch' ) ) {
			return;
		}
		var chosen = wrap.querySelector( '#varChosen' );
		wrap.addEventListener( 'click', function ( e ) {
			var btn = closest( e.target, '.swatch' );
			if ( ! btn ) {
				return;
			}
			Array.prototype.forEach.call( wrap.querySelectorAll( '.swatch' ), function ( o ) {
				o.setAttribute( 'aria-pressed', o === btn ? 'true' : 'false' );
			} );
			var value = btn.getAttribute( 'data-val' ) || '';
			if ( chosen ) {
				chosen.textContent = value;
			}
			/* The quotation link carries the variant, so the enquiry names it. */
			Array.prototype.forEach.call( document.querySelectorAll( '.pdp__buy a[href*="quote-request"]' ), function ( a ) {
				var url = a.getAttribute( 'href' ).split( '#' )[ 0 ].replace( /([?&])variant=[^&]*/, '$1' ).replace( /[?&]$/, '' );
				a.setAttribute( 'href', url + ( url.indexOf( '?' ) === -1 ? '?' : '&' ) + 'variant=' + encodeURIComponent( value ) );
			} );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		initDrawer();
		initScroll();
		initPanels();
		initFaq();
		initSwatches();
	} );
}() );
