<?php
class Learn2Learn_Questionnaire {
    
    private $questionnaire_name;
    private $questionnaire_menu;
    private $questionnaire_array;

    function __construct(){

        $menu_object = $this->get_nav_menu_object_by_location("journey-map-questions") ?? false;

        $questionnaire_name = isset($menu_object) ? $menu_object->name : false;
        $questionnaire_menu = $this->get_nav_menu_items_by_object_name( $questionnaire_name ) ?? false;

        $this->questionnaire_name = $questionnaire_name;
        $this->questionnaire_menu = $questionnaire_menu;

        $this->build_menu_tree_structure();

    }

    public function get_questionnaire_items(){

        return array (
            "name" => $this->questionnaire_name,
            "items" => $this->questionnaire_array
        );

    }

    private function get_nav_menu_object_by_location($location){

        $locations = get_nav_menu_locations();
        $object = wp_get_nav_menu_object( $locations[$location] );

        return $object;

    }

    private function get_nav_menu_items_by_object_name( $name ){

        return wp_get_nav_menu_items( $name );

    }

    private function build_menu_tree_structure(){

        if(!$this->questionnaire_menu || empty($this->questionnaire_menu))
            return;

        $questions_array = $options_array = $content_items_array = $topics_array = [];
        $question_order = $option_order = 0;

        foreach($this->questionnaire_menu as $menu_object){

            switch($menu_object->object) {

                case "question":

                    array_push($questions_array, [
                        "question_id" => intval($menu_object->object_id),
                        "question_title" => $menu_object->title,
                        "question_order" => $question_order,
                        'menu_item_id' => intval($menu_object->ID)
                    ]);
    
                    $question_order++;
                    $option_order = 0;

                    break;

                case "option";
                    
                    $options_array[$menu_object->menu_item_parent][] = [
                        "option_id" => intval($menu_object->object_id),
                        "option_title" => $menu_object->title,
                        "option_order" => $option_order
                    ];

                    $option_order++;

                    break;

                case "content-item":

                    $content_items_array[$menu_object->menu_item_parent][] = $menu_object->object_id;

                    break;

            }

        }

        foreach($questions_array as $key => $question){

            $menu_item_id = intval($question["menu_item_id"]);
            $question_options = $options_array[$menu_item_id] ?? false;

            if(!$question_options)
                continue;

            foreach($question_options as $k => $option){

                $topics = get_the_terms($option["option_id"], "topic");
                $topic_ids = [];

                if (!empty($topics)){
                    foreach($topics as $topic_obj){
                        array_push($topic_ids, $topic_obj->term_id);
                    }
                }

                $question_options[$k]["content_items"] = isset($content_items_array[$option["option_id"]]) ? implode(",", $content_items_array[$option["option_id"]]) : false;
                $question_options[$k]["topic_ids"] = !empty($topic_ids) ? implode(",", $topic_ids) : false;
            }

            $questions_array[$key]["question_options"] = $question_options;

            unset($questions_array[$key]["menu_item_id"]);

        }

        $this->questionnaire_array = $questions_array;

    }

}