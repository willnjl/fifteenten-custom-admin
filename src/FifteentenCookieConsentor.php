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
        add_action('admin_menu', [$this, 'register_plugin_page']); // Create Admin Page
        $this->enabled = $this->analyticsAreEnabled();
        if($this->enabled){   
            $this->duration = $this->getDuration();
            $this->domain = $this->getDomain();
            $this->id = $this->analyticsId();
            add_action( 'wp_enqueue_scripts', [$this,'fiteenten_cc_scripts'] );
            add_action('wp_head', [$this, 'renderGaScripts']);
        }
        
    }
    
    
    function register_plugin_page()
    {

        // add_submenu_page(
        //      string $parent_slug,
        //      string $page_title,
        //      string $menu_title,
        //      string $capability,
        //      string $menu_slug,
        //      callable $function = '',
        //      int $position = null 
        // )
        add_submenu_page(
            "fifteenten-theme-settings",
            "Analytics",
            "Analytics",
            "manage_options",
            'fifteenten_analytics_settings',
            [$this, 'page_cb'],
            100
        );
    } 


    public function initSettings()
    {
  
        register_setting( $this->optionsGroup, 'fifteenten_enable_analytics');
        register_setting( $this->optionsGroup, 'fifteenten_analytics_id');
        register_setting( $this->optionsGroup, 'fifteenten_analytics_domain');
        register_setting( $this->optionsGroup, 'fifteenten_analytics_duration');

        
        add_settings_section(
            'fifteenten_analytics_options',
            'Analytics Options',
            [$this, 'section_cb'],
            'fifteenten_analytics_settings'
        );


        // add_settings_field(
            // string $id,
            // string $title,
            // callable $callback,
            // string $page,
            // string $section = 'default',
            // array $args = array() 
        // )
        add_settings_field(
            'analytics-enabled',
            'Enably Analytics',
            [$this, 'setting_analytics_enabled'],
            'fifteenten_analytics_settings',
            'fifteenten_analytics_options'
        );
        add_settings_field(
            'analytics-name',
            'Google Tag Manager ID',
            [$this, 'setting_analytics_id'],
            'fifteenten_analytics_settings',
            'fifteenten_analytics_options'
        );
       
        add_settings_field(
            'analytics-domain',
            'Domain Name',
            [$this, 'setting_analytics_domain'],
            'fifteenten_analytics_settings',
            'fifteenten_analytics_options'
        );
         add_settings_field(
            'analytics-duration',
            'Save cookie consent for',
            [$this, 'setting_analytics_duration'],
            'fifteenten_analytics_settings',
            'fifteenten_analytics_options'
        );
    }


    public function page_cb()
     {
        ?>
        <form method='post' action="options.php">
            <!-- settings_fields( string $option_group )  -->
            <?php settings_fields( $this->optionsGroup ); ?>
            <!-- do_settings_sections( string $page ) -->
            <?php do_settings_sections('fifteenten_analytics_settings'); ?>
            <?php settings_errors(); ?>
            <?php submit_button(); ?>
        </form>
        <?
    }

    public function section_cb()
    {
        echo "Setup";
    }
    
    public function setting_analytics_enabled()
    {?>
        <input type='checkbox' name='fifteenten_enable_analytics' value='1'  <?php checked(1, get_option('fifteenten_enable_analytics', true)); ?>/>
    <?
    }
    public function setting_analytics_id()
    {
        $id = esc_attr(get_option('fifteenten_analytics_id')); 
        echo "<input type='text' name='fifteenten_analytics_id' value='". $id ."' placeholder='GTM-XXXXXXX'/>";
    }
    public function setting_analytics_domain()
    {
        $domain = esc_attr(get_option('fifteenten_analytics_domain')); 
        echo "<input type='text' name='fifteenten_analytics_domain' value='". $domain ."' placeholder='example.co.uk'/>";
    }
    public function setting_analytics_duration()
    {
        $option = esc_attr(get_option('fifteenten_analytics_duration')); 
        ?>
        <input type='number' min="1" name='fifteenten_analytics_duration' value='<?= $option ?>' placeholder='60'/> Days
        <?
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