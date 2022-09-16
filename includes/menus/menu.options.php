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

add_action( 'admin_init',  'lf_l2l_settings_fields' );

function lf_l2l_settings_fields(){

	// I created variables to make the things clearer
	$page_slug = 'l2l-options';
	$option_group = 'l2l-options-settings';
    $section_id = 'l2l_general_section';
    $main_heading_name = 'main-heading';
    $main_paragraph_name = 'main-paragraph';

	// 1. create section
	add_settings_section(
		$section_id , // section ID
		'General Settings', // title (optional)
		'', // callback function to display the section (optional)
		$page_slug
	);

	// 2. register fields
	register_setting( $option_group, $main_heading_name, array("type" => "string", "sanitize_callback" => "l2l_options_main_heading_sanitize") );
	register_setting( $option_group, $main_paragraph_name, array("type" => "string", "sanitize_callback" => "l2l_options_main_paragraph_sanitize") );

	// 3. add fields
	add_settings_field(
		$main_heading_name,
		'Main Heading',
		'l2l_options_main_heading_textbox', // function to print the field
		$page_slug,
		$section_id,  // section ID
        array(
            'label_for' => $main_heading_name, 
            'name' => $main_heading_name
        )
	);

	add_settings_field(
		$main_paragraph_name,
		'Main Paragraph',
		'l2l_options_main_paragraph_textarea',
		$page_slug,
		$section_id,
		array(
			'label_for' => $main_paragraph_name,
			'name' => $main_paragraph_name // pass any custom parameters
		)
	);

}

// custom callback function to print checkbox field HTML
function l2l_options_main_heading_textbox( $args ) {
    $name = $args['name'];
    $value = get_option($name, '');
    printf(
        "<input type='text' id='%s' name='%s' value='%s' />",
        $name,
        $name,
        $value
    );
}

function l2l_options_main_paragraph_textarea( $args ) {
    $name = $args['name'];
    $value = get_option($name, '');
    printf(
        "<textarea id='%s' name='%s'>%s</textarea>",
        $name,
        $name,
        $value
    );
}

// custom sanitization function for a checkbox field
function l2l_options_main_heading_sanitize( $value ) {
	return sanitize_text_field((trim($value)));
}

function l2l_options_main_paragraph_sanitize( $value ) {
	return strval((trim($value)));
}