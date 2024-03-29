<?php
class Learn2Learn_Userprogress extends Learn2Learn_Database {

    private $username;
    
    function __construct($username = null){

        parent::__construct();

        $this->username = $username;

        date_default_timezone_set('Australia/Sydney');

    }

    public function get_user_progress(){

        return $this->select_from_table($this->content_progress_table, array("user_id" => "%s"), array($this->username));

    }

    public function get_user_progress_by_progress_id($progress_id){

        return $this->select_from_table($this->content_progress_table, array("progress_id" => "%d"), array($progress_id));

    }

    public function get_user_progress_by_page_id($page_id){

        return $this->select_user_progress_record($this->username, $page_id);

    }

    public function add_new_user_progress_by_page_id($content_id){

        /*
        **  Add new user record
        **  If already exists and progress is 0, then update the record. i.e. Change progress to 1
        **  If already exists and progress is 1, then return true
        */

        $exists = $this->get_user_progress_by_page_id($content_id);

        // If user record exists.
        if($exists){

            // If their progress is marked as incomplete, then structure where clause for a DB update
            if ($exists->progress === 0){

                $progress_id = intval($exists->progress_id);
                $where_clause = array(
                    "progress_id" => $progress_id
                );

            } else {

                // Else, if their progress ID is set as complete, return with true

                return true;

            }
            
        }

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

        // If progress ID is set, then update the record
        if ($progress_id){

            $success = $this->db->update($this->content_progress_table, $table_data, $where_clause, $data_format);

        } else {

            // Else, insert new record

            $success = $this->db->insert($this->content_progress_table, $table_data, $data_format);

        }

        return ($success ? true : false);

    }


}