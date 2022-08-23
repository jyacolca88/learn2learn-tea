<?php

class Auth_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'auth';


        register_rest_route( $namespace, '/' . $resource_name . '/(?P<uid>[\w]+)' . '/(?P<pass>[\w]+)' . '/(?P<key>[\w]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'authorise'),
                'permission_callback'  => array ( $this, 'authorise_permissions_check' ),
                'args'                  => array ()
            )

        ));

    }

    public function authorise( $request ){

        // $uid = "28ba6aa6d6b8e8562dfc0fc62248ceff";
        // $pass = "eccbc87e4b5ce2fe28308fd9f2a7baf3";
        // $key = "1c832d45df63812d44ceb5fb569a287a";

        // http://localhost:3000/launch/28ba6aa6d6b8e8562dfc0fc62248ceff/eccbc87e4b5ce2fe28308fd9f2a7baf3/1c832d45df63812d44ceb5fb569a287a

        $uid = sanitize_text_field($request["uid"]);
        $pass = sanitize_text_field($request["pass"]);
        $key = sanitize_text_field($request["key"]);

        $L2l_Auth= new Learn2Learn_Auth($uid, $pass, $key);
        $token_data = $L2l_Auth->authenticate();

        return new WP_REST_Response( $token_data, 200 );

    }

    public function authorise_permissions_check( $request ){

        return '__return_true';

    }

}