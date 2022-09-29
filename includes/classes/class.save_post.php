<?php
class Learn2Learn_Save_Post{

    private $post;
    private $post_id;
    private $post_content;

    function __construct($post_id){

        $post = get_post($post_id);
        $is_autosave = wp_is_post_autosave( $post_id );
        $is_revision = wp_is_post_revision( $post_id );

        if (!$is_autosave && !$is_revision && !empty($post) && $post->post_type == "content-item"){
            $this->post = $post;
            $this->post_id = $post->ID;
            $this->post_content = $post->post_content;
        }
            
    }

    // change the video to have a wrapper of 16:9
    public function wrap_youtube_iframe_with_16_9_responsive_ratio(){

        if (!isset($this->post))
            return;

            $dom = new DOMDocument();
            $post->loadHTML( $content );

            $images = $dom->getElementsByTagName( 'img' );

            foreach ( $images as $image ) {

                if ( empty( $image->getAttribute( 'alt' ) ) ) {

                    $src = $image->getAttribute( 'src' );
                    $alt = pathinfo( $src, PATHINFO_FILENAME );

                    $image->setAttribute( 'alt', $alt );

                }
            }

            $content = $dom->saveHTML();
        // $this->post_content = apply_filters('the_content', $this->post_content) . "<p>hello world 123</p>";

    }

    public function update_post(){

        if (!isset($this->post))
            return;

        remove_action('save_post', 'lf_l2l_save_post');

        wp_update_post(array(
            'ID'           => $this->post_id,
            'post_content' => wpautop(trim($this->post_content))
        ));

        add_action('save_post', 'lf_l2l_save_post');

    }

    // change if notes shortcode detected, render actual html



}