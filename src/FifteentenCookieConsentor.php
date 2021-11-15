<?php

namespace classes;

class FifteentenCookieConsentor{

    private $id;
    private $enabled;
    private $optionsGroup;
    private $defaultText;

    public function __construct($optionsGroup = __FILE__)
    {   
        $this->optionsGroup = $optionsGroup;
        $this->defaultText = "We use cookies to improve website performance, please use the below options to determine whether you're happy with this.";

        // Register Settings or Plugin Options
        add_action( 'admin_init', [$this, 'initSettings'] );
        // Create Admin Page
        add_action('admin_menu', [$this, 'register_plugin_page']); 
        // create html shortcode for Popup
        add_shortcode('FifteentenAnalyticsPopup', [$this, 'shortcode_cb']);
        
        
 
        $this->enabled = $this->analyticsAreEnabled();
        if($this->enabled){   
            $this->duration = $this->getDuration();
            $this->domain = $this->getDomain();
            $this->id = $this->analyticsId();
            add_action( 'wp_enqueue_scripts', [$this,'fiteenten_cc_scripts'] );
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
        $page = add_submenu_page(
            "fifteenten-theme-settings",
            "Analytics",
            "Analytics",
            "manage_options",
            'fifteenten_analytics_settings',
            [$this, 'page_cb'],
            100
        );

        //Enqeueue css for popup in admin
        add_action('admin_print_styles-' . $page, [$this,'admin_custom_css']);
    } 


    public function initSettings()
    {
       
        
        register_setting(
            $this->optionsGroup,
            'fifteenten_enable_analytics'
        );
        add_settings_field(
            'analytics-enabled',
            'Enable Analytics',
            [$this, 'setting_analytics_enabled'],
            'fifteenten_analytics_settings',
            'fifteenten_analytics_options'
        );

        register_setting(
            $this->optionsGroup,
            'fifteenten_analytics_id'
        );
        add_settings_field(
            'analytics-name',
            'Google Tag Manager ID',
            [$this, 'setting_analytics_id'],
            'fifteenten_analytics_settings',
            'fifteenten_analytics_options'
        );
        register_setting(
            $this->optionsGroup,
            'fifteenten_analytics_domain'
        );
        add_settings_field(
            'analytics-domain',
            'Domain Name',
            [$this, 'setting_analytics_domain'],
            'fifteenten_analytics_settings',
            'fifteenten_analytics_options'
        );
        register_setting(
            $this->optionsGroup,
            'fifteenten_analytics_duration'
        );

         add_settings_section(
            'fifteenten_analytics_options',
            'Analytics Options',
            [$this, 'section_analytics_cb'],
            'fifteenten_analytics_settings'
        );
         add_settings_field(
            'analytics-duration',
            'Save cookie consent for',
            [$this, 'setting_analytics_duration'],
            'fifteenten_analytics_settings',
            'fifteenten_analytics_options'
        );
        
        
        add_settings_section(
            'fifteenten_analytics_personalise',
            'Popup Customisation',
            '',
            'fifteenten_analytics_settings'
        );

        register_setting(
            $this->optionsGroup,
            'fifteenten_popup_text'
        );
        add_settings_field(
           'popup-text',
           'Popup Text',
           [$this, 'setting_customise_text'],
           'fifteenten_analytics_settings',
           'fifteenten_analytics_personalise',
       );
      
        register_setting(
            $this->optionsGroup,
            'fifteenten_popup_bg'
        );
        add_settings_field(
           'popup-bg',
           'Background Color',
           [$this, 'setting_customise_bg'],
           'fifteenten_analytics_settings',
           'fifteenten_analytics_personalise',
       );
        register_setting(
            $this->optionsGroup,
            'fifteenten_popup_button-color'
        );
        add_settings_field(
           'popup-btn',
           'Button Color',
           [$this, 'setting_customise_button'],
           'fifteenten_analytics_settings',
           'fifteenten_analytics_personalise',
       );
        register_setting(
            $this->optionsGroup,
            'fifteenten_popup_button-color--hover'
        );
        add_settings_field(
           'popup-btn--hover',
           'Button Color <small>:hover</small>',
           [$this, 'setting_customise_hover'],
           'fifteenten_analytics_settings',
           'fifteenten_analytics_personalise',
       );
        register_setting(
            $this->optionsGroup,
            'fifteenten_popup_button-border-color'
        );
        add_settings_field(
           'popup-btn-border',
           'Button Border',
           [$this, 'setting_customise_border'],
           'fifteenten_analytics_settings',
           'fifteenten_analytics_personalise',
       );
        register_setting(
            $this->optionsGroup,
            'fifteenten_popup_button-border-hover'
        );
        add_settings_field(
           'popup-btn-border--hver',
           'Button Border <small>:hover</small>',
           [$this, 'setting_customise_border_hover'],
           'fifteenten_analytics_settings',
           'fifteenten_analytics_personalise',
       );
    }


    public function page_cb()
     {
        ?>
        <form method='post' action="options.php">
            <?php settings_fields( $this->optionsGroup ); ?>
            <?php do_settings_sections('fifteenten_analytics_settings'); ?>
            <hr>
               <div class="fifteenten-popup-preview">
                   <h5>
                       Preview
                   </h5>
                    <?= do_shortcode('[FifteentenAnalyticsPopup]'); ?>
                </div>
            <?php settings_errors(); ?>
            <?php submit_button(); ?>
        </form>
        <hr>
     
        <?
    }

    public function section_analytics_cb()
    {?>
        <div>
            <p>
                Don't forget to add the following code to header.php
            </p>
            <code><?php echo ' &lt;?= do_shortcode("[FifteentenAnalyticsPopup]"); ?&gt; '?></code>
        </div>
    <?
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
    
    public function setting_customise_bg()
    {

        $val = esc_attr(get_option('fifteenten_popup_bg'),);
        ?>
        <input type='text' name='fifteenten_popup_bg' value="<?= $val ?>" data-default-color="#0ff"  class="my-color-field"/>
        <?
    }
    public function setting_customise_button()
    {

        $val = esc_attr(get_option('fifteenten_popup_button-color'));
        ?>
        <input type='text' name='fifteenten_popup_button-color' value="<?= $val ?>" class="my-color-field" />
        <?
    }
    public function setting_customise_hover()
    {

        $val = esc_attr(get_option('fifteenten_popup_button-color--hover'));
        ?>
        <input type='text' name='fifteenten_popup_button-color--hover' value="<?= $val ?>" class="my-color-field" />
        <?
    }
    public function setting_customise_border()
    {

        $val = esc_attr(get_option('fifteenten_popup_button-border-color'));
        ?>
        <input type='text' name='fifteenten_popup_button-border-color' value="<?= $val ?>" class="my-color-field" />
        <?
    }
    public function setting_customise_border_hover()
    {

        $val = esc_attr(get_option('fifteenten_popup_button-border-hover'));
        ?>
        <input type='text' name='fifteenten_popup_button-border-hover' value="<?= $val ?>" class="my-color-field" />
        <?
    }
    public function setting_customise_text()
    {
        $val = esc_attr(get_option('fifteenten_popup_text'), $this->defaultText);
        ?>
        <textarea name="fifteenten_popup_text" class="" style="height:175px" ><?= $val ?></textarea>
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

    public function admin_custom_css()
    {
        wp_enqueue_style( 'fiteenten-cc-style', __FIFTEENTEN_CUSTOM_ADMIN_DIR_PATH__ . 'assets/css/cookieconsent.css', array(), _S_VERSION );
	    wp_enqueue_style( 'wp-color-picker' );
	    wp_enqueue_script( 'fifteenten_wp_cp', __FIFTEENTEN_CUSTOM_ADMIN_DIR_PATH__ . 'assets/js/fifteenten_wp_cp.js', array( 'wp-color-picker' ), false, true );

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
    <?
    }

    public function shortcode_cb()
    {
        $text = esc_attr(get_option('fifteenten_popup_text', $this->defaultText));
        $color_bg =  esc_attr(get_option('fifteenten_popup_bg'));
        $color_button = esc_attr(get_option('fifteenten_popup_button-color'));
        $color_button_hover = esc_attr(get_option('fifteenten_popup_button-color--hover'));
        $color_button_border = esc_attr(get_option('fifteenten_popup_button-border-color'));
        $color_button_border_hover = esc_attr(get_option('fifteenten_popup_button-border-hover'));
        ?>

        <style>
            #cookie-popup{
                background: <?= $color_bg; ?>;
            }
            #cookie-popup .btn{
                background: <?= $color_button; ?>;
                border-color: <?= $color_button_border; ?>;
            }
            #cookie-popup .btn:hover{
                background: <?= $color_button_hover; ?>;
                border-color: <?= $color_button_border_hover; ?>;
            }

            

        </style>
        <div id="cookie-popup">
            <p>
             <?= empty($text) ? $this->defaultText : $text; ?>
            </p>
            <a href="/cookie-policy" target="_blank">Click here to find out more</a>
            <div class="btn-container">
                <input
                type="submit"
                name="ctl00$ButtonCAccept"
                value="I'm OK with that"
                id="ButtonCAccept"
                class="btn btn-accept"
                />
                <input
                type="submit"
                name="ctl00$ButtonCReject"
                value="Decline All"
                id="ButtonCReject"
                class="btn btn-reject"
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