/**
 * The wishlist.
 *
 * Saved products live in this browser's localStorage and nowhere else, which is
 * what the approved wishlist page tells the visitor. Nothing is sent to the
 * server except the ids, and only to ask it to render the rows - the server
 * decides what a product costs, whether it is priced on enquiry and how its
 * availability reads, so that a saved item says the same thing here as it does
 * on the shop.
 *
 * Loaded on every page, because the heart on a product card has to work
 * wherever a product card appears.
 */
( function () {
	'use strict';

	var KEY = 'star_electric_wishlist';

	function closest( el, sel ) {
		return el && el.closest ? el.closest( sel ) : null;
	}

	/**
	 * Read the saved ids.
	 *
	 * Storage can throw outright in a private window or with site data blocked,
	 * so every read and write is guarded; a browser that refuses to remember
	 * simply gets an empty wishlist rather than a broken page.
	 */
	function read() {
		try {
			var raw = window.localStorage.getItem( KEY );
			var list = raw ? JSON.parse( raw ) : [];
			return Object.prototype.toString.call( list ) === '[object Array]' ? list : [];
		} catch ( e ) {
			return [];
		}
	}

	function write( list ) {
		try {
			window.localStorage.setItem( KEY, JSON.stringify( list ) );
		} catch ( e ) {
			/* Nothing to do: the page still works, it just will not remember. */
		}
	}

	function toggle( id ) {
		var list = read();
		var i = list.indexOf( id );
		if ( i === -1 ) {
			list.push( id );
		} else {
			list.splice( i, 1 );
		}
		write( list );
		return i === -1;
	}

	function remove( id ) {
		var list = read();
		var i = list.indexOf( id );
		if ( i !== -1 ) {
			list.splice( i, 1 );
			write( list );
		}
	}

	/** Keep the header badge honest. */
	function syncCount() {
		var n = read().length;
		Array.prototype.forEach.call(
			document.querySelectorAll( '[data-count="wishlist"]' ),
			function ( el ) {
				el.textContent = String( n );
			}
		);
	}

	/** Show which cards are already saved. */
	function syncHearts() {
		var list = read();
		Array.prototype.forEach.call(
			document.querySelectorAll( '.js-wish' ),
			function ( btn ) {
				var on = list.indexOf( btn.getAttribute( 'data-id' ) ) !== -1;
				btn.classList.toggle( 'is-active', on );
				btn.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
				btn.setAttribute( 'aria-label', on ? 'Remove from wishlist' : 'Add to wishlist' );
			}
		);
	}

	/**
	 * Render the wishlist page from the saved ids.
	 */
	function renderPage() {
		var body = document.getElementById( 'wishlistBody' );
		var panel = document.getElementById( 'wishlistPanel' );
		var empty = document.getElementById( 'wishlistEmpty' );
		if ( ! body || 'undefined' === typeof window.starWishlist ) {
			return;
		}

		var ids = read();

		function showEmpty() {
			body.innerHTML = '';
			if ( panel ) {
				panel.hidden = true;
			}
			if ( empty ) {
				empty.hidden = false;
			}
		}

		if ( ! ids.length ) {
			showEmpty();
			return;
		}

		var data = new window.FormData();
		data.append( 'action', window.starWishlist.action );
		data.append( 'ids', ids.join( ',' ) );

		window.fetch( window.starWishlist.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: data
		} ).then( function ( r ) {
			return r.json();
		} ).then( function ( res ) {
			if ( ! res || ! res.success || ! res.data.count ) {
				showEmpty();
				return;
			}
			body.innerHTML = res.data.rows;
			if ( panel ) {
				panel.hidden = false;
			}
			if ( empty ) {
				empty.hidden = true;
			}
		} ).catch( function () {
			/* A failed lookup must not present an empty wishlist as an empty
			   one - that would look like the saved items had been lost. */
			if ( panel ) {
				panel.hidden = true;
			}
			var note = document.getElementById( 'wishlistError' );
			if ( note ) {
				note.hidden = false;
			}
		} );
	}

	document.addEventListener( 'click', function ( e ) {
		var heart = closest( e.target, '.js-wish' );
		if ( heart ) {
			e.preventDefault();
			toggle( heart.getAttribute( 'data-id' ) );
			syncHearts();
			syncCount();
			return;
		}

		var kill = closest( e.target, '[data-wish-remove]' );
		if ( kill ) {
			e.preventDefault();
			remove( kill.getAttribute( 'data-wish-remove' ) );
			syncCount();
			syncHearts();
			renderPage();
		}
	} );

	document.addEventListener( 'DOMContentLoaded', function () {
		syncCount();
		syncHearts();
		renderPage();
	} );
}() );
