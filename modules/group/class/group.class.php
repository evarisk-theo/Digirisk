<?php
/**
 * Classe gérant les groupements
 *
 * @author Jimmy Latour <jimmy@evarisk.com>
 * @since 6.0.0
 * @version 6.4.0
 * @copyright 2015-2017 Evarisk
 * @package DigiRisk
 */

namespace digi;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe gérant les groupements
 */
class Group_Class extends \eoxia\Post_Class {

	/**
	 * Le nom du modèle
	 *
	 * @var string
	 */
	protected $model_name = '\digi\group_model';

	/**
	 * Le post type
	 *
	 * @var string
	 */
	protected $post_type = 'digi-group';

	/**
	 * La clé principale du modèle
	 *
	 * @var string
	 */
	protected $meta_key = '_wpdigi_society';

	/**
	 * Le préfixe de l'objet dans DigiRisk
	 *
	 * @var string
	 */
	public $element_prefix = 'GP';

	/**
	 * La fonction appelée automatiquement avant la création de l'objet dans la base de donnée
	 *
	 * @var array
	 */
	protected $before_post_function = array( '\digi\construct_identifier' );

	/**
	 * La fonction appelée automatiquement avant la modification de l'objet dans la base de donnée
	 *
	 * @var array
	 */
	protected $before_put_function = array();

	/**
	 * La fonction appelée automatiquement après la récupération de l'objet dans la base de donnée
	 *
	 * @var array
	 */
	protected $after_get_function = array( '\digi\get_identifier', '\digi\get_full_society' );

	/**
	 * La route pour accéder à l'objet dans la rest API
	 *
	 * @var string
	 */
	protected $base = 'group';

	/**
	 * La version de l'objet
	 *
	 * @var string
	 */
	protected $version = '0.1';

	/**
	 * Le nom pour le resgister post type
	 *
	 * @var string
	 */
	protected $post_type_name = 'Groupements';

	/**
	 * Construction du tableau contenant les risques pour l'arborescence complète du premier élément demandé / Build an array with all risks for element and element's subtree
	 *
	 * @param object $element L'élément actuel dont il faut récupérer la liste des risques de manière récursive / Current element where we have to get risk list recursively.
	 *
	 * @return array Les risques pour l'arborescence complète non ordonnées mais construits de façon pour l'export / Unordered risks list for complete tree, already formatted for export
	 *
	 * @since 0.1
	 * @version 6.2.5.0
	 */
	public function get_element_tree_risk( $element, $with_picture = true ) {
		$risks_in_tree = array();

		$risks_in_tree = $this->build_risk_list_for_export( $element, $with_picture );

		/**	Liste les enfants direct de l'élément / List children of current element	*/
		$group_list = group_class::g()->get( array( 'posts_per_page' => -1, 'post_parent' => $element->id, 'post_status' => array( 'publish', 'draft' ) ), false );
		foreach ( $group_list as $group ) {
			$risks_in_tree = array_merge( $risks_in_tree, $this->get_element_tree_risk( $group ) );
		}

		$work_unit_list = workunit_class::g()->get( array( 'posts_per_page' => -1, 'post_parent' => $element->id, 'post_status' => array( 'publish', 'draft' ) ), false );
		foreach ( $work_unit_list as $workunit ) {
			$risks_in_tree = array_merge( $risks_in_tree, $this->build_risk_list_for_export( $workunit, $with_picture ) );
		}

		return $risks_in_tree;
	}

	/**
	 * Récupères les elements enfants
	 *
	 * @param object $element L'élement parent.
	 * @param string $tabulation ?.
	 * @param array  $extra_params ?.
	 *
	 * @since 0.1
	 * @version 6.2.8.0
	 */
	public function get_element_sub_tree( $element, $tabulation = '', $extra_params = null ) {
		$element_children = array();
		$element_tree = '';

		$element_children[ $element->unique_identifier ] = array( 'nomElement' => $tabulation . ' ' . $element->unique_identifier . ' - ' . $element->title, ) ;
		if ( ! empty( $extra_params ) ) {
			if ( ! empty( $extra_params['default'] ) ) {
				$element_children[ $element->unique_identifier ] = wp_parse_args( $extra_params['default'], $element_children[ $element->unique_identifier ] );
			}
			if ( ! empty( $extra_params['value'] ) &&  array_key_exists( $element->unique_identifier, $extra_params['value'] ) ) {
				$element_children[ $element->unique_identifier ] = wp_parse_args( $extra_params['value'][ $element->unique_identifier ], $element_children[ $element->unique_identifier ] );
			}
		}
		/**	Liste les enfants direct de l'élément / List children of current element	*/
		$group_list = group_class::g()->get( array( 'posts_per_page' => -1, 'post_parent' => $element->id , 'post_status' => array( 'publish', 'draft', ), ), false );
		foreach ( $group_list as $group ) {
			$element_children = array_merge( $element_children, $this->get_element_sub_tree( $group, $tabulation . '-', $extra_params ) );
		}

		$tabulation = $tabulation . '-';
		$work_unit_list = workunit_class::g()->get( array( 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'posts_per_page' => -1, 'post_parent' => $element->id, 'post_status' => array( 'publish', 'draft', ), ), false );
		foreach ( $work_unit_list as $workunit ) {
			$workunit_definition[ $workunit->unique_identifier ] = array( 'nomElement' => $tabulation . ' ' . $workunit->unique_identifier . ' - ' . $workunit->title, );

			if ( !empty( $extra_params ) ) {
				if ( !empty( $extra_params[ 'default' ] ) ) {
					$workunit_definition[ $workunit->unique_identifier ] = wp_parse_args( $extra_params[ 'default' ], $workunit_definition[ $workunit->unique_identifier ] );
				}
				if ( !empty( $extra_params[ 'value' ] ) &&  array_key_exists( $workunit->unique_identifier, $extra_params[ 'value' ] ) ) {
					$workunit_definition[ $workunit->unique_identifier ] = wp_parse_args( $extra_params[ 'value' ][ $workunit->unique_identifier ], $workunit_definition[ $workunit->unique_identifier ] );
				}
			}
			$element_children = array_merge( $element_children, $workunit_definition );
		}

		usort( $work_unit_list, function( $a, $b ) {
			if ( $a->unique_key === $b->unique_key ) {
				return 0;
			}

			return ( $a->unique_key > $b->unique_key ) ? -1 : 1;
		} );

		return $element_children;
	}

	/**
	 * Récupères l'id des elements enfants
	 *
	 * @param integer $element_id L'ID de l'élement parent.
	 * @param array   $list_id La liste des ID.
	 *
	 * @since 0.1
	 * @version 6.2.5.0
	 */
	public function get_element_sub_tree_id( $element_id, $list_id ) {
		$group_list = group_class::g()->get( array( 'posts_per_page' => -1, 'post_parent' => $element_id , 'post_status' => array( 'publish', 'draft', ), ) );
		if ( ! empty( $group_list ) ) {
			foreach ( $group_list as $group ) {
				$list_id[] = array( 'id' => $group->id, 'workunit' => array() );
				// $list_id[count($list_id) - 1] = array();
				// $list_id[count($list_id) - 1]['workunit'] = array();
				$work_unit_list = workunit_class::g()->get( array( 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'posts_per_page' => -1, 'post_parent' => $group->id, 'post_status' => array( 'publish', 'draft', ), ), false );
				foreach ( $work_unit_list as $workunit ) {
					$list_id[ (count( $list_id ) - 1) ]['workunit'][]['id'] = $workunit->id;
				}
				$list_id = $this->get_element_sub_tree_id( $group->id, $list_id );
			}
		}
		else {
			$work_unit_list = workunit_class::g()->get( array( 'posts_per_page' => -1, 'post_parent' => $element_id, 'post_status' => array( 'publish', 'draft', ), ), false );
			foreach ( $work_unit_list as $workunit ) {
				// $list_id[count($list_id) - 1 == -1 ? 0 : count($list_id) - 1]['workunit'][]['id'] = $workunit->id;
			}
		}


		return $list_id;
	}

	/**
	 * Construction de la liste des risques pour un élément donné / Build risks' list for a given element
	 *
	 * @param object $element La définition complète de l'élément dont il faut retourner la liste des risques / The entire element we want to get risks list for.
	 *
	 * @return array La liste des risques construite pour l'export / Risks' list builded for export
	 *
	 * @since 6.0.0
	 * @version 6.3.0
	 */
	public function build_risk_list_for_export( $element, $with_picture = true ) {
		$risk_list = Risk_Class::g()->get( array(
			'post_parent' => $element->id,
		) );

		$element_duer_details = array();
		foreach ( $risk_list as $risk ) {
			$comment_list = '';
			if ( ! empty( $risk->comment ) ) :
				foreach ( $risk->comment as $comment ) :
					$comment_list .= $comment->date['date_input']['fr_FR']['date'] . ' : ' . $comment->content . "
";
				endforeach;
			endif;

			$picture = __( 'No picture defined', 'digirisk' );

			if ( ! empty( $risk->thumbnail_id ) && $with_picture ) {
				$picture_definition = wp_get_attachment_image_src( $risk->thumbnail_id, 'digirisk-element-thumbnail' );
				$picture_path       = str_replace( site_url( '/' ), ABSPATH, $picture_definition[0] );

				if ( is_file( $picture_path ) ) {
					$picture = array(
						'type'   => 'picture',
						'value'  => str_replace( site_url( '/' ), ABSPATH, $picture_definition[0] ),
						'option' => array(
							'size' => 9,
						),
					);
				}
			}

			$element_duer_details[] = apply_filters( 'risk_duer_additional_data', array(
				'idElement'         => $element->unique_identifier,
				'nomElement'        => $element->unique_identifier . ' - ' . $element->title,
				'identifiantRisque' => $risk->unique_identifier . '-' . $risk->evaluation->unique_identifier,
				'quotationRisque'   => $risk->evaluation->risk_level['equivalence'],
				'niveauRisque'      => $risk->evaluation->scale,
				'nomDanger'         => $risk->risk_category->name,
				'commentaireRisque' => $comment_list,
				'actionPrevention'  => '',
				'photoAssociee'     => $picture,
			), $risk );
		}

		if ( ! empty( $risk_list_to_order ) ) {
			foreach ( $risk_list_to_order as $risk_level => $risk_for_export ) {
				$final_level = ! empty( Evaluation_Method_Class::g()->list_scale[ $risk_level ] ) ? Evaluation_Method_Class::g()->list_scale[ $risk_level ] : '';
				$element_duer_details[ 'risq' . $final_level ]['value']   = $risk_for_export;
				$element_duer_details[ 'risqPA' . $final_level ]['value'] = $risk_for_export;
			}
		}

		return $element_duer_details;
	}
}

Group_Class::g();
