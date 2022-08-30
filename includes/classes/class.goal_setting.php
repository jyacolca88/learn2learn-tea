<?php
class Learn2Learn_Goal_Setting extends Learn2Learn_Database {

    private $username;
    private $goals_table;
    private $steps_table;

    function __construct($username = null){

        $this->username = $username;
        $this->goals_table = $this->prefix . "lfgs_goals";
        $this->steps_table = $this->prefix . "lfgs_steps";

    }

    // Get Goals 
    public function get_goals(){

        // Get Goals and related Steps
        $this->get_all_goals_by_user();

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
            FROM {$this->goals_table} 
            WHERE user_id = %s
        ", $this->username);

        $results = $this->db->get_results($sql);

    }

    private function get_goal_with_steps($goal_id){

        $goal_id = intval($goal_id);

        $sql = $this->db->prepare(

        );

    }


} 