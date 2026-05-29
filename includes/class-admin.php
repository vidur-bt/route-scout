<?php
/**
 * Admin page handler.
 *
 * @package RouteScout
 */

namespace RouteScout;

/**
 * Registers admin page and enqueues assets.
 */
class Admin {

	/**
	 * Register hooks.
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register admin menu.
	 */
	public function register_menu(): void {
		add_submenu_page(
			'tools.php',
			__( 'Route Scout', 'route-scout' ),
			__( 'Route Scout', 'route-scout' ),
			'manage_options',
			'route-scout',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Enqueue assets.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public function enqueue_assets( string $hook_suffix ): void {
		if ( 'tools_page_route-scout' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_script(
			'route-scout-app',
			ROUTE_SCOUT_PLUGIN_URL . 'assets/js/route-scout.js',
			array( 'wp-api-fetch', 'wp-i18n' ),
			ROUTE_SCOUT_VERSION,
			true
		);

		wp_enqueue_style(
			'route-scout-app',
			ROUTE_SCOUT_PLUGIN_URL . 'assets/css/route-scout.css',
			array(),
			ROUTE_SCOUT_VERSION
		);

		wp_localize_script(
			'route-scout-app',
			'routeScout',
			array(
				'restUrl'  => rest_url( 'route-scout/v1/' ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'siteUrl'  => site_url(),
				'wpApiUrl' => rest_url( 'wp/v2/' ),
			)
		);
	}

	/**
	 * Render admin page.
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'route-scout' ) );
		}
		?>
		<div class="wrap route-scout-page">
			<h1><?php esc_html_e( 'Route Scout', 'route-scout' ); ?></h1>
			<p><?php esc_html_e( 'Browse and test WordPress REST API endpoints.', 'route-scout' ); ?></p>

			<div class="route-scout-container">
				<div class="route-scout-panel route-scout-left">
					<h2><?php esc_html_e( 'Endpoints', 'route-scout' ); ?></h2>
					<input
						type="text"
						id="route-search"
						class="route-scout-search"
						placeholder="<?php esc_attr_e( 'Search routes...', 'route-scout' ); ?>"
					>
					<div id="route-list" class="route-scout-list"></div>
				</div>

				<div class="route-scout-panel route-scout-center">
					<h2><?php esc_html_e( 'Request', 'route-scout' ); ?></h2>
					<div class="route-scout-method-selector">
						<select id="request-method" class="route-scout-method">
							<option value="GET">GET</option>
							<option value="POST">POST</option>
							<option value="PUT">PUT</option>
							<option value="DELETE">DELETE</option>
							<option value="PATCH">PATCH</option>
						</select>
						<div class="route-scout-path-wrapper">
							<input
								type="text"
								id="request-path"
								class="route-scout-path"
								placeholder="<?php esc_attr_e( '/wp/v2/posts', 'route-scout' ); ?>"
								autocomplete="off"
							>
							<div id="path-dropdown" class="route-scout-path-dropdown"></div>
						</div>
					</div>

					<div class="route-scout-tabs">
						<button class="route-scout-tab-btn active" data-tab="params">
							<?php esc_html_e( 'Params', 'route-scout' ); ?>
						</button>
						<button class="route-scout-tab-btn" data-tab="body">
							<?php esc_html_e( 'Body', 'route-scout' ); ?>
						</button>
						<button class="route-scout-tab-btn" data-tab="collections">
							<?php esc_html_e( 'Saved', 'route-scout' ); ?>
						</button>
					</div>

					<div id="params-tab" class="route-scout-tab active">
						<div id="params-list" class="route-scout-params"></div>
					</div>

					<div id="body-tab" class="route-scout-tab">
						<textarea
							id="request-body"
							class="route-scout-body-input"
							placeholder="<?php esc_attr_e( 'JSON body (POST/PUT/PATCH)', 'route-scout' ); ?>"
						></textarea>
					</div>

					<div id="collections-tab" class="route-scout-tab">
						<button id="save-request-btn" class="button">
							<?php esc_html_e( 'Save Current Request', 'route-scout' ); ?>
						</button>
						<div id="saved-requests" class="route-scout-saved-requests"></div>
					</div>

					<button id="send-request-btn" class="button button-primary">
						<?php esc_html_e( 'Send Request', 'route-scout' ); ?>
					</button>
				</div>

				<div class="route-scout-panel route-scout-right">
					<h2><?php esc_html_e( 'Response', 'route-scout' ); ?></h2>
					<div id="response-info" class="route-scout-response-info"></div>
					<div class="route-scout-response-tabs">
						<button class="route-scout-response-tab-btn active" data-tab="formatted">
							<?php esc_html_e( 'Formatted', 'route-scout' ); ?>
						</button>
						<button class="route-scout-response-tab-btn" data-tab="raw">
							<?php esc_html_e( 'Raw', 'route-scout' ); ?>
						</button>
					</div>
					<div id="formatted-tab" class="route-scout-response-tab active">
						<pre id="response-formatted" class="route-scout-response"></pre>
					</div>
					<div id="raw-tab" class="route-scout-response-tab">
						<pre id="response-raw" class="route-scout-response"></pre>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
