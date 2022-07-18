<?php
class Learn2Learn_Lessons {

    private $category;
    private $lesson;
    private $pages;
    private $user_id;
    private $db_user_progress;
    
    function __construct($lesson_id, $user_id = null){

        // Initialise properties
        $this->category = get_post( wp_get_post_parent_id( $lesson_id ) );
        $this->lesson = get_post($lesson_id);
        $this->pages = get_children(array('post_parent' => $lesson_id,'order' => 'ASC','orderby' => 'menu_order'));
        $this->user_id = $user_id;
        
        // Initialise Database connection
        $this->db_user_progress = new Learn2Learn_Database();

    }

    public function get_lesson_data(){

        if (is_null($this->lesson))
            return;
        
        $category_data_array = $this->get_category_data_array();
        $lesson_data_array = $this->get_lesson_data_array();
        $pages_data_array = $this->get_pages_data_array();

        $lesson_data_array["pages"] = $pages_data_array;

        return array_merge($category_data_array, $lesson_data_array);

    }

    private function get_category_data_array(){

        $category_lesson_image = get_field( "image_for_lessons", $this->category->ID );
        $category_completion_record = $this->db_user_progress->select_user_progress_record($this->user_id, $this->category->ID);
        $category_completion = (is_object($category_completion_record) ? $category_completion_record->progress : null);

        return array(
            'catgegory_id' => $this->category->ID,
            'category_slug' => $this->category->post_name,
            'category_lesson_image' => $category_lesson_image,
            "category_colours" => array(
                "primary" => get_field( "primary_colour", $this->category->ID),
                "secondary" => get_field( "secondary_colour", $this->category->ID),
                "tertiary" => get_field( "tertiary_colour", $this->category->ID)
            ),
            'category_completion' => $category_completion
        );

    }

    private function get_lesson_data_array(){

        $lesson_completion_record = $this->db_user_progress->select_user_progress_record($this->user_id, $this->lesson->ID);
        $lesson_completion = (is_object($lesson_completion_record) ? $lesson_completion_record->progress : null);

        return array(
            'lesson_id' => $this->lesson->ID,
            'lesson_slug' => $this->lesson->post_name,
            'lesson_title' => apply_filters ( 'the_title', $this->lesson->post_title ),
            'lesson_content' => apply_filters( 'the_content', $this->lesson->post_content ),
            "lesson_reading_time" => esc_html(get_field( "reading_time", $this->lesson->ID) ),
            'lesson_completion' => $lesson_completion
        );

    }

    private function get_pages_data_array(){

        $pages_array = array();
        $page_ids = array();

        foreach($this->pages as $key => $page){

            $lesson_page = array(
                'page_id' => $page->ID,
                'page_slug' => $page->post_name,
                'page_order' => $page->menu_order,
                'page_title' => apply_filters( 'the_title', $page->post_title ),
                'page_content' => apply_filters( 'the_content', $page->post_content )
            );

            array_push($page_ids, $page->ID);
            array_push($pages_array, $lesson_page);

        }

        $pages_progress_records = $this->db_user_progress->select_user_progress_records_in($this->user_id, $page_ids);

        foreach($pages_array as $key => $page){

            for($i=0; $i < count($pages_progress_records); $i++){

                if ($pages_progress_records[$i]->content_id == $page["page_id"]){
                    $pages_array[$key]["page_completion"] = $pages_progress_records[$i]->progress;
                }

            }

        }

        return $pages_array;

    }

}