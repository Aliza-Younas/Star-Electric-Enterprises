/**
 * The brand directory filter.
 *
 * Search box and A–Z bar, filtering a list the server has already rendered in
 * full. Nothing is fetched and nothing is hidden from a visitor without
 * JavaScript - they simply get the whole directory, which is the honest
 * fallback for a page whose job is to list every brand.
 */
( function () {
	'use strict';

	var grid = document.getElementById( 'brandDirectory' );
	var bar = document.getElementById( 'azBar' );
	var box = document.getElementById( 'brandSearch' );
	var empty = document.getElementById( 'brandEmpty' );
	var count = document.getElementById( 'brandCount' );

	if ( ! grid ) {
		return;
	}

	var families = document.getElementById( 'familyDirectory' );
	var section = document.getElementById( 'familySection' );
	var items = Array.prototype.slice.call( grid.children );
	var famItems = families ? Array.prototype.slice.call( families.children ) : [];
	var letter = '';

	/* The approved page counts the two kinds of listing separately, because a
	   brand listed at family level is not something a shopper can browse. */
	function label( shown, family ) {
		return String( shown ) + ' brands with individual products' +
			( family ? ' · ' + String( family ) + ' listed at family level' : '' );
	}

	function matches( el, term ) {
		var name = el.getAttribute( 'data-brand' ) || '';
		var first = el.getAttribute( 'data-letter' ) || '';
		return ( '' === letter || first === letter ) &&
			( '' === term || name.indexOf( term ) !== -1 );
	}

	function apply() {
		var term = box ? box.value.trim().toLowerCase() : '';
		var shown = 0;
		var family = 0;

		items.forEach( function ( li ) {
			var ok = matches( li, term );
			li.hidden = ! ok;
			if ( ok ) {
				shown++;
			}
		} );

		famItems.forEach( function ( el ) {
			var ok = matches( el, term );
			el.hidden = ! ok;
			if ( ok ) {
				family++;
			}
		} );

		if ( empty ) {
			empty.hidden = shown > 0 || family > 0;
		}
		grid.hidden = 0 === shown;
		if ( section ) {
			section.hidden = 0 === family;
		}

		if ( count ) {
			count.textContent = label( shown, family );
		}
	}

	if ( box ) {
		box.addEventListener( 'input', apply );
	}

	apply();

	if ( bar ) {
		bar.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest ? e.target.closest( '[data-letter]' ) : null;
			if ( ! btn ) {
				return;
			}
			if ( btn.disabled ) {
				return;
			}
			letter = btn.getAttribute( 'data-letter' );
			Array.prototype.forEach.call( bar.querySelectorAll( '[data-letter]' ), function ( o ) {
				o.setAttribute( 'aria-pressed', o === btn ? 'true' : 'false' );
			} );
			apply();
		} );
	}
}() );
