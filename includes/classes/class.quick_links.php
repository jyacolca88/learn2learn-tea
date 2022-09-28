<?php
class Learn2Learn_Quick_Links extends Learn2Learn_Database {

    private $quick_links_name;
    private $quick_links_menu;

    function __construct(){

        $menu_object = $this->get_nav_menu_object_by_location("quick-links-menu") ?? false;

        $quick_links_name = isset($menu_object) ? $menu_object->name : false;
        $quick_links_menu = $this->get_nav_menu_items_by_object_name( $quick_links_name ) ?? false;

        $this->quick_links_name = $quick_links_name;
        $this->quick_links_menu = $quick_links_menu;

    }

    public function get_quick_links(){

        return $this->quick_lnks_menu;

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