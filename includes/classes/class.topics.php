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

        $user_topics = self::convert_comma_separated_to_array(get_user_meta($user_id, $meta_key, true));

        return $user_topics;

    }

    public static function get_topic_ids_by_user_id($user_id){

        $meta_key = self::$meta_key_topic_ids;
        $user_topics = get_user_meta( $user_id, $meta_key, true );
        $user_topics = self::convert_comma_separated_to_array($user_topics);
        return $user_topics;

    }

    public static function get_lessons_from_topics_by_username($username){

        // Get username
        $username = sanitize_text_field($username);
        if (!$username) return new WP_Error('no_username_error', 'No username provided', array('status' => 400));

        // Find user by username
        $user = get_user_by("login", $username);
        if (!$user) return new WP_Error('no_user_exists', 'No user exists with that username', array('status' => 400));

        // Get User ID
        $user_id = intval($user->ID);

        // Get user_meta
        $meta_key = self::$meta_key_topic_ids;
        $topic_ids = get_user_meta($user_id, $meta_key, true);
        if (!$topic_ids) return;

        // Convert to array (terms_array)
        $terms_array = self::convert_comma_separated_to_array($topic_ids);

        // Set up parameters for get_posts function
        $tagged_lessons_args = array (
            'numberposts' => -1,
            'post_type'  => 'content-item',
            'post_status' => 'publish',
            'orderby'    => 'menu_order',
            'sort_order' => 'ASC',
            'tax_query' => array(
                array(
                    'taxonomy' => 'topic',
                    'field'    => 'term_id',
                    'terms'    => $terms_array
                ),
            )
        );

        // Get lessons by tags/terms (topics)
        $lessons = get_posts($tagged_lessons_args);

        return $lessons;

    }

    
    private static function convert_comma_separated_to_array($comma_separated){

        if( strpos($comma_separated, ",") !== false ) {

            $array = explode(",", $comma_separated);

        } else {

            $array = array($comma_separated);

        }

        return $array;

    }

}