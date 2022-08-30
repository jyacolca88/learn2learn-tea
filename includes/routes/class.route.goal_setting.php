<?php

class Goal_Setting_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'goalsetting';


        register_rest_route( $namespace, '/' . $resource_name . '/(?P<username>[\w]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_goals'),
                'permission_callback'  => array ( $this, 'get_goals_permissions_check' ),
                'args'                  => array ()
            )

        ));

    }

    public function get_goals( $request ){

        $username = sanitize_text_field($request["username"]);
        $L2l_Goal_Setting = new Learn2Learn_Goal_Setting($username);
        $goals = $L2l_Goal_Setting->get_goals();

        return new WP_REST_Response( $goals, 200 );

    }

    public function get_goals_permissions_check( $request ){

        return '__return_true';

    }

}