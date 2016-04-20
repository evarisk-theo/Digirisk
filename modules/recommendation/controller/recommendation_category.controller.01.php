<?php if ( !defined( 'ABSPATH' ) ) exit;
/**
 * Fichier contenant les utilitaires pour la gestion des catégories de préconisation et les préconisations / File with all utilities for managing recommendation categories and recommendations
 *
 * @author Evarisk development team <dev@evarisk.com>
 * @version 6.0
 */

/**
 * Classe contenant les utilitaires pour la gestion des catégories de préconisations / Class with all utilities for managing recommendation categories
 *
 * @author Evarisk development team <dev@evarisk.com>
 * @version 6.0
 */
class wpdigi_recommendation_category_ctr_01 extends term_ctr_01 {

	/**
	 * Nom du modèle à utiliser / Model name to use
	 * @var string
	 */
	protected $model_name   = 'wpdigi_recommendation_category_mdl_01';
	/**
	 * Type de l'élément dans wordpress / Wordpress element type
	 * @var string
	 */
	protected $taxonomy    	= 'digi-recommendation-category';
	/**
	 * Nom du champs (meta) de stockage des données liées / Name of field (meta) for linked datas storage
	 * @var string
	 */
	protected $meta_key    	= '_wpdigi_recommendationcategory';

	/**	Défini la route par défaut permettant d'accèder à l'élément depuis WP Rest API  / Define the default route for accessing to element from WP Rest API	*/
	protected $base = 'digirisk/recommendation-category';
	protected $version = '0.1';

	public $element_prefix = 'R';

	/* PRODEST:
	{
		"name": "__construct",
		"description": "Instanciation des outils pour la gestion des catégories de préconisation et les préconisations / Instanciate utilities for managing recommendation categories and recommendations",
		"type": "function",
		"check": false,
		"author":
		{
			"email": "dev@evarisk.com",
			"name": "Alexandre T"
		},
		"version": 1.0
	}
	*/
	function __construct() {
		/**	Instanciation du controlleur parent / Instanciate the parent controller */
		parent::__construct();

		/**	Inclusion du modèle / Include model	*/
		include_once( WPDIGI_RECOM_PATH . 'model/recommendation_category.model.01.php' );

		/**	Define taxonomy for recommendation categories	*/
		add_action( 'init', array( $this, 'recommendation_category_type' ), 0 );
	}

	/* PRODEST:
	{
		"name": "recommendation_category_type",
		"description": "Création du type d'élément interne a wordpress pour gérer les catégories de danger / Create wordpress element type for managing danger categories",
		"type": "function",
		"check": false,
		"author":
		{
			"email": "dev@evarisk.com",
			"name": "Alexandre T"
		},
		"version": 1.0
	}
	*/
	function recommendation_category_type() {
		$labels = array(
			'name'              => __( 'Recommendation categories', 'wpdigi-i18n' ),
			'singular_name'     => __( 'Recommendation category', 'wpdigi-i18n' ),
			'search_items'      => __( 'Search recommendation categories', 'wpdigi-i18n' ),
			'all_items'         => __( 'All recommendation categories', 'wpdigi-i18n' ),
			'parent_item'       => null,
			'parent_item_colon' => null,
			'edit_item'         => __( 'Edit recommendation category', 'wpdigi-i18n' ),
			'update_item'       => __( 'Update recommendation category', 'wpdigi-i18n' ),
			'add_new_item'      => __( 'Add New recommendation category', 'wpdigi-i18n' ),
			'new_item_name'     => __( 'New recommendation category Name' , 'wpdigi-i18n'),
			'menu_name'         => __( 'Recommendation category', 'wpdigi-i18n' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'recommendation-category' ),
		);

		register_taxonomy( $this->taxonomy, array( 'risk', 'societies' ), $args );
	}

}

global $digi_recommendation_category_controller;
$digi_recommendation_category_controller = new wpdigi_recommendation_category_ctr_01();
