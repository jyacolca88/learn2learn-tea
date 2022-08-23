<?php
class Learn2Learn_Topics {

    private static $meta_key_topic_ids = "topic_ids";

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
        $meta_key = self::$meta_key_topic_ids;

        if (get_user_meta($user_id, $meta_key) !== false){

            update_user_meta($user_id, $meta_key, $topic_ids);

        } else {

            add_user_meta($user_id, $meta_key, $topic_ids, true);

        }

        return get_user_meta($user_id, $meta_key, true);

    }

    public static function get_topic_ids_by_user_id($user_id){

        $meta_key = self::$meta_key_topic_ids;
        $user_topics = get_user_meta( $user_id, $meta_key, true );
        return $user_topics;

    }

}