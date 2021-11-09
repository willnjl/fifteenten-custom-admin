<?php 

namespace classes;

class FifteentenCommentDisabler
{

    public function __construct($isEnabled)
    {

        if($isEnabled){

            add_action('admin_init', [$this, 'disable_comments_post_types_support']);
            add_filter('comments_open', [$this, 'disable_comments_status'], 20, 2);
            add_filter('pings_open', [$this, 'disable_comments_status'], 20, 2);
            add_filter('comments_array', [$this, 'disable_comments_hide_existing_comments'], 10, 2);
            add_action('admin_menu', [$this, 'disable_comments_admin_menu']);
            add_action('admin_init', [$this, 'disable_comments_admin_menu_redirect']);
            add_action('admin_init', [$this, 'disable_comments_dashboard']);
            add_action('init', [$this, 'disable_comments_admin_bar']);
            add_action( 'wp_before_admin_bar_render', [$this, 'remove_admin_bar_links' ]);
        }
    }

    public function initOptions()
    {
        add_option('fifteenten_custom_disable_comments', true);
          register_setting( 'fifteenten_custom_admin_options', 'fifteenten_custom_disable_comments' );
    }

    public function disable_comments_post_types_support() {
        $post_types = get_post_types();
        foreach ($post_types as $post_type) {
            if(post_type_supports($post_type, 'comments')) {
                remove_post_type_support($post_type, 'comments');
                remove_post_type_support($post_type, 'trackbacks');
            }
	    }
    }

    // Close comments on the front-end
    public function disable_comments_status() {
        return false;
    }


    // Hide existing comments
    public function disable_comments_hide_existing_comments($comments) {
        $comments = array();
        return $comments;
    }

    // Remove comments page in menu
    public function disable_comments_admin_menu() {
        remove_menu_page('edit-comments.php');
        remove_submenu_page( 'options-general.php', 'options-discussion.php' );
    }

    // Redirect any user trying to access comments page
    public function disable_comments_admin_menu_redirect() {
        global $pagenow;
        if ($pagenow === 'edit-comments.php') {
            wp_redirect(admin_url()); exit;
        }
    }

    // Remove comments metabox from dashboard
    public function disable_comments_dashboard() {
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    }


    // Remove comments links from admin bar
    public function disable_comments_admin_bar() {
        if (is_admin_bar_showing()) {
            remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
        }
    }


    public function remove_admin_bar_links() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
        $wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
        $wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
        $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
        $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
        $wp_admin_bar->remove_menu('comments');         // Remove the comments link
    }


}