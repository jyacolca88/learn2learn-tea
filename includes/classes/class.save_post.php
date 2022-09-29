<?php
class Learn2Learn_Save_Post{

    private $post;
    private $post_id;
    private $post_content;

    function __construct($post_id){

        $post = get_post($post_id);
        $is_autosave = wp_is_post_autosave( $post_id );
        $is_revision = wp_is_post_revision( $post_id );

        if (!$is_autosave && !$is_revision && !empty($post)){
            $this->post = $post;
            $this->post_id = $post->ID;
            $this->post_content = $post->post_content;
        }
            
    }

    // change the video to have a wrapper of 16:9
    public function wrap_youtube_iframe_with_16_9_responsive_ratio(){

        // if (!$this->post);
        //     return;

        $this->post_content = $this->post_content . "<p>hello world 123</p>";

    }

    public function update_post(){

        // if (!$this->post);
        //     return;

        remove_action('save_post', 'lf_l2l_save_post');

        wp_update_post(array(
            'ID'           => $this->post_id,
            'post_content' => wpautop(trim($this->post_content))
        ));

        add_action('save_post', 'lf_l2l_save_post');

    }

    // change if notes shortcode detected, render actual html



}