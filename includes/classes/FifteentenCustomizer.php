<?php 


function fifteenten_page()
{
     return "<h1> hello World </h1>";
}

class FifteentenCustomizer
{


     protected $pluginSlug = "test-plugin";
     protected $pluginUrl = '';

    public function __construct()
    {
          $this->pluginUrl = plugins_url('Fifteenten-Customizer/');
          add_action('admin_menu', [$this, 'register_plugin_page']); // Create Admn Page
          add_action( 'admin_enqueue_scripts', [$this, 'admin_enqueue'] ); 
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

     function admin_enqueue( $hook_suffix ) 
     {
          $attachment_post_id = get_option( 'media_selector_attachment_id', 0 );
          wp_enqueue_media();
          wp_enqueue_style('
               fifteenten-custom-admin-css',
               $this->getPluginUrl() . "assets/css/admin.css",
               [],
               '0.00.00',
          );
          wp_enqueue_script(
               'fiteenten-media-select',
               $this->getPluginUrl() . "assets/js/fifteenten_customiser_media-selector.js",
               array(),
               _S_VERSION,
               true
          );
          wp_localize_script('fiteenten-media-select', 'attachment', ['id' => $attachment_post_id ]);
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
     public function getPluginUrl()
     {
          return $this->pluginUrl;
     }
}

