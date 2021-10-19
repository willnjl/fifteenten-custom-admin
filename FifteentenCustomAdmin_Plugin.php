<?php
   /**
   * Plugin Name: Fifteenten Admin  Customiser
   * Plugin URI: 
   * description:  Initialises Fifteenten's Custom Admin Settings
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
      $this->pluginUrl = plugins_url('Fifteenten-custom-admin/');
      $this->customSettings = new Classes\FifteentenCustomAdmin($this->pluginUrl, "FifteenTen Admin Settings");
      $this->commentDisabler = new Classes\FifteentenCommentDisabler($this->customSettings->commentsAreDisabled());
      
   }
}


$FifteentenCustomAdmin = new FifteentenCustomAdmin_Plugin();