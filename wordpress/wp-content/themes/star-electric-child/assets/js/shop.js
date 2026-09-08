/**
 * Shop, category and brand archive behaviour.
 *
 * The filters are a real GET form, so every one of these is an enhancement
 * rather than a requirement: without JavaScript the panel still submits, the
 * sort still applies and each chip still links to the filtered URL. What this
 * adds is the immediacy the approved storefront has - a filter applies as soon
 * as it is ticked.
 */
( function () {
	'use strict';

	var form = document.getElementById( 'shopFilters' );
	if ( ! form ) {
		return;
	}

	function $$( sel, root ) {
		return Array.prototype.slice.call( ( root || document ).querySelectorAll( sel ) );
	}

	function submit() {
		// A new filter selection always starts from page one.
		$$( 'input[name="paged"], input[name="product-page"]', form ).forEach( function ( el ) {
			el.parentNode.removeChild( el );
		} );
		form.submit();
	}

	/**
	 * The panel is rendered twice - sidebar and mobile drawer. Keep them in
	 * step so the drawer never contradicts the sidebar behind it.
	 */
	function sync( source ) {
		var group = source.getAttribute( 'data-filter' );
		if ( ! group ) {
			return;
		}
		$$( '[data-filter="' + group + '"]', form ).forEach( function ( el ) {
			if ( el === source ) {
				return;
			}
			if ( 'checkbox' === el.type && el.value === source.value ) {
				el.checked = source.checked;
			} else if ( 'number' === el.type ) {
				el.value = source.value;
			}
		} );
	}

	form.addEventListener( 'change', function ( e ) {
		var el = e.target;

		if ( 'sortSelect' === el.id ) {
			submit();
			return;
		}

		if ( el.hasAttribute && el.hasAttribute( 'data-filter' ) ) {
			sync( el );

			// Inside the mobile drawer the selections are applied by its own
			// "Show results" button - reloading under the shopper's finger on
			// every tick would close the drawer they are still using.
			if ( el.closest && el.closest( '#filterDrawer' ) ) {
				return;
			}

			// A price is typed, so wait for the field to be left rather than
			// reloading on every keystroke.
			if ( 'number' !== el.type ) {
				submit();
			}
		}
	} );

	form.addEventListener( 'keydown', function ( e ) {
		if ( 'Enter' === e.key && e.target.hasAttribute && e.target.hasAttribute( 'data-filter' ) ) {
			e.preventDefault();
			submit();
		}
	} );

	/**
	 * Collapsible filter groups.
	 */
	form.addEventListener( 'click', function ( e ) {
		var toggle = e.target.closest ? e.target.closest( '[data-toggle-panel]' ) : null;
		if ( toggle ) {
			e.preventDefault();
			var body = document.getElementById( toggle.getAttribute( 'aria-controls' ) );
			var open = 'true' === toggle.getAttribute( 'aria-expanded' );
			toggle.setAttribute( 'aria-expanded', open ? 'false' : 'true' );
			if ( body ) {
				body.hidden = open;
			}
			return;
		}

		var chip = e.target.closest ? e.target.closest( '[data-chip-href]' ) : null;
		if ( chip ) {
			e.preventDefault();
			window.location.href = chip.getAttribute( 'data-chip-href' );
			return;
		}

		var view = e.target.closest ? e.target.closest( '[data-view]' ) : null;
		if ( view ) {
			e.preventDefault();
			var wanted = view.getAttribute( 'data-view' );
			var field = form.querySelector( 'input[name="view"]' );
			if ( ! field ) {
				field = document.createElement( 'input' );
				field.type = 'hidden';
				field.name = 'view';
				form.appendChild( field );
			}
			field.value = wanted;
			submit();
		}
	} );

	/**
	 * The mobile filter drawer.
	 */
	( function () {
		var drawer = document.getElementById( 'filterDrawer' );
		var open = document.getElementById( 'filterOpen' );
		if ( ! drawer || ! open ) {
			return;
		}

		function show() {
			drawer.hidden = false;
			document.body.classList.add( 'no-scroll' );
			window.requestAnimationFrame( function () {
				drawer.classList.add( 'is-open' );
			} );
		}

		function hide() {
			drawer.classList.remove( 'is-open' );
			document.body.classList.remove( 'no-scroll' );
			window.setTimeout( function () {
				drawer.hidden = true;
			}, 250 );
		}

		open.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			show();
		} );

		drawer.addEventListener( 'click', function ( e ) {
			if ( e.target.closest && e.target.closest( '[data-side-close]' ) ) {
				e.preventDefault();
				hide();
			}
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && ! drawer.hidden ) {
				hide();
			}
		} );
	}() );
}() );
