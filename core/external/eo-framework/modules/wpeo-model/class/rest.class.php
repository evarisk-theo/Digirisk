<?php
/**
 * Gestion des routes
 *
 * @author Jimmy Latour <dev@eoxia.com>
 * @since 1.4.0
 * @version 1.6.0
 * @copyright 2015-2017
 * @package wpeo_model
 * @subpackage class
 */

namespace eoxia001;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( '\eoxia001\Rest_Class' ) ) {
	/**
	 * Gestion des utilisateurs (POST, PUT, GET, DELETE)
	 */
	class Rest_Class extends Singleton_Util {

		/**
		 * [construct description]
		 */
		protected function construct() {
			add_action( 'rest_api_init', array( $this, 'register_routes' ), 20 );
		}

		/**
		 * Check user capability to access to element
		 *
		 * @param string $cap The capability name to check.
		 *
		 * @since 1.5.1
		 * @version 1.5.1
		 *
		 * @return string The rest api base for current element
		 */
		public function check_cap( $cap ) {
			return current_user_can( $this->capabilities[ $cap ] );
		}

		/**
		 * Return the base for rest api.
		 *
		 * @since 1.5.1
		 * @version 1.5.1
		 *
		 * @return string The rest api base for current element
		 */
		public function get_rest_base() {
			return $this->base;
		}

		/**
		 * Défini et ajoute les routes dans l'api rest de WordPress
		 *
		 * @since 1.4.0
		 * @version 1.6.0
		 */
		public function register_routes() {
			$element_namespace = new \ReflectionClass( get_called_class() );
			register_rest_route( $element_namespace->getNamespaceName() . '/v' . Config_Util::$init['eo-framework']->wpeo_model->api_version , '/' . $this->base . '/schema', array(
				array(
					'method' 		=> \WP_REST_Server::READABLE,
					'callback'	=> array( $this, 'get_schema' ),
				),
			) );

			register_rest_route( $element_namespace->getNamespaceName() . '/v' . Config_Util::$init['eo-framework']->wpeo_model->api_version , '/' . $this->base, array(
				array(
					'methods' 		=> \WP_REST_Server::READABLE,
					'callback'	=> array( $this, 'get_from_parent' ),
					'permission_callback' => function() {
						if ( ( ! in_array( $_SERVER['REMOTE_ADDR'], Config_Util::$init['eo-framework']->wpeo_model->allowed_ip_for_unauthentified_access_rest, true ) ) && ! $this->check_cap( 'get' ) ) {
							return false;
						}
						return true;
					},
				),
				array(
					'methods' 		=> \WP_REST_Server::CREATABLE,
					'callback'	=> array( $this, 'create_from_parent' ),
					'permission_callback' => function() {
						if ( ( ! in_array( $_SERVER['REMOTE_ADDR'], Config_Util::$init['eo-framework']->wpeo_model->allowed_ip_for_unauthentified_access_rest, true ) ) && ! $this->check_cap( 'post' ) ) {
							return false;
						}
						return true;
					},
				),
			), true );

			register_rest_route( $element_namespace->getNamespaceName() . '/v' . Config_Util::$init['eo-framework']->wpeo_model->api_version , '/' . $this->base . '/(?P<id>[\d]+)', array(
				array(
					'method' => \WP_REST_Server::READABLE,
					'callback'	=> array( $this, 'get_from_parent' ),
					'permission_callback' => function() {
						if ( ( ! in_array( $_SERVER['REMOTE_ADDR'], Config_Util::$init['eo-framework']->wpeo_model->allowed_ip_for_unauthentified_access_rest, true ) ) && ! $this->check_cap( 'get' ) ) {
							return false;
						}
						return true;
					},
				),
				array(
					'methods' 		=> \WP_REST_Server::CREATABLE,
					'callback'	=> array( $this, 'create_from_parent' ),
					'permission_callback' => function() {
						if ( ( ! in_array( $_SERVER['REMOTE_ADDR'], Config_Util::$init['eo-framework']->wpeo_model->allowed_ip_for_unauthentified_access_rest, true ) ) && ! $this->check_cap( 'put' ) ) {
							return false;
						}
						return true;
					},
				),
			), true );

		}

		/**
		 * Get element(s) from parent object type
		 *
		 * @since 1.5.1
		 * @version 1.5.1
		 *
		 * @param  WP_Http::request $request The current Rest API request.
		 *
		 * @return mixed                     Element list or single element if id was specified.
		 */
		public function get_from_parent( $request ) {
			$args = array();
			$single = false;

			if ( ! empty( $request ) && ( ! empty( $request['id'] ) ) ) {
				$args['id'] = $request['id'];
				$single = true;
			}

			return $this->get( $args, $single );
		}

		/**
		 * Create / Update element from request
		 *
		 * @since 1.6.0
		 * @version 1.6.0
		 *
		 * @param  WP_Http::request $request The current Rest API request.
		 *
		 * @return mixed                     New created element.
		 */
		public function create_from_parent( $request ) {
			return $this->update( $request->get_params() );
		}

		public function set_callback( $cb_slug, $data ) {

		}

		public function delete_callback( $cb_slug, $func_slug ) {
			if ( isset( $this->$cb_slug ) ) {
				foreach ( $this->$cb_slug as $key => $func ) {
					if ( $func === $func_slug ) {
						array_splice( $this->$cb_slug, $key, 1 );
					}
				}
			}
		}
	}

}
