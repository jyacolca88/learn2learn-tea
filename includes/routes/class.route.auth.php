<?php

class Auth_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'auth';


        register_rest_route( $namespace, '/' . $resource_name, array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'authorise'),
                'permissions_callback'  => array ( $this, 'authorise_permissions_check' ),
                'args'                  => array ()
            )

        ));

    }

    public function authorise( $request ){

        $uid = "28ba6aa6d6b8e8562dfc0fc62248ceff";
        $pass = "eccbc87e4b5ce2fe28308fd9f2a7baf3";
        $key = "574cde5d2a552ca933e93d3a827546e0";

        $L2l_Auth= new Learn2Learn_Auth($uid, $pass, $key);
        $token_data = $L2l_Auth->authenticate();
        

        return new WP_REST_Response( $token_data, 200 );

    }

    public function authorise_permissions_check( $request ){

        return '__return_true';

    }

}