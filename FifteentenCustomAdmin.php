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


/**
 * The code that runs during plugin activation.
 */
function activate_fifteenten_custom_admin() {
	
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_fifteenten_custom_admin() {

    
}

register_activation_hook( __FILE__, 'activate_fifteenten_custom_admin' );
register_deactivation_hook( __FILE__, 'deactivate_fifteenten_custom_admin' );


// Load Functions
require_once plugin_dir_path( __FILE__ ) . 'includes/functions/load.php';


// Plugin Instance
require_once plugin_dir_path( __FILE__ ) . 'includes/classes/FifteentenCustomAdmin.php';

$fifteentencustom_admin = new FifteentenCustomAdmin();
