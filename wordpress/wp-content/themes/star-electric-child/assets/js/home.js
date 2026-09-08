/**
 * Homepage behaviour: the campaign carousel and the horizontal rails.
 *
 * Ported from the approved storefront's js/main.js. The carousel's entrance
 * animations are transform-only by design: an opacity-based entrance that
 * stalls leaves a campaign invisible, which is worse than one that does not
 * animate at all.
 */
( function () {
	'use strict';

	var HERO_INTERVAL = 7000;

	function $( sel, root ) {
		return ( root || document ).querySelector( sel );
	}

	function $$( sel, root ) {
		return Array.prototype.slice.call( ( root || document ).querySelectorAll( sel ) );
	}

	/**
	 * The campaign banner.
	 */
	function initHero() {
		var track = $( '#heroTrack' );
		if ( ! track ) {
			return;
		}

		var items = $$( '.hs', track );
		if ( ! items.length ) {
			return;
		}

		var nav = $( '#heroNav' );
		var buttons = nav ? $$( '.hnav', nav ) : [];
		var status = $( '#heroStatus' );
		var banner = $( '#heroBanner' );
		var still = window.matchMedia &&
			window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		var index = 0;
		var timer = null;
		var paused = false;

		// The progress line is a CSS animation, so its duration has to agree
		// with the timer that actually advances the slide.
		if ( banner ) {
			banner.style.setProperty( '--hb-dur', HERO_INTERVAL + 'ms' );
		}

		// Re-running an animation on an element that already carries it needs
		// the class dropped and a reflow forced before it is put back.
		function replay( el ) {
			if ( ! el ) {
				return;
			}
			el.classList.remove( 'is-live' );
			void el.offsetWidth;
			el.classList.add( 'is-live' );
		}

		function paint() {
			track.style.transform = 'translate3d(' + ( -index * 100 ) + '%,0,0)';

			items.forEach( function ( el, i ) {
				el.classList.toggle( 'is-active', i === index );
				if ( i !== index ) {
					el.classList.remove( 'is-live' );
				}
				// Off-screen slides leave the tab order so keyboard focus never
				// lands on a control the visitor cannot see.
				$$( 'a,button', el ).forEach( function ( c ) {
					if ( i === index ) {
						c.removeAttribute( 'tabindex' );
					} else {
						c.setAttribute( 'tabindex', '-1' );
					}
				} );
			} );

			buttons.forEach( function ( b, i ) {
				if ( i === index ) {
					b.setAttribute( 'aria-current', 'true' );
				} else {
					b.removeAttribute( 'aria-current' );
				}
				b.classList.toggle( 'is-active', i === index );
			} );

			if ( ! still ) {
				replay( items[ index ] );
				var fill = buttons[ index ] && buttons[ index ].querySelector( '.hnav__fill' );
				if ( fill ) {
					fill.style.animation = 'none';
					void fill.offsetWidth;
					fill.style.animation = '';
				}
			}

			if ( status ) {
				var label = buttons[ index ] ? buttons[ index ].getAttribute( 'aria-label' ) : '';
				status.textContent = 'Slide ' + ( index + 1 ) + ' of ' + items.length + ': ' + label;
			}
		}

		function go( n ) {
			index = ( n + items.length ) % items.length;
			paint();
		}

		// is-paused freezes the progress line in place; the timer is cleared
		// with it, so what the visitor sees and what the clock does stay in step.
		function mark() {
			if ( banner ) {
				banner.classList.toggle( 'is-paused', ! timer );
			}
		}

		function stop() {
			window.clearInterval( timer );
			timer = null;
			mark();
		}

		function play() {
			window.clearInterval( timer );
			timer = null;
			if ( ! still && ! paused && ! document.hidden ) {
				timer = window.setInterval( function () {
					go( index + 1 );
				}, HERO_INTERVAL );
			}
			mark();
		}

		buttons.forEach( function ( b, i ) {
			b.addEventListener( 'click', function () {
				go( i );
				play();
			} );
		} );

		var prev = $( '#heroPrev' );
		var next = $( '#heroNext' );
		if ( prev ) {
			prev.addEventListener( 'click', function () {
				go( index - 1 );
				play();
			} );
		}
		if ( next ) {
			next.addEventListener( 'click', function () {
				go( index + 1 );
				play();
			} );
		}

		if ( banner ) {
			banner.addEventListener( 'mouseenter', function () {
				paused = true;
				stop();
			} );
			banner.addEventListener( 'mouseleave', function () {
				paused = false;
				play();
			} );
			banner.addEventListener( 'focusin', function () {
				paused = true;
				stop();
			} );
			banner.addEventListener( 'focusout', function () {
				paused = false;
				play();
			} );
			banner.addEventListener( 'keydown', function ( e ) {
				if ( 'ArrowRight' === e.key ) {
					e.preventDefault();
					go( index + 1 );
					play();
				} else if ( 'ArrowLeft' === e.key ) {
					e.preventDefault();
					go( index - 1 );
					play();
				}
			} );
		}

		// Nothing should keep ticking in a tab nobody is looking at.
		document.addEventListener( 'visibilitychange', function () {
			if ( document.hidden ) {
				stop();
			} else {
				play();
			}
		} );

		// Swipe. Horizontal intent only, so a vertical scroll is never hijacked.
		var x0 = null;
		var y0 = null;
		var dx = 0;

		track.addEventListener( 'pointerdown', function ( e ) {
			if ( 'mouse' === e.pointerType && 0 !== e.button ) {
				return;
			}
			x0 = e.clientX;
			y0 = e.clientY;
			dx = 0;
		} );

		track.addEventListener( 'pointermove', function ( e ) {
			if ( null === x0 ) {
				return;
			}
			dx = e.clientX - x0;
			if ( Math.abs( dx ) > Math.abs( e.clientY - y0 ) && Math.abs( dx ) > 8 ) {
				track.style.transform =
					'translate3d(calc(' + ( -index * 100 ) + '% + ' + dx + 'px),0,0)';
			}
		} );

		[ 'pointerup', 'pointercancel', 'pointerleave' ].forEach( function ( ev ) {
			track.addEventListener( ev, function () {
				if ( null === x0 ) {
					return;
				}
				if ( Math.abs( dx ) > 60 ) {
					go( index + ( dx < 0 ? 1 : -1 ) );
				} else {
					paint();
				}
				x0 = null;
				dx = 0;
				play();
			} );
		} );

		paint();
		play();
	}

	/**
	 * Horizontal rails: the department strip and each product rail.
	 */
	function initRails() {
		$$( '.rail__head' ).forEach( function ( head ) {
			var scope = head.parentNode;
			var track = $( '.rail__track', scope ) || $( '.dept-track', scope );
			if ( ! track ) {
				return;
			}

			var prev = $( '[data-rail-prev]', head );
			var next = $( '[data-rail-next]', head );

			function step() {
				return Math.max( 200, Math.round( track.clientWidth * 0.8 ) );
			}

			function sync() {
				var max = track.scrollWidth - track.clientWidth - 1;
				if ( prev ) {
					prev.disabled = track.scrollLeft <= 0;
				}
				if ( next ) {
					next.disabled = track.scrollLeft >= max;
				}
			}

			if ( prev ) {
				prev.addEventListener( 'click', function () {
					track.scrollBy( { left: -step(), behavior: 'smooth' } );
				} );
			}
			if ( next ) {
				next.addEventListener( 'click', function () {
					track.scrollBy( { left: step(), behavior: 'smooth' } );
				} );
			}

			track.addEventListener( 'scroll', sync, { passive: true } );
			window.addEventListener( 'resize', sync );
			sync();
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		initHero();
		initRails();
	} );
}() );
