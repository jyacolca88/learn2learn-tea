<?php

require_once get_template_directory() . '/includes/menus/class.menu.options.php';
require_once get_template_directory() . '/includes/menus/class.settings.fields.php';

add_action( 'admin_menu', 'lf_l2l_tea_options_menu' );
 
function lf_l2l_tea_options_menu(){

    $L2L_Menu_Options = new Learn2Learn_Menu_Options();
    $L2L_Menu_Options->add_menu_page();
}
 
function lf_l2l_tea_options_menu_callback(){
	require_once get_template_directory() . '/includes/menus/menu.options.content.php';
}

add_action( 'admin_init',  'lf_l2l_settings_fields' );

function lf_l2l_settings_fields(){

    $L2L_Settings_Fields = new Learn2Learn_Settings_Fields();

	// Add Section
	$L2L_Settings_Fields->add_section("l2l-generation-section", "General Settings");

	// Add Main Heading field
	$L2L_Settings_Fields->register_and_add_field("l2l-main-heading", "Main Heading", "text", array("style" => "width:22rem; max-width:100%"));

	// Add Main Paragraph field
	$L2L_Settings_Fields->register_and_add_field("l2l-main-paragraph", "Main Paragraph", "textarea", array("style" => "width: 22rem; max-width:100%; height: 5rem; min-height: 5rem; max-height: 5rem;"));

}