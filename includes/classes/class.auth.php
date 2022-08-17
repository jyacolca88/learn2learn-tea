<?php
class Learn2Learn_Auth{

    private $user_id;
    private $lms_user_id;
    private $username;
    private $password;
    private $key;
    
    function __construct($lms_user_id = null, $password = null, $key = null){

        $this->lms_user_id = (is_null($lms_user_id) ? $this->fetch_id() : $lms_user_id);
        $this->password = (is_null($password) ? $this->fetch_password() : $password);
        $this->key = (is_null($key) ? $this->fetch_key() : $key);

        $this->validate_key();
        $this->generate_username();

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

        }

    }

    private function generate_username(){

        $this->username = ($this->lms_user_id ? wp_hash("l2l" . $this->lms_user_id . "2021") : null);

    }

    public function get_username(){

        return $this->username;
        // Check if username exists
        // If exists, check password, and login
        // If username does NOT exist
        // Create user, set password, and login

    }


}