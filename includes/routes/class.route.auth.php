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
            "Content-type: application/json; charset=UTF-8",
            "Accept-language: en"
        ];

        $ch = curl_init();

        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true
        ]);

        $postData = [
            "username" => "Johnny",
            "password" => "mT4knYdHbPDOPcbXXDYQLok"
        ];

        curl_setopt($ch, CURLOPT_URL, $rest_url . "/token");
        curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($ch);

        $status_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        curl_close($ch);

        $data = json_decode($response, true);

        if ($status_code === 422){
            echo "Invalid data<br/>";
            print_r($data["errors"]);
            exit;
        }

        if ($status_code !== 200){
            echo "Unexpected status code: $status_code<br />";
            print_r($data);
            exit;    
        }

        return new WP_REST_Response( $data, 200 );

    }

    public function authorise_permissions_check( $request ){

        return '__return_true';

    }

}