<?php
class Learn2Learn_Content_Items {

    public $items;
    public $user_id;
    public $base_url;
    public $home_items;
    public $map_items;
    public $content_items;
    public $overall_progress;

    function __construct($user_id = null){

        $this->user_id = $user_id;
        $this->items = $this->get_raw_content_items();
        $this->base_url = get_bloginfo("url");

        $this->set_home_and_map_items();

    }

    private function get_raw_content_items(){

        $content_items = lf_l2l_get_nav_menu_items_by_location("journey-content-items");
        if (isset($content_items) && !empty($content_items))
            return $this->build_content_tree($content_items);

    }

    public function get_rest_route_content_items(){

        $lesson_items = array();

        $content_items = lf_l2l_get_nav_menu_items_by_location("journey-content-items");

        if (isset($content_items) && !empty($content_items)){
            $content_items = $this->build_content_tree($content_items);
            $index = 0;

            foreach($content_items as $category_key => $category){

                $lesson_items[$index] = array(
                    "ID" => $category->object_id,
                    "title" => $category->title,
                    "primary" => get_field( "primary_colour", $category->object_id),
                    "secondary" => get_field( "secondary_colour", $category->object_id),
                    "tertiary" => get_field( "tertiary_colour", $category->object_id),
                    "image" => get_the_post_thumbnail_url( $category->object_id, 'large' ),
                    "description" => get_field( "category_description", $category->object_id)
                );

                if (isset($category->wpse_children) && $lessons = $category->wpse_children){

                    $lesson_items[$index]["number_of_lessons"] = count($lessons);

                    $lesson_index = 0;
                    foreach($lessons as $lesson_key => $lesson){

                        $lesson_items[$index]["lessons"][$lesson_index] = array(
                            "ID" => $lesson->object_id,
                            "title" => $lesson->title,
                            "description" => get_field( "description_academic_facing", $lesson->object_id),
                            "vuws_item_desc" => get_field( "description_student_perspective", $lesson->object_id)
                        );

                        if ($terms = get_the_terms( $lesson->object_id, "filter" )){
                            $filters = array();
                            foreach($terms as $term){
                                array_push($filters, $term->slug);
                            }
                            $lesson_items[$index]["lessons"][$lesson_index]["filters"] = $filters;
                        }

                        $lesson_index++;

                    }

                }
                
                $index++;

            }

        }

        return $lesson_items;

    }

    private function build_content_tree( array &$elements, $parentId = 0 ) {

        $branch = array();
        foreach ( $elements as &$element )
        {
            if ( $element->menu_item_parent == $parentId )
            {
                $children = $this->build_content_tree( $elements, $element->ID );
                if ( $children )
                    $element->wpse_children = $children;
    
                $branch[$element->ID] = $element;
                unset( $element );
            }
        }
        return $branch;
    
    }

    private function set_home_and_map_items(){

        if (!is_array($this->items))
            return;

        if (!is_null($this->user_id)) {
            $Learn2Learn_Database = new Learn2Learn_Database();
            $user_progress = $Learn2Learn_Database->select_all_user_progress_records($this->user_id);
        }

        $content_items = array();
        $home_items = array();
        $map_items = array();

        $overall_progress = 0;
        $overall_number_of_items = 0;

        foreach($this->items as $item){

            if ($item->object != "content-item"){
                continue;
            }

            $home_items[$item->object_id] = array(
                "ID" => intval($item->object_id), 
                "title" => $item->title,
                "order" => $item->menu_order, 
                'image' => get_the_post_thumbnail_url( $item->object_id,'large' ),
                "primary" => get_field( "primary_colour", $item->object_id),
                "secondary" => get_field( "secondary_colour", $item->object_id),
                "tertiary" => get_field( "tertiary_colour", $item->object_id),
                "slug" => get_post_field( "post_name", $item->object_id)
            );

            if (!is_array($item->wpse_children))
                continue;

            $home_items[$item->object_id]["number_of_map_items"] = count($item->wpse_children);
            $overall_number_of_items += $home_items[$item->object_id]["number_of_map_items"];

            $temp_map_items = array();

            $number_of_completed_map_items = 0;

            $order = 1;

            foreach($item->wpse_children as $child_item){

                if ($child_item->object != "content-item"){
                    continue;
                }

                $temp_map_items[$child_item->object_id] = array(
                    "id" => (int) $child_item->object_id, 
                    "title" => $child_item->title,
                    "url" => $this->base_url . "?p=" . $child_item->object_id,
                    "order" => $order,
                    "slug" => get_post_field( "post_name", $child_item->object_id ),
                    "reading_time" => get_field( "reading_time", $child_item->object_id),
                    "is_interactive" => $this->get_lesson_interactive($child_item->object_id)
                );

                if (isset($user_progress) && ($user_progress_key = array_search($child_item->object_id, array_column($user_progress, 'content_id'))) > -1){
                    $temp_map_items[$child_item->object_id]["progress"] = intval($user_progress[$user_progress_key]->progress);
                    if ($user_progress[$user_progress_key]->progress == 1)
                        $number_of_completed_map_items++;
                } else {
                    $temp_map_items[$child_item->object_id]["progress"] = 0;
                }
                
                $order++;

            }

            $map_items[$item->object_id] = array(
                "content_topic" => $item->title,
                "content_items" => $temp_map_items
            );

            $home_items[$item->object_id]["number_of_completed_map_items"] = $number_of_completed_map_items;
            $overall_progress += $home_items[$item->object_id]["number_of_completed_map_items"];

            $content_items[$item->object_id] = $home_items[$item->object_id];
            $content_items[$item->object_id]["lessons"] = $map_items[$item->object_id]["content_items"];

        }

        $this->home_items = $home_items;
        $this->map_items = $map_items;
        $this->content_items = $content_items;
        $this->overall_progress = floor(($overall_progress / $overall_number_of_items) * 100);

    }

    private function get_lesson_interactive($content_item_id){

        $filters = get_the_terms( $content_item_id, 'filter' );
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

    public function get_home_items(){

        return $this->home_items;

    }

    public function get_map_items($topic_id = null){

        return (!is_null($topic_id) ? $this->map_items[$topic_id] : $this->map_items);

    }

    public function get_content_items(){

        return $this->content_items;

    }

    public function get_overall_progress(){

        return $this->overall_progress;

    }

    public function get_progress_by_content_item_id($content_item_id){

        foreach($this->map_items as $topic_id => $topic_array){
            
            if (array_key_exists($content_item_id, $topic_array["content_items"])) {
                return $topic_array["content_items"][$content_item_id]["progress"];
            }

        }

    }

} 