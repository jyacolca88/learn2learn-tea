<?php
class Learn2Learn_Database {

    protected $db;
    protected $prefix;
    protected $content_progress_table;
    protected $survey_answers_table;
    protected $thumbs_table;

    function __construct(){

        global $wpdb;
        $this->db = $wpdb;
        $this->prefix = $wpdb->prefix;

        $this->content_progress_table = $this->prefix . "user_content_progress";
        $this->survey_answers_table = $this->prefix . "user_survey_answers";
        $this->thumbs_table = $this->prefix . "user_thumbs";

    }

    /**
     * 
     * @table = DB table name without the prefix
     * @where_format = associated array field_name => format (i.e. %s or %d)
     * @where_value = array of values related to where_format
     * @columns = array of column names. If null, then Selects all columns
     * 
     */

    public function select_columns_by_ids_from_table($table_without_prefix = null, $where_format = array(), $where_values = array(), $columns = null){

        if (is_null($table_without_prefix))
            return;

        $table = $this->prefix . $table_without_prefix;

        return $this->select_from_table($table, $where_format, $where_values, $columns);

    }

    protected function select_from_table($table = null, $where_format = array(), $where_values = array(), $columns = null, $array_output = false){

        if (is_null($table))
            return;

        $select = $this->get_sanitized_select($columns);

        $where = (!empty($where_format) && !empty($where_values) ? $this->get_where_clause($where_format) : "");

        $query = "
            SELECT {$select}
            FROM $table 
            $where
        ";

        if ($array_output){
            return $this->db->get_results($this->db->prepare($query, $where_values), ARRAY_A);
        } else {
            return $this->db->get_results($this->db->prepare($query, $where_values));
        }

    }

    private function get_sanitized_select($columns){

        $select = "*";

        if (!is_null($columns) && is_array($columns) && !empty($columns)){

            $sanitized_columns = array();

            foreach($columns as $column){

                array_push($sanitized_columns, filter_var($column, FILTER_SANITIZE_STRING));

            }

            $select = implode(",", $sanitized_columns);

        }

        return $select;

    }

    private function get_where_clause($where_format){

        $where = "";

        if (!empty($where_format)){

            $where = "WHERE ";
            $i = 0;

            foreach($where_format as $col => $format){

                if ($i > 0){
                    $where .= " AND ";
                }

                $where .= "$col = $format";
                $i++;

            }

        }

        return $where;

    }

    /******************** SELECT ALL USER PROGRESS RECORDS [BEGIN] ********************/
    public function select_all_user_progress_records($user_id, $page_id = null, $progress = null, $overall = false){

        if ( $overall ){

            $category_ids = lf_l2l_get_category_ids();

            if ( empty($category_ids) )
                return;

            $category_ids = implode(",", $category_ids);

            $query = "
                SELECT * 
                FROM {$this->content_progress_table} 
                WHERE user_id = %s 
                AND progress = %d 
                AND page_id IN ( {$category_ids} )
            ";

        } else {

            $query = 
                "
                SELECT * 
                FROM {$this->content_progress_table} 
                WHERE user_id = %s
                " 
                . (!is_null($page_id) ? " AND page_id = %d" : "") 
                . (!is_null($progress) ? " AND progress = %d" : "");

        }

        $values = array($user_id);

        if (!is_null($page_id))
            array_push($values, $page_id);

        if (!is_null($progress))
            array_push($values, $progress);

        return $this->db->get_results(

            $this->db->prepare($query, $values)

        );

    }

    /******************** SELECT USER PROGRESS RECORD [BEGIN] ********************/
    public function select_user_progress_record($username, $content_id, $user_id = null){

        // if user_id is passed, get username
        if ($user_id){ $username = $this->get_username_by_user_id($user_id); }

        return $this->db->get_row(

            $this->db->prepare("
                SELECT * FROM {$this->content_progress_table} WHERE user_id = %s AND content_id = %d
                ", $username, $content_id )

        );

    }

    /******************** SELECT USER PROGRESS RECORDS IN (ARRAY) [BEGIN] ********************/
    public function select_user_progress_records_in($username, $content_ids, $user_id = null){

        return $this->select_user_records_in($username, $content_ids, $user_id, $this->content_progress_table);

        // if user_id is passed, get username
        if ($user_id){ $username = $this->get_username_by_user_id($user_id); }

        // Add int placeholders to array depending on number of items in content_ids
        $in_str_arr = array_fill( 0, count( $content_ids ), '%d' );

        // Conver array to string with separating ','
        $in_str = join( ',', $in_str_arr );

        // Store content_ids into SQL values
        $sql_values = $content_ids;

        // Add username to the beginning of the array
        array_unshift($sql_values, $username);

        return $this->db->get_results(

            $this->db->prepare("
                SELECT * FROM {$this->content_progress_table} 
                WHERE user_id = %s AND content_id IN ({$in_str})
                ", $sql_values )

        );

    }

    public function select_user_thumbs_records_in($username, $content_ids, $user_id = null){

        return $this->select_user_records_in($username, $content_ids, $user_id, $this->thumbs_table);

    }

    public function select_user_records_in($username, $content_ids, $user_id = null, $table){

        // if user_id is passed, get username
        if ($user_id){ $username = $this->get_username_by_user_id($user_id); }

        // Add int placeholders to array depending on number of items in content_ids
        $in_str_arr = array_fill( 0, count( $content_ids ), '%d' );

        // Conver array to string with separating ','
        $in_str = join( ',', $in_str_arr );

        // Store content_ids into SQL values
        $sql_values = $content_ids;

        // Add username to the beginning of the array
        array_unshift($sql_values, $username);

        $sql_statement = "
            SELECT * FROM {$table} 
            WHERE user_id = %s AND 
        ";

        if ($table == $this->content_progress_table){
            $sql_statement .= "content_id IN ({$in_str})";
        } else if ($table == $this->thumbs_table){
            $sql_statement .= "page_id IN ({$in_str})";
        }

        return $this->db->get_results(

            $this->db->prepare($sql_statement, $sql_values )

        );

    }

    public function select_user_thumbs_for_lesson($username, $lesson_id, $user_id = null){

        // if user_id is passed, get username
        if ($user_id){ $username = $this->get_username_by_user_id($user_id); }

        //$table = null, $where_format = array(), $where_values = array(), $columns = null, $array_output = false
        return $this->select_from_table($this->thumbs_table, array("user_id" => "%s", "page_id" => "%d"), array($username, $lesson_id), array("thumbs"));

    }

    private function get_username_by_user_id($user_id){

        $user_id = intval($user_id);
        $user = get_user_by('ID', $user_id);
        return $user->user_login;

    }

}