<?php
class Learn2Learn_Userprogress extends Learn2Learn_Database {

    private $username;
    
    function __construct($username = null){

        parent::__construct();

        $this->username = $username;

        date_default_timezone_set('Australia/Sydney');

    }

    public function get_user_progress_by_page_id($page_id){

        return $this->select_user_progress_record($this->username, $page_id);

    }

    public function add_new_user_progress_by_page_id($content_id){

        $exists = $this->select_user_progress_record($this->username, $content_id);
        if($exists){ return $exists; } 

        $user_id = $this->username;
        $content_id = intval($content_id);
        $page_id = intval(wp_get_post_parent_id($content_id));
        $progress = 1;
        $time = current_time('mysql');

        $table_data = array (
            'user_id' => $user_id,
            'content_id' => $content_id,
            'page_id' => $page_id,
            'progress' => $progress,
            'time' => $time
        );

        $data_format = array('%s', '%d', '%d', '%d', '%s');

        $success = $this->db->insert($this->content_progress_table, $table_data, $data_format);

        return ($success ? true : false);

    }


}