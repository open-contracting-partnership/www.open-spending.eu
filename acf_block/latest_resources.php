<?php
$heading   = (function_exists( 'get_field' ) && $heading = get_field( 'heading' )) ? $heading : '';
$paragraph = (function_exists( 'get_field' ) && $paragraph = get_field( 'paragraph' )) ? $paragraph : '';

$view_all_resources_url   = home_url( '/resources/' );
$view_all_resources_label = 'View all resources';

$resource_cards = array(
	array(
		'icon'      => 'Tool.svg',
		'label'     => 'Tool',
		'post_type' => 'toolkit',
	),
	array(
		'icon'      => 'Evidence.svg',
		'label'     => 'Evidence',
		'post_type' => 'evidence',
	),
	array(
		'icon'      => 'Best-Practices.svg',
		'label'     => 'Best Practices',
		'post_type' => 'best_practices',
	),
);

?>

<section class="latest_resources py-10 sm:py-14 md:py-20">
	<div>
		<div class="container">
			<h2 class="text-center text-n-100 font-bold">
				<?php echo wp_kses_post( $heading ); ?>
			</h2>
			<div class="text-n-80 text-center max-w-[472px] mt-4 mx-auto text-base sm:text-lg">
				<?php echo wp_kses_post( $paragraph ); ?>
			</div>
			<div class="mt-8 sm:mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
				<?php foreach ( $resource_cards as $card ) {
					$card_query = new WP_Query(array(
						'post_type'      => $card['post_type'],
						'posts_per_page' => 1,
						'post_status'    => array('publish'),
					));
				?>
				<div class="bg-n-0 px-8 py-6 rounded-3xl resources-card">
					<div class="flex gap-x-2.5 items-center">
						<img loading="lazy" src="<?php echo esc_url( get_template_directory_uri() . '/dist/images/icons/' . $card['icon'] ); ?>"
							alt="icon" class="p-5 rounded-2xl resources-image">
						<div class="text-n-70"><?php echo esc_html( $card['label'] ); ?></div>
					</div>
					<?php if ( $card_query->have_posts() ) {
						$card_query->the_post();
						$excerpt = excerpt( 200 );
					?>
					<div class="my-5 sm:mb-10">
						<h3 class="font-bold"><a
								href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
						<div class="text-n-60 mt-3"><?php echo esc_html( $excerpt ); ?></div>
					</div>
					<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="flex gap-x-2.5 items-center learn-more-btn">
						Learn more
						<?php echo useSvg( 'right-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
					</a>
					<?php }
					wp_reset_postdata();
					?>
				</div>
				<?php } ?>
			</div>
			<?php if ( $view_all_resources_url ) { ?>
			<div class="flex justify-center mt-8">
				<a href="<?php echo esc_url( $view_all_resources_url ); ?>"
					class="!text-n-100 font-bold py-2.5 px-4 border border-teal rounded-lg transition-all duration-300 hover:bg-teal hover:!text-n-0 more-resources-btn">
					<?php echo esc_html( $view_all_resources_label ); ?>
				</a>
			</div>
			<?php } ?>
		</div>
	</div>
</section>
