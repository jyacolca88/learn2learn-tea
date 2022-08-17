<?php

class Contentitems_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'contentitems';


        register_rest_route( $namespace, '/' . $resource_name, array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_content_items'),
                'permission_callback'  => array ( $this, 'get_content_items_permissions_check' ),
                'args'                  => array ()
            )

        ));

    }

    public function get_content_items( $request ){

        $L2l_Content_Items = new Learn2Learn_Content_Items("85daa4da50ba3931755b1960bf8f1083");
        $content_items = $L2l_Content_Items->get_content_items();

        return new WP_REST_Response( $content_items, 200 );

    }

    public function get_content_items_permissions_check( $request ){

        return '__return_true';

    }

}