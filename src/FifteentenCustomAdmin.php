<?php 

namespace classes;


class FifteentenCustomAdmin
{

     protected $url;
     protected $slug;
     
    public function __construct($url, $slug)
    {     

          $this->url = $url;
          $this->slug = $slug;

          add_action('admin_menu', [$this, 'register_plugin_page']); // Create Adimn Page
          add_action( 'admin_enqueue_scripts', [$this, 'admin_enqueue'] ); // Enqueue Scripts For Media Browser
          add_action( 'admin_menu', [$this, 'register_settings'] ); // Register Settings or Plugin Options
          add_action( 'admin_menu', [$this, 'acfOptionsEnable'] ); // Register Settings or Plugin Options
          add_action( 'login_enqueue_scripts', [$this,'replaceAdminLogo'] ); // Enqueue Scripts and CSS For Logo Change
    }

     function register_plugin_page()
     {
          add_menu_page(
               '15/10 Custom Admin',
               '15.10 Admin',
               'manage_options',
               $this->getSlug(),
               'fifteenten_custom_admin_page_contents'
          );
    } 


     function admin_enqueue( $hook_suffix ) 
     {
    
          wp_enqueue_media();
          

          
          wp_enqueue_style('
               fifteenten-custom-admin-css',
               $this->getUrl( "assets/css/admin.css") ,
               [],
               '0.00.00',
          );
          
          wp_enqueue_script(
               'fiteenten-media-select',
               $this->getUrl("assets/js/fifteenten_customiser_media-selector.js"),
               [],
               '0.00.00',
               
          );
          
          wp_localize_script('fiteenten-media-select', 'attachment', [
               'id' => $this->getAttachmentPostID()
          ]);
     }


     function register_settings()
     {

          // Change Admin Logo
          add_option('media_selector_attachment_id', 0);
          register_setting( 'fifteenten_custom_admin_options', 'media_selector_attachment_id' );
  
          // Disable Comments
          add_option('fifteenten_custom_disable_comments', true);
          register_setting( 'fifteenten_custom_admin_options', 'fifteenten_custom_disable_comments' );
          
          
          // Enable ACF Page
          add_option('fifteenten_custom_acf_options', true);
          register_setting( 'fifteenten_custom_admin_options', 'fifteenten_custom_acf_options');
          
     }


     public function replaceAdminLogo()
     {
          wp_enqueue_style(
               'fifteenten-custom-admin-css',
               $this->getUrl( "assets/css/login.css"),
               [],
               '0.00.00',
          );


            wp_enqueue_script(
               'fiteenten-logo-swap',
               $this->getUrl("assets/js/fifteenten_customiser_logo-swap.js"),
               [],
               '0.00.00',
               true
          );

           wp_localize_script('fiteenten-logo-swap', 'attachment', [
               'props' => wp_get_attachment_image_src($this->getAttachmentPostID(), 'medium')
          ]);
     }



     public function acfOptionsEnable()
     {

          if(get_option( 'fifteenten_custom_acf_options', true)){

               if( function_exists('acf_add_options_page') ) {
                    $settings = [
                         'page_title' => 'Fields',
                         'menu_title' => 'Fields',
                         'capability' => 'edit_posts',
                    ];
                    acf_add_options_page($settings);      
               }   
          }
     }

     public function getSlug()
     {
          return $this->slug;
     }
     public function getUrl($dest)
     {
          return $this->url . $dest ; 
     }
     public function getAttachmentPostID()
     {    
          return get_option( 'media_selector_attachment_id', 0 );
     }

     public function commentsAreDisabled()
     {
        return get_option( 'fifteenten_custom_disable_comments', false);
     }
     public function acfOptionsEnabled()
     {
        return get_option( 'fifteenten_custom_admin_options', true);
     }

}
