<?php
get_header();

while (have_posts()) {
    the_post(); ?>

    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?=get_theme_file_uri('images/ocean.jpg');?>);"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?=the_title();?></h1>
            <div class="page-banner__intro">
                <p>DON'T FORGET TO REPLACE ME LATER.</p>
            </div>
        </div>
    </div>

    <div class="container container--narrow page-section">
        <?php
        $parentID = wp_get_post_parent_id( get_the_ID() );
        if( $parentID ) { ?>
            <div class="metabox metabox--position-up metabox--with-home-link">
                <p>
                    <a class="metabox__blog-home-link" href="<?=get_the_permalink( $parentID );?>">
                        <i class="fa fa-home" aria-hidden="true"></i> 
                        Back to <?=get_the_title( $parentID );?>
                    </a> 
                    <span class="metabox__main"><?=the_title();?></span>
                </p>
            </div>
            <?php
        } 
        $isParent = get_pages([
            'child_of'  => get_the_ID()
        ]);
        if( $parentID or  $isParent ) { ?>
            <div class="page-links">
                <h2 class="page-links__title"><a href="<?=get_the_permalink( $parentID );?>"><?=get_the_title( $parentID );?></a></h2>
                <ul class="min-list">
                    <?php 
                    $findChildrenOf = $parentID ? $parentID : get_the_ID();
                    wp_list_pages([
                        'title_li'  => false,
                        'child_of'  => $findChildrenOf
                    ]); ?>
                </ul>
            </div>
            <?php
        } ?>

        <div class="generic-content">
            <?=the_content();?>
        </div>

    </div>
<?php
}
get_footer(); ?>