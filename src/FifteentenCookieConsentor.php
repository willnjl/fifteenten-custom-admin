<?php
namespace classes;

use Classes\FifteentenDecline;

class FifteentenCookieConsentor{

    private $id;
    private $enabled;
    private $optionsGroup;
    private $defaultText;
    private $rest_url;
    private $namespace;
    private $decline;
    public function __construct($optionsGroup = __FILE__)
    {   
        $this->optionsGroup = $optionsGroup;
        $this->namespace = '/cookieconsent/v1';
        $this->rest_url = site_url() . '/wp-json' . $this->namespace;

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
            $this->decline = new FifteentenDecline($this->rest_url, $this->namespace);         
        }
        
    }
    
    
    function register_plugin_page()
    {
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
          
        if($this->decline) :  //decline settings
        add_settings_section(
           'fifteenten_decline_stats',
           'Decline Statistics',
           [$this, 'section_decline_cb'],
           'fifteenten_analytics_settings'
       );
        endif; 
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

    public function section_decline_cb()
    { if($this->decline) : 
    ?>
        <ul>
            <li><strong>Total</strong>  <?php echo $this->decline->count(); ?></li>
            <li><strong>This Month</strong> <?php echo $this->decline->thisMonth(); ?></li>
            <li><strong>Last Month</strong>  <?php echo $this->decline->lastMonth(); ?></li>
        </ul>
    <? endif;
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
        wp_enqueue_style( 'fiteenten-cc-style', _FIFTEENTEN_PLUGIN_PATH_ . 'assets/css/cookieconsent.css', array(), _FIFTEENTEN_VERSION_ );
        wp_enqueue_script( 'fifteenten-axios', 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js', array(), _FIFTEENTEN_VERSION_, true );
        wp_enqueue_script( 'fifteenten-cc-script', _FIFTEENTEN_PLUGIN_PATH_ . 'assets/js/fifteenten_cookieconsent.js', array(), _FIFTEENTEN_VERSION_, true );
        wp_localize_script('fifteenten-cc-script', 'siteSettings', [
            'gtm' => [
                'containerId' => $this->id,
                'validConsentDuration' => $this->duration,
                'domain' => $this->domain,
            ],   
            'rest' => [
                'url'  => $this->rest_url,
                'nonce' => wp_create_nonce( 'wp_rest' ),
            ],
        ]);
    }

    public function admin_custom_css()
    {
        wp_enqueue_style( 'fiteenten-cc-style', _FIFTEENTEN_PLUGIN_PATH_ . 'assets/css/cookieconsent.css', array(), _FIFTEENTEN_VERSION_ );
	    wp_enqueue_style( 'wp-color-picker' );
	    wp_enqueue_script( 'fifteenten_wp_cp', _FIFTEENTEN_PLUGIN_PATH_ . 'assets/js/fifteenten_wp_cp.js', array( 'wp-color-picker' ), false, true );

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
    <?
    }

    public function shortcode_cb()
    {
        ?>
        <div id="cookie-popup">
            <div class="cc__wrap">
                <h2>
                    Cookie Information 🍪
                </h2>
                <p>
                    We use cookies, just like (almost) everyone else, to improve our understanding of how to improve our own website for our visitors.
                </p>
                <p>
                    We want to make sure we’re providing the most informative and best arranged experience for our visitors, so we deploy a handful of industry-standard cookies to do so.
                </p>
                <p>
                    Information in these cookies is available in our <a href="/cookies-policy" class="link">Cookies Policy</a> . Please note, our site is unlikely to function without ‘necessary’ cookies (same as other sites requiring cookies for aspects of their functionality). But you can choose whether or not to opt into marketing cookies below.
                </p>
                <ul>
                    <li class="cc__preference_container">
                        <div class="cc__toggle_holder cc__toggle--essential cc__toggle--active">
                            <div class="cc__toggle_circle cc__toggle--active"></div>
                        </div>
                        <label for="pref_essential">
                           <strong>Necessary Cookies</strong>  - These are the cookies that are required to make the website work
                        </label>
                    </li>
                    <li class="cc__preference_container">
                        <div class="cc__toggle_holder cc__toggle--preference" data-preference="marketing">
                            <div class="cc__toggle_circle"></div>
                            <input type="checkbox" class="cc__checkbox" name="pref_analytics" >
                        </div>
                         <label for="pref_analytics">
                           <strong>Marketing Cookies</strong> - These are the cookies that are required for us to learn how to improve the experience of our website visitors
                        </label>
                    </li>
                </ul>
                <div class="cc__btn-container">
                    <button
                    id="ButtonCUpdate"
                    class="cc__btn cc_btn--update"
                    >
                        Update to My Selection
                    </button>
                    <button
                    id="ButtonCAccept"
                    value="Agree to All & Proceed"
                    class="cc__btn cc__btn--accept"
                    >
                        Agree to All & Proceed
                    </button>
                    
                </div>
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
