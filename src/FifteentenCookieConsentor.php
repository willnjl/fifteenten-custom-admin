<?php

namespace classes;

class FifteentenCookieConsentor{

    private $id;
    private $enabled;

    public function __construct()
    {   

        add_action( 'admin_menu', [$this, 'initSettings'] ); // Register Settings or Plugin Options 
        $this->enabled = $this->analyticsAreEnabled();
        $this->id = $this->analyticsId();

        if($this->enabled){   
            add_action( 'wp_enqueue_scripts', [$this,'fiteenten_cc_scripts'] );
        }
    
    }


    public function initSettings()
    {
       // Enable Anaylytics Settings
          add_option('fifteenten_enable_analytics', true);
          register_setting( 'fifteenten_custom_admin_options', 'fifteenten_enable_analytics');
          // Enable Anaylytics ID
          add_option('fifteenten_analytics_id', '');
          register_setting( 'fifteenten_custom_admin_options', 'fifteenten_analytics_id');
    }



   public function fiteenten_cc_scripts() 
    {
            // wp_enqueue_script( 'fiteenten-chocolat', get_template_directory_uri() . '/js/chocolat.js', array(), _S_VERSION, true );
        wp_enqueue_style( 'fiteenten-cc-style', __FIFTEENTEN_CUSTOM_ADMIN_DIR_PATH__ . 'assets/css/cookieconsent.css', array(), _S_VERSION );
        wp_enqueue_script( 'fifteenten-cc-script', __FIFTEENTEN_CUSTOM_ADMIN_DIR_PATH__ . 'assets/js/fifteenten_cookieconsent.js', array(), _S_VERSION, true );
          
        wp_localize_script('fifteenten-cc-script', 'siteSettings', [
            'gtm' => [
                'containerId' => $this->id,
                'validConsentDuration' => $this->duration,
                'domain' => $this->domain,
            ]   
        ]);
    }

    public function analyticsAreEnabled()
     {
        return get_option( 'fifteenten_enable_analytics', true);
     }

     public function analyticsId()
     {
        return get_option( 'fifteenten_analytics_id', true);
     }
}