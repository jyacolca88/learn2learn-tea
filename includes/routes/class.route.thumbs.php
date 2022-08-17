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
                'permission_callback'  => array ( $this, 'get_thumbs_permissions_check' ),
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, $resource_name . '/(?P<id>[\d]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_thumb'),
                'permission_callback'  => array ( $this, 'get_thumb_permissions_check' )
            )

        ));

        register_rest_route( $namespace, $resource_name . '/page/(?P<page_id>[\d]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_thumb_for_page'),
                'permission_callback'  => array ( $this, 'get_thumb_for_page_permissions_check' )
            )

        ));

    }

    public function get_thumbs( $request ){

        $L2l_Thumbs = new Learn2Learn_Thumbs("85daa4da50ba3931755b1960bf8f1083");
        $thumbs = $L2l_Thumbs->get_all_thumbs();
        // return $L2l_Thumbs->get_thumb_by_id(5);
        // return $L2l_Thumbs->get_user_thumb_by_page_id(31);

        return new WP_REST_Response( $thumbs, 200 );

    }

    public function get_thumbs_permissions_check( $request ){

        return '__return_true';

    }

    public function get_thumb( $request ){

        $thumb_id = (int) $request['id'];

        $L2l_Thumbs = new Learn2Learn_Thumbs("85daa4da50ba3931755b1960bf8f1083");
        $thumb_data = $L2l_Thumbs->get_thumb_by_id($thumb_id);

        return new WP_REST_Response( $thumb_data, 200 );

    }

    public function get_thumb_permissions_check( $request ){

        return '__return_true';

    }

    public function get_thumb_for_page( $request ){

        $page_id = (int) $request['page_id'];

        $L2l_Thumbs = new Learn2Learn_Thumbs("85daa4da50ba3931755b1960bf8f1083");
        $thumb_data = $L2l_Thumbs->get_user_thumb_by_page_id($page_id);

        return new WP_REST_Response( $thumb_data, 200 );

    }

    public function get_thumb_for_page_permissions_check( $request ){

        return '__return_true';

    }

}