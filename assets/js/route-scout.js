/**
 * Route Scout main application.
 *
 * @package RouteScout
 */

( function () {
	const state = {
		routes: [],
		currentRoute: null,
		method: 'GET',
		path: '',
		params: {},
		body: '',
		response: null,
		savedRequests: [],
	};

	/**
	 * Initialize the application.
	 */
	async function init() {
		loadSavedRequests();
		await loadRoutes();
		attachEventListeners();
	}

	/**
	 * Load all registered routes.
	 */
	async function loadRoutes() {
		try {
			const response = await wp.apiFetch( {
				path: '/route-scout/v1/routes',
			} );

			state.routes = response || [];
			renderRouteList( state.routes );
		} catch ( error ) {
			console.error( 'Failed to load routes:', error );
			showError( 'Failed to load routes' );
		}
	}

	/**
	 * Render the route list.
	 *
	 * @param {Array} routes Routes to render.
	 */
	function renderRouteList( routes ) {
		const container = document.getElementById( 'route-list' );
		container.innerHTML = '';

		if ( ! routes.length ) {
			container.innerHTML = '<p>No routes found</p>';
			return;
		}

		const grouped = groupBy( routes, 'namespace' );

		Object.keys( grouped ).sort().forEach( ( namespace ) => {
			const group = document.createElement( 'div' );
			group.className = 'route-scout-group';

			const title = document.createElement( 'h3' );
			title.className = 'route-scout-group-title';
			title.textContent = namespace;
			group.appendChild( title );

			grouped[ namespace ].forEach( ( route ) => {
				const item = document.createElement( 'div' );
				item.className = 'route-scout-item';

				const methodBadges = route.methods.map( ( m ) => {
					const color = getMethodColor( m );
					return `<span class="route-scout-method-badge" style="background-color: ${color}">${m}</span>`;
				} ).join( '' );

				item.innerHTML = `${methodBadges} <span class="route-scout-path-text">${escapeHtml( route.route )}</span>`;
				item.onclick = () => selectRoute( route );

				group.appendChild( item );
			} );

			container.appendChild( group );
		} );
	}

	/**
	 * Select a route and populate the request panel.
	 *
	 * @param {Object} route The selected route.
	 */
	function selectRoute( route ) {
		state.currentRoute = route;
		document.getElementById( 'request-method' ).value = route.methods[ 0 ] || 'GET';
		document.getElementById( 'request-path' ).value = route.route;
		state.path = route.route;
		state.method = route.methods[ 0 ] || 'GET';

		renderParamInputs( route.args || [] );
	}

	/**
	 * Render param input fields.
	 *
	 * @param {Array} args Route args.
	 */
	function renderParamInputs( args ) {
		const container = document.getElementById( 'params-list' );
		container.innerHTML = '';

		if ( ! args.length ) {
			container.innerHTML = '<p>No parameters</p>';
			return;
		}

		args.forEach( ( arg ) => {
			const wrapper = document.createElement( 'div' );
			wrapper.className = 'route-scout-param-input';

			const label = document.createElement( 'label' );
			label.textContent = arg;

			const input = document.createElement( 'input' );
			input.type = 'text';
			input.placeholder = `Value for ${arg}`;
			input.onchange = ( e ) => {
				state.params[ arg ] = e.target.value;
			};

			wrapper.appendChild( label );
			wrapper.appendChild( input );
			container.appendChild( wrapper );
		} );
	}

	/**
	 * Send the REST request.
	 */
	async function sendRequest() {
		const method = document.getElementById( 'request-method' ).value;
		const path = document.getElementById( 'request-path' ).value;
		const body = document.getElementById( 'request-body' ).value;

		state.method = method;
		state.path = path;
		state.body = body;

		if ( ! path ) {
			showError( 'Please enter a path' );
			return;
		}

		try {
			const response = await wp.apiFetch( {
				path: '/route-scout/v1/proxy',
				method: 'POST',
				data: {
					method: method,
					path: path,
					params: state.params,
					body: body,
				},
			} );

			state.response = response;
			renderResponse( response );
		} catch ( error ) {
			renderError( error.message );
		}
	}

	/**
	 * Render the response.
	 *
	 * @param {Object} response The response object.
	 */
	function renderResponse( response ) {
		const statusEl = document.getElementById( 'response-info' );
		statusEl.innerHTML = `<strong>Status:</strong> ${response.status} | <strong>Time:</strong> ${response.time_ms}ms`;

		const formatted = JSON.stringify( response.body, null, 2 );
		const raw = JSON.stringify( response.body );

		document.getElementById( 'response-formatted' ).textContent = formatted;
		document.getElementById( 'response-raw' ).textContent = raw;
	}

	/**
	 * Save the current request.
	 */
	function saveRequest() {
		const name = prompt( 'Save this request as:' );
		if ( ! name ) return;

		const request = {
			name: name,
			method: state.method,
			path: state.path,
			params: { ...state.params },
			body: state.body,
		};

		state.savedRequests.push( request );
		saveSavedRequests();
		renderSavedRequests();
	}

	/**
	 * Render saved requests.
	 */
	function renderSavedRequests() {
		const container = document.getElementById( 'saved-requests' );
		container.innerHTML = '';

		if ( ! state.savedRequests.length ) {
			container.innerHTML = '<p>No saved requests</p>';
			return;
		}

		state.savedRequests.forEach( ( req, index ) => {
			const item = document.createElement( 'div' );
			item.className = 'route-scout-saved-item';

			const name = document.createElement( 'strong' );
			name.textContent = req.name;

			const loadBtn = document.createElement( 'button' );
			loadBtn.className = 'button button-small';
			loadBtn.textContent = 'Load';
			loadBtn.onclick = () => loadSavedRequest( index );

			const deleteBtn = document.createElement( 'button' );
			deleteBtn.className = 'button button-small';
			deleteBtn.textContent = 'Delete';
			deleteBtn.onclick = () => deleteSavedRequest( index );

			item.appendChild( name );
			item.appendChild( loadBtn );
			item.appendChild( deleteBtn );
			container.appendChild( item );
		} );
	}

	/**
	 * Load a saved request.
	 *
	 * @param {number} index Request index.
	 */
	function loadSavedRequest( index ) {
		const req = state.savedRequests[ index ];
		if ( ! req ) return;

		document.getElementById( 'request-method' ).value = req.method;
		document.getElementById( 'request-path' ).value = req.path;
		document.getElementById( 'request-body' ).value = req.body;

		state.method = req.method;
		state.path = req.path;
		state.body = req.body;
		state.params = { ...req.params };
	}

	/**
	 * Delete a saved request.
	 *
	 * @param {number} index Request index.
	 */
	function deleteSavedRequest( index ) {
		state.savedRequests.splice( index, 1 );
		saveSavedRequests();
		renderSavedRequests();
	}

	/**
	 * Save to localStorage.
	 */
	function saveSavedRequests() {
		localStorage.setItem( 'routeScoutRequests', JSON.stringify( state.savedRequests ) );
	}

	/**
	 * Load from localStorage.
	 */
	function loadSavedRequests() {
		const stored = localStorage.getItem( 'routeScoutRequests' );
		if ( stored ) {
			state.savedRequests = JSON.parse( stored );
			renderSavedRequests();
		}
	}

	/**
	 * Attach event listeners.
	 */
	function attachEventListeners() {
		// Search routes.
		document.getElementById( 'route-search' ).addEventListener( 'keyup', ( e ) => {
			const query = e.target.value.toLowerCase();
			const filtered = state.routes.filter( ( route ) => route.route.toLowerCase().includes( query ) );
			renderRouteList( filtered );
		} );

		// Method selector.
		document.getElementById( 'request-method' ).addEventListener( 'change', ( e ) => {
			state.method = e.target.value;
		} );

		// Path input.
		document.getElementById( 'request-path' ).addEventListener( 'change', ( e ) => {
			state.path = e.target.value;
		} );

		// Send request.
		document.getElementById( 'send-request-btn' ).addEventListener( 'click', sendRequest );

		// Save request.
		document.getElementById( 'save-request-btn' ).addEventListener( 'click', saveRequest );

		// Tab switching.
		document.querySelectorAll( '.route-scout-tab-btn' ).forEach( ( btn ) => {
			btn.addEventListener( 'click', ( e ) => {
				const tab = e.target.dataset.tab;
				document.querySelectorAll( '.route-scout-tab' ).forEach( ( t ) => {
					t.classList.remove( 'active' );
				} );
				document.querySelectorAll( '.route-scout-tab-btn' ).forEach( ( b ) => {
					b.classList.remove( 'active' );
				} );
				document.getElementById( tab + '-tab' ).classList.add( 'active' );
				e.target.classList.add( 'active' );
			} );
		} );

		// Response tab switching.
		document.querySelectorAll( '.route-scout-response-tab-btn' ).forEach( ( btn ) => {
			btn.addEventListener( 'click', ( e ) => {
				const tab = e.target.dataset.tab;
				document.querySelectorAll( '.route-scout-response-tab' ).forEach( ( t ) => {
					t.classList.remove( 'active' );
				} );
				document.querySelectorAll( '.route-scout-response-tab-btn' ).forEach( ( b ) => {
					b.classList.remove( 'active' );
				} );
				document.getElementById( tab + '-tab' ).classList.add( 'active' );
				e.target.classList.add( 'active' );
			} );
		} );
	}

	/**
	 * Utility: Group array by property.
	 *
	 * @param {Array} arr Array to group.
	 * @param {string} key Property key.
	 * @return {Object} Grouped object.
	 */
	function groupBy( arr, key ) {
		return arr.reduce( ( acc, item ) => {
			const group = item[ key ];
			if ( ! acc[ group ] ) {
				acc[ group ] = [];
			}
			acc[ group ].push( item );
			return acc;
		}, {} );
	}

	/**
	 * Get method color.
	 *
	 * @param {string} method HTTP method.
	 * @return {string} Color.
	 */
	function getMethodColor( method ) {
		const colors = {
			GET: '#61affe',
			POST: '#49cc90',
			PUT: '#fca130',
			DELETE: '#f93e3e',
			PATCH: '#50e3c2',
		};
		return colors[ method ] || '#999';
	}

	/**
	 * Escape HTML.
	 *
	 * @param {string} text Text to escape.
	 * @return {string} Escaped text.
	 */
	function escapeHtml( text ) {
		const div = document.createElement( 'div' );
		div.textContent = text;
		return div.innerHTML;
	}

	/**
	 * Render an error into the response panel.
	 *
	 * @param {string} message Error message.
	 */
	function renderError( message ) {
		const statusEl = document.getElementById( 'response-info' );
		statusEl.innerHTML = `<span class="route-scout-error">Error</span>`;

		document.getElementById( 'response-formatted' ).textContent = message;
		document.getElementById( 'response-raw' ).textContent = message;
	}

	/**
	 * Show error message (used for non-request errors only).
	 *
	 * @param {string} message Error message.
	 */
	function showError( message ) {
		const container = document.getElementById( 'route-list' );
		container.innerHTML = `<p class="route-scout-error-text">${message}</p>`;
	}

	// Initialize on load.
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
