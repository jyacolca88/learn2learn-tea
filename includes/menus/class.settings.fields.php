<?php
class Learn2Learn_Settings_Fields extends Learn2Learn_Menu_Options {

    private $options_group;
    private $section_id;
    private $section_title;
    private $field_id;
    private $field_title;
    private $field_args;

    function __construct() {

        $this->options_group = $this->page_slug . "-settings";

    }

    public function add_section($section_id, $section_title){

        $this->section_id = sanitize_text_field($section_id);
        $this->section_title = sanitize_text_field($section_title);
        add_settings_section($this->section_id, $this->section_title, '', $this->page_slug);

    }

    public function register_and_add_field($field_id, $field_title, $field_type="text", $field_args = array()){

        $this->field_id = sanitize_text_field($field_id);
        $this->field_title = sanitize_text_field($field_title);
        $this->field_args = array(
            "label_for" => $this->field_id,
            "name" => $this->field_id
        );

        if (!empty($field_args))
            $this->field_args = array_merge($this->field_args, $field_args);

        switch($field_type){

            case "text":
                $sanitize_method = "sanitize_text_field";
                $render_method = "render_text_field";
                break;

            case "textarea":
                $sanitize_method = "sanitize_textarea_field";
                $render_method = "render_textarea_field";
                break;

            case "checkbox":
                $sanitize_method = "sanitize_checkbox_field";
                $render_method = "render_checkbox_field";
                break;

        }


        if(!$sanitize_method || !$render_method)
            return;

        // Register setting
        register_setting($this->options_group, $this->field_id, array($this, $sanitize_method));
        // Add field
        add_settings_field($this->field_id, $this->field_title, array($this, $render_method), $this->page_slug, $this->section_id, $this->field_args);

    }

    public function render_text_field($args){

        extract($this->retrieve_values_from_args($args));

        printf(
            "<input type='text' id='%s' name='%s' value='%s' style='%s' />",
            $name, $name, $value, $style
        );

    }

    public function render_textarea_field($args){

        extract($this->retrieve_values_from_args($args));

        printf(
            "<textarea id='%s' name='%s' style='%s'>%s</textarea>",
            $name, $name, $style, $value
        );

    }

    public function render_checkbox_field($args){

        extract($this->retrieve_values_from_args($args));
        $checkbox_html = "<label><input type='checkbox' id='%s' name='%s' style='%s' " . checked( $value, 'yes', false )  . " /> On</label>";

        printf(
            $checkbox_html,
            $name, $name, $style
        );

    }

    private function retrieve_values_from_args($args){

        $name = (isset($args["name"]) ? sanitize_text_field($args["name"]) : "");
        $value = (get_option($name) !== null ? sanitize_text_field(get_option($name)) : "");
        $style = (isset($args["style"]) ? esc_attr(($args["style"])) : "");

        return array(
            "name" => $name,
            "value" => $value,
            "style" => $style
        );

    }

    public function sanitize_text_field($value){

        return sanitize_text_field((trim($value)));

    }

    public function sanitize_textarea_field($value){

        return strval((trim($value)));

    }

    public function sanitize_checkbox_field($value){

        return (isset($value) ? 'yes' : 'no');

    }


}