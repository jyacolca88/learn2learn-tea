<?php

class Thumbs_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'thumbs';


        register_rest_route( $namespace, '/' . $resource_name, array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_thumbs'),
                'permissions_callback'  => array ( $this, 'get_thumbs_permissions_check' ),
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, $resource_name . '/(?P<id>[\d]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_thumb'),
                'permissions_callback'  => array ( $this, 'get_thumb_permissions_check' )
            )

        ));

    }

    public function get_thumbs( $request ){

        // $L2l_Content_Items = new Learn2Learn_Content_Items("85daa4da50ba3931755b1960bf8f1083");
        // $lessons = $L2l_Content_Items->get_map_items();

        $L2l_Thumbs = new Learn2Learn_Thumbs("85daa4da50ba3931755b1960bf8f1083");
        return $L2l_Thumbs->get_thumb_for_page();

        // return new WP_REST_Response( $lessons, 200 );

    }

    public function get_thumbs_permissions_check( $request ){

        return '__return_true';

    }

    public function get_thumb( $request ){

        $lesson_id = (int) $request['id'];

        $L2L_Lessons = new Learn2Learn_Lessons( $lesson_id, "85daa4da50ba3931755b1960bf8f1083" );
        $lesson_data = $L2L_Lessons->get_thumb_data( $lesson_id );

        return new WP_REST_Response( $lesson_data, 200 );

    }

    public function get_thumb_permissions_check( $request ){

        return '__return_true';

    }

}