<?php if ( !defined( 'ABSPATH' ) ) exit;
/**
 * Fichier du controlleur principal pour les catégories de documents dans Digirisk / Controller file for attachment categories for Digirisk
 *
 * @author Evarisk development team <dev@evarisk.com>
 * @version 6.0
 */

/**
 * Classe du controlleur principal pour les catégories de documents dans Digirisk / Controller class for attachment categories for Digirisk
 *
 * @author Evarisk development team <dev@evarisk.com>
 * @version 6.0
 */
class document_unique_class extends post_class {
	protected $model_name   				= 'document_unique_model';
	protected $post_type    				= 'attachment';
	public $attached_taxonomy_type  = 'attachment_category';
	protected $meta_key    					= '_wpdigi_document';
	protected $base 								= 'digirisk/printed-document';
	protected $version 							= '0.1';
	public $element_prefix 					= 'DOC';
	protected $before_put_function = array( 'construct_identifier' );
	protected $after_get_function = array( 'get_identifier' );
}