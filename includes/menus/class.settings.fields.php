<?php
class Learn2Learn_Settings_Fields extends Learn2Learn_Menu_Options {

    private $options_group;
    private $section_id;

    function __construct() {

        $this->options_group = $this->page_slug . "-settings";

    }


}