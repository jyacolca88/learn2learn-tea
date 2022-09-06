<?php

class Userprogress_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'userprogress';

        register_rest_route( $namespace, $resource_name . '/(?P<progress_id>[\d]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_user_progress_by_id'),
                'permission_callback'  => array ( $this, 'get_user_progress_by_id_permissions_check' )
            )

        ));

        register_rest_route( $namespace, $resource_name . '/user/(?P<username>[\w]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_user_progress_by_username'),
                'permission_callback'  => array ( $this, 'get_user_progress_by_username_permissions_check' )
            )

        ));

        register_rest_route( $namespace, $resource_name . '/page/(?P<page_id>[\d]+)' . '/(?P<username>[\w]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_user_progress_for_page'),
                'permission_callback'  => array ( $this, 'get_user_progress_for_page_permissions_check' )
            )

        ));

        register_rest_route( $namespace, $resource_name . '/page', array(

            array(
                'methods'               => WP_REST_Server::CREATABLE,
                'callback'              => array ( $this, 'add_new_user_progress_for_page'),
                'permission_callback'  => array ( $this, 'add_new_user_progress_for_page_permissions_check' )
            )

        ));

    }

    public function get_user_progress_by_id( $request ){

        $username = sanitize_text_field($request["username"]);
        $progress_id = intval($request["progress_id"]);

        $L2L_User_Progress = new Learn2Learn_Userprogress($username);
        $user_progress = $L2L_User_Progress->get_user_progress_by_progress_id($progress_id);

        return new WP_REST_Response( $user_progress, 200 );

    }

    public function get_user_progress_by_id_permissions_check( $request ){

        return current_user_can( 'read' );

    }

    public function get_user_progress_by_username( $request ){

        $username = sanitize_text_field($request["username"]);

        $L2L_User_Progress = new Learn2Learn_Userprogress($username);
        $user_progress = $L2L_User_Progress->get_user_progress();

        return new WP_REST_Response( $user_progress, 200 );

    }

    public function get_user_progress_by_username_permissions_check(){

        return current_user_can( 'read' );

    }

    public function get_user_progress_for_page( $request ){

        $username = sanitize_text_field($request["username"]);
        $page_id = intval($request["page_id"]);

        $L2L_User_Progress = new Learn2Learn_Userprogress($username);
        $user_progress = $L2L_User_Progress->get_user_progress_by_page_id($page_id);

        return new WP_REST_Response( $user_progress, 200 );

    }

    public function get_user_progress_for_page_permissions_check( $request ){

        return current_user_can( 'read' );

    }

    public function add_new_user_progress_for_page( $request ){

        $post_data = $request->get_params();

        $username = sanitize_text_field($post_data["username"]);
        $page_id = intval($post_data["page_id"]);

        $L2L_User_Progress = new Learn2Learn_Userprogress($username);
        $user_progress = $L2L_User_Progress->add_new_user_progress_by_page_id($page_id);

        return new WP_REST_Response( $user_progress, 200 );

    }

    public function add_new_user_progress_for_page_permissions_check(){

        return current_user_can( 'read' );

    }

}