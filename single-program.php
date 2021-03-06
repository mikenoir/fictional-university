<?php
get_header();
while (have_posts()) :
    the_post(); ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?= get_theme_file_uri('images/ocean.jpg'); ?>);"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?= the_title(); ?></h1>
            <div class="page-banner__intro">
                <p>DON'T FORGET TO REPLACE ME LATER.</p>
            </div>
        </div>
    </div>
    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p>
                <a class="metabox__blog-home-link" href="<?=get_post_type_archive_link( 'program' ); ?>">
                    <i class="fa fa-home" aria-hidden="true"></i>
                    All Programs
                </a>
                <span class="metabox__main"><?=the_title();?></span>
            </p>
        </div>
        <div class="generic-content">
            <?= the_content(); ?>
        </div>
        <?php
        $relatedProfessors = new WP_Query([
            'post_type'			=> 'professor',
            'posts_per_page'	=> -1,
            'orderby'			=> 'title',
            'order'				=> 'ASC',
            'meta_query'		=> [
                [
                    'key'       => 'related_programs',
                    'compare'   => 'LIKE',
                    'value'     => '"'.get_the_ID().'"'
                ]
            ]
        ]);
        if( $relatedProfessors->have_posts() ):
            echo '<hr class="section-break"><h2 class="headline headline--medium">'.get_the_title().' Professors</h2> <ul class="professor-cards">';
            while( $relatedProfessors->have_posts() ):
                $relatedProfessors->the_post(); ?>
                <li class="professor-card__list-item">
                    <a class="professor-card" href="<?php the_permalink() ?>">
                        <img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscape'); ?>" alt="">
                        <span class="professor-card__name"><?php the_title(); ?></span>
                    </a>
                </li>
                <?php
            endwhile; 
            echo '</ul>';
        endif;
        wp_reset_postdata();

        $today = date('Ymd');
        $homePageEvents = new WP_Query([
            'post_type'			=> 'event',
            'posts_per_page'	=> 2,
            'orderby'			=> 'meta_value_num',
            'meta_key'			=> 'event_date',
            'order'				=> 'ASC',
            'meta_query'		=> [
                [
                    'key'		=> 'event_date',
                    'compare'	=> '>=',
                    'value'		=>  $today,
                    'type'		=> 'numeric'
                ],
                [
                    'key'       => 'related_programs',
                    'compare'   => 'LIKE',
                    'value'     => '"'.get_the_ID().'"'
                ]
            ]
        ]);
        if( $homePageEvents->have_posts() ):
            echo '<hr class="section-break"><h2 class="headline headline--medium">Upcoming '.get_the_title().' Events</h2>';
            while( $homePageEvents->have_posts() ):
                $homePageEvents->the_post(); ?>
                <div class="event-summary">
                    <a class="event-summary__date t-center" href="<?=the_permalink();?>">
                        <span class="event-summary__month">
                            <?php
                            $eventDate = new DateTime( get_field( 'event_date' ) );
                            echo $eventDate->format('M'); ?>
                        </span>
                        <span class="event-summary__day">
                            <?php
                            $eventDate = new DateTime( get_field( 'event_date' ) );
                            echo $eventDate->format('d'); ?>
                        </span>
                    </a>
                    <div class="event-summary__content">
                        <h5 class="event-summary__title headline headline--tiny">
                            <a href="<?=the_permalink();?>"><?=the_title();?></a>
                        </h5>
                        <p>
                            <?=has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 18 ); ?>
                            <a href="<?=the_permalink();?>" class="nu gray">Learn more</a>
                        </p>
                    </div>
                </div>
                <?php
            endwhile; 
        endif;
        wp_reset_postdata(); ?>
    </div>
<?php
endwhile;
get_footer(); ?>