<?php
class Learn2Learn_Userprogress {

    private $user_id;
    private $db;
    private $db_table = "user_content_progress";

    private $where_format = array();
    private $where_values = array();
    private $columns = null;
    
    function __construct($user_id = null){

        $this->user_id = $user_id;
        $this->db = new Learn2Learn_Database();

    }

    public function get_userprogress_by_id($progress_id){

        $progress_id = intval($progress_id);

        $this->where_format = array("progress_id" => "%d");
        $this->where_values = array($progress_id);

        return $this->run_select_query();

    }

    public function get_userprogress_by_page_id($page_id){

        if (is_null($this->user_id))
            return;

        $page_id = intval($page_id);

        $this->where_format = array ("page_id" => "%d", "user_id" => "%s");
        $this->where_values = array($page_id, $this->user_id);

        return $this->run_select_query();

    }

    private function run_select_query(){

        return $this->db->select_columns_by_ids_from_table(
            $this->db_table,
            $this->where_format,
            $this->where_values,
            $this->columns
        );

        // print_r($thumbs);

        return $thumbs;

    }


}