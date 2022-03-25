<?php

namespace classes;


class FifteentenCustomAdmin
{

     private $url;
     private $slug;
     private $optionsGroup;

     public function __construct(string $slug, string $optionsGroup = "fifteenten_custom_admin_options")
     {

          $this->url = _FIFTEENTEN_PLUGIN_PATH_;
          $this->slug = $slug;
          $this->optionsGroup = $optionsGroup;

          add_action('admin_menu', [$this, 'register_plugin_page']); // Create Admin Page
          add_action('admin_enqueue_scripts', [$this, 'admin_enqueue']); // Enqueue Scripts For Media Browser
          add_action('admin_init', [$this, 'register_settings']); // Register Settings or Plugin Options
          add_action('login_enqueue_scripts', [$this, 'replaceAdminLogo']); // Enqueue Scripts and CSS For Logo Change


     }

     function register_plugin_page()
     {

          // add_menu_page( 
          // string $page_title,
          // string $menu_title,
          // string $capability,
          // string $menu_slug,
          // callable $function = '',
          // string $icon_url = '',
          // int $position = null 
          // 
          // )

          $icon_url = _FIFTEENTEN_PLUGIN_PATH_ . 'assets/1510.svg';
          add_menu_page(
               '15/10 Theme Admin',
               '15.10',
               'manage_options',
               $this->getSlug(),
               [$this, 'page_cb'],
               $icon_url
          );


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
               $this->getSlug(),
               "Login Logo",
               "Login Logo",
               "manage_options",
               'fifteenten_logo_setting',
               [$this, 'page_backend_logo_cb']
          );
     }
     function register_settings()
     {

          // Change Admin Logo
          add_option('media_selector_attachment_id', 0);
          register_setting($this->optionsGroup, 'fifteenten_custom_disable_comments');

          register_setting(
               $this->optionsGroup,
               'media_selector_attachment_id'
          );

          add_settings_section(
               'fifteenten_general_information',
               'General Information',
               [$this, 'section_general_information'],
               $this->getSlug(),
          );
          add_settings_section(
               'fifteenten_general_settings',
               'General Settings',
               [$this, 'section_general_settings'],
               $this->getSlug(),
          );

          add_settings_field(
               'fifteenten_disable_comments_field',
               'Disable Comments',
               [$this, 'setting_comments_disable'],
               $this->getSlug(),
               'fifteenten_general_settings'
          );

          add_settings_section(
               'fifteenten_backend_logo_options',
               'Login Logo',
               [$this, 'settings_backend_logo_cb'],
               'fifteenten_logo_setting',
          );
     }



     public function section_general_information()
     { ?>
          <p>
               Welcome to your custom built wordpress theme, built by Fifteenten in Bristol.
          </p>
          <p>
               For support or any other enquiries, contact <a href="mailto:mike@fifteenten.co.uk">Mike</a>.
          </p>
          <p>
               <a href="fifteeenten.co.uk" target="_blank">fifteenten.co.uk</a><br>
          <address>36 King Street, Bristol, BS1 4DZ </address>
          </p>
          <hr>
     <?
     }

     public function section_general_settings()
     {
     ?>
     <?
     }

     public function setting_comments_disable()
     { ?>
          <input type='checkbox' name='fifteenten_custom_disable_comments' value='1' <?php checked(1, get_option('fifteenten_custom_disable_comments', true)); ?> />
     <?
     }

     function admin_enqueue($hook_suffix)
     {
          wp_enqueue_media();

          wp_enqueue_style(
               '
               fifteenten-custom-admin-css',
               $this->getUrl("assets/css/admin.css"),
               [],
               _FIFTEENTEN_VERSION_,
          );

          wp_enqueue_script(
               'fiteenten-media-select',
               $this->getUrl("assets/js/fifteenten_customiser_media-selector.js"),
               [],
               _FIFTEENTEN_VERSION_,

          );

          wp_localize_script('fiteenten-media-select', 'attachment', [
               'id' => $this->getAttachmentPostID()
          ]);
     }


     public function page_cb()
     {
     ?>
          <form method='post' action="options.php">
               <!-- settings_fields( string $option_group )  -->
               <?php settings_fields('fifteenten_custom_admin_options'); ?>
               <!-- do_settings_sections( string $page ) -->
               <?php do_settings_sections($this->getSlug()); ?>
               <?php settings_errors(); ?>
               <?php submit_button(); ?>
          </form>
     <?
     }

     public function page_backend_logo_cb()
     {
     ?>
          <form method='post' action="options.php">
               <?php settings_fields($this->optionsGroup); ?>
               <?php do_settings_sections('fifteenten_logo_setting'); ?>
          </form>
     <?
     }
     public function settings_backend_logo_cb()
     {
          if (isset($_POST['submit_image_selector']) && isset($_POST['image_attachment_id'])) {
               update_option('image_attachment_id', absint($_POST['image_attachment_id']));
          }
     ?>
          <p>
               Set the logo for the login screen
          </p>
          <div class='image-preview-wrapper'>
               <img id='image-preview' src='<?php echo wp_get_attachment_url(get_option('media_selector_attachment_id', 1)); ?>' width='200'>
          </div>
          <div>
               <input id="upload_image_button" type="button" class="button" value="<?php _e('Select Image'); ?>" />
               <input type='hidden' name='media_selector_attachment_id' id='image_attachment_id' value='<?php echo get_option('media_selector_attachment_id'); ?>'>
               <input type="submit" name="submit_image_selector" value="Save" class="button-primary">
          </div>

<?
     }

     public function replaceAdminLogo()
     {
          wp_enqueue_style(
               'fifteenten-custom-admin-css',
               $this->getUrl("assets/css/login.css"),
               [],
               _FIFTEENTEN_VERSION_,
          );


          wp_enqueue_script(
               'fiteenten-logo-swap',
               $this->getUrl("assets/js/fifteenten_customiser_logo-swap.js"),
               [],
               _FIFTEENTEN_VERSION_,
               true
          );

          wp_localize_script('fiteenten-logo-swap', 'attachment', [
               'props' => wp_get_attachment_image_src($this->getAttachmentPostID(), 'medium')
          ]);
     }





     public function getSlug()
     {
          return $this->slug;
     }
     public function getUrl($dest)
     {
          return $this->url . $dest;
     }
     public function getAttachmentPostID()
     {
          return get_option('media_selector_attachment_id', 0);
     }
}
