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

	// 1. create section
	add_settings_section(
		$section_id , // section ID
		'General Settings', // title (optional)
		'', // callback function to display the section (optional)
		$page_slug
	);

	// 2. register fields
	register_setting( $option_group, 'main-heading', array("type" => "string", "sanitize_callback" => "l2l_options_main_heading_sanitize") );
	register_setting( $option_group, 'num_of_slides', 'absint' );

	// 3. add fields
	add_settings_field(
		'main-heading',
		'Main Heading',
		'l2l_options_main_heading_textbox', // function to print the field
		$page_slug,
		$section_id,  // section ID
        array(
            'name' => 'main-heading'
        )
	);

	add_settings_field(
		'num_of_slides',
		'Number of slides',
		'rudr_number',
		$page_slug,
		$section_id,
		array(
			'label_for' => 'num_of_slides',
			'class' => 'hello', // for <tr> element
			'name' => 'num_of_slides' // pass any custom parameters
		)
	);

}

// custom callback function to print field HTML
function rudr_number( $args ){
	printf(
		'<input type="number" id="%s" name="%s" value="%d" />',
		$args[ 'name' ],
		$args[ 'name' ],
		get_option( $args[ 'name' ], 2 ) // 2 is the default number of slides
	);
}
// custom callback function to print checkbox field HTML
function l2l_options_main_heading_textbox( $args ) {
	// $value = get_option( 'main-heading' );
    print_r($args);
    // $name = $args['name'];
    // $value = get_option($name, '');
    // printr(
    //     "<input type='text' id='%s' name='%s' value='%s' />",
    //     $name,
    //     $name,
    //     $value
    // );
}

// custom sanitization function for a checkbox field
function l2l_options_main_heading_sanitize( $value ) {
	return sanitize_text_field((trim($value)));
}