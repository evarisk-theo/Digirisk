<?php
/**
 * Classe gérant les mises à jour de DigiRisk.
 *
 * @package DigiRisk
 * @subpackage Module/Update_Manager
 *
 * @since 6.2.8.0
 * @version 6.2.8.0
 */

namespace digi;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe gérant les mises à jour de DigiRisk.
 */
class Update_Manager extends \eoxia001\Singleton_Util {

	protected function construct() {}

	public function display() {
		$waiting_updates = get_option( '_digi_waited_updates', array() );
		\eoxia001\View_Util::exec( 'digirisk', 'update_manager', 'main', array(
			'waiting_updates' => $waiting_updates,
		) );
	}
}

new Update_Manager();
