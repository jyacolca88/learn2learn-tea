<?php
class Learn2Learn_Database {

    private $db;
    private $prefix;
    private $content_progress_table;
    private $survey_answers_table;
    private $thumbs_table;

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

    public function select_columns_by_ids_from_table($table = null, $where_format = array(), $where_values = array(), $columns = null){

        if (is_null($table))
            return;

        $table = $this->prefix . $table;

        $select = $this->get_sanitized_select($columns);

        $where = (!empty($where_format) && !empty($where_values) ? $this->get_where_clause($where_format) : "");

        $query = "
            SELECT {$select}
            FROM $table 
            $where
        ";

        // print_r($query);

        return $this->db->get_results(

            $this->db->prepare($query, $where_values)

        );

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
    public function select_user_progress_record($user_id, $content_id){

        return $this->db->get_row(

            $this->db->prepare("
                SELECT * FROM {$this->content_progress_table} WHERE user_id = %s AND content_id = %d
                ", $user_id, $content_id )

        );

    }

    /******************** SELECT USER PROGRESS RECORDS IN (ARRAY) [BEGIN] ********************/
    public function select_user_progress_records_in($user_id, $content_ids){

        // Add int placeholders to array depending on number of items in content_ids
        $in_str_arr = array_fill( 0, count( $content_ids ), '%d' );

        // Conver array to string with separating ','
        $in_str = join( ',', $in_str_arr );

        // Store content_ids into SQL values
        $sql_values = $content_ids;

        // Add user id to the beginning of the array
        array_unshift($sql_values, $user_id);

        return $this->db->get_results(

            $this->db->prepare("
                SELECT * FROM {$this->content_progress_table} 
                WHERE user_id = %s AND content_id IN ({$in_str})
                ", $sql_values )

        );

    }

}