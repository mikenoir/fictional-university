<?php

if( ! is_user_logged_in() ) {
    wp_redirect( site_url('/') );
    exit;
}
else {
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
        <div class="create-note">
            <h2 class="headline headline--medium">Create New Note</h2>
            <input class="new-note-title" type="text" placeholder="Title">
            <textarea class="new-note-body" name="" id="" placeholder="Your note here..."></textarea>
            <span class="submit-note">Create Note</span>
            <span class="note-limit-message">Note limit reached: delete an existing note to make room for a new one.</span>
        </div>
        <ul class="min-list link-list" id="my-notes">
            <?php
            $userNotes = new WP_Query([
                'post_type'         => 'note',
                'posts_per_page'    => -1,
                'author'            => get_current_user_id()
            ]);
            
            while( $userNotes->have_posts() ) {
                $userNotes->the_post(); ?>
                <li data-id="<?php the_ID(); ?>">
                    <input readonly class="note-title-field" value="<?=str_replace('Private: ', '', esc_attr(get_the_title()));?>">
                    <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
                    <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                    <textarea readonly class="note-body-field"><?=esc_attr(wp_strip_all_tags(get_the_content()));?></textarea>
                    <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
<?php
}
get_footer(); } ?>