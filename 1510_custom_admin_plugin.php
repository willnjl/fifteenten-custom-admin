<?php
   /**
   * Plugin Name: 15_10 Wordpress Custom Theme Installation
   * Plugin URI: 
   * description:  Initialises 1510's Custom WP Installation
   * Author: Will @ Fifteenten
   * Author URI: fifteenten.co.uk
   * License: GPL2
   */


// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require __DIR__ . '/vendor/autoload.php';


/**
 * The code that runs during plugin activation.
 */
function activate_fifteenten_recruitment() {	
   \Classes\FifteentenDecline::activate();
}
   
   
function deactivate_fifteenten_recruitment() {
   // Classes\FifteentenCookieConsentor::deactivate();
}

register_activation_hook( __FILE__, 'activate_fifteenten_recruitment' );
register_deactivation_hook( __FILE__, 'deactivate_fifteenten_recruitment' );

class FifteentenCustomAdmin_Plugin
{  

   public $version = "1.1.10";

   public function __construct()
   {
      $this->define( '_FIFTEENTEN_VERSION_', $this->version );
      $this->define( '_FIFTEENTEN_PLUGIN_PATH_', plugin_dir_url( __FILE__  ));
      
      $this->customSettings = new Classes\FifteentenCustomAdmin("fifteenten-theme-settings");
      $this->customFields = new \Classes\FifteenTenCustomACF();
      $this->cookieConsentor =  new \Classes\FifteentenCookieConsentor();
      $this->commentDisabler = new Classes\FifteentenCommentDisabler();

   }


   /**
	 * define
	 */
	function define( $name, $value = true ) {
		if( !defined($name) ) {
			define( $name, $value );
		}
	}
}


$FifteentenCustomAdmin = new FifteentenCustomAdmin_Plugin();