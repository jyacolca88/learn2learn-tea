<?php
class Learn2Learn_Goal_Setting extends Learn2Learn_Database {

    private $username;
    private $goals_table;
    private $steps_table;

    function __construct($username = null){

        parent::__construct();

        $this->username = $username;
        $this->goals_table = $this->prefix . "lfgs_goals";
        $this->steps_table = $this->prefix . "lfgs_steps";

    }

    // Get Goals 
    public function get_goals(){

        // Get Goals and related Steps
        return $this->get_all_goals_by_user();

    }


    // Add new Goal
    public function add_new_goal($goal_data){

        // Format and Sanitize User Input
        $user_data = $this->format_and_sanitize_user_input($goal_data);
        return $user_data;

        // Split Steps from Goals (if applicable)


        // Insert Goal to DB, return Goal ID

    }

    // Update Goal
    public function update_goal($goal_id, $goal_data){



    }

    // Delete Goal
    public function delete_goal($goal_id){



    }

    private function get_all_goals_by_user(){

        $sql = $this->db->prepare("
            SELECT * 
            FROM {$this->goals_table} g
            INNER JOIN {$this->steps_table} s 
            ON g.goal_id = s.goal_id 
            WHERE g.user_id = %s 
            ORDER BY g.goal_id ASC, s.step_order ASC
        ", $this->username);

        $results = $this->db->get_results($sql);

        return $this->optimise_raw_results_into_associative_array($results);

    }

    private function optimise_raw_results_into_associative_array($results){

        if (!is_array($results) || empty($results)) {return;}

        $goals_array = [];
        $steps_array = [];  

        foreach($results as $record){

            if (isset($record->step_id) && !empty($record->step_id)){

                $step_title = stripslashes($this->encrypt_decrypt_string($record->step_title, true));

                $steps_array[$record->goal_id][$record->step_order] = array(
                    "step_id" => $record->step_id,
                    "step_title" => $step_title,
                    "step_completed_by" => $record->step_completed_by,
                    "step_status" => $record->step_status,
                    "step_order" => $record->step_order
                );

            } else {

                $steps_array[$record->goal_id] = false;

            }
            
            $goal_title = stripslashes($this->encrypt_decrypt_string($record->goal_title, true));
            $goal_reflection = (!empty($record->goal_reflection) ? stripslashes($this->encrypt_decrypt_string($record->goal_reflection, true)) : null);

            $goals_array[$record->goal_id] = array(
                "goal_id" => $record->goal_id,
                "goal_title" => $goal_title,
                "goal_completed_by" => $record->goal_completed_by,
                "goal_status" => $record->goal_status,
                "goal_reflection" => $goal_reflection,
                "steps" => $steps_array[$record->goal_id]
            );

        }

        return array_values($goals_array);

    }

    /**
     * 
     * Split goals and steps from data array
     * 
     * @param   array   $data       santitized user input values from form
     * @return  array   array       associative array of goal and steps
     * 
     */
    private function split_goal_and_steps($sanitized_data){

        $goal_data = array();
        $multiple_steps_data = array();

        foreach($sanitized_data as $key => $value){

            if (empty($value) && $value != 0)
                continue;

            if (strpos($key, 'goal_') !== false) {

                if ($key == "goal_title" || $key == "goal_reflection")
                    $value = $this->encrypt_decrypt_string($value);

                $goal_data[$key] = $value;
            }

            if ($key == "steps" && is_array($value)){

                foreach($value as $k => $v ){

                    // $value is array
                    // $k is index of array
                    // $v then is assoc array of key=>value

                }

            }

            // if (strpos($key, 'step_') !== false) {
            //     $index = substr($key, -1);
            //     $key = substr($key, 0, strrpos( $key, '_') );

            //     if ($key == "step_title")
            //         $value = $this->encrypt_decrypt_string($value);

            //     $multiple_steps_data[$index][$key] = $value;
            // }

            if ($key == "user_id"){
                $goal_data[$key] = $value;
            }

        }

        if (isset($sanitized_data["user_id"])){
            foreach($multiple_steps_data as $key => $value){
                $multiple_steps_data[$key]["user_id"] = $sanitized_data["user_id"];
            }
        }

        return array(
            "goal" => $goal_data,
            "steps" => $multiple_steps_data
        );

    }


    /**
     * 
     * Format user input into database field and value pairs and sanitize the data
     * 
     * @param   array   $unsafe_input   raw user input data from form
     * @return  array   $safe_input     sanitized db field and value pair array
     * 
     */
    private function format_and_sanitize_user_input($unsafe_input){

        if (!is_array($unsafe_input))
            return;

        $safe_input = array();

        foreach($unsafe_input as $key => $value){

            if (is_array($value)){

                // $value is array
                // $k = index of array
                // $v = array of assoc

                foreach($value as $index => $array){

                    foreach($array as $arr_key => $arr_value){

                        $safe_input[$key][$index][$arr_key] = sanitize_text_field(trim($arr_value));

                    }

                }

            } else {

                $safe_input[$key] = sanitize_text_field(trim($value));

            }

        }

        return $safe_input;

    }


    /**
     * 
     * Encrypt and Descrypt string, returns encrypted or decrypted string
     * 
     * @param   string   $string        raw plain text string
     * @param   bool     $decrypt       optional boolean flag to trigger decryption
     * @return  string   $output        returns encrypted or decrypted string
     * 
     */
    private function encrypt_decrypt_string( $string, $decrypt = false ) {

        if(empty($string))
            return $string;
        
        $secret_key = 'D355B64824A381B1A944601B05A36BE3801E859D';   // SHA1 of: lfgs
        $secret_iv = '68A6FFE86D0E72E768168B49D33B1B91B06C08DD';    // SHA1 of: goal_setting
    
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
    
        if( $decrypt ) {
            
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );

        } else {
            
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );

        }
    
        return ($output ? $output : $string );

    }

    /***********************************************/
    /********** GOAL DATABASE QUERIES [BEGIN] ******/
    /***********************************************/

    /******************** INSERT GOAL [BEGIN] ********************/

    private function db_insert_goal($insert_data){

        // Sanitize Data
        foreach($insert_data as $key => $value){
            $insert_data[$key] = sanitize_text_field(trim($value));
        }

        // Data Format
        $data_format = "%s";

        // Insert recored and return ID
        if ($this->db->insert( $this->goals_table, $insert_data, $data_format ))
            return $this->db->insert_id;

    }

    /******************** INSERT GOAL [END] ********************/

    /******************** UPDATE GOAL [BEGIN] ********************/

    private function db_update_goal($update_data){

        $data_format = array();

        foreach($update_data as $key => $value){

            if ($key == "goal_id")
                continue;

            // Sanitize Data
            $update_data[$key] = sanitize_text_field(trim($value));

            // Populate Data Format
            array_push($data_format, "%s");

        }

        $where_clause = array (
            "goal_id" => intval($update_data["goal_id"])
        );

        unset($update_data["goal_id"]);

        return $this->db->update( $this->goals_table, $update_data, $where_clause, $data_format );

    }

    /******************** UPDATE GOAL [END] ********************/

    /******************** DELETE GOAL [BEGIN] ********************/

    private function db_delete_goal($id){

        $where_format = "%d";

        $where_clause = array (
            "goal_id" => intval($id)
        );

        return $this->db->delete( $this->goals_table, $where_clause, $where_format );

    }

    /******************** DELETE GOAL [END] ********************/

    /*********************************************/
    /********** GOAL DATABASE QUERIES [END] ******/
    /*********************************************/

    /***********************************************/
    /********** STEP DATABASE QUERIES [BEGIN] ******/
    /***********************************************/

    /******************** INSERT STEP [BEGIN] ********************/

    private function db_insert_step($insert_data){

        $data_format = array();

        foreach($insert_data as $key => $value){

            // Sanitize Data
            $insert_data[$key] = sanitize_text_field(trim($value));

            // Populate Data Format
            switch($key){

                case "goal_id":
                case "step_status":
                case "step_order":
                    array_push($data_format, "%d");
                    break;

                default:
                    array_push($data_format, "%s");
                
            }

        }

        if ($this->db->insert( $this->steps_table, $insert_data, $data_format ))
            return $this->db->insert_id;

    }

    /******************** INSERT STEP [END] ********************/

    /******************** UPDATE STEP [BEGIN] ********************/

    private function db_update_step($update_data){

        $data_format = array();

        foreach($update_data as $key => $value){

            if ($key == "step_id")
                continue;

            // Sanitize Data
            $update_data[$key] = sanitize_text_field(trim($value));

            // Populate Data Format
            switch($key){

                case "goal_id":
                case "step_status":
                case "step_order":
                    array_push($data_format, "%d");
                    break;

                default:
                    array_push($data_format, "%s");
                
            }

        }

        $where_clause = array (
            "step_id" => intval($update_data["step_id"])
        );

        unset($update_data["step_id"]);

        return $this->db->update( $this->steps_table, $update_data, $where_clause, $data_format );

    }

    /******************** UPDATE STEP [END] ********************/

    /******************** DELETE STEP [BEGIN] ********************/

    private function db_delete_step($id){

        $where_format = "%d";

        $where_clause = array (
            "step_id" => intval($id)
        );

        return $this->db->delete( $this->steps_table, $where_clause, $where_format );

    }

    /******************** DELETE STEP [END] ********************/

    /*********************************************/
    /********** STEP DATABASE QUERIES [END] ******/
    /*********************************************/

} 