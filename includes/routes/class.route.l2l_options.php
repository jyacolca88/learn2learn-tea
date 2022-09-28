<?php

class Learn2Learn_Options_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'options';


        register_rest_route( $namespace, '/' . $resource_name . "/(?P<user_id>[\d]+)", array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_options'),
                'permission_callback'  => array ( $this, 'get_options_permissions_check' ),
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, '/' . $resource_name . "/questionnaire-launched" . "/(?P<user_id>[\d]+)", array(

            array(
                'methods'               => WP_REST_Server::EDITABLE,
                'callback'              => array ( $this, 'set_questionnaire_already_launched'),
                'permission_callback'  => array ( $this, 'set_questionnaire_already_launched_permissions_check' ),
                'args'                  => array ()
            )

        ));

    }

    public function get_options( $request ){

        $user_id = intval($request["user_id"]);

        $results = $this->get_results_from_options_table();
        $fields = $this->format_options_results_into_assoc_array($results);
        $questionnaire_already_launched = $this->get_questionnaire_already_launched($user_id);

        if (is_array($fields)){
            $fields["questionnaire_already_launched"] = $questionnaire_already_launched;
        }

        $Quick_Links_Obj = new Learn2Learn_Quick_Links;
        $quick_links = $Quick_Links_Obj->get_quick_links();

        $fields["quick_links"] = $quick_links;

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

    private function get_questionnaire_already_launched($user_id){

        return (get_user_meta( $user_id, 'l2l_quesitonnaire_already_launched' , true ) ? true : false);

    }

    public function set_questionnaire_already_launched($request){

        $user_id = intval($request["user_id"]);

        $already_launched = $this->get_questionnaire_already_launched($user_id);

        if (!$already_launched){

            $meta_id = add_user_meta( $user_id, 'l2l_quesitonnaire_already_launched', true, true );

            $already_launched = ($meta_id ? true : false);

        } else {

            $already_launched = true;

        }

        return new WP_REST_Response( $already_launched , 200 );

    }

    public function set_questionnaire_already_launched_permissions_check(){

        return current_user_can( 'read' );

    }

}