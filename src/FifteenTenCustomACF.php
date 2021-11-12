<?php

namespace classes;

class FifteenTenCustomACF{

    private $enabled;   
    private $optionsGroup;  


   public function __construct($optionsGroup = "fifteenten_custom_acf_options")
    {   
        $this->optionsGroup = $optionsGroup;
        add_action( 'admin_menu', [$this, 'initSettings'] ); // Register Settings or Plugin Options
        $this->enabled = $this->acfOptionsEnabled();
        if($this->enabled){
            add_action( 'admin_menu', [$this, 'acfOptionsEnable'] ); // Register Settings or Plugin Option
        }
        
    }



    public function initSettings()
    {
        add_option('fifteenten_show_acf_page', true);
        register_setting( $this->optionsGroup, 'fifteenten_show_acf_page'); 
    }


    public function acfOptionsEnabled()
    {
        return get_option( 'fifteenten_show_acf_page', false);
    }


    public function acfOptionsEnable()
    {
        if($this->acfOptionsEnabled()){
            $this->createOptionsPage();
            $this->createSiteFields();
        }
    }

    public function createOptionsPage()
    {
        if( function_exists('acf_add_options_page') ) {     
            $settings = [
                'page_title' => 'Brand Info',
                'menu_title' => 'Brand Info',
                'capability' => 'edit_posts',
                'icon_url' => 'dashicons-admin-site-alt'
            ];
            acf_add_options_page($settings);      
        }  
    }

    public function createSiteFields()
    {
        if( function_exists('acf_add_local_field_group') ):
            acf_add_local_field_group(array(
                'key' => 'group_618d84dc8db54',
                'title' => 'Site Options',
                'fields' => array(
                    array(
                        'key' => 'field_618d85040d685',
                        'label' => 'Company Details',
                        'name' => 'company_details',
                        'type' => 'group',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_618d971f0d686',
                                'label' => 'Email',
                                'name' => 'email',
                                'type' => 'email',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                            ),
                            array(
                                'key' => 'field_618d97430d687',
                                'label' => 'Telephone',
                                'name' => 'telephone',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_618d9752e8769',
                        'label' => 'Socials',
                        'name' => 'socials',
                        'type' => 'group',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'block',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_618d9762e876a',
                                'label' => 'Instagram',
                                'name' => 'instagram',
                                'type' => 'url',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                            ),
                            array(
                                'key' => 'field_618d976fe876b',
                                'label' => 'Linked In',
                                'name' => 'linked_in',
                                'type' => 'url',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                            ),
                            array(
                                'key' => 'field_618d977ae876c',
                                'label' => 'Facebook',
                                'name' => 'facebook',
                                'type' => 'url',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                            ),
                            array(
                                'key' => 'field_618d9784e876d',
                                'label' => 'Twitter',
                                'name' => 'twitter',
                                'type' => 'url',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                            ),
                        ),
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'acf-options-brand-info',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ));

            endif;
    }
}