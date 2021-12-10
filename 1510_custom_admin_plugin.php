<?php
   /**
   * Plugin Name: 15_10 Wordpress Custom Theme Installation
   * Plugin URI: 
   * description:  Initialises 1510's Custom WP Install.
   * Version: 1.2
   * Author: Will @ Fifteenten
   * Author URI: fifteenten.co.uk
   * License: GPL2
   */


// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


define( '__FIFTEENTEN_CUSTOM_ADMIN_DIR_PATH__', plugin_dir_url( __FILE__ ) );

// require __DIR__ . '/src/functions.php';
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

   protected $pluginUrl;
   
   public function __construct()
   {

      $this->pluginUrl = __FIFTEENTEN_CUSTOM_ADMIN_DIR_PATH__;
      $this->customSettings = new Classes\FifteentenCustomAdmin($this->pluginUrl, "fifteenten-theme-settings");
      $this->customFields = new \Classes\FifteenTenCustomACF();
      $this->cookieConsentor =  new \Classes\FifteentenCookieConsentor();
      $this->commentDisabler = new Classes\FifteentenCommentDisabler();
      
   }
}


$FifteentenCustomAdmin = new FifteentenCustomAdmin_Plugin();