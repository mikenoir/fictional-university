<?php

function universityRegisterSearch() {
    register_rest_route( 'university/v1', 'search', [
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => 'universitySearchResults'
    ]);
}
function universitySearchResults( $data ) {
    
    $mainQuery = new WP_Query([
        'post_type'         => [ 'post', 'page', 'professor', 'program', 'campus', 'event' ],
        'posts_per_page'    => -1,
        's'                 => sanitize_text_field($data['term'])
    ]);

    $results = [
        'generalInfo'   => [],
        'professors'    => [],
        'programs'      => [],
        'events'        => [],
        'campuses'      => []
    ];

    while( $mainQuery->have_posts() ) {
        $mainQuery->the_post();
        switch( get_post_type() ):
            case 'professor':
                array_push( $results['professors'], [
                    'title' => get_the_title(),
                    'permalink'   => get_the_permalink(),
                    'postType'  => get_post_type(),
                    'authorName'    => get_the_author() ,
                    'image' => get_the_post_thumbnail_url( false, 'landscape' )
                ]);
                break;
            case 'program':
                array_push( $results['programs'], [
                    'title' => get_the_title(),
                    'permalink'   => get_the_permalink(),
                    'postType'  => get_post_type(),
                    'authorName'    => get_the_author(),
                    'id'    => get_the_ID() 
                ]);
                break;
            case 'event':
                $eventDate = new DateTime( get_field( 'event_date' ) );
                $description = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 18 );
                array_push( $results['events'], [
                    'title' => get_the_title(),
                    'permalink'   => get_the_permalink(),
                    'postType'  => get_post_type(),
                    'authorName'    => get_the_author(),
                    'month' => $eventDate->format('M'),
                    'day'   =>  $eventDate->format('d'),
                    'description'   => $description
                ]);
                break;
            case 'campus':
                array_push( $results['campuses'], [
                    'title' => get_the_title(),
                    'permalink'   => get_the_permalink(),
                    'postType'  => get_post_type(),
                    'authorName'    => get_the_author() 
                ]);
                break;
            default:
                array_push( $results['generalInfo'], [
                    'title' => get_the_title(),
                    'permalink'   => get_the_permalink(),
                    'postType'  => get_post_type(),
                    'authorName'    => get_the_author() 
                ]);
                break;
        endswitch;
    }

    if( $results['programs'] ):

        $programsMetaQuery = [ 'relation'  => 'OR' ];

        foreach( $results['programs'] as $item ):
            array_push( $programsMetaQuery, [
                'key'   => 'related_programs',
                'compare'   => 'LIKE',
                'value'     => '"' . $item['id'] . '"'
            ]);
        endforeach;

        $programRelationshipQuery = new WP_Query([
            'post_type'     => [ 'professor', 'event' ],
            'meta_query'    => $programsMetaQuery
        ]);

        while( $programRelationshipQuery->have_posts() ):
            $programRelationshipQuery->the_post();
            if( get_post_type() == 'professor' ){
                array_push( $results['professors'], [
                    'title' => get_the_title(),
                    'permalink'   => get_the_permalink(),
                    'postType'  => get_post_type(),
                    'authorName'    => get_the_author() ,
                    'image' => get_the_post_thumbnail_url( false, 'professorLandscape' )
                ]);
            }
            if( get_post_type() == 'event' ){
                $eventDate = new DateTime( get_field( 'event_date' ) );
                $description = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 18 );
                array_push( $results['events'], [
                    'title' => get_the_title(),
                    'permalink'   => get_the_permalink(),
                    'postType'  => get_post_type(),
                    'authorName'    => get_the_author(),
                    'month' => $eventDate->format('M'),
                    'day'   =>  $eventDate->format('d'),
                    'description'   => $description
                ]);
            }
        endwhile;

        $results['professors'] = array_values( array_unique( $results['professors'], SORT_REGULAR ) );
        $results['events'] = array_values( array_unique( $results['events'], SORT_REGULAR ) );
    endif;

    return $results;
}
add_action( 'rest_api_init', 'universityRegisterSearch' );