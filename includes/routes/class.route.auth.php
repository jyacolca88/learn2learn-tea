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

        // $L2l_Content_Items = new Learn2Learn_Content_Items("85daa4da50ba3931755b1960bf8f1083");
        // $content_items = $L2l_Content_Items->get_content_items();
        $rest_url = get_rest_url(null, "/jwt-auth/v1");
        $message = "Auth Route. JWT EndPoint: " . $rest_url;

        $headers = [
            
        ];

        $ch = curl_init($rest_url);

        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true
        ]);

        $postData = [
            "username" => "admin",
            "password" => "12345"
        ];

        curl_setopt($ch, CURLOPT_URL, $rest_url . "/token");
        curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($response, true);

        var_dump($data);

        // return new WP_REST_Response( $message, 200 );

    }

    public function authorise_permissions_check( $request ){

        return '__return_true';

    }

}