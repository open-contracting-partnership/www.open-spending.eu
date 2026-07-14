<?php

$id = get_the_ID();
$posttype = get_post_type($id);
$feature_img = get_the_post_thumbnail_url();

$campaign = get_field('campaign');
$campaign_videos = (is_array($campaign) && isset($campaign['campaign_videos'])) ? $campaign['campaign_videos'] : null;

?>

<?php echo breadcrumb_section($id); ?>

<div class="container py-10 sm:pt-16 lg:pb-28">

    <div class="<?php echo esc_attr($posttype . '-' . $id); ?> ">
        <?php if ($feature_img) { ?>
            <div class="pt-[45%] relative">
                <img src="<?php echo esc_url($feature_img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class=" absolute top-0 h-full w-full object-cover rounded-xl">
            </div>
        <?php } ?>

        <div class="py-8 sm:pb-16 sm:pt-14 lg:pt-20 block sm:grid gap-x-4 grid-cols-1 sm:grid-cols-12">
            <div class="sm:col-span-1 lg:col-span-3">
            </div>
            <div class="lg:col-start-4 sm:col-span-10 lg:col-span-7 text-lg news-detail-content">
                <?php the_content(); ?>
            </div>
        </div>
    </div>

    <!-- Campaign videos -->
    <?php if ($campaign_videos) { ?>
        <div class="campaign-videos">
            <div>
                <h2 class="font-bold">Campaign videos</h2>
            </div>
            <hr class="bg-n-100 h-1">
            <div class="py-8 sm:pt-9 sm:pb-12 grid gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($campaign_videos as $video) {
                    $youtube_embed_url = $video['youtube_embed_url'];
                    $thumbnail_image   = ($video['thumbnail_image']) ? $video['thumbnail_image'] : get_template_directory_uri() . '/dist/images/sample-image.jpg';
                    $video_title       = ($video['video_title']) ? $video['video_title'] : 'Poverty in Europe';
                    $has_video         = (bool) $youtube_embed_url;
                    $thumb_class       = $has_video ? 'campaign-vid-thumbnail' : 'campaign-vid-thumbnail--empty';
                ?>
                    <div>
                        <div class="pt-[60%] <?php echo esc_attr($thumb_class); ?> relative"<?php if ($has_video) : ?> data-src="<?php echo esc_url($youtube_embed_url); ?>"<?php endif; ?>>
                            <img src="<?php echo esc_url($thumbnail_image); ?>" alt="<?php echo esc_attr($video_title); ?>" class="absolute top-0 h-full w-full object-cover rounded-xl">

                            <div class="video-title absolute left-5 bottom-4 z-20">
                                <p class="font-bold text-n-0"><?php echo esc_html($video_title); ?></p>
                            </div>
                        </div>

                        <?php if ($has_video) : ?>
                        <div class="fixed z-30 top-0 left-0 w-screen h-screen video-page">
                            <div class="relative flex  items-center justify-center h-full container">
                                <iframe width="642px" height="361px" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                <div tabindex="0" class="absolute p-3 text-xs text-white border border-white border-solid rounded-full cursor-pointer video-close top-4 right-4">
                                    <?php echo useSvg('cross-icon'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>


    <!-- Related news  -->
    <?php
    $args = array(
        'post_type'      => 'news',
        'posts_per_page' => 3,
        'post_status'    => array('publish'),
        'meta_query'     => array(
            array(
                'key'     => 'realted_campaign_realted_campaign',
                'value'   => $id,
                'compare' => 'LIKE',
            ),
        ),
    );
    $current_id = $id;
    $the_query = new WP_Query($args);
    if ($the_query->have_posts()) { ?>
        <div class="related-news">
            <h2 class="font-bold">Related news</h2>
            <hr class="bg-n-100 h-1">
            <div class="pt-9 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                <?php
                while ($the_query->have_posts()) {
                    $the_query->the_post();
                    $news_id = get_the_ID();
                    $excerpt = excerpt(115);
                    $feature_img = (has_post_thumbnail()) ? get_the_post_thumbnail_url() : get_template_directory_uri() . '/dist/images/default-post-img.jpg';
                ?>
                    <div id="news_<?php echo (int) $news_id; ?>" class="p-5 border border-n-30 rounded-3xl news-card-hover">
                        <div class="pt-[63.8%] relative">
                            <a href="<?php echo esc_url(get_the_permalink()); ?>">
                                <img src="<?php echo esc_url($feature_img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class=" absolute top-0 h-full w-full object-cover rounded-2xl ">
                            </a>
                        </div>
                        <div class="pt-6">
                            <a href="<?php echo esc_url(get_the_permalink()); ?>" class="text-heading-3 font-bold !text-n-100 card-title-hover leading-tight">
                                <?php echo esc_html(get_the_title()); ?></a>
                            <p class="mt-2 text-sm text-n-60 mb-4"> <?php echo esc_html($excerpt); ?></p>
                            <a href="<?php echo esc_url(get_the_permalink()); ?>" class="flex gap-x-2.5 items-center learn-more-btn">
                                Learn more
                                <?php echo useSvg('right-arrow'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="flex justify-center mt-8">
                <a href="<?php echo esc_url(add_query_arg('campaign_post', $current_id, home_url('/news/'))); ?>" class="!text-n-100 font-bold py-2.5 px-4 border border-teal rounded-lg transition-all duration-300 hover:bg-teal hover:!text-n-0">
                    View all news
                </a>
            </div>
        </div>
    <?php }
    wp_reset_postdata();
    ?>
</div>


<!--  Other Campaigns  -->
<div class="bg-n-10">
    <div class="container py-10 sm:py-14 md:pt-24 md:pb-32">
        <h2 class="font-bold">Other Campaigns</h2>
        <?php
        $args = array(
            'post_type'      => 'campaign',
            'posts_per_page' => 2,
            'post_status'    => array('publish'),
            'post__not_in'   => array($id),
        );
        $the_query = new WP_Query($args);
        if ($the_query->have_posts()) { ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mt-8">
                <?php
                while ($the_query->have_posts()) {
                    $the_query->the_post();
                    $other_campaign_id = get_the_ID();
                    $excerpt = excerpt(115);
                    $feature_img = (has_post_thumbnail()) ? get_the_post_thumbnail_url() : get_template_directory_uri() . '/dist/images/default-post-img.jpg';
                ?>
                    <div id="campaign_<?php echo (int) $other_campaign_id; ?>" class="card-subtle-hover bg-n-0 rounded-3xl">
                        <div class="pt-[65%] relative card-image-container">
                            <a href="<?php echo esc_url(get_the_permalink()); ?>">
                                <img src="<?php echo esc_url($feature_img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class=" absolute top-0 h-full w-full object-cover rounded-t-3xl">
                            </a>
                        </div>
                        <div class="bg-n-0 px-8 sm:px-12 py-6 rounded-b-3xl">
                            <a href="<?php echo esc_url(get_the_permalink()); ?>" class="text-lg font-bold !text-n-100 card-title-hover">
                                <?php echo esc_html(get_the_title()); ?></a>
                            <p class="mt-2.5 text-sm text-n-60 mb-4"> <?php echo esc_html($excerpt); ?></p>
                            <a href="<?php echo esc_url(get_the_permalink()); ?>" class="flex gap-x-2.5 items-center learn-more-btn">
                                Learn More
                                <?php echo useSvg('right-arrow'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php }
        wp_reset_postdata();
        ?>
    </div>
</div>
