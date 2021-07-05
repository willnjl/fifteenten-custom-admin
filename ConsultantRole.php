<?php

/**
 * Create A Consultant Role
 */

add_filter( 'register_post_type_args', 'change_caps_of_job_post' , 10, 2 );

function change_caps_of_job_post( $args, $post_type ){

    // if Post Type is not Job do nothing
 if ( 'job' !== $post_type ) {
     return $args;
 }

 // Change the capabilities of the "job" post_type
 $args['capabilities'] = array(
            'edit_post' => 'edit_job',
            'edit_posts' => 'edit_jobs',
            'edit_others_posts' => 'edit_other_jobs',
            'publish_posts' => 'publish_jobs',
            'read_post' => 'read_job',
            'read_private_posts' => 'read_private_jobs',
            'delete_post' => 'delete_jobs'
        );

  // Give the job post type it's arguments
  return $args;

}

// Create A role Called Consultant
function create_consultant_role(){
    $args = array(
        'read' => true,
        'edit_posts' => false,   
    );       
    add_role( 'consultant',' Consultant', $args );
};


// Choose Capablilites That Need Adding
function set_caps($role_name){
    $role = get_role($role_name);
    $role->add_cap( 'read_job');
    $role->add_cap( 'edit_job' );
    $role->add_cap( 'edit_jobs' );
    $role->add_cap( 'edit_other_jobs' );
    $role->add_cap( 'edit_published_jobs' );
    $role->add_cap( 'publish_jobs' );
    $role->add_cap( 'read_private_jobs' );
    $role->add_cap( 'delete_jobs' );
    $role->add_cap(  'delete_private_jobs' ); 
     $role->add_cap('delete_published_jobs' );
     $role->add_cap('delete_others_jobs' );
}
function remove_caps($role_name){
    $role =  get_role($role_name);
    $role->remove_cap( 'read_job');
    $role->remove_cap( 'edit_job' );
    $role->remove_cap( 'edit_jobs' );
    $role->remove_cap( 'edit_other_jobs' );
    $role->remove_cap( 'edit_published_jobs' );
    $role->remove_cap( 'publish_jobs' );
    $role->remove_cap( 'read_private_jobs' );
    $role->remove_cap( 'delete_jobs' );
    $role->remove_cap(  'delete_private_jobs' ); 
     $role->remove_cap('delete_published_jobs' );
     $role->remove_cap('delete_others_jobs' );
}


// Set consultant capabilities
function set_consultant_caps(){
    $roles = array('consultant', 'administrator', 'editor');
    foreach($roles as $role_title){
        set_caps($role_title);
    }
}



add_action( 'init', 'create_consultant_role' );
add_action( 'admin_init', 'set_consultant_caps', 999 );

register_deactivation_hook( __FILE__ , 'remove_new_caps' );

function remove_new_caps(){
    $roles = array('consultant');
    foreach($roles as $role_title){
        remove_caps($role_title);
    }
}
