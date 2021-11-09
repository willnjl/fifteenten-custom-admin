<?php

namespace classes;

class FifteentenCookieConsentor{

    private $id;

    public function __construct(bool $enabled = false, string $id = null)
    {

        $this->id = $id;

        if($enabled){   
            add_action( 'wp_enqueue_scripts', [$this,'fiteenten_cc_scripts'] );
        }
    
    }


    public function initSettings()
    {
        
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
}