<?php 

class FifteentenCustomAdmin
{


     protected $pluginSlug = "test-plugin";
     protected $pluginUrl = '';


     
    public function __construct()
    {
          $this->pluginUrl = plugins_url('Fifteenten-custom-admin/');
          add_action('admin_menu', [$this, 'register_plugin_page']); // Create Admn Page
          add_action( 'admin_enqueue_scripts', [$this, 'admin_enqueue'] ); 
          add_action( 'admin_menu', [$this, 'register_settings'] );
          add_action( 'login_enqueue_scripts', [$this,'replaceAdminLogo'] );

    }

     function register_plugin_page()
     {
          add_menu_page(
               'Test Plugin Page',
               'Test Plugin',
               'manage_options',
               $this->getPluginSlug(),
               'fifteenten_custom_admin_page_contents'
          );
    } 

     function admin_enqueue( $hook_suffix ) 
     {
         
           
          
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
               '0.00.00',
               true
          );
          


          wp_localize_script('fiteenten-media-select', 'attachment', [
               'id' => $this->getAttachementPostID()
          ]);
     }


     function register_settings()
     {
          add_option('fifteenten_custom_admin_backend_logo', 0);
          register_setting( 'fifteenten_custom_admin_options', 'fifteenten_custom_admin_backend_logo' );
     }


     public function getPluginSlug()
     {
          return $this->pluginSlug;
     }
     public function getPluginUrl()
     {
          return $this->pluginUrl;
     }
     public function getAttachementPostID()
     {
          return get_option( 'media_selector_attachment_id', 0 );
     }

     public function replaceAdminLogo()
     {
          wp_enqueue_style(
               'fifteenten-custom-admin-css',
               $this->getPluginUrl() . "assets/css/login.css",
               [],
               '0.00.00',
          );

            wp_enqueue_script(
               'fiteenten-logo-swap',
               $this->getPluginUrl() . "assets/js/fifteenten_customiser_logo-swap.js",
               array(),
               '0.00.00',
               true
          );



           wp_localize_script('fiteenten-logo-swap', 'attachment', [
               'props' => wp_get_attachment_image_src($this->getAttachementPostID(), 'medium')
          ]);
     }
}

