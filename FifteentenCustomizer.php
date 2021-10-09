<?php
   /**
   * Plugin Name: Fifteenten Theme Customiser
   * Plugin URI: 
   * description:  Initialises Fifteenten's Custom Theme Settings
   * Version: 1.2
   * Author: Will @ Fifteenten
   * Author URI: fifteenten.co.uk
   * License: GPL2
   */


// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The code that runs during plugin activation.
 */
function activate_fifteenten_customizer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/classes/FifteentenCustomizeActivator.php';
	$fifteentenCustomizerActivator = new FifteentenCustomizeActivator();  // correct
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_fifteenten_customizer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/classes/FifteentenCustomizeDeactivator.php';
	
    FifteentenCustomizeDeactivator::deactivate();
    
}

register_activation_hook( __FILE__, 'activate_fifteenten_customizer' );
register_deactivation_hook( __FILE__, 'deactivate_fifteenten_customizer' );


// Load Functions
require_once plugin_dir_path( __FILE__ ) . 'includes/functions/load.php';


// Plugin Instance
require_once plugin_dir_path( __FILE__ ) . 'includes/classes/FifteentenCustomizer.php';

$fifteentenCustomizer = new FifteentenCustomizer();
