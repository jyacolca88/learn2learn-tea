<?php

class Lessons_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'lessons';
        $resource_name_alt = 'personalisedlessons';


        register_rest_route( $namespace, '/' . $resource_name . '/(?P<username>[\w]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_lessons'),
                'permission_callback'  => array ( $this, 'get_lessons_permissions_check' ),
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, $resource_name . '/(?P<username>[\w]+)' . '/(?P<lesson_id>[\d]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_lesson'),
                'permission_callback'  => array ( $this, 'get_lesson_permissions_check' )
            )

        ));

        register_rest_route( $namespace, $resource_name_alt . '/(?P<user_id>[\d]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_personal_lessons'),
                'permission_callback'  => array ( $this, 'get_personal_lessons_permissions_check' )
            )

        ));

        register_rest_route( $namespace, $resource_name . '/get_embed_item_data/(?P<lesson_id>[\d]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_lesson_data_for_embed_code'),
                'permission_callback'  => array ( $this, 'get_lesson_data_for_embed_code_permissions_check' )
            )

        ));

    }

    public function get_lessons( $request ){

        $username = sanitize_text_field($request["username"]);

        // $L2l_Content_Items = new Learn2Learn_Content_Items("85daa4da50ba3931755b1960bf8f1083");
        $L2l_Content_Items = new Learn2Learn_Content_Items($username);
        $lessons = $L2l_Content_Items->get_map_items();

        return new WP_REST_Response( $lessons, 200 );

    }

    public function get_lessons_permissions_check( $request ){

        return current_user_can( 'read' );

    }

    public function get_lesson( $request ){

        $username = sanitize_text_field($request["username"]);
        $lesson_id = intval($request['lesson_id']);

        // $L2L_Lessons = new Learn2Learn_Lessons( $lesson_id, "85daa4da50ba3931755b1960bf8f1083" );
        $L2L_Lessons = new Learn2Learn_Lessons( $lesson_id, $username );
        $lesson_data = $L2L_Lessons->get_lesson_data( $lesson_id );

        return new WP_REST_Response( $lesson_data, 200 );

    }

    public function get_lesson_permissions_check( $request ){

        return current_user_can( 'read' );

    }

    public function get_personal_lessons( $request ){

        $user_id = sanitize_text_field($request["user_id"]);
        $lessons = Learn2Learn_Topics::get_lessons_from_topics_by_user_id($user_id);

        return new WP_REST_Response( $lessons, 200 );
        
    }

    public function get_personal_lessons_username( $request ){

        $username = sanitize_text_field($request["username"]);
        $lessons = Learn2Learn_Topics::get_lessons_from_topics_by_username($username);

        return new WP_REST_Response( $lessons, 200 );
        
    }

    public function get_personal_lessons_permissions_check ( $request ){

        return current_user_can( 'read' );

    }

    public function get_lesson_data_for_embed_code( $request ){

        $response = false;

        // Get Content ID parameter
        $lesson_id = $request->get_param( 'lesson_id' );

        //If Content ID and Category ID are set
        if ($lesson_id && $category_id = wp_get_post_parent_id($lesson_id)){

            // Get Content name and associated data for embed item
            $response = array(
                "title" => get_bloginfo( "name" ),
                "lesson_name" => get_the_title($lesson_id),
                'lesson_desc' => get_field( "description_student_perspective", $lesson_id),
                "item_image" => get_the_post_thumbnail_url( $category_id, 'large' ),
                "color_primary" => get_field( "primary_colour", $category_id),
                "color_secondary" => get_field( "secondary_colour", $category_id)
            );

        }

        return new WP_REST_Response( $response, 200 );

    }

    public function get_lesson_data_for_embed_code_permissions_check(){

        return "__return_true";

    }

}