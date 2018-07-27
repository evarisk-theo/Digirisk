<?php
/**
 * Gestion des modules.
 * Les externals doivent être placés dans modules/
 *
 * @author Jimmy Latour <dev@eoxia.com>
 * @since 0.1.0
 * @version 1.2.0
 * @copyright 2015-2017 Eoxia
 * @package WPEO_Util
 */

namespace eoxia001;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\eoxia001\Module_Util' ) ) {
	/**
	 * Gestion des modules
	 */
	class Module_Util extends \eoxia001\Singleton_Util {
		/**
		 * Le constructeur obligatoirement pour utiliser la classe \eoxia001\Singleton_Util
		 *
		 * @since 0.1.0
		 * @version 1.0.0
		 *
		 * @return void
		 */
		protected function construct() {}

		/**
		 * Parcours le fichier digirisk.config.json pour récupérer les chemins vers tous les modules.
		 * Initialise ensuite un par un, tous ses modules.
		 *
		 * @since 0.1.0
		 * @version 1.0.0
		 *
		 * @param string $path        Le chemin vers le module externe.
		 * @param string $plugin_slug Le slug principale du plugin dans le fichier principale config.json.
		 *
		 * @return WP_Error|boolean {
		 *																		WP_Error Si le fichier est inexistant ou si le plugin n'a pas de submodule.
		 *                                    boolean  True si aucune erreur s'est produite.
		 *}.
		 */
		public function exec_module( $path, $plugin_slug ) {
			if ( empty( \eoxia001\Config_Util::$init[ $plugin_slug ] ) ) {
				return new \WP_Error( 'broke', sptrinf( __( 'Main config %s not init', $plugin_slug ), $plugin_slug ) );
			}

			if ( empty( \eoxia001\Config_Util::$init[ $plugin_slug ]->modules ) ) {
				return new \WP_Error( 'broke', __( 'No module to load', $plugin_slug ) );
			}

			if ( ! empty( \eoxia001\Config_Util::$init[ $plugin_slug ]->modules ) ) {
				foreach ( \eoxia001\Config_Util::$init[ $plugin_slug ]->modules as $module_json_path ) {
					self::inc_config_module( $plugin_slug, $path . $module_json_path );
					self::inc_module( $plugin_slug, $path . $module_json_path );
				}
			}

			return true;
		}

		/**
		 * Appelle la méthode init_config de \eoxia001\Config_Util pour initialiser les configs du module
		 *
		 * @since 0.1.0
		 * @version 1.0.0
		 *
		 * @param string $plugin_slug      Le slug du module externe à initialiser.
		 * @param string $module_json_path Le chemin vers le dossier du module externe.
		 *
		 * @return void
		 */
		public function inc_config_module( $plugin_slug, $module_json_path ) {
			\eoxia001\Config_Util::g()->init_config( $module_json_path, $plugin_slug );
		}

		/**
		 * Inclus les dépendences du module (qui sont défini dans le config.json du module en question)
		 *
		 * @since 0.1.0
		 * @version 1.0.0
		 *
		 * @param string $plugin_slug      Le slug du module externe à initialiser.
		 * @param string $module_json_path Le chemin vers le dossier du module externe.
		 *
		 * @return void
		 */
		public function inc_module( $plugin_slug, $module_json_path ) {
			$module_name = basename( $module_json_path, '.config.json' );
			$module_path = dirname( $module_json_path );

			if ( ! isset( \eoxia001\Config_Util::$init[ $plugin_slug ]->$module_name->state ) || ( isset( \eoxia001\Config_Util::$init[ $plugin_slug ]->$module_name->state ) && \eoxia001\Config_Util::$init[ $plugin_slug ]->$module_name->state ) ) {
				if ( ! empty( \eoxia001\Config_Util::$init[ $plugin_slug ]->$module_name->dependencies ) ) {
					$can_inc = true;

					// Vérification de la dépendence d'inclusion.
					if ( ! empty( \eoxia001\Config_Util::$init[ $plugin_slug ]->$module_name->dependencies_func ) ) {
						if ( ! empty( \eoxia001\Config_Util::$init[ $plugin_slug ]->$module_name->dependencies_func ) ) {
							foreach ( \eoxia001\Config_Util::$init[ $plugin_slug ]->$module_name->dependencies_func as $element ) {
								if ( ! call_user_func( $element ) ) {
									$can_inc = false;
									break;
								}
							}
						}
					}

					if ( $can_inc ) {
						foreach ( \eoxia001\Config_Util::$init[ $plugin_slug ]->$module_name->dependencies as $dependence_folder => $list_option ) {
							$path_to_module_and_dependence_folder = $module_path . '/' . $dependence_folder . '/';

							if ( ! empty( $list_option->priority ) ) {
								$this->inc_priority_file( $path_to_module_and_dependence_folder, $dependence_folder, $list_option->priority );
							}

							\eoxia001\Include_util::g()->in_folder( $path_to_module_and_dependence_folder );
						}
					}
				}
			}
		}

		/**
		 * Inclus les fichiers prioritaires qui se trouvent dans la clé "priority" dans le .config.json du module
		 *
		 * @since 0.1.0
		 * @version 1.0.0
		 *
		 * @param  string $path_to_module_and_dependence_folder Le chemin vers le module.
		 * @param  string $dependence_folder                    le chemin vers le dossier à inclure.
		 * @param  array  $list_priority_file                    La liste des chemins des fichiers à inclure en priorité.
		 * @return void                                       	nothing
		 */
		public function inc_priority_file( $path_to_module_and_dependence_folder, $dependence_folder, $list_priority_file ) {
			if ( ! empty( $list_priority_file ) ) {
				foreach ( $list_priority_file as $file_name ) {
					$path_file = realpath( $path_to_module_and_dependence_folder . $file_name . '.' . $dependence_folder . '.php' );

					require_once( $path_file );
				}
			}
		}

		/**
		 * Désactives ou actives un module
		 *
		 * @since 1.2.0
		 * @version 1.2.0
		 *
		 * @param string $namespace Le SLUG principale de lu plugin.
		 * @param string $slug      Le SLUG du module en question.
		 * @param bool $state       true ou false.
		 */
		public function set_state( $namespace, $slug, $state ) {
			$path_to_json = plugin_dir_path( __FILE__ ) . '../../../modules/' . $slug . '/' . $slug . '.config.json';
			$json_content = \eoxia001\JSON_Util::g()->open_and_decode( $path_to_json );
			$json_content->state = $state;
			$json_content = json_encode( $json_content, JSON_PRETTY_PRINT );
			$json_content = preg_replace_callback( '/\\\\u([0-9a-f]{4})/i', function ( $matches ) {
				$sym = mb_convert_encoding( pack( 'H*', $matches[1] ), 'UTF-8', 'UTF-16' );
				return $sym;
			}, $json_content );

			$file = fopen( $path_to_json, 'w+' );
			fwrite( $file, str_replace( '\/', '/', $json_content ) );
			fclose( $file );
		}
	}
} // End if().
