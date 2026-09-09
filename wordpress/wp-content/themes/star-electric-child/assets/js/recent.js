/**
 * Recently viewed products.
 *
 * The approved product page ends with the last few products this visitor
 * looked at. The list is the visitor's own and stays in their browser's
 * sessionStorage - it is gone when the tab closes, nothing about it reaches the
 * server beyond the ids needed to draw the cards, and no account is required.
 *
 * The cards themselves are drawn by WordPress rather than here, so a product
 * with no published price shows "Request a Quote" in this rail for the same
 * reason and in the same words as everywhere else.
 */
( function () {
	'use strict';

	var KEY = 'see_seen';
	var KEEP = 8;
	var SHOW = 4;

	var grid = document.getElementById( 'recentGrid' );
	var section = document.getElementById( 'recentSection' );
	if ( ! grid || ! section || 'undefined' === typeof starRecent ) {
		return;
	}

	var current = grid.getAttribute( 'data-current' ) || '';

	function read() {
		try {
			var raw = window.sessionStorage.getItem( KEY );
			var list = raw ? JSON.parse( raw ) : [];
			return Array.isArray( list ) ? list.filter( Boolean ).map( String ) : [];
		} catch ( e ) {
			return [];
		}
	}

	function write( list ) {
		try {
			window.sessionStorage.setItem( KEY, JSON.stringify( list.slice( 0, KEEP ) ) );
		} catch ( e ) {
			/* A browser with storage switched off simply has no history. */
		}
	}

	var seen = read();
	var history = seen.filter( function ( id ) {
		return id !== current;
	} );

	/* Record this visit before drawing, so a reload does not lose it. */
	write( [ current ].concat( history ) );

	if ( ! history.length ) {
		return;
	}

	var body = 'action=' + encodeURIComponent( starRecent.action ) +
		'&ids=' + encodeURIComponent( history.join( ',' ) ) +
		'&exclude=' + encodeURIComponent( current ) +
		'&limit=' + SHOW;

	window.fetch( starRecent.ajaxUrl, {
		method: 'POST',
		credentials: 'same-origin',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: body
	} ).then( function ( r ) {
		return r.json();
	} ).then( function ( res ) {
		if ( ! res || ! res.success || ! res.data || ! res.data.count ) {
			return;
		}
		grid.innerHTML = res.data.cards;
		section.hidden = false;
	} ).catch( function () {
		/* The rail is a convenience; a failed request leaves the page as it was. */
	} );
}() );
