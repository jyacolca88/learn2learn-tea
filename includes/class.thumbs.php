<?php
class Learn2Learn_Thumbs {

    private $user_id;
    
    function __construct($user_id = null){

        $this->user_id = $user_id;

    }

    public function get_thumb_for_page(){

        $db = new Learn2Learn_Database();

        // $thumbs = $db->select_columns_by_ids_from_table(
        //     "user_thumbs", 
        //     array("user_id" => "%s", "page_id" => "%d"), 
        //     array("85daa4da50ba3931755b1960bf8f1083", 31), 
        //     array("thumb_id","thumbs","created")
        // );

        $thumbs = $db->select_columns_by_ids_from_table(
            "user_thumbs"
        );

        print_r($thumbs);

    }


}