<?php

namespace classes;

class FifteentenCookieConsentor{

    private $id;
    private $enabled;
    private $optionsGroup;


    public function __construct($optionsGroup = "fifteenten_cc_options")
    {   
        $this->optionsGroup = $optionsGroup;
        add_action( 'admin_menu', [$this, 'initSettings'] ); // Register Settings or Plugin Options
        
        $this->enabled = $this->analyticsAreEnabled();
        if($this->enabled){   

            $this->duration = $this->getDuration();
            $this->domain = $this->getDomain();
            $this->id = $this->analyticsId();
            add_action( 'wp_enqueue_scripts', [$this,'fiteenten_cc_scripts'] );
            add_action('wp_head', [$this, 'renderGaScripts']);
        }
        
    }
    
    
    public function initSettings()
    {
       // Enable Anaylytics Settings
        add_option('fifteenten_enable_analytics', true);
        register_setting( $this->optionsGroup, 'fifteenten_enable_analytics');
        // Enable Anaylytics ID
        add_option('fifteenten_analytics_id', '');
        register_setting( $this->optionsGroup, 'fifteenten_analytics_id');
          
        // Cookie Domain Setting
        add_option('fifteenten_analytics_domain', '');
        register_setting( $this->optionsGroup, 'fifteenten_analytics_domain');

        // Consent Lifetime Duration
        add_option('fifteenten_analytics_duration', 90);
        register_setting( $this->optionsGroup, 'fifteenten_analytics_duration');
    }



    public function fiteenten_cc_scripts() 
    {
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


    public function renderGaScripts()
    {
        wp_die();
        fifteenten_ga_scripts(100);
    }

    public function analyticsAreEnabled()
    {
        return  get_option( 'fifteenten_enable_analytics', false);
    }

    public function analyticsId()
    {
        return  get_option( 'fifteenten_analytics_id', '');
    }
    public function getDomain()
    {
        return  get_option( 'fifteenten_analytics_domain', '');
    }
    public function getDuration()
    {
        return get_option( 'fifteenten_analytics_duration', 90);
    }
}