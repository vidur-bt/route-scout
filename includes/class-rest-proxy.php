<?php
/**
 * REST API Proxy handler.
 *
 * @package RouteScout
 */

namespace RouteScout;

/**
 * Proxies REST API requests for testing.
 */
class Rest_Proxy {

	/**
	 * Register hooks.
	 */
	public function register(): void {
		add_action( 'rest_api_init', array( $this, 'register_route' ) );
	}

	/**
	 * Register the proxy endpoint.
	 */
	public function register_route(): void {
		register_rest_route(
			'route-scout/v1',
			'/proxy',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'proxy_request' ),
				'permission_callback' => array( $this, 'check_permission' ),
				'args'                => array(
					'method' => array(
						'description' => 'HTTP method',
						'type'        => 'string',
						'required'    => true,
						'enum'        => array( 'GET', 'POST', 'PUT', 'DELETE', 'PATCH' ),
					),
					'path'   => array(
						'description' => 'REST API path',
						'type'        => 'string',
						'required'    => true,
					),
					'params' => array(
						'description' => 'Request parameters',
						'type'        => 'object',
						'default'     => array(),
					),
					'body'   => array(
						'description' => 'Request body',
						'type'        => 'string',
					),
				),
			)
		);
	}

	/**
	 * Check permission.
	 */
	public function check_permission(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Proxy a REST request.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function proxy_request( \WP_REST_Request $request ) {
		$method = strtoupper( $request->get_param( 'method' ) );
		$path   = $request->get_param( 'path' );
		$params = $request->get_param( 'params' ) ?? array();
		$body   = $request->get_param( 'body' ) ?? '';

		// Validate path is a registered route.
		if ( ! $this->is_registered_route( $path ) ) {
			return new \WP_Error(
				'invalid_path',
				'Route not found',
				array( 'status' => 404 )
			);
		}

		$start_time = microtime( true );

		// Create internal request.
		$internal_request = new \WP_REST_Request( $method, $path );

		// Add params.
		if ( is_array( $params ) ) {
			foreach ( $params as $key => $value ) {
				if ( 'GET' === $method ) {
					$internal_request->set_query_params( array( $key => $value ) );
				} else {
					$internal_request->set_body_params( array( $key => $value ) );
				}
			}
		}

		// Set body for POST/PUT/PATCH.
		if ( in_array( $method, array( 'POST', 'PUT', 'PATCH' ), true ) && ! empty( $body ) ) {
			$internal_request->set_body( $body );
		}

		// Execute.
		$response = rest_do_request( $internal_request );
		$elapsed  = round( ( microtime( true ) - $start_time ) * 1000, 2 );

		if ( is_wp_error( $response ) ) {
			return new \WP_REST_Response(
				array(
					'error'    => $response->get_error_code(),
					'message'  => $response->get_error_message(),
					'time_ms'  => $elapsed,
					'status'   => 400,
				),
				400
			);
		}

		$status = $response->get_status();
		$data   = $response->get_data();

		return new \WP_REST_Response(
			array(
				'status'  => $status,
				'body'    => $data,
				'time_ms' => $elapsed,
			),
			200
		);
	}

	/**
	 * Check if a route is registered.
	 *
	 * @param string $path The route path.
	 * @return bool
	 */
	private function is_registered_route( string $path ): bool {
		$server = rest_get_server();
		$routes = $server->get_routes();
		return isset( $routes[ $path ] );
	}
}
