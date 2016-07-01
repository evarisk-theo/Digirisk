<?php if ( !defined( 'ABSPATH' ) ) exit;

class third_class extends post_class {
  protected $model_name   = 'third_mdl_01';
	protected $post_type    = 'third-display';
	protected $meta_key    	= 'third_display';

	/**	Défini la route par défaut permettant d'accèder aux sociétés depuis WP Rest API  / Define the default route for accessing to risk from WP Rest API	*/
	protected $base = 'digirisk/third';
	protected $version = '0.1';

	public $element_prefix = 'T';

  protected function construct() {
    include_once( THIRD_PATH . '/model/third.model.01.php' );
  }

  public function save_data( $data ) {
    // @todo : Sécurité
    // if ( empty( $data ) || empty( $data['full_name' ] ) || empty( $data['contact']['phone'] ) || empty( $data['contact']['address_id'] ) ) {
    //   return false;
    // }

    return $this->create( $data );
  }
}

third_class::get();