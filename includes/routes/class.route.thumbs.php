<?php

class Thumbs_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'thumbs';


        register_rest_route( $namespace, '/' . $resource_name . '/user' . '/(?P<username>[\w]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_thumbs'),
                'permission_callback'  => array ( $this, 'get_thumbs_permissions_check' ),
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, $resource_name . '/(?P<thumb_id>[\d]+)' . '/(?P<username>[\w]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_thumb'),
                'permission_callback'  => array ( $this, 'get_thumb_permissions_check' )
            )

        ));

        register_rest_route( $namespace, $resource_name . '/page/(?P<page_id>[\d]+)' . '/(?P<username>[\w]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_thumb_for_page'),
                'permission_callback'  => array ( $this, 'get_thumb_for_page_permissions_check' )
            )

        ));

        register_rest_route( $namespace, $resource_name . '/page', array(

            array(
                'methods'               => WP_REST_Server::EDITABLE,
                'callback'              => array ( $this, 'put_thumb_for_page'),
                'permission_callback'  => array ( $this, 'put_thumb_for_page_permissions_check' )
            )

        ));

    }

    public function get_thumbs( $request ){

        $username = sanitize_text_field($request["username"]);

        $L2l_Thumbs = new Learn2Learn_Thumbs($username);
        $thumbs = $L2l_Thumbs->get_all_thumbs_by_username();

        return new WP_REST_Response( $thumbs, 200 );

    }

    public function get_thumbs_permissions_check( $request ){

        return current_user_can( 'read' );

    }

    public function get_thumb( $request ){

        $username = sanitize_text_field($request["username"]);
        $thumb_id = intval($request['thumb_id']);

        $L2l_Thumbs = new Learn2Learn_Thumbs($username);
        $thumb_data = $L2l_Thumbs->get_thumb_by_id($thumb_id);

        return new WP_REST_Response( $thumb_data, 200 );

    }

    public function get_thumb_permissions_check( $request ){

        return current_user_can( 'read' );

    }

    public function get_thumb_for_page( $request ){

        $username = sanitize_text_field($request["username"]);
        $page_id = intval($request['page_id']);

        $L2l_Thumbs = new Learn2Learn_Thumbs($username);
        $thumb_data = $L2l_Thumbs->get_user_thumb_by_page_id($page_id);

        return new WP_REST_Response( $thumb_data, 200 );

    }

    public function get_thumb_for_page_permissions_check( $request ){

        return current_user_can( 'read' );

    }

    public function put_thumb_for_page( $request ){

        $post_data = $request->get_params();

        $user_id = sanitize_text_field($post_data["user_id"]);
        $page_id = intval($post_data['page_id']);
        $thumbs = sanitize_text_field($post_data["thumbs"]);

        $array = array(
            "user_id" => $user_id,
            "page_id" => $page_id,
            "thumbs" => $thumbs
        );

        return new WP_REST_Response( $array, 200 );

    }

    public function put_thumb_for_page_permissions_check(){

        return current_user_can( 'read' );

    }

}