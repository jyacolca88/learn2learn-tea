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

        /*
        Select *, 
        FROM goal_table g
        WHERE g.user_id = username
        

        SELECT * 
        FROM steps_table s
        WHERE 
        */

        $results = $this->db->get_results($sql);

        return $this->optimise_raw_results_into_associative_array($results);

    }
    private function optimise_raw_results_into_associative_array($results){

        if (!is_array($results) || empty($results)) {return;}

        $goals_array = [];
        $steps_array = [];  

        foreach($results as $record){

            if (isset($record->step_id) && !empty($record->step_id)){

                $step_title = $this->encrypt_decrypt_string($record->step_title, true);
                $goal_title = $this->encrypt_decrypt_string($record->goal_title, true);

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
            
            $goal_reflection = (!empty($record->goal_reflection) ? $this->encrypt_decrypt_string($record->goal_reflection, true) : null);

            $goals_array[$record->goal_id] = array(
                "goal_id" => $record->goal_id,
                "goal_title" => $goal_title,
                "goal_completed_by" => $record->goal_completed_by,
                "goal_status" => $record->goal_status,
                "goal_reflection" => $goal_reflection,
                "steps" => $steps_array[$record->goal_id]
            );

        }

        return $goals_array;

    }

    private function get_goal_with_steps($goal_id){

        $goal_id = intval($goal_id);

        $sql = $this->db->prepare(

        );

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



} 