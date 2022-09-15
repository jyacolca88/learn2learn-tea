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