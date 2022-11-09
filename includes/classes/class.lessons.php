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
        $this->pages = get_children(array('post_parent' => $lesson_id,'order' => 'ASC','orderby' => 'menu_order', 'post_status' => 'publish', 'post-type' => 'content-item'));
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
        $lesson_completion = (is_object($lesson_completion_record) ? intval($lesson_completion_record->progress) : null);
        $lesson_interactive = $this->get_lesson_interactive();

        return array(
            'lesson_id' => $this->lesson->ID,
            'lesson_slug' => $this->lesson->post_name,
            'lesson_title' => apply_filters ( 'the_title', $this->lesson->post_title ),
            'lesson_content' => apply_filters( 'the_content', $this->lesson->post_content ),
            "lesson_reading_time" => esc_html(get_field( "reading_time", $this->lesson->ID) ),
            'lesson_completion' => $lesson_completion,
            'lesson_interactive' => $lesson_interactive
        );

    }

    private function filter_page_content($page){

        // Check for Notes embed
        $page = $this->check_notes_embed($page);

        // Check for Video Embed


        return $page;

    }

    private function check_notes_embed($page){

        $content = $page->post_content;

        if (strpos($content, "[lfl2lnotestextbox]") === false)
            return $page;

        // Content constains shortcode, so render a Div with corresponding data attributes
        // https://lf.westernsydney.edu.au/p/learn2learn/wordpress-notes/notes-input.html?l2l_user_id=85daa4da50ba3931755b1960bf8f1083&l2l_module_id=473&l2l_page_id=35&l2l_item_id=224
        // l2l_user_id = $username, l2l_module_id = $category_id, l2l_page_id = $lesson_id, l2l_item_id = $page_id (lesson_page)

        // $page_id = $page->ID;
        // $lesson_id = $page->post_parent;
        // $category_id = wp_get_post_parent_id($lesson_id);
        // $iframe_url = get_site_url(null, "/wordpress-notes/notes-input.html");

        // $div = "<div class='lfl2lnotestextbox' data-l2l-module-id='" . $category_id . "' data-l2l-page-id='" . $lesson_id . "' data-l2l-item-id='" . $page_id . "' data-l2l-iframe-url='" . $iframe_url . "'></div>";
        // $content = str_replace("[lfl2lnotestextbox]", $div, $content);

        $iframe_url = get_site_url(null, "/wordpress-notes/notes-input.html");


        $html = '<div class="wp-block-embed">';
        $html .= '<div class="wp-block-embed__wrapper">';
        $html .= '<iframe class="lf-l2l-notes-iframe" data-l2l-iframe-url="' . $iframe_url . '"></iframe>';
        $html .= '</div>';
        $html .= '</div>';

        $page->post_content = str_replace("[lfl2lnotestextbox]", $html, $content);

        // TODO: Need to use JS to target .lfl2lnotestextbox and replace with iframe

        return $page;

    }

    private function get_lesson_interactive(){

        $filters = get_the_terms( $this->lesson->ID, 'filter' );
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

    private function get_pages_data_array(){

        $pages_array = array();
        $page_ids = array();

        foreach($this->pages as $key => $page){

            $page = $this->filter_page_content($page);

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

        if (!empty($page_ids)) {

            $pages_progress_records = $this->db_user_progress->select_user_progress_records_in($this->user_id, $page_ids);
            $pages_thumbs_records = $this->db_user_progress->select_user_thumbs_records_in($this->user_id, $page_ids);

            foreach($pages_array as $key => $page){

                for($i=0; $i < count($pages_progress_records); $i++){

                    if ($pages_progress_records[$i]->content_id == $page["page_id"]){
                        $pages_array[$key]["page_completion"] = intval($pages_progress_records[$i]->progress);
                    }

                }

                for ($i=0; $i < count($pages_thumbs_records); $i++){

                    if ($pages_thumbs_records[$i]->page_id == $page["page_id"]){
                        $pages_array[$key]["page_thumb"] = strval($pages_thumbs_records[$i]->thumbs);
                    }

                }

            }

        }

        return $pages_array;

    }

}