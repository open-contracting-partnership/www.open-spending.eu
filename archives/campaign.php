<?php
/**
 * Archive listing for campaigns, in editor-set order.
 *
 * @package OpenSpendingCoalition
 */

$current_id = get_the_ID();
$posttype   = get_post_type( $current_id );

if ( have_posts() ) {
	?>
	<?php echo breadcrumb_section( $current_id ); ?>
<div class="container">
	<div class="<?php echo esc_attr( $posttype ); ?> py-10 sm:py-14 lg:pt-16 lg:pb-32 grid gird-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-7 gap-y-6">
		<?php
		while ( have_posts() ) {
			the_post();
			$card_id     = get_the_ID();
			$permalink   = get_the_permalink( $card_id );
			$post_title  = get_the_title();
			$feature_img = ( has_post_thumbnail() ) ? get_the_post_thumbnail_url() : get_template_directory_uri() . '/dist/images/default-post-img.jpg';
			$excerpt     = excerpt();
			?>

		<div data-id="<?php echo (int) $card_id; ?>" class="card-subtle-hover bg-n-0 rounded-3xl">
			<a href="<?php echo esc_url( $permalink ); ?>">
				<div class="pt-[100%] relative card-image-container">
					<img src="<?php echo esc_url( $feature_img ); ?>" alt="<?php echo esc_attr( $post_title ); ?>"
						class="absolute top-0 h-full w-full object-cover rounded-t-3xl">
				</div>
			</a>
			<div class="info bg-n-0 px-8 py-6 rounded-b-3xl">
				<a href="<?php echo esc_url( $permalink ); ?>" class="text-lg font-bold !text-n-100 card-title-hover">
					<?php echo esc_html( $post_title ); ?>
				</a>
				<p class="mt-2.5 text-sm text-n-60 mb-4"> <?php echo esc_html( $excerpt ); ?></p>
				<a href="<?php echo esc_url( $permalink ); ?>" class="flex gap-x-2.5 items-center learn-more-btn">
					Learn More
					<?php echo inline_svg( 'right-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
				</a>
			</div>
		</div>

		<?php } ?>
	</div>
</div>
	<?php
}
echo main_query_pagination();
