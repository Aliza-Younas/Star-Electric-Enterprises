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

	document.addEventListener( 'DOMContentLoaded', function () {
		initDrawer();
		initScroll();
	} );
}() );
