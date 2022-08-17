<?php

class Categories_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'categories';


        register_rest_route( $namespace, '/' . $resource_name, array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_categories'),
                'permission_callback'  => array ( $this, 'get_categories_permissions_check' ),
                'args'                  => array ()
            )

        ));

    }

    public function get_categories( $request ){

        $L2l_Content_Items = new Learn2Learn_Content_Items("85daa4da50ba3931755b1960bf8f1083");
        $categories = $L2l_Content_Items->get_home_items();

        return new WP_REST_Response( $categories, 200 );

    }

    public function get_categories_permissions_check( $request ){

        return '__return_true';

    }

}