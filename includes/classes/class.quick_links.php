<?php
class Learn2Learn_Quick_Links extends Learn2Learn_Database {

    private $quick_links_name;
    private $quick_links_menu;
    private $quick_links;

    function __construct(){

        $menu_object = $this->get_nav_menu_object_by_location("quick-links-menu") ?? false;

        $quick_links_name = isset($menu_object) ? $menu_object->name : false;
        $quick_links_menu = $this->get_nav_menu_items_by_object_name( $quick_links_name ) ?? false;

        $this->quick_links_name = $quick_links_name;
        $this->quick_links_menu = $quick_links_menu;

        $this->format_quick_links();

    }

    public function get_quick_links(){

        return $this->quick_links;

    }

    private function format_quick_links(){

        if (!is_array($this->quick_links_menu))
            return;

        $formatted_quick_links = array();

        foreach($this->quick_links_menu as $menu_item){

            $iframe_url = (get_field( "iframe_url", $menu_item->object_id) ? esc_url(get_field( "iframe_url", $menu_item->object_id)) : false);
            $lesson = get_field("lesson", $menu_item->object_id);

            if (get_field("lesson", $menu_item->object_id) && is_object(get_field("lesson", $menu_item->object_id))){
                
            }

            array_push($formatted_quick_links, array(
                "title" => $menu_item->title,
                "iframe_url" => $iframe_url,
                "lesson" => $lesson
            ));

        }

        $this->quick_links = $formatted_quick_links;

    }

    private function get_nav_menu_object_by_location($location){

        $locations = get_nav_menu_locations();
        $object = wp_get_nav_menu_object( $locations[$location] );

        return $object;

    }

    private function get_nav_menu_items_by_object_name( $name ){

        return wp_get_nav_menu_items( $name );

    }

} 