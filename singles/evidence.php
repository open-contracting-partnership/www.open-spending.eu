<?php

$current_id = get_the_ID();
$posttype = get_post_type( $current_id );
$feature_img = get_the_post_thumbnail_url();
$published_date = get_the_date( 'd M, Y' );
$tags = get_the_tags();

?>

<?php echo breadcrumb_section( $current_id ); ?>

<div class="container py-10 sm:pt-16 lg:pb-20">

	<div class="<?php echo esc_attr( $posttype . '-' . $current_id ); ?> ">
		<?php if ( $feature_img ) { ?>
			<div class="pt-[45%] relative">
				<img src="<?php echo esc_url( $feature_img ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class=" absolute top-0 h-full w-full object-cover rounded-xl">
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
							<?php foreach ( $tags as $post_tag ) { ?>
								<li class="text-sm px-2 py-1 bg-n-10 rounded-xl max-w-fit mt-2.5" data-tag="<?php echo esc_attr( $post_tag->slug ); ?>">
									<?php echo esc_html( $post_tag->name ); ?> </li>
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

<?php render_other_posts_grid( 'evidence', $current_id, 'Other evidences', 115 ); ?>
