/**
 * Drives the catalogue import from the dashboard.
 *
 * One batch per request, strictly sequential, resuming from the offset the
 * server reports. A failed request is retried a few times with a widening gap
 * before the run stops - shared hosting drops the occasional connection, and
 * abandoning a 4,348-record import over one blip would be its own bug.
 */
( function () {
	'use strict';

	var CFG = window.STAR_ELECTRIC_IMPORT || {};
	var MAX_RETRIES = 3;

	/**
	 * POST to admin-ajax and return the parsed payload.
	 */
	function post( action, body ) {
		var data = new FormData();
		data.append( 'action', action );
		data.append( 'nonce', CFG.nonce );
		Object.keys( body || {} ).forEach( function ( key ) {
			data.append( key, body[ key ] );
		} );

		return fetch( CFG.ajaxUrl, {
			method: 'POST',
			body: data,
			credentials: 'same-origin'
		} ).then( function ( response ) {
			return response.text().then( function ( text ) {
				var json;
				try {
					json = JSON.parse( text );
				} catch ( e ) {
					// A PHP fatal or an HTML error page: surface the start of it
					// rather than a useless "unexpected token" message.
					throw new Error(
						'HTTP ' + response.status + ' - ' + text.slice( 0, 200 )
					);
				}
				if ( ! json.success ) {
					throw new Error(
						( json.data && json.data.message ) || 'Request failed.'
					);
				}
				return json.data;
			} );
		} );
	}

	function sleep( ms ) {
		return new Promise( function ( resolve ) {
			setTimeout( resolve, ms );
		} );
	}

	/**
	 * One step panel.
	 */
	function Step( root ) {
		this.root = root;
		this.key = root.dataset.step;
		this.config = ( CFG.steps && CFG.steps[ this.key ] ) || {};
		this.bar = root.querySelector( '.star-step__bar span' );
		this.status = root.querySelector( '.star-step__status' );
		this.errors = root.querySelector( '.star-step__errors' );
		this.errorList = this.errors.querySelector( 'ul' );
		this.runBtn = root.querySelector( '.star-step__run' );
		this.stopBtn = root.querySelector( '.star-step__stop' );
		this.resetBtn = root.querySelector( '.star-step__reset' );

		this.running = false;
		this.stopped = false;

		var saved = ( CFG.progress && CFG.progress[ this.key ] ) || {};
		this.offset = parseInt( saved.offset, 10 ) || 0;
		this.total = parseInt( saved.total, 10 ) || 0;
		this.paint();

		this.runBtn.addEventListener( 'click', this.run.bind( this ) );
		this.stopBtn.addEventListener( 'click', this.stop.bind( this ) );
		this.resetBtn.addEventListener( 'click', this.reset.bind( this ) );
	}

	Step.prototype.paint = function () {
		var pct = this.total > 0
			? Math.min( 100, Math.round( ( this.offset / this.total ) * 100 ) )
			: 0;
		this.bar.style.width = pct + '%';
	};

	Step.prototype.say = function ( text ) {
		this.status.textContent = text;
	};

	Step.prototype.addErrors = function ( list ) {
		if ( ! list || ! list.length ) {
			return;
		}
		this.errors.hidden = false;
		list.forEach( function ( message ) {
			var li = document.createElement( 'li' );
			li.textContent = message;
			this.errorList.appendChild( li );
		}, this );
	};

	Step.prototype.busy = function ( isBusy ) {
		this.running = isBusy;
		this.runBtn.disabled = isBusy;
		this.stopBtn.disabled = ! isBusy;
		this.resetBtn.disabled = isBusy;
		this.root.classList.toggle( 'is-running', isBusy );
	};

	Step.prototype.stop = function () {
		this.stopped = true;
		this.say( 'Stopping after this batch...' );
	};

	Step.prototype.reset = function () {
		if ( this.running ) {
			return;
		}
		var self = this;
		post( 'star_electric_import_reset', { step: this.key } )
			.then( function () {
				self.offset = 0;
				self.total = 0;
				self.errorList.innerHTML = '';
				self.errors.hidden = true;
				self.paint();
				self.say( 'Not started' );
			} )
			.catch( function ( error ) {
				self.say( 'Could not reset: ' + error.message );
			} );
	};

	/**
	 * Run batches until the step reports itself complete.
	 */
	Step.prototype.run = function () {
		var self = this;
		this.stopped = false;
		this.busy( true );

		var size = parseInt( this.config.batch, 10 ) || 1;
		var retries = 0;

		function batch() {
			if ( self.stopped ) {
				self.busy( false );
				self.say(
					'Stopped at ' + self.offset +
					( self.total ? ' of ' + self.total : '' ) +
					'. Press Run to resume.'
				);
				return Promise.resolve();
			}

			return post( 'star_electric_import', {
				step: self.key,
				offset: self.offset,
				size: size
			} )
				.then( function ( data ) {
					retries = 0;

					var b = data.batch || {};
					var t = data.totals || {};

					self.offset = parseInt( b.offset, 10 ) || 0;
					self.total = parseInt( b.total, 10 ) || 0;
					self.paint();
					self.addErrors( b.errors );

					var created = parseInt( t.created, 10 ) || 0;
					var updated = ( parseInt( t.updated, 10 ) || 0 ) +
						( parseInt( t.reused, 10 ) || 0 );
					var failed = parseInt( t.failed, 10 ) || 0;

					self.say(
						self.offset + ' of ' + self.total + ' - ' +
						created + ' created, ' + updated + ' updated, ' +
						failed + ' failed'
					);

					if ( b.complete ) {
						self.busy( false );
						self.root.classList.add( 'is-done' );
						self.say(
							'Complete. ' + created + ' created, ' + updated +
							' updated, ' + failed + ' failed.'
						);
						return Promise.resolve();
					}

					return batch();
				} )
				.catch( function ( error ) {
					retries += 1;
					if ( retries > MAX_RETRIES ) {
						self.busy( false );
						self.addErrors( [ error.message ] );
						self.say(
							'Stopped at ' + self.offset +
							'. Press Run to resume from here.'
						);
						return Promise.resolve();
					}
					self.say(
						'Request failed (' + error.message + '). Retry ' +
						retries + ' of ' + MAX_RETRIES + '...'
					);
					return sleep( retries * 2000 ).then( batch );
				} );
		}

		batch();
	};

	/**
	 * The reconciliation table.
	 */
	function wireAudit() {
		var button = document.getElementById( 'star-import-audit' );
		var table = document.getElementById( 'star-import-audit-table' );
		if ( ! button || ! table ) {
			return;
		}

		button.addEventListener( 'click', function () {
			button.disabled = true;
			button.textContent = 'Running audit...';

			post( 'star_electric_import_audit', {} )
				.then( function ( data ) {
					var body = table.querySelector( 'tbody' );
					body.innerHTML = '';

					( data.rows || [] ).forEach( function ( row ) {
						var tr = document.createElement( 'tr' );
						row.forEach( function ( cell, index ) {
							var td = document.createElement( 'td' );
							td.textContent = cell;
							if ( index === 3 ) {
								td.className = cell === 'OK'
									? 'star-import__ok'
									: 'star-import__bad';
							}
							tr.appendChild( td );
						} );
						body.appendChild( tr );
					} );

					table.hidden = false;
				} )
				.catch( function ( error ) {
					window.alert( 'Audit failed: ' + error.message );
				} )
				.finally( function () {
					button.disabled = false;
					button.textContent = 'Run audit';
				} );
		} );
	}

	/**
	 * Recount the catalogue taxonomies.
	 */
	function wireRecount() {
		var button = document.getElementById( 'star-import-recount' );
		if ( ! button ) {
			return;
		}

		button.addEventListener( 'click', function () {
			var label = button.textContent;
			button.disabled = true;
			button.textContent = 'Recounting...';

			post( 'star_electric_import_recount', {} )
				.then( function ( data ) {
					button.textContent = 'Recounted ' + ( data.terms || 0 ) + ' terms';
					window.setTimeout( function () {
						button.textContent = label;
					}, 4000 );
				} )
				.catch( function ( error ) {
					window.alert( 'Recount failed: ' + error.message );
					button.textContent = label;
				} )
				.finally( function () {
					button.disabled = false;
				} );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		Array.prototype.forEach.call(
			document.querySelectorAll( '.star-step' ),
			function ( root ) {
				return new Step( root );
			}
		);
		wireAudit();
		wireRecount();
	} );
}() );
