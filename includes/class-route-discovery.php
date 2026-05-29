<?php
/**
 * Route Discovery handler.
 *
 * @package RouteScout
 */

namespace RouteScout;

/**
 * Discovers registered REST API routes.
 */
class Route_Discovery {

	/**
	 * Register hooks.
	 */
	public function register(): void {
		add_action( 'rest_api_init', array( $this, 'register_route' ) );
	}

	/**
	 * Register the route discovery endpoint.
	 */
	public function register_route(): void {
		register_rest_route(
			'route-scout/v1',
			'/routes',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_routes' ),
				'permission_callback' => array( $this, 'check_permission' ),
				'args'                => array(),
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
	 * Get all registered routes.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_routes(): \WP_REST_Response {
		$server = rest_get_server();
		$routes = $server->get_routes();
		$data   = array();

		foreach ( $routes as $route => $route_data ) {
			$methods     = array();
			$args        = array();
			$description = '';

			if ( isset( $route_data['endpoints'] ) ) {
				foreach ( $route_data['endpoints'] as $endpoint ) {
					if ( isset( $endpoint['methods'] ) ) {
						$methods = array_merge( $methods, array_keys( $endpoint['methods'] ) );
					}
					if ( isset( $endpoint['args'] ) ) {
						$args = array_merge( $args, array_keys( $endpoint['args'] ) );
					}
				}
			}

			$methods = array_unique( array_map( 'strtoupper', $methods ) );

			$namespace = $this->extract_namespace( $route );

			$data[] = array(
				'route'       => $route,
				'methods'     => array_values( $methods ),
				'namespace'   => $namespace,
				'args'        => array_values( array_unique( $args ) ),
				'description' => $description,
			);
		}

		usort( $data, fn( $a, $b ) => strcmp( $a['route'], $b['route'] ) );

		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * Extract namespace from route.
	 *
	 * @param string $route The route path.
	 * @return string
	 */
	private function extract_namespace( string $route ): string {
		if ( preg_match( '#^/([^/]+/[^/]+)#', $route, $matches ) ) {
			return $matches[1];
		}
		return 'core';
	}
}
