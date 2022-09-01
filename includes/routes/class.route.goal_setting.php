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

        register_rest_route( $namespace, '/' . $resource_name . '/goal/', array(

            array(
                'methods'               => WP_REST_Server::EDITABLE,
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

        return new WP_REST_Response( "delete route here", 200 );

    }

    public function delete_goal_permissions_check(){

        return '__return_true';

    }

}