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
	$L2L_Settings_Fields->add_section("l2l-generation-section", "General Settings");

	// Add Main Heading field
	$main_heading_style = array("style" => "width:22rem; max-width:100%");
	$L2L_Settings_Fields->register_and_add_field("l2l-main-heading", "Main Heading", "text", $main_heading_style);

}

// function lf_l2l_settings_fields(){

// 	// I created variables to make the things clearer
// 	$page_slug = 'l2l-options';
// 	$option_group = 'l2l-options-settings';
//     $section_id = 'l2l_general_section';
//     $main_heading_name = 'l2l-main-heading';
//     $main_paragraph_name = 'l2l-main-paragraph';

// 	// 1. create section
// 	add_settings_section(
// 		$section_id , // section ID
// 		'General Settings', // title (optional)
// 		'', // callback function to display text after title but before fields (optional)
// 		$page_slug
// 	);

// 	// 2. register fields
// 	register_setting( $option_group, $main_heading_name, array("type" => "string", "sanitize_callback" => "l2l_options_main_heading_sanitize") );
// 	register_setting( $option_group, $main_paragraph_name, array("type" => "string", "sanitize_callback" => "l2l_options_main_paragraph_sanitize") );

// 	// 3. add fields
// 	add_settings_field(
// 		$main_heading_name,
// 		'Main Heading',
// 		'l2l_options_main_heading_textbox', // function to print the field
// 		$page_slug,
// 		$section_id,  // section ID
//         array(
//             'label_for' => $main_heading_name, 
//             'name' => $main_heading_name,
//             'style' => "width:22rem; max-width:100%"
//         )
// 	);

// 	add_settings_field(
// 		$main_paragraph_name,
// 		'Main Paragraph',
// 		'l2l_options_main_paragraph_textarea',
// 		$page_slug,
// 		$section_id,
// 		array(
// 			'label_for' => $main_paragraph_name,
// 			'name' => $main_paragraph_name, // pass any custom parameters
//             'style' => "width: 22rem; max-width:100%; height: 5rem; min-height: 5rem; max-height: 5rem;"
// 		)
// 	);

// }

// // custom callback function to print checkbox field HTML
// function l2l_options_main_heading_textbox( $args ) {
//     $name = $args['name'];
//     $value = get_option($name, '');
//     $style = $args['style'];
//     printf(
//         "<input type='text' id='%s' name='%s' value='%s' style='%s' />",
//         $name,
//         $name,
//         $value,
//         $style
//     );
// }

// function l2l_options_main_paragraph_textarea( $args ) {
//     $name = $args['name'];
//     $value = get_option($name, '');
//     $style = $args['style'];
//     printf(
//         "<textarea id='%s' name='%s' style='%s'>%s</textarea>",
//         $name,
//         $name,
//         $style,
//         $value
//     );
// }

// // custom sanitization function for a checkbox field
// function l2l_options_main_heading_sanitize( $value ) {
// 	return sanitize_text_field((trim($value)));
// }

// function l2l_options_main_paragraph_sanitize( $value ) {
// 	return strval((trim($value)));
// }