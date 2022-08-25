<?php

class Lessons_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'lessons';


        register_rest_route( $namespace, '/' . $resource_name, array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_lessons'),
                'permission_callback'  => array ( $this, 'get_lessons_permissions_check' ),
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, $resource_name . '/(?P<id>[\d]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_lesson'),
                'permission_callback'  => array ( $this, 'get_lesson_permissions_check' )
            )

        ));

        register_rest_route( $namespace, $resource_name . '/personalisedlessons' . '/(?P<username>[\w]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_personal_lessons'),
                'permission_callback'  => array ( $this, 'get_personal_lessons_permissions_check' )
            )

        ));

    }

    public function get_lessons( $request ){

        $L2l_Content_Items = new Learn2Learn_Content_Items("85daa4da50ba3931755b1960bf8f1083");
        $lessons = $L2l_Content_Items->get_map_items();

        return new WP_REST_Response( $lessons, 200 );

    }

    public function get_lessons_permissions_check( $request ){

        return '__return_true';

    }

    public function get_lesson( $request ){

        $lesson_id = (int) $request['id'];

        $L2L_Lessons = new Learn2Learn_Lessons( $lesson_id, "85daa4da50ba3931755b1960bf8f1083" );
        $lesson_data = $L2L_Lessons->get_lesson_data( $lesson_id );

        return new WP_REST_Response( $lesson_data, 200 );

    }

    public function get_lesson_permissions_check( $request ){

        return '__return_true';

    }

    public function get_personal_lessons( $request ){

        // Get username
        $username = sanitize_text_field($request["username"]);

        if (!$username) return new WP_REST_Response( "No username provided", 200 );
        
        // Find user by username
        $user = get_user_by("login", $username);
        if (!$user) return new WP_REST_Response( "No user found with that username", 200 );

        // Get user_meta topic_ids
        $user_id = intval($user->ID);
        $topic_ids = get_user_meta($user_id, "topic_ids", true);

        //"24,25,21,22,23,31,29,30,34,27"

        /*
            'post_type'  => 'content-item',
            'post_status' => 'publish',
            'orderby'    => 'menu_order',
            'sort_order' => 'ASC',
        */

        $lessons = get_posts( array(
            'numberposts' => -1
        ) );

        return new WP_REST_Response( $lessons, 200 );
        
    }

    public function get_personal_lessons_permissions_check ( $request ){

        return '__return_true';

    }

}