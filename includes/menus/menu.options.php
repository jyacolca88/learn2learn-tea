<?php

add_action( 'admin_menu', 'lf_l2l_tea_options_menu' );
 
function lf_l2l_tea_options_menu(){
 
	add_menu_page(
		'Learn2Learn Options Menu', // page <title>Title</title>
		'L2L Options', // link text
		'manage_options', // user capabilities
		'l2l-options', // page slug
		'lf_l2l_tea_options_menu_callback', // this function prints the page content
		'dashicons-lightbulb', // icon (from Dashicons for example)
		4 // menu position
	);
}
 
function lf_l2l_tea_options_menu_callback(){
	require_once get_template_directory() . '/includes/menus/menu.options.content.php';
}