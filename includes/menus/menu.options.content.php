<div class="wrap">
    <h1><?php echo get_admin_page_title() ?></h1>
    <form method="post" action="options.php">
        <?php
            settings_fields( 'l2l-options-settings' ); // settings group name
            do_settings_sections( 'l2l-options' ); // just a page slug
            submit_button(); // "Save Changes" button
        ?>
    </form>
</div>

<?php

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