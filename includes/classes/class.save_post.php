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
            $dom->loadHTML( $this->post_content );

            $images = $dom->getElementsByTagName( 'img' );

            foreach ( $images as $image ) {

                if ( empty( $image->getAttribute( 'alt' ) ) ) {

                    $src = $image->getAttribute( 'src' );
                    $alt = pathinfo( $src, PATHINFO_FILENAME );

                    $image->setAttribute( 'alt', $alt );

                }
            }

            $this->post_content = $dom->saveHTML();
        // $this->post_content = apply_filters('the_content', $this->post_content) . "<p>hello world 123</p>";

    }

    // Detect Notes shortcode and change to become iframe with parameters - [lfl2lnotestextbox]
    // https://lf.westernsydney.edu.au/p/learn2learn/wordpress-notes/notes-input.html?l2l_user_id=85daa4da50ba3931755b1960bf8f1083&l2l_module_id=473&l2l_page_id=35&l2l_item_id=224
    // l2l_user_id = $username, l2l_module_id = $category_id, l2l_page_id = $lesson_id, l2l_item_id = $page_id (lesson_page)
    public function render_notes_feature(){

        if (!isset($this->post))
            return;

        $page_id = $this->post_id;
        $lesson_id = $this->post->post_parent;
        $category_id = wp_get_post_parent_id($lesson_id);

        $html = "<div class='wp-block'>";
        $html .= "<div class='wp-block-embed_wrapper'>";
        $html .= "<iframe width='800' height='600' class'lf-l2l-notes-iframe'></iframe>";
        $html .= "</div>";
        $html .= "</div>";

        $render = "<div class='lf-l2l-render-notes' data-l2l-module-id='' data-l2l-page-id='' data-l2l-item-id=''></div>";

        $content = str_replace($vowels, "", $this->post_content);

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