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

        if (!$results) {return;}

        $goals_array = [];

        foreach($results as $record){

            $steps_array = [];  

            $steps_array[$record->step_order] = array(
                "step_id" => $record->step_id,
                "step_title" => $record->step_title,
                "step_completed_by" => $record->step_completed_by,
                "step_status" => $record->step_status,
                "step_order" => $record->step_order
            );

            $goals_array[$record->goal_id] = array(
                "goal_id" => $record->goal_id,
                "goal_title" => $record->goal_title,
                "goal_completed_by" => $record->goal_completed_by,
                "goal_status" => $record->goal_status,
                "goal_reflection" => $record->goal_reflection,
                "steps" => $steps_array
            );

        }

        return $goals_array;

    }

    private function get_goal_with_steps($goal_id){

        $goal_id = intval($goal_id);

        $sql = $this->db->prepare(

        );

    }


} 