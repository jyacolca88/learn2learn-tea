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

        date_default_timezone_set('Australia/Sydney');

    }

    // Get Goals 
    public function get_goals(){

        // Get Goals and related Steps
        return $this->get_all_goals_by_user();

    }

    public function get_goal($goal_id){

        return $this->get_goal_by_goal_id(intval($goal_id));

    }


    // Add new Goal
    public function add_new_goal($submitted_data){

        // Format and Sanitize User Input
        $sanitized_user_data = $this->format_and_sanitize_user_input($submitted_data);

        // Split Steps from Goals
        $goal_and_steps_data = $this->split_goal_and_steps($sanitized_user_data);
        $goal_data = $goal_and_steps_data["goal"];
        $steps_data = $goal_and_steps_data["steps"];

        // Insert Goal to DB, return Goal ID
        $success_steps_insert = array();
        $success_steps_expected = count($steps_data);

        if ($goal_id = $this->db_insert_goal($goal_data)){

            if (!empty($steps_data)){

                foreach($steps_data as $step_data){

                    if (empty($step_data["step_title"]))
                        continue;
    
                    if (empty($step_data["step_completed_by"]) || $step_data["step_completed_by"] == "0000-00-00")
                        $step_data["step_completed_by"] = date("Y-m-d");
    
                    $step_data["goal_id"] = $goal_id;
    
                    if ($step_id = $this->db_insert_step($step_data))
                        array_push($success_steps_insert, $step_id);
    
                }

            }

        }

        $return_data = array();

        if (count($success_steps_insert) == $success_steps_expected && isset($goal_id)){
            $return_data["success"] = true;
            $return_data["goal"] = $this->get_goal_by_goal_id($goal_id);
        } else {
            $return_data["success"] = false;
        }

        return $return_data;

    }

    // Update Goal
    public function update_goal($submitted_data){

        // Format and Sanitize User Input
        $sanitized_user_data = $this->format_and_sanitize_user_input($submitted_data);

        // Split Steps from Goals
        $goal_and_steps_data = $this->split_goal_and_steps($sanitized_user_data);

        // Goal Data
        $goal_data = $goal_and_steps_data["goal"];

        if (!isset($goal_data["goal_id"]))
            return;

        // Get Goal ID from Goal Data
        $goal_id = intval($goal_data["goal_id"]);

        $steps_data = (isset($goal_and_steps_data["steps"]) && is_array($goal_and_steps_data["steps"]) ? $goal_and_steps_data["steps"] : array());

        $goal_update_success = $this->db_update_goal($goal_data);
        $steps_update_success = false;
        $step_deleted = false;

        if (!empty($steps_data)){

            foreach($steps_data as $step_data){

                // If Step Title is empty, delete step then re-order
                if (empty($step_data["step_title"]) && isset($step_data["step_id"])){
                    $this->db_delete_step(intval($step_data["step_id"]));
                    $step_deleted = true;
                    continue;
                }
                    
        
                if (empty($step_data["step_completed_by"]) || $step_data["step_completed_by"] == "0000-00-00")
                    $step_data["step_completed_by"] = date("Y-m-d");
        
                $step_data["goal_id"] = $goal_id;
        
                if (!isset($step_data["step_id"]) || empty($step_data["step_id"]) || $step_data["step_id"] == "undefined"){
                    $steps_update_success = $this->db_insert_step($step_data);
                } else {
                    $steps_update_success = $this->db_update_step($step_data);
                }
        
            }

        }

        $return_data = array();

        if (isset($goal_update_success) || isset($steps_update_success)){

            $goal_array = $this->get_goal_by_goal_id($goal_id);
            $current_steps_array = array();

            // If Step has been deleted, re-order steps array
            if ($step_deleted){

                $current_steps_array = $goal_array->steps;

                if (isset($current_steps_array) && !empty($current_steps_array)){

                    $order = 0;

                    foreach($current_steps_array as $step){

                        $id = intval($step->step_id);

                        $step_data = array(
                            "step_id" => $id, 
                            "step_order" => $order
                        );
                        $this->db_update_step($step_data);
                        $order++;

                    }
                }

            }

            $return_data["success"] = true;
            $return_data["goal"] = $this->get_goal_by_goal_id($goal_id);
            $return_data["current_steps_array"] = $current_steps_array;
        } else {
            $return_data["success"] = false;
        }

        return $return_data;

    }

    // Delete Goal
    public function delete_goal($goal_id){

        if(!$goal_id)
            return;

        $delete_success = false;

        // Need to delete steps before attempting to delete goal (Foreign key constraint)

        if ($steps = $this->get_steps_by_goal_id($goal_id)){

            if (is_array($steps) && !empty($steps)){

                foreach($steps as $step){

                    $step_delete = $this->db_delete_step($step->step_id);

                }

            }

        }

        if ($goal_delete = $this->db_delete_goal($goal_id))
            $delete_success = true;

        return $delete_success;

    }

    private function get_all_goals_by_user_with_steps_only(){

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

    public function get_all_goals_by_user(){

        $goals_array = array();

        $goal_columns = array(
            "goal_id",
            "goal_title",
            "goal_completed_by",
            "goal_finish_date",
            "goal_status",
            "goal_reflection"
        );

        $goals = $this->select_from_table($this->goals_table, array("user_id" => "%s"), array($this->username), $goal_columns, true);
        if (is_array($goals) && !empty($goals)){

            $step_columns = array(
                "step_id",
                "step_title",
                "step_completed_by",
                "step_status",
                "step_order"
            );
            
            foreach($goals as $index => $goal){

                $goals_array[$index] = $goal;
                $goals_array[$index]["steps"] = [];

                // Decrypt Goal Title and Reflection
                $goals_array[$index]["goal_title"] = stripslashes($this->encrypt_decrypt_string($goal["goal_title"], true));
                $goals_array[$index]["goal_reflection"] = (!empty($goal["goal_reflection"]) ? stripslashes($this->encrypt_decrypt_string($goal["goal_reflection"], true)) : null);

                $goal_id = intval($goal['goal_id']);
                $steps = $this->select_from_table($this->steps_table, array("goal_id" => "%d"), array($goal_id), $step_columns, true);

                if (is_array($steps) && !empty($steps)){

                    $steps_array = array();

                    foreach($steps as $key => $step){

                        $steps_array[$key] = $step;
                        $steps_array[$key]["step_title"] = stripslashes($this->encrypt_decrypt_string($step["step_title"], true));

                    }

                    $goals_array[$index]["steps"] = $steps_array;
                }

            }

        }
        return $goals_array;

    }

    private function get_goal_by_goal_id($goal_id){

        $goal_id = intval($goal_id);
        if ($goal_id < 1){ return; }

        $sql = $this->db->prepare("
            SELECT * 
            FROM {$this->goals_table} g
            INNER JOIN {$this->steps_table} s 
            ON g.goal_id = s.goal_id 
            WHERE g.goal_id = %d 
            ORDER BY g.goal_id ASC, s.step_order ASC
        ", $goal_id);

        $results = $this->db->get_results($sql);

        if (empty($results)){
            $results = $this->select_from_table($this->goals_table, array("goal_id" => "%d"), array($goal_id));
        }

        $results_array = $this->optimise_raw_results_into_associative_array($results);
        $return = is_array($results_array) ? reset($results_array) : false;

        return $return;

    }

    private function get_steps_by_goal_id($goal_id){

        $sql = $this->db->prepare("
            SELECT * 
            FROM $this->steps_table 
            WHERE goal_id = %d 
            ORDER BY step_order, step_id ASC
        ", $goal_id);

        return $this->db->get_results($sql);

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

                $steps_array[$record->goal_id] = [];

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

                 foreach($value as $index => $array){

                    foreach($array as $arr_key => $arr_value){

                        if ($arr_key == "step_title")
                            $arr_value = $this->encrypt_decrypt_string($arr_value);

                        $multiple_steps_data[$index][$arr_key] = $arr_value;

                    }

                }

            }

        }

        if (!empty($goal_data)){
            $goal_data["user_id"] = $this->username;
        }

        if (isset($goal_data["user_id"])){
            foreach($multiple_steps_data as $key => $value){
                $multiple_steps_data[$key]["user_id"] = $this->username;
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