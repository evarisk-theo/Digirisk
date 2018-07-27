<?php
/**
 * Les filtres pour les sociétés
 *
 * @author Evarisk <dev@evarisk.com>
 * @since 6.2.2
 * @version 6.6.0
 * @copyright 2015-2018 Evarisk
 * @package DigiRisk
 */

namespace digi;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Les filtres pour les sociétés
 */
class Society_Filter {

	/**
	 * Le constructeur
	 *
	 * @since 6.2.2
	 * @version 6.6.0
	 */
	public function __construct() {
		add_filter( 'society_identity', array( $this, 'callback_society_identity' ), 10, 2 );
		add_filter( 'digi_tab', array( $this, 'callback_tab' ), 2, 2 );
	}

	/**
	 * Affiches l'identité en haut de la vue principale d'une société.
	 *
	 * @param  Society_Model $element           Les données de la société.
	 * @param  boolean       $editable_identity Si le titre est modifiable ou pas.
	 *
	 * @return void
	 *
	 * @since 6.2.2
	 * @version 6.6.0
	 */
	public function callback_society_identity( $element, $editable_identity = false ) {
		\eoxia001\View_Util::exec( 'digirisk', 'society', 'identity', array(
			'element'           => $element,
			'editable_identity' => $editable_identity,
		), false );
	}

	/**
	 * Ajoutes l'onglet "Informations" à la société.
	 *
	 * @since 6.3.0
	 * @version 6.3.0
	 *
	 * @param  array   $tab_list La liste des filtres.
	 * @param  integer $id L'ID de la société.
	 * @return array
	 */
	public function callback_tab( $tab_list, $id ) {
		$tab_list['digi-society']['informations'] = array(
			'type' => 'text',
			'text' => __( 'Informations', 'digirisk' ),
			'title' => __( 'Informations', 'digirisk' ),
		);

		return $tab_list;
	}
}

new Society_Filter();
