<?php
class Learn2Learn_Auth{

    private $user_id;
    private $lms_user_id;
    private $username;
    private $password;
    private $key;
    private $jwt_auth_rest_url;
    private $jwt_auth_response;
    private $error_response;
    
    function __construct($lms_user_id = null, $password = null, $key = null){

        $this->lms_user_id = (is_null($lms_user_id) ? $this->fetch_id() : $lms_user_id);
        $this->password = (is_null($password) ? $this->fetch_password() : $password);
        $this->key = (is_null($key) ? $this->fetch_key() : $key);

        $this->jwt_auth_rest_url = get_rest_url(null, "/jwt-auth/v1");

        $this->validate_key();
        $this->generate_username();

    }

    public function authenticate(){

        // If username or password is not set, exit
        if (!$this->username || !$this->password) return;

        // If username does NOT exist
        if (!username_exists($this->username)){

            // Create user and set password
            $user_id =  wp_create_user($this->username, $this->password);

        }

        // Use JWT Authentication with cURL
        $this->curl_jwt_authentication();

        // Return JWT Auth Response
        return $this->jwt_auth_response;

    }

    private function fetch_id(){

        if (isset($_GET["uid"]) && !empty($_GET["uid"])){ 
            $uid = sanitize_title(urldecode($_GET['uid']));
        } else if (isset($_POST["uid"]) && !empty($_POST["uid"])){ 
            $uid = sanitize_title(urldecode($_POST['uid']));
        } else if (is_user_logged_in() && current_user_can( 'edit_pages' )){
            $uid = get_current_user_id();
        } else {
            $uid = null;
        }

        return $uid;

    }

    private function fetch_password(){

        if (isset($_GET["pass"]) && !empty($_GET["pass"])){
            return sanitize_text_field(urldecode($_GET['pass']));
        } else if (isset($_POST["pass"]) && !empty($_POST["pass"])){
            return sanitize_text_field(urldecode($_POST['pass']));
        }

    }

    private function fetch_key(){

        if (isset($_GET["key"]) && !empty($_GET["key"])){
            return sanitize_text_field(urldecode($_GET['key']));
        } else if (isset($_POST["key"]) && !empty($_POST["key"])){
            return sanitize_text_field(urldecode($_POST['key']));
        }

    }

    private function validate_key(){

        if ((is_user_logged_in() && current_user_can( 'edit_pages' )) || is_null($this->lms_user_id))
            return;

        $uid = $this->lms_user_id;

        date_default_timezone_set('UTC');

        $first = substr($uid, 0, 2);
        $last = substr($uid, -2);

        $code = $first . "20210222" . $last . date("j");
        $key = md5($code);
        
        date_default_timezone_set('Australia/Sydney');

        if ($key !== $this->key){
            
            $this->user_id = null;
            $this->lms_user_id = null;
            $this->username = null;
            $this->password = null;
            $this->key = null;

            $this->error_response = [
                "error_message" => "Session has expired"
            ];

        }

    }

    private function generate_username(){

        $this->username = ($this->lms_user_id ? wp_hash("l2l" . $this->lms_user_id . "2021") : null);

    }

    private function curl_jwt_authentication(){

        // If username or password is not set, exit
        if (!$this->username || !$this->password) return;

        // Set up token URL (JWT Authentication for WP REST API)
        $token_url = $this->jwt_auth_rest_url . "/token";

        // Set up headers
        $headers = [
            "Content-type: application/json; charset=UTF-8",
            "Accept-language: en"
        ];

        // Set up payload
        $payload = [
            "username" => $this->username,
            "password" => $this->password
        ];

        // Initialise cURL
        $ch = curl_init();

        // Set up cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $token_url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload)
        ]);

        // Store response from cURL
        $response = curl_exec($ch);

        // Get status code response
        $status_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        // Close cURL
        curl_close($ch);

        // Store JSON decoded response
        $data = json_decode($response, true);

        // If status code is invalid, print errors
        if ($status_code === 422){
            echo "Invalid data<br/>";
            print_r($data["errors"]);
            exit;
        }

        // If status code does not return OK, print data
        if ($status_code !== 200){
            echo "Unexpected status code: $status_code<br />";
            print_r($data);
            exit;
        }

        // Set array data of cURL response
        $this->jwt_auth_response = $data;

    }

}