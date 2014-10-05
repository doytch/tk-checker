<?php

/**
 * Fired during plugin activation
 *
 * @link       http://qedev.com
 * @since      1.0.0
 *
 * @package    TKChecker
 * @subpackage TKChecker/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    TKChecker
 * @subpackage TKChecker/includes
 * @author     Mark Hurst Deutsch <admin@qedev.com>
 */
class TKCheckerActivator {

	/**
	 * Activation function.
	 *
	 * Runs on plugin activation to set up defaults and sane state.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$default_settings = array(
			'wildcard'				=> 'TK',
			'excerpt_length' 		=> 30,
			'no_tks_text'			=> 'Research is all done!'
		);
		update_option('tk_settings', $default_settings);
	}

}
