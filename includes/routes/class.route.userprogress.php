<?php

class Userprogress_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'userprogress';

        register_rest_route( $namespace, $resource_name . '/(?P<id>[\d]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_userprogress'),
                'permission_callback'  => array ( $this, 'get_userprogress_permissions_check' )
            )

        ));

        register_rest_route( $namespace, $resource_name . '/page/(?P<page_id>[\d]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_userprogress_for_page'),
                'permission_callback'  => array ( $this, 'get_userprogress_for_page_permissions_check' )
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

    public function get_userprogress( $request ){

        // $thumb_id = (int) $request['id'];

        // $L2l_Thumbs = new Learn2Learn_Thumbs("85daa4da50ba3931755b1960bf8f1083");
        // $thumb_data = $L2l_Thumbs->get_thumb_by_id($thumb_id);

        // return new WP_REST_Response( $thumb_data, 200 );

        // $tags = get_the_terms(838, "topic");

        $locations = get_nav_menu_locations();
        $object = wp_get_nav_menu_object( $locations["journey-map-questions"] );
        $menu = wp_get_nav_menu_items( $object->name );

        return new WP_REST_Response( $menu, 200 );

    }

    public function get_thumb_permissions_check( $request ){

        return '__return_true';

    }

    public function get_userprogress_for_page( $request ){

        // $page_id = (int) $request['page_id'];

        // $L2l_Thumbs = new Learn2Learn_Thumbs("85daa4da50ba3931755b1960bf8f1083");
        // $thumb_data = $L2l_Thumbs->get_user_thumb_by_page_id($page_id);

        // return new WP_REST_Response( $thumb_data, 200 );

    }

    public function get_userprogress_for_page_permissions_check( $request ){

        return '__return_true';

    }

    public function add_new_user_progress_for_page( $request ){

        $post_data = $request->get_params();

        return new WP_REST_Response( $post_data, 200 );

    }

    public function add_new_user_progress_for_page_permissions_check(){

        return current_user_can( 'read' );

    }

}