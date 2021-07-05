<?php


// Set Login logo
function my_login_logo() { 
    $img_dir = get_stylesheet_directory_uri() . '/login-logo.png';
    $image_info = getimagesize($img_dir);
    $str = '<style type="text/css"> .login h1 a{';
    $str .= 'width:' . $image_info[0] ."px !important; ";
    $str .= 'height:' . $image_info[1] ."px !important; ";
    $str .= "max-height: 85px !important;";
    $str .= "max-width: 100% !important;";
    $str .= 'background-image: url('. $img_dir .')!important;';
    $str .= 'background-position: center !important; ';
    $str .= 'background-repeat: no-repeat !important; ';
    $str .= 'background-size: contain !important;';
    $str .= 'margin-bottom: 2rem !important;';
    $str .= "}</style>";
    echo $str;
}

add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo_url() {
    return home_url();
}

add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return get_bloginfo('name');
}

add_filter( 'login_headertitle', 'my_login_logo_url_title' );
