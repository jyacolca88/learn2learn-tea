<?php
class Learn2Learn_Topics {

    // TODO: Get all topics that are associated to a Content Item
    public static function get_all_topics(){

        return get_terms( array(
            'taxonomy' => 'topic',
            'hide_empty' => true,
        ) );

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

    public static function save_topic_ids_by_user_id($topic_ids, $user_id){

        // TODO: get user_meta, and update if exists
        // TODO: if doesn't exist, add user_meta of topic_ids
        if (get_user_meta($user_id, "topic_ids") !== false){

            update_user_meta($user_id, "topic_ids", $topic_ids);

        } else {

            add_user_meta($user_id, "topic_ids", $topic_ids, true);

        }

        return get_user_meta($user_id, "topic_ids", true);

    }

    public static function get_topic_ids_by_user_id($user_id){

        $user_topics = get_user_meta( $user_id, "topic_ids", true );
        return $user_topics;

    }

}