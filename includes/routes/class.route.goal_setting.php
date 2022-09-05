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

        register_rest_route( $namespace, '/' . $resource_name . '/goal/(?P<username>[\w]+)' . '/(?P<goal_id>[\d]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_goal'),
                'permission_callback'  => array ( $this, 'get_goal_permissions_check' ),
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, '/' . $resource_name . '/goal/', array(

            array(
                'methods'               => 'POST',
                'callback'              => array ( $this, 'post_goal'),
                'permission_callback'  => array ( $this, 'post_goal_permissions_check' ),
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, '/' . $resource_name . '/goal/', array(

            array(
                'methods'               => 'PUT',
                'callback'              => array ( $this, 'put_goal'),
                'permission_callback'  => array ( $this, 'put_goal_permissions_check' ),
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, '/' . $resource_name . '/goal/', array(

            array(
                'methods'               => WP_REST_Server::DELETABLE,
                'callback'              => array ( $this, 'delete_goal'),
                'permission_callback'  => array ( $this, 'delete_goal_permissions_check' ),
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, '/' . $resource_name . '/goals-test/(?P<username>[\w]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_goals_test'),
                'permission_callback'  => array ( $this, 'get_goals_test_permissions_check' ),
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

        return current_user_can( 'read' );

    }

    public function get_goal( $request ){

        $username = sanitize_text_field($request["username"]);
        $goal_id = intval($request["goal_id"]);

        $L2l_Goal_Setting = new Learn2Learn_Goal_Setting($username);
        $goal = $L2l_Goal_Setting->get_goal($goal_id);

        return new WP_REST_Response( $goal, 200 );

    }

    public function get_goal_permissions_check( $request ){

        return current_user_can( 'read' );

    }

    public function post_goal( $request ){

        $post_data = $request->get_params();

        $username = sanitize_text_field($post_data["username"]);

        $L2l_Goal_Setting = new Learn2Learn_Goal_Setting($username);

        $new_goal = $post_data["goal"];

        $goal = $L2l_Goal_Setting->add_new_goal($new_goal);

        return new WP_REST_Response( $goal, 200 );

    }

    public function post_goal_permissions_check(){

        return current_user_can( 'read' );

    }

    public function put_goal ( $request ){

        $post_data = $request->get_params();

        $username = sanitize_text_field($post_data["username"]);
        $goal_update_data = $post_data["goal"];

        $L2l_Goal_Setting = new Learn2Learn_Goal_Setting($username);

        $updated_goal = $L2l_Goal_Setting->update_goal($goal_update_data);

        return new WP_REST_Response( $updated_goal, 200 );

    }

    public function put_goal_permissions_check (){

        return current_user_can( 'read' );

    }

    public function delete_goal( $request ){

        $post_data = $request->get_params();

        $username = sanitize_text_field($post_data["username"]);
        $goal_id = intval($post_data["goal_id"]);

        $L2l_Goal_Setting = new Learn2Learn_Goal_Setting($username);

        $goal_deleted_status = $L2l_Goal_Setting->delete_goal($goal_id);

        return new WP_REST_Response( $goal_deleted_status, 200 );

    }

    public function delete_goal_permissions_check(){

        return current_user_can( 'read' );

    }

    public function get_goals_test( $request ){

        $username = sanitize_text_field($request["username"]);

        $L2l_Goal_Setting = new Learn2Learn_Goal_Setting($username);
        $goals = $L2l_Goal_Setting->get_all_goals_by_user();

        return new WP_REST_Response( $goals, 200 );

    }

    public function get_goals_test_permissions_check(){

        return '__return_true';

    }

}