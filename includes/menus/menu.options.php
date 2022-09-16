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

add_action( 'admin_init',  'rudr_settings_fields' );

function rudr_settings_fields(){

	// I created variables to make the things clearer
	$page_slug = 'l2l-options';
	$option_group = 'l2l-options-settings';

	// 1. create section
	add_settings_section(
		'rudr_section_id', // section ID
		'', // title (optional)
		'', // callback function to display the section (optional)
		$page_slug
	);

	// 2. register fields
	register_setting( $option_group, 'slider_on', 'rudr_sanitize_checkbox' );
	register_setting( $option_group, 'num_of_slides', 'absint' );

	// 3. add fields
	add_settings_field(
		'slider_on',
		'Display slider',
		'rudr_checkbox', // function to print the field
		$page_slug,
		'rudr_section_id' // section ID
	);

	add_settings_field(
		'num_of_slides',
		'Number of slides',
		'rudr_number',
		$page_slug,
		'rudr_section_id',
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
function rudr_checkbox( $args ) {
	$value = get_option( 'slider_on' );
	?>
		<label>
			<input type="checkbox" name="slider_on" <?php checked( $value, 'yes' ) ?> /> Yes
		</label>
	<?php
}

// custom sanitization function for a checkbox field
function rudr_sanitize_checkbox( $value ) {
	return 'on' === $value ? 'yes' : 'no';
}