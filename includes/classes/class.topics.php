<?php
class Learn2Learn_Topics {

    // TODO: Get all topics that are associated to a Content Item
    public static function get_all_topics(){

        return wp_get_post_terms( 0, 'topic', array( 'fields' => 'all' ) );

    }


    public static function get_topic_by_id($id){

        return get_term( $id , 'topic' );

    }

    public static function get_topics_by_ids($ids){

        if(!is_array($ids)) $ids = explode(",", $ids); // convert to array
        if(empty($ids)) return; // exit if ids empty

        return get_terms('topic', array(
            'include' => $ids
        ));

    }

}