<?php
class Learn2Learn_Userprogress extends Learn2Learn_Database {

    private $username;
    
    function __construct($username = null){

        parent::__construct();

        $this->username = $username;

    }

    public function get_user_progress_by_page_id($page_id){

        return $this->select_user_progress_record($this->username, $page_id);

    }


}