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
                'permission_callback'  => function() {
                    return current_user_can( 'read' );
                },
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, '/' . $resource_name . "/onboarding", array(

            array(
                'methods'               => WP_REST_Server::EDITABLE,
                'callback'              => array ( $this, 'set_onboarding_learn2learn'),
                'permission_callback'  => function() {
                    return current_user_can( 'read' );
                },
                'args'                  => array ()
            )

        ));

    }

    public function get_options( $request ){

        $user_id = intval($request["user_id"]);

        $results = $this->get_results_from_options_table();
        $fields = $this->format_options_results_into_assoc_array($results);
        $questionnaire_already_launched = $this->get_questionnaire_already_launched($user_id);
        $onboarding_l2l = $this->get_onboarding_learn2learn($user_id, "l2l_onboarding");
        $onboarding_gs = $this->get_onboarding_learn2learn($user_id, "l2l_onboarding_gs");
        $onboarding_sp = $this->get_onboarding_learn2learn($user_id, "l2l_onboarding_sp");

        if (is_array($fields)){
            $fields["questionnaire_launched"] = $questionnaire_already_launched;
            $fields["onboarding"] = $onboarding_l2l;
            $fields["onboarding_gs"] = $onboarding_gs;
            $fields["onboarding_sp"] = $onboarding_sp;
        }

        $fields["site_name"] = get_bloginfo("name");

        $Quick_Links_Obj = new Learn2Learn_Quick_Links($user_id);
        $quick_links = $Quick_Links_Obj->get_quick_links();
        $fields["quick_links"] = $quick_links;

        return new WP_REST_Response( $fields, 200 );

    }

    private function format_options_results_into_assoc_array($results){

        $fields = array();

        if (isset($results) && !empty($results)){

            foreach($results as $record){

                $return_key = str_replace("l2l-", "", $record->option_name);
                $return_key = str_replace("-", "_", $return_key);

                $return_value = $record->option_value;

                if ($return_key == "embedded-lesson-continue-text") { $return_value = esc_html($record->option_value); }
                if ($return_key == "personalisation_image"){ $return_value = wp_get_attachment_image_url($record->option_value, "full"); }

                $fields[$return_key] = $return_value;

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

        return (get_user_meta( $user_id, 'l2l_quesitonnaire_launched' , true ) ? true : false);

    }

    public function set_questionnaire_already_launched($request){

        $user_id = intval($request["user_id"]);

        $already_launched = $this->get_questionnaire_already_launched($user_id);

        if (!$already_launched){

            $meta_id = add_user_meta( $user_id, 'l2l_quesitonnaire_launched', true, true );

            $already_launched = ($meta_id ? true : false);

        } else {

            $already_launched = true;

        }

        return new WP_REST_Response( $already_launched , 200 );

    }

    private function get_onboarding_learn2learn($user_id, $onboarding_key_name){

        return (get_user_meta($user_id, $onboarding_key_name, true) ? true : false);

    }

    private function update_user_meta_for_onboarding($user_id, $onboarding_key_name){

        $onboarding_launched = $this->get_onboarding_learn2learn($user_id, $onboarding_key_name);

        if (!$onboarding_launched){

            $meta_id = add_user_meta($user_id, $onboarding_key_name, true, true);

            $onboarding_launched = ($meta_id ? true : false);

        } else {

            $onboarding_launched = true;

        }

        return $onboarding_launched;

    }

    public function set_onboarding_learn2learn($request){

        $user_id = intval($request["user_id"]);
        $onboarding = strval(sanitize_text_field($request["onboarding"]));

        switch($onboarding){

            case "learn2learn":
                $onboarding_key_name = "l2l_onboarding";
                break;

            case "goal-setting":
                $onboarding_key_name = "l2l_onboarding_gs";
                break;

            case "study-planner":
                $onboarding_key_name = "l2l_onboarding_sp";
                break;

        }

        if (!$onboarding_key_name) return "INVALID Onboarding Value";

        $valid_key_names = array("l2l_onboarding", "l2l_onboarding_gs", "l2l_onboarding_sp");
        $is_valid_key_name = in_array($onboarding_key_name, $valid_key_names);

        if (!$is_valid_key_name) return "INVALID Onboarding Key";

        $onboarding_launched = $this->update_user_meta_for_onboarding($user_id, $onboarding_key_name);

        return new WP_REST_Response( $onboarding_launched, 200 );

    }

}