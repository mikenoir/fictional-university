<?php
get_header();
while (have_posts()) :
    the_post(); ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?= get_field('page_banner_background_image') ? get_field('page_banner_background_image')['sizes']['pageBanner'] : get_theme_file_uri('images/ocean.jpg'); ?>);"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?= the_title(); ?></h1>
            <div class="page-banner__intro">
                <p><?php the_field('page_banner_subtitle');?></p>
            </div>
        </div>
    </div>
    <div class="container container--narrow page-section">
        <div class="generic-content">
            <div class="row group">
                <div class="one-third">
                    <?php the_post_thumbnail('professorPortrait'); ?>
                </div>
                <div class="two-thirds">
                    <?php
                    $likeCount = new WP_Query([
                        'post_type'         => 'like',
                        'meta_query'        => [
                            [
                                'key'       => 'like_professor_id',
                                'compare'   => '=',
                                'value'     => get_the_ID()
                            ]
                        ],
                        'posts_per_page'    => -1
                    ]);
                    
                    $existStatus = 'no';
                    
                    if( is_user_logged_in() ) {
                        $existQuery = new WP_Query([
                            'author'            => get_current_user_id(),
                            'post_type'         => 'like',
                            'meta_query'        => [
                                [
                                    'key'       => 'like_professor_id',
                                    'compare'   => '=',
                                    'value'     => get_the_ID()
                                ]
                            ],
                            'posts_per_page'    => -1
                        ]);
    
                        if( $existQuery->found_posts ) {
                            $existStatus = 'yes';
                        }
                    } ?>
                    <span class="like-box" data-like="<?=$existQuery->found_posts != 0 ? $existQuery->posts[0]->ID : null; ?>" data-exists="<?=$existStatus;?>" data-professor="<?php the_ID();?>">
                        <i class="fa fa-heart-o" aria-hidden="true"></i>
                        <i class="fa fa-heart" aria-hidden="true"></i>
                        <span class="like-count"><?=$likeCount->found_posts;?></span>
                    </span>
                    <?php the_content(); ?>
                </div>
            </div>
        </div>

        <?php
        $relatedPrograms = get_field('related_programs');
        if (!empty($relatedPrograms)) : ?>
            <hr class="section-break">
            <h2 class="headline headline--medium">Subjects Taught</h2>
            <ul class="link-list min-list">
                <?php
                foreach ($relatedPrograms  as $program) :
                    echo '<li><a href="' . get_the_permalink($program) . '">' . get_the_title($program) . '</a></li>';
                endforeach;  ?>
            </ul>
        <?php
        endif; ?>
    </div>
<?php
endwhile;
get_footer(); ?>