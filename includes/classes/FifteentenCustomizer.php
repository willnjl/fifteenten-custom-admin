<?php 


function fifteenten_page()
{
     return "<h1> hello World </h1>";
}

class FifteentenCustomizer
{


     protected $pluginSlug = "test-plugin";


    public function __construct()
    {
          add_action('admin_menu', [$this, 'register_plugin_page']); // Create Admn Page
          add_action( 'admin_enqueue_scripts', [$this, 'add_media_script'] ); 
          add_action( 'admin_menu', [$this, 'register_settings'] );
    }

     function register_plugin_page()
     {
          add_menu_page(
               'Test Plugin Page',
               'Test Plugin',
               'manage_options',
               $this->getPluginSlug(),
               'fifteenten_customizer_page_contents'
          );
    } 

     function add_media_script( $hook_suffix ) 
     {
          wp_enqueue_media();
          wp_enqueue_script( 'fiteenten-media-select', plugins_url('Fifteenten-Customizer/assets/js/fifteenten_customiser_media-selector.js'), array(), _S_VERSION, true );
     }


     function register_settings()
     {
          add_option('fifteenten_customizer_backend_logo', 0);
          register_setting( 'fifteenten_customizer_options', 'fifteenten_customizer_backend_logo' );
     }


     public function getPluginSlug()
     {
          return $this->pluginSlug;
     }
}

