<?php

/*********************************************************/
/******* LEARN 2 LEARN TEA - WP REST API [BEGIN] *********/
/*********************************************************/

require_once get_template_directory() . '/includes/routes/class.route.contentitems.php';
require_once get_template_directory() . '/includes/routes/class.route.categories.php';
require_once get_template_directory() . '/includes/routes/class.route.lessons.php';
require_once get_template_directory() . '/includes/routes/class.route.questionnaire.php';
require_once get_template_directory() . '/includes/routes/class.route.thumbs.php';


function lf_l2l_register_rest_routes(){

    $content_items_route = new Contentitems_Learn2Learn_Custom_Route();
    $content_items_route->register_routes();

    $categories_route = new Categories_Learn2Learn_Custom_Route();
    $categories_route->register_routes();

    $lessons_route = new Lessons_Learn2Learn_Custom_Route();
    $lessons_route->register_routes();

    $questionnaire_route = new Questionnaire_Learn2Learn_Custom_Route();
    $questionnaire_route->register_routes();

    $thumbs_route = new Thumbs_Learn2Learn_Custom_Route();
    $thumbs_route->register_routes();

}

add_action( 'rest_api_init', 'lf_l2l_register_rest_routes' );

// ROUTES:
// getcontentitems
// getcategories
// getcategory
// getlessons
// getlesson

/*******************************************************/
/******* LEARN 2 LEARN TEA - WP REST API [END] *********/
/*******************************************************/