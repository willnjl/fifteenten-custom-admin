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


define( 'FIFTEENTEN_CUSTOMISER_DIR_PATH', plugins_url( '', __FILE__ ) );

require __DIR__ . '/src/functions.php';
require __DIR__ . '/vendor/autoload.php';



class FifteentenCustomAdmin_Plugin
{  

   protected $pluginUrl;
   
   public function __construct()
   {
      $this->pluginUrl = plugin_dir_url( __FILE__ );
      $this->customSettings = new Classes\FifteentenCustomAdmin($this->pluginUrl, "FifteenTen Admin Settings");
      $this->commentDisabler = new Classes\FifteentenCommentDisabler($this->customSettings->commentsAreDisabled());
      
   }
}


$FifteentenCustomAdmin = new FifteentenCustomAdmin_Plugin();