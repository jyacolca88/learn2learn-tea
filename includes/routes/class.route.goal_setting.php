<?php

class Goal_Setting_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'goalsetting';


        register_rest_route( $namespace, '/' . $resource_name . '/goals/(?P<username>[\w]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_goals'),
                'permission_callback'  => array ( $this, 'get_goals_permissions_check' ),
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, '/' . $resource_name . '/goal/', array(

            array(
                'methods'               => WP_REST_Server::CREATABLE,
                'callback'              => array ( $this, 'post_goal'),
                'permission_callback'  => array ( $this, 'post_goal_permissions_check' ),
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

    public function post_goal( $request ){

        $post_data = $request->get_params();

        $username = sanitize_text_field($post_data["username"]);

        $L2l_Goal_Setting = new Learn2Learn_Goal_Setting($username);

        // TESTING ADD goal
        // $new_goal = array(
        //     "goal_title" => "New Goal HERE!",
        //     "goal_completed_by" => "2022-08-31",
        //     "steps" => array(
        //         array(
        //             "step_title" => "New Step Here, Step 1",
        //             "step_completed_by" => "2022-08-31",
        //             "step_order" => 0
        //         ),
        //         array(
        //             "step_title" => "New Step Here, Step 2",
        //             "step_completed_by" => "2022-08-31",
        //             "step_order" => 1
        //         ),
        //         array(
        //             "step_title" => "New Step Here, Step 3",
        //             "step_completed_by" => "2022-08-31",
        //             "step_order" => 2
        //         )
        //     )
        // );

        $new_goal = $post_data["goal"];

        $goal = $L2l_Goal_Setting->add_new_goal($new_goal);

        return new WP_REST_Response( $goal, 200 );

    }

    public function post_goal_permissions_check(){

        return current_user_can( 'read' );

    }

}