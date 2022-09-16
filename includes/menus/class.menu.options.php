<?php
class Learn2Learn_Menu_Options {

    protected $page_title;
    protected $page_link_text;
    protected $user_capabilities;
    protected $page_slug = "l2l-options";
    protected $page_link_icon;

    function __construct() {

        $this->page_title = "Learn2Learn Options Menu";
        $this->page_link_text = "L2L Options";
        $this->user_capabilities = "manage_options";
        $this->page_link_icon = "dashicons-lightbulb";

    }

    public function add_menu_page(){

        add_menu_page(
            $this->page_title,
            $this->page_link_text,
            $this->user_capabilities,
            $this->page_slug,
            array($this,'render_page'),
            $this->page_link_icon,
            3
        );

    }

    private function render_page(){

        require_once get_template_directory() . '/includes/menus/menu.options.content.php';

    }

}
