<?php
/**
 * Title: Hidden 404
 * Slug: www-open-spending-eu/hidden-404
 * Inserter: no
 */

if (!function_exists('useSvg')) {
    function useSvg($filename = 'long-arrow-right')
    {
        $icon = get_stylesheet_directory() . '/dist/images/icons/' . $filename . '.svg';

        $svg_icon_content = @file_get_contents($icon);

        return $svg_icon_content;
    }
}
?>
<!-- wp:spacer {"height":"var(--wp--preset--spacing--30)"} -->
<div style="height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"align":"wide","layout":{"type":"default"},"style":{"spacing":{"margin":{"top":"5px"}}}} -->
<div class="wp-block-group alignwide" style="margin-top:5px">
    <!-- wp:paragraph -->
    <div class="error-details py-10 md:py-14 lg:py-20">
        <div class="w-[300px] sm:w-[350px] h-[201px] relative mx-auto">
            <div class="four-first absolute left-0 top-0 text-[#427fa5] text-[150px] sm:text-[170px]">
                <?php echo useSvg('four'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
            </div>
            <div
                class="zero absolute left-[116px] sm:left-[132px] top-[70px] z-[30] text-[#d4cd40] text-[108px] sm:text-[124px]">
                <?php echo useSvg('zero'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
            </div>
            <div
                class="patch absolute left-[44px] sm:left-[60px] top-[154px] sm:top-[170px] z-[20] text-n-90 text-[30px]">
                <?php echo useSvg('patch'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
            </div>
            <div
                class="patch absolute left-[44px] sm:left-[60px] top-[164px] sm:top-[180px] z-[20] text-n-100 text-[20px]">
                <?php echo useSvg('patch-second'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
            </div>
            <div class="four-second absolute right-0 -top-5 z-[10] text-[#427fa5] text-[150px] sm:text-[170px]">
                <?php echo useSvg('four'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
            </div>
        </div>
        <div class="text-center">
            <h2 class="mt-6 sm:mt-10 mb-1 sm:mb-4 font-bold text-n-100">
                Looks like you are lost.
            </h2>
            <div class="text-base mb-[5px] text-n-80">
                We couldn’t find the page you’re looking for.
            </div>
            <div class="text-sm text-n-80 back-links">
                Go back to
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    homepage
                </a> or <a href="<?php echo esc_url(home_url('/contact-us')); ?>">
                    contact us </a>
                about a problem.
            </div>
        </div>
    </div>
    <!-- /wp:paragraph -->

</div>
<!-- /wp:group -->