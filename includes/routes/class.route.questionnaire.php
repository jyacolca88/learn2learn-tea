<?php

class Questionnaire_Learn2Learn_Custom_Route extends WP_REST_Controller {

    public function register_routes(){

        $version = '1';
        $namespace = 'learn2learn/v' . $version;
        $resource_name = 'questionnaire';


        register_rest_route( $namespace, '/' . $resource_name, array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_questionnaires'),
                'permissions_callback'  => array ( $this, 'get_questionnaires_permissions_check' ),
                'args'                  => array ()
            )

        ));

        register_rest_route( $namespace, $resource_name . '/(?P<id>[\d]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_questionnaire'),
                'permissions_callback'  => array ( $this, 'get_questionnaire_permissions_check' )
            )

        ));

        register_rest_route( $namespace, $resource_name . '/topic/(?P<topic_id>[\d]+)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_topic'),
                'permissions_callback'  => array ( $this, 'get_topic_permissions_check' )
            )

        ));

        register_rest_route ( $namespace, $resource_name . '/topic/(?P<topic_ids>)', array(

            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array ( $this, 'get_topics' ),
                'permissions_callcback' => array ( $this, 'get_topics_permissions_check' )
            )

        ));

    }

    public function get_questionnaires( $request ){

        // return "List all questionnaires here... Menus with menu location Journey Map Questions";

        $L2l_Questionnaire = new Learn2Learn_Questionnaire();
        $questionnaire_items = $L2l_Questionnaire->get_questionnaire_items();

        return new WP_REST_Response( $questionnaire_items, 200 );

    }

    public function get_questionnaires_permissions_check( $request ){

        return '__return_true';

    }

    public function get_questionnaire( $request ){

        return "Return questionnaire specified by menu ID";

        // $lesson_id = (int) $request['id'];

        // $L2L_Lessons = new Learn2Learn_Lessons( $lesson_id, "85daa4da50ba3931755b1960bf8f1083" );
        // $lesson_data = $L2L_Lessons->get_questionnaire_data( $lesson_id );

        // return new WP_REST_Response( $lesson_data, 200 );

    }

    public function get_questionnaire_permissions_check( $request ){

        return '__return_true';

    }

    public function get_topic( $request ){

        $topic_id = (int) $request['topic_id'];

        $topic = Learn2Learn_Topics::get_topic_by_id($topic_id);
        return new WP_REST_Response( $topic, 200 );
        // return new WP_REST_Response( "hello world!", 200 );

    }

    public function get_topic_permissions_check( $request ){

        return '__return_true';

    }

    public function get_topics( $request ){

        $topic_ids = sanitize_text_field($request['topic_ids']);

        $topics = Learn2Learn_Topics::get_topics_by_ids($topic_ids);
        return new WP_REST_Response( $topics, 200 );

    }

    public function get_topics_permissions_check( $request ){

        return '__return_true';

    }

}