<?php
/**
 * Gestion de l'objet Config_Util::$init.
 *
 * @author Jimmy Latour <dev@eoxia.com>
 * @since 0.1.0
 * @version 1.0.1
 * @copyright 2015-2017 Eoxia
 * @package WPEO_Util
 */

namespace eoxia001;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\eoxia001\Config_Util' ) ) {

	/**
	 * Gestion de l'objet Config_Util::$init.
	 */
	class Config_Util extends \eoxia001\Singleton_Util {

		/**
		 * Un tableau contenant toutes les configurations des fichies config.json
		 *
		 * @var array
		 */
		public static $init = array();

		/**
		 * Le constructeur obligatoirement pour utiliser la classe \eoxia001\Singleton_Util
		 *
		 * @return void nothing
		 */
		protected function construct() {}

		/**
		 * Initialise les fichiers de configuration
		 *
		 * @since 0.1.0
		 * @version 1.0.1
		 *
		 * @param string $path_to_config_file Le chemin vers le fichier config.json.
		 * @param string $plugin_slug         Le SLUG du plugin définis dans le fichier principale de config.json.
		 *
		 * @return WP_Error|boolean {
		 *																		WP_Error Si le fichier est inexistant ou si le plugin ne contient pas de slug.
		 *                                    boolean  True si aucune erreur s'est produite.
		 *}.
		 */
		public function init_config( $path_to_config_file, $plugin_slug = '' ) {
			if ( empty( $path_to_config_file ) ) {
				return new \WP_Error( 'broke', __( 'Unable to load file', 'eoxia' ) );
			}

			$tmp_config = JSON_Util::g()->open_and_decode( $path_to_config_file );

			if ( empty( $tmp_config->slug ) ) {
				return new \WP_Error( 'broke', __( 'This plugin need to have a slug', 'eoxia' ) );
			}

			if ( ! empty( $plugin_slug ) ) {
				$abspath = str_replace( '\\', '/', ABSPATH );

				$slug = $tmp_config->slug;
				$tmp_path = str_replace( '\\', '/', self::$init[ $plugin_slug ]->path );
				$tmp_config->module_path = $tmp_config->path;

				$tmp_config->url = str_replace( $abspath, site_url('/'), $tmp_path . $tmp_config->path );
				$tmp_config->url = str_replace( '\\', '/', $tmp_config->url );
				$tmp_config->path = $tmp_path . $tmp_config->path;
				if ( isset( $tmp_config->external ) && ! empty( $tmp_config->external ) ) {
					self::$init['external']->$slug = $tmp_config;
				} else {
					self::$init[ $plugin_slug ]->$slug = $tmp_config;
				}
			} else {
				self::$init[ $tmp_config->slug ] = $tmp_config;
			}
		}
	}
} // End if().
