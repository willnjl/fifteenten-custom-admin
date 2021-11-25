<?php

namespace Classes;

use Carbon\Carbon;

class FifteentenDecline{

    private $namespace;
    private $rest;

    public function __construct(string $rest, string $namespace)
    {   
        $this->namespace = $namespace;
        $this->rest = $rest;
        add_action( 'rest_api_init', [$this, 'register_cc_endpoint']);
    }

    public static function activate()
    {
        global $wpdb;
        Self::upgrade_200();
    }


    public static function upgrade_200()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->base_prefix . 'cc_decline';
        $sql = "CREATE TABLE `{$table_name}`(
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        created_at datetime NOT NULL,
        expires_at datetime NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        maybe_create_table( $table_name , $sql );
    }

    public static function upgrade() {

        $saved_version = (int) get_site_option('wp_cc_decline_db_version');
        if ($saved_version < 200 && $this::upgrade_200()) {
            update_site_option('wp_cc_decline_db_version', 200);
        }
    }


    public function register_cc_endpoint()
    {
        register_rest_route( $this->namespace, '/decline', [
            'methods' => 'POST',
            'callback' => [$this, 'store'],
            'permission_callback' => [$this, 'nonce_check']
        ]);
    }

    public function nonce_check($request)
    {
        $nonce = $request->get_params()['data']['_wpnonce'];
        return wp_verify_nonce($nonce,'wp_rest');
    }

    public function store($request)
    {
        global $wpdb;
        $now = Carbon::now();
        
        $wpdb->insert( $wpdb->base_prefix . 'cc_decline',[
            'created_at' => $now->toIso8601String(),
            'expires_at' => $now->add(1, 'day')->toIso8601String(),
        ]);
        
        return new \WP_REST_Response($now, 200);;
    }
    
    
    public function count()
    {
        global $wpdb;

        $wpdb->get_results("SELECT * FROM " . $wpdb->base_prefix . "cc_decline");

        return $wpdb->num_rows;
        
    }
    public function lastMonth()
    {
        global $wpdb;

        $wpdb->get_results("SELECT * FROM " . $wpdb->base_prefix . "cc_decline");

        return $wpdb->num_rows;
        
    }
    
    public function thisMonth()
    {

        $now = Carbon::now();

        $start = $now->startOfMonth()->format('d-m-Y h:s');
        $end = $now->format('d-m-Y h:s');

        global $wpdb;

        $wpdb->get_results("
        SELECT * FROM " . $wpdb->base_prefix . "cc_decline
        WHERE created_at BETWEEN '" . $start . "' AND '" . $end . "';");
        


        return $wpdb->num_rows;
        
    }

}