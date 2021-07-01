<?php

add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes() {
    register_rest_route( 'university/v1', 'manageLike', [
        'methods'   => 'POST',
        'callback'  => 'createLike'
    ]);
    register_rest_route( 'university/v1', 'manageLike', [
        'methods'   => 'DELETE',
        'callback'  => 'deleteLike'
    ]);
}

function createLike( $data ) {
    
    if( is_user_logged_in() ) {
        $existQuery = new WP_Query([
            'author'            => get_current_user_id(),
            'post_type'         => 'like',
            'meta_query'        => [
                [
                    'key'       => 'like_professor_id',
                    'compare'   => '=',
                    'value'     => sanitize_text_field($data['professorId'])
                ]
            ],
            'posts_per_page'    => -1
        ]);
        if( $existQuery->found_posts == 0 && get_post_type(sanitize_text_field($data['professorId'])) == 'professor' ) {
            return wp_insert_post([
                'post_type'     => 'like',
                'post_status'   => 'publish',
                'post_title'    => 'Our PHP Create Post Test',
                'meta_input'    => [
                    'like_professor_id' => sanitize_text_field($data['professorId'])
                ]
            ]);
        } else {
            die("Invalid professor ID");
        }
    }
    else {
        die("Only logged in users can create a Like.");
    }
}

function deleteLike( $data ) {
    $likeId = sanitize_text_field( $data['like'] );
    if( get_current_user_id() == get_post_field( 'post_author', $likeId) && get_post_type( $likeId ) == 'like' ) {
        wp_delete_post( $likeId, true );
        return 'Congrats';
    } else {
        die("You do not have permission to delete that.");
    }
}