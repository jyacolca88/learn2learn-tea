<?php
class Learn2Learn_Thumbs extends Learn2Learn_Database {

    private $user_id;

    private $where_format = array();
    private $where_values = array();
    private $columns = null;
    
    function __construct($username = null){

        parent::__construct();

        $this->user_id = $username;

    }

    public function get_all_thumbs_by_username(){

        $this->where_format = array("user_id" => "%s");
        $this->where_values = array($this->user_id);

        return $this->run_select_query();

    }

    public function get_thumb_by_id($thumb_id){

        $thumb_id = intval($thumb_id);

        $this->where_format = array("thumb_id" => "%d");
        $this->where_values = array($thumb_id);

        return $this->run_select_query();

    }

    public function get_user_thumb_by_page_id($page_id){

        if (is_null($this->user_id))
            return;

        $page_id = intval($page_id);

        $this->where_format = array ("page_id" => "%d", "user_id" => "%s");
        $this->where_values = array($page_id, $this->user_id);

        return $this->run_select_query();

    }

    private function run_select_query(){

        return $this->select_from_table(
            $this->thumbs_table,
            $this->where_format,
            $this->where_values,
            $this->columns
        );

    }

    /******************** INSERT OR UPDATE USER THUMBS [BEGIN] ********************/

    public function insert_or_update_user_thumbs($content_id, $thumbs){

        $table_data = array (
            'user_id' => strval(sanitize_text_field($this->user_id)),
            'page_id' => intval(sanitize_text_field($content_id)),
            'thumbs' => strval(sanitize_text_field($thumbs))
        );

        $data_format = array('%s', '%d', '%s');

        if ($db_record = $this->get_user_thumb_by_page_id($content_id)){

            if (is_array($db_record)){
                reset($db_record);
            }

            $where_clause = array(
                "thumb_id" => $db_record->thumb_id
            );

            return $where_clause;

            $success = $this->db->update( $this->thumbs_table, $table_data, $where_clause, $data_format );

        } else {

            $success = $this->db->insert( $this->thumbs_table, $table_data, $data_format );

        }

        return $success;

    }

    /******************** INSERT OR UPDATE USER THUMBS [END] **********************/

}