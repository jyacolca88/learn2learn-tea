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

        $lessons = self::get_lessons_from_topics_by_user_id($user_id);

        return $lessons;

    }

    public static function get_lessons_from_topics_by_user_id($user_id){

        // Get User ID
        $user_id = intval($user_id);

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

        // Get filtered fields, additional fields and user progress for lessons
        $lessons = self::get_required_fields_and_user_progress_from_lessons($lessons, $user_id);

        return $lessons;

    }

    private static function get_required_fields_and_user_progress_from_lessons($lessons, $user_id){

        // If lessons parameter is not an array or it's an empty value, or if user ID is not set, exit
        if (!is_array($lessons) || empty($lessons) || !$user_id) return;

        $new_lessons_array = array();

        $db_user_progress = new Learn2Learn_Database();

        foreach($lessons as $lesson){

            $lesson_completion_record = $db_user_progress->select_user_progress_record(null, $lesson->ID, $user_id);
            $lesson_completion = (is_object($lesson_completion_record) ? intval($lesson_completion_record->progress) : null);
            $lesson_interactive = self::get_lesson_interactive($lesson->ID);

            $lesson_array = array(
                'personalised_lesson_id' => $lesson->ID,
                'personalised_lesson_slug' => $lesson->post_name,
                'personalised_lesson_title' => apply_filters ( 'the_title', $lesson->post_title ),
                "personalised_lesson_reading_time" => esc_html(get_field( "reading_time", $lesson->ID) ),
                'personalised_lesson_completion' => $lesson_completion,
                'personalised_lesson_interactive' => $lesson_interactive
            );

            array_push($new_lessons_array, $lesson_array);

        }

        $lessons = $new_lessons_array;

        return $lessons;

    }

    private static function get_lesson_interactive($lesson_id){

        $filters = get_the_terms( $lesson_id, 'filter' );
        if (!$filters || empty($filters)) return false;

        $is_interactive = false;
        $possible_interactive_array = [];

        foreach($filters as $filter){

            if ($filter->slug == "interactive"){
                $is_interactive = true;
            } else {
                array_push($possible_interactive_array, $filter->slug);
            }

        }

        return ($is_interactive ? $possible_interactive_array[0] : false);

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