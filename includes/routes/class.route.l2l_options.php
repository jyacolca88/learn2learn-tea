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

        $results = $this->get_results_from_options_table();
        $fields = $this->format_options_results_into_assoc_array($results);

        return new WP_REST_Response( $fields, 200 );

    }

    private function format_options_results_into_assoc_array($results){

        $fields = array();

        if (isset($results) && !empty($results)){

            foreach($results as $record){

                $fields[$record->option_name] = $record->option_value;

            }

        }

        return $fields;

    }

    private function get_results_from_options_table(){

        global $wpdb;

        $sql = "
            SELECT * 
            FROM `wp_options` 
            WHERE `option_name` 
            LIKE %s
        ";

        return $wpdb->get_results(
            $wpdb->prepare(
                $sql, "%l2l-%"
            )
        );

    }

    public function get_options_permissions_check( $request ){

        return current_user_can( 'read' );

    }

}