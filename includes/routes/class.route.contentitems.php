<?php

class Contentitems_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'contentitems';


        register_rest_route( $namespace, '/' . $resource_name . '/(?P<username>[\w]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_content_items'),
                'permission_callback'  => array ( $this, 'get_content_items_permissions_check' ),
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, '/' . $resource_name . '/overallprogress/(?P<username>[\w]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_overall_progress'),
                'permission_callback'  => array ( $this, 'get_overall_progress_permissions_check' ),
                'args'                  => array ()
            )

        ));

    }

    public function get_content_items( $request ){

        $username = sanitize_text_field($request["username"]);
        $L2l_Content_Items = new Learn2Learn_Content_Items($username);
        $content_items = $L2l_Content_Items->get_content_items();

        return new WP_REST_Response( $content_items, 200 );

    }

    public function get_content_items_permissions_check( $request ){

        return current_user_can( 'read' );

    }

    public function get_overall_progress($request){

        $username = sanitize_text_field($request["username"]);
        $L2l_Content_Items = new Learn2Learn_Content_Items($username);
        $overall_progress = $L2l_Content_Items->get_overall_progress();

        return new WP_REST_Response( $overall_progress, 200 );

    }

    public function get_overall_progress_permissions_check( $request ){

        return current_user_can( 'read' );

    }

}