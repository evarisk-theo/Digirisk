<?php
/**
* @TODO : A Détailler
*
* @author Jimmy Latour <jimmy.latour@gmail.com>
* @version 0.1
* @copyright 2015-2016 Eoxia
* @package risk
* @subpackage filter
*/

if ( !defined( 'ABSPATH' ) ) exit;

class user_filter {
	public function __construct() {
		add_filter( 'digi_tab', array( $this, 'callback_tab' ) );
	}

	public function callback_tab( $list_tab ) {
		$list_tab['digi-workunit']['user'] = array(
			'text' => __( 'User', 'wpdigi-i18n' ),
		);
		return $list_tab;
	}
}

new user_filter();