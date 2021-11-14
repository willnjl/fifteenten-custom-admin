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
        add_shortcode('FifteentenAnalyticsPopup', [$this, 'shortcode_cb']);
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
            <?php settings_fields( $this->optionsGroup ); ?>
            <?php do_settings_sections('fifteenten_analytics_settings'); ?>
            <?php settings_errors(); ?>
            <?php submit_button(); ?>
        </form>

        <hr>

        <div style="opacity: 0.8">
            <p >
                Don't forget to add the following code to header.php
            </p>
            <code><?php echo ' &lt;?= do_shortcode("[FifteentenAnalyticsPopup]"); ?&gt; '?></code>
        </div>
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
        ?>
    
        <script>
            // Define dataLayer and the gtag function.
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}

            // Default ad_storage to 'denied'.
            gtag('consent', 'default', {
                'ad_storage': 'denied',
                'analytics_storage': 'denied',
            });
            
        </script>

        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?= esc_attr(trim($this->analyticsId())); ?>');</script>

        </script>
        
    <?
    }

    public function shortcode_cb()
    {?>

        <div id="cookie-popup">
            <p>
                We use cookies to improve website performance, please use the below options
                to determine whether you're happy with this.
            </p>
            <a href="/cookie-policy" target="_blank">Click here to find out more</a>
            <div class="btn-container">
                <input
                type="submit"
                name="ctl00$ButtonCAccept"
                value="I'm OK with that"
                id="ButtonCAccept"
                class="btn btn-accept"
                onclick="consentGranted()"
                />
                <input
                type="submit"
                name="ctl00$ButtonCReject"
                value="Decline All"
                id="ButtonCReject"
                class="btn btn-reject"
                onclick="consentDenied()"
                />
            </div>
        </div>
    
    <?
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