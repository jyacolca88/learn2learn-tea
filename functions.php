<?php

add_action( 'after_setup_theme', 'lf_l2ltea_theme_setup' );

function lf_l2ltea_theme_setup(){

    /* Add Theme Support for Page Thumbnails (Featured Image) */
    add_theme_support( 'post-thumbnails' );

    /* Initialise nav menus */
    add_action( 'init', 'lf_l2ltea_register_menus' );

    /* Remove Menu Pages if NOT admin */
    add_action( 'admin_init', 'lf_l2ltea_remove_menu_pages' );

    // Hide items under Appearance menu except WP Menus
    add_action('admin_menu', 'lf_l2ltea_hide_menu', 10);

}

/********** REGISTER NAVIGATION MENUS [BEGIN] **********/

function lf_l2ltea_register_menus(){

    if (function_exists('register_nav_menus')) {
        register_nav_menus(
            array(
                'journey-map-questions' => 'Journey Map Questions', 
                'journey-content-items' => 'Journey Content Items',
                'embed-site-menu' => "Embed Site Menu Items",
                'quick-links-menu' => 'Quick Links Menu'
            )
        );
    }

}

/********** REGISTER NAVIGATION MENUS [END] **********/

/********** GET NAV MENUS BY LOCATION [BEGIN] **********/

function lf_l2l_get_nav_menu_items_by_location( $location, $args = [] ) {
 
    // Get all locations
    $locations = get_nav_menu_locations();
 
    // Get object id by location
    $object = wp_get_nav_menu_object( $locations[$location] );
 
    // Get menu items by menu name
    $menu_items = wp_get_nav_menu_items( $object->name, $args );
 
    // Return menu post objects
    return $menu_items;
}

/********** GET NAV MENUS BY LOCATION [END] **********/

/********** REMOVE MENU PAGES FOR NON ADMIN USERS [BEGIN] **********/

function lf_l2ltea_remove_menu_pages() {

    global $user_ID;

    if ( !current_user_can( 'manage_options' ) ) {
        remove_menu_page('edit.php'); // Posts
        remove_menu_page( 'edit.php?post_type=page' ); // Pages
        remove_menu_page('edit-comments.php'); // Comments
    }
}

/********** REMOVE MENU PAGES FOR NON ADMIN USERS [END] **********/

/********** HIDE MENU ITEMS UNDER APPEARANCE EXCEPT MENU FOR EDITOR USERS [BEGIN] **********/

function lf_l2ltea_hide_menu() {

    $role_object = get_role( 'editor' );
    $role_object->add_cap( 'edit_theme_options' );

    $user = wp_get_current_user();
   
   // Check if the current user is an Editor
   if ( in_array( 'editor', (array) $user->roles ) ) {
       
       // They're an editor, so grant the edit_theme_options capability if they don't have it
       if ( ! current_user_can( 'edit_theme_options' ) ) {
           $role_object = get_role( 'editor' );
           $role_object->add_cap( 'edit_theme_options' );
       }
       
       // Hide the Themes page
       remove_submenu_page( 'themes.php', 'themes.php' );

       // Hide the Widgets page
       remove_submenu_page( 'themes.php', 'widgets.php' );

       // Hide the Customize page
       remove_submenu_page( 'themes.php', 'customize.php' );

       // Remove Customize from the Appearance submenu
       global $submenu;
       unset($submenu['themes.php'][6]);
   }
}

/********** HIDE MENU ITEMS UNDER APPEARANCE EXCEPT MENU FOR EDITOR USERS [END] **********/

function lf_l2l_get_category_ids(){

    $content_items = lf_l2l_get_nav_menu_items_by_location("journey-content-items");
    $category_ids = array();
    if (is_array($content_items) && !empty($content_items)){

        foreach($content_items as $item){

            if ($item->menu_item_parent == "0" && $category_id = $item->object_id)
                array_push($category_ids, intval($category_id));

        }

    }
    return $category_ids;

}

if (class_exists("Jwt_Auth")){

    function lf_l2l_customise_jwt_auth_token_return( $data, $user ) {
        $response = array(
            'token' => $data['token'],
            'user_id' => $user->ID,
            'username' => $user->data->user_login
        );
        return $response;
    }
    
    add_filter( 'jwt_auth_token_before_dispatch', 'lf_l2l_customise_jwt_auth_token_return', 10, 2 );

}

require_once get_template_directory() . '/includes/include.classes.php';
require_once get_template_directory() . '/includes/include.restapi.php';
require_once get_template_directory() . '/includes/menus/menu.options.php';



/* TESTING CODE BELOW */

function lf_l2l_save_post( $post_id ) {
    
    $Save_Post = new Learn2Learn_Save_Post($post_id);
    $Save_Post->wrap_youtube_iframe_with_16_9_responsive_ratio();
    $Save_Post->update_post();
 
}

add_action( 'save_post', 'lf_l2l_save_post' );