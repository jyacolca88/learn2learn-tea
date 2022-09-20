<?php

class Learn2Learn_Options_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'options';


        register_rest_route( $namespace, '/' . $resource_name, array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_options'),
                'permission_callback'  => array ( $this, 'get_options_permissions_check' ),
                'args'                  => array ()
            )

        ));

    }

    public function get_options( $request ){

        $L2L_Settings_Field = new Learn2Learn_Settings_Fields();
        $fields = $L2L_Settings_Field->get_all_fields_for_rest_route();

        return new WP_REST_Response( $fields, 200 );

    }

    public function get_options_permissions_check( $request ){

        return current_user_can( 'read' );

    }

}