<?php

$id = get_the_ID();
$posttype = get_post_type( $id );
$feature_img = get_the_post_thumbnail_url();
$published_date = get_the_date( 'd M, Y' );
$tags = get_the_tags();

$realted_campaign = get_field( 'realted_campaign' );
$realted_campaign = (is_array( $realted_campaign ) && isset( $realted_campaign['realted_campaign'] )) ? $realted_campaign['realted_campaign'] : null;

?>

<?php echo breadcrumb_section( $id ); ?>
<div class="container py-10 sm:pt-16 lg:pb-20">

	<div class="<?php echo esc_attr( $posttype . '-' . $id ); ?> ">
		<?php if ( $feature_img ) { ?>
			<div class="pt-[45%] relative">
				<?php render_feature_image( array( 'lazy' => false, 'priority' => true, 'sizes' => '(max-width: 1200px) 100vw, 1200px', 'class' => 'absolute top-0 h-full w-full object-cover rounded-xl' ) ); ?>
			</div>
		<?php } ?>
		<div class="pt-8 sm:pt-14 lg:pt-20 grid gap-x-4 grid-cols-1 sm:grid-cols-12">
			<div class="sm:col-span-3">
				<div class="flex items-center gap-x-4 sm:block">
					<p class="text-lg font-bold">Published date</p>
					<p class="text-n-60 text-sm mt-1"> <?php echo esc_html( $published_date ); ?> </p>
				</div>

				<?php if ( $tags ) { ?>
					<div class="mt-4 sm:mt-9">
						<p class="text-lg font-bold">Related tags</p>
						<ul class="flex gap-x-4 sm:block">
							<?php foreach ( $tags as $tag ) { ?>
								<li class="text-sm px-2 py-1 bg-n-10 rounded-xl max-w-fit mt-2.5" data-tag="<?php echo esc_attr( $tag->slug ); ?>">
									<?php echo esc_html( $tag->name ); ?> </li>
							<?php } ?>
						</ul>
					</div>
				<?php } ?>

			</div>
			<div class="mt-8 sm:mt-0 sm:col-start-4 sm:col-span-9 lg:col-span-7 text-base sm:text-lg single-detail-content">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
</div>

<div class="bg-n-10">
	<?php if ( $realted_campaign ) { ?>
		<div class="realted_campaign container py-10 sm:py-14 lg:pt-24 lg:pb-32">
			<h2 class="font-bold">Related Campaigns</h2>
			<div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mt-8">
				<?php foreach ( $realted_campaign as $campaign_id ) {
					$campaign_title       = get_the_title( $campaign_id );
					$campaign_permalink   = get_the_permalink( $campaign_id );
				?>
					<div class="campaign">
						<a href="<?php echo esc_url( $campaign_permalink ); ?>">
							<div class="pt-[65%] relative">
								<?php render_feature_image( array( 'post_id' => $campaign_id, 'alt' => $campaign_title, 'class' => 'absolute top-0 h-full w-full object-cover rounded-t-3xl' ) ); ?>
							</div>
							<div class="bg-n-0 px-8 sm:px-12 py-6 rounded-b-3xl">
								<span class="text-lg font-bold !text-n-100"><?php echo esc_html( $campaign_title ); ?></span>
								<span class="flex gap-x-2.5 items-center learn-more-btn mt-4">
									Learn More
									<?php echo useSvg( 'right-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
								</span>
							</div>
						</a>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</div>
