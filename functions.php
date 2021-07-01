<?php
require get_theme_file_path( '/inc/like-route.php' );
require get_theme_file_path( '/inc/search-route.php' );

function university_custom_rest() {
    register_rest_field('post', 'authorName', [
        'get_callback'  => function() { return get_the_author(); }
    ]);

    register_rest_field('note', 'userNoteCount', [
        'get_callback'  => function() { return count_user_posts( get_current_user_id(), 'note' ); }
    ]);
}
add_action( 'rest_api_init', 'university_custom_rest' );

function university_files() {
    wp_enqueue_script( 'main-university-js', get_theme_file_uri('/js/scripts-bundled.js'), null, '1.0', true );
    wp_enqueue_script( 'university-search', get_theme_file_uri('/js/modules/Search.js'), ['jquery'], '1.0', true );
    wp_enqueue_script( 'university-mynotes', get_theme_file_uri('/js/modules/MyNotes.js'), ['jquery'], '1.0', true );
    wp_enqueue_script( 'university-like', get_theme_file_uri('/js/modules/Like.js'), ['jquery'], '1.0', true );
    wp_enqueue_style( 'custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i' );
    wp_enqueue_style( 'fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
    wp_enqueue_style( 'university_main_styles', get_stylesheet_uri() );
    wp_localize_script( 'university-search', 'universityData', [
        'root_url'      => get_site_url(),
        'nonce'         => wp_create_nonce('wp_rest')
    ]);
}
add_action( 'wp_enqueue_scripts', 'university_files' );

function university_features() { 
    add_theme_support('title-tag');
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'professorLandscape', 400, 260, true );
    add_image_size( 'professorPortrait', 480, 650, true );
    add_image_size( 'pageBanner', 1500, 350, true );
    register_nav_menu( 'headerMenuLocation', 'Header Menu Location');
    register_nav_menu( 'footerLocationOne', 'Footer Location One');
    register_nav_menu( 'footerLocationTwo', 'Footer Location Two');
}
add_action( 'after_setup_theme', 'university_features' );

function uninversity_adjust_queries( $query ) {
    if( !is_admin() AND is_post_type_archive('event') AND $query->is_main_query() ) {

        $today = date('Ymd');

        $query->set( 'meta_key', 'event_date' );
        $query->set( 'orderby', 'meta_value_num' );
        $query->set( 'order', 'ASC' );
        $query->set( 'meta_query', [
            [
                'key'		=> 'event_date',
                'compare'	=> '>=',
                'value'		=>  $today,
                'type'		=> 'numeric'
            ]
        ]);
    }
    if( !is_admin() AND is_post_type_archive('program') AND $query->is_main_query() ) {
        $query->set( 'order', 'ASC' );
        $query->set( 'orderby', 'title' );
        $query->set( 'posts_per_page', -1 );
    }
}
add_action( 'pre_get_posts', 'uninversity_adjust_queries' );

function universityMapKey( $api ) {
    $api['key'] = 'AIzaSyAgxIrXmQBN5D2QQrVBKZic8qm8w5v-iUU';
    return $api;
}
add_filter( 'acf/fields/google_map/api', 'universityMapKey' );

// Redirecto subscriber accounts out of admin and onto homepage
add_action( 'admin_init', 'redirectSubsToFrontend' );
function redirectSubsToFrontend() {
    $ourCurrentUser = wp_get_current_user();
    if( count( $ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber' ) {
        wp_redirect( site_url('/') );
        exit;
    } 
}

//Customize Login Screen
add_filter( 'login_headerurl', 'ourHeaderUrl' );
function ourHeaderUrl() {
    return esc_url( site_url('/') );
}

add_action( 'login_enqueue_scripts', 'ourLoginCSS' );
function ourLoginCSS() {
    wp_enqueue_style( 'university_main_styles', get_stylesheet_uri() );
}

add_filter( 'login_headertitle', 'ourLoginTitle' );
function ourLoginTitle(){
    return get_bloginfo('name');
}

//Force note posts to be private
add_filter( 'wp_insert_post_data', 'makeNotePrivate', 10, 2 );
function makeNotePrivate( $data, $postarr ) {
    if( $data['post_type'] == 'note' && $data['post_status'] != 'trash' ) {
         $data['post_status'] = 'private';
    }
    if( $data['post_type'] == 'note' ) {
        if( count_user_posts( get_current_user_id(), 'note' ) > 4 && !$postarr['ID'] ) {
            die("You have reached your note limit.");
        }
        $data['post_content'] = sanitize_textarea_field( $data['post_content'] );
        $data['post_title'] = sanitize_text_field( $data['post_title'] );
    }
    return $data;
}