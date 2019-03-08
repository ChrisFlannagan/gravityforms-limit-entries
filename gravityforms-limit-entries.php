<?php

/**
 * Plugin Name: Better Gravityforms Limit Entries with CTA
 * Plugin URI: https://github.com/ChrisFlannagan/gravityforms-limit-entries
 * Author: MrFlannagan
 * Author URI: https://whoischris.com
 * Description: Limit the number of entries a gravity form can take
 * Text Domain: gflimitentries
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'GF_Limit_Entries' ) ) {

	define( 'GF_LIMIT_ENTRIES_VERSION', 0.1 );

	class GF_Limit_Entries {
		public static function init() {
			require_once( plugin_dir_path( __FILE__ ) . 'classes/settings.php' );
			require_once( plugin_dir_path( __FILE__ ) . 'classes/display.php' );

			$settings = new GFLE_Settings();
			$display = new GFLE_Display();

			$settings->hook();
			$display->hook();
		}
	}

}

add_action( 'plugins_loaded', [ 'GF_Limit_Entries', 'init' ] );