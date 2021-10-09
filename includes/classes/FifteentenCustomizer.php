<?php 


function fifteenten_page()
{
     return "<h1> hello World </h1>";
}

class FifteentenCustomizer
{




    public function __construct()
    {
         add_action('admin_menu', [$this, 'register_plugin_page']);
    }

     function register_plugin_page(){
        add_menu_page(
            'Test Plugin Page',
            'Test Plugin',
            'manage_options',
            'test-plugin',
            'fifteenten_customizer_page_contents'
        );
    } 



     public function getSlug()
     {
          return $this->plugin_slug;
     }
    
}

