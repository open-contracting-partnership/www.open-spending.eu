<?php
$heading     = (function_exists( 'get_field' ) && $heading = get_field( 'heading' )) ? $heading : '';
$paragraph   = (function_exists( 'get_field' ) && $paragraph = get_field( 'paragraph' )) ? $paragraph : '';
$btn_text    = (function_exists( 'get_field' ) && $btn_text = get_field( 'btn_text' )) ? $btn_text : 'See more';
$button_link = get_post_type_archive_link( 'member' );

$args = array(
	'post_type'      => 'member',
	'posts_per_page' => -1,
	'post_status'    => array('publish'),
	'tax_query'      => array(
		array(
			'taxonomy' => 'type_of_member',
			'field'    => 'slug',
			'terms'    => 'person', // Experts
		),
	),
);

$the_query = new WP_Query( $args );

$render_member_card = function () {
	$member_id = get_the_ID();
	$members = get_field( 'members', $member_id );
	$logoprofile_photo = is_array( $members ) ? ($members['logoprofile_photo'] ?? '') : '';
	$photo_src = ($logoprofile_photo) ? $logoprofile_photo : get_template_directory_uri() . '/dist/images/default-post-img.jpg';
	?>
	<div id="member_<?php echo (int) $member_id; ?>" class="member-item mb-6">
		<div class="pt-[124%] relative overflow-hidden rounded-3xl homepage-member-container">
			<?php render_acf_image( $photo_src, array( 'alt' => get_the_title(), 'class' => 'absolute top-0 h-full w-full object-cover rounded-2xl transition-all duration-300' ) ); ?>
		</div>
		<h4 class="mt-2 lg:mt-4 font-bold text-n-100">
			<?php echo esc_html( get_the_title() ); ?>
		</h4>
	</div>
	<?php
};

?>

<section class="member_block member-person pt-10 sm:pt-14 md:pt-20">
	<div class="container">
		<div class="md:flex gap-x-16">
			<div class="md:max-w-[50%] md:mt-20 mb-8 md:mb-0">
				<h2 class="text-n-100 font-bold sm:max-w-[260px] text-center sm:text-start">
					<?php echo wp_kses_post( $heading ); ?>
				</h2>
				<div class="text-n-80 mt-4 mx-auto text-base sm:text-lg mb-8 md:mb-12">
					<?php echo wp_kses_post( $paragraph ); ?>
				</div>
				<a href="<?php echo esc_url( $button_link ); ?>" class="flex gap-x-2.5 items-center learn-more-btn">
					<?php echo esc_html( $btn_text ); ?>
					<?php echo useSvg( 'right-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
				</a>
			</div>
			<?php
			if ( $the_query->have_posts() ) { ?>
			<div class="member-animation">
				<div class="member-data">
					<div class="left-side-data -mt-28">
						<?php
						$i = 0;
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							if ( $i % 2 == 0 ) {
								$render_member_card();
							}
							$i++;
						}
						?>
					</div>
					<div class="right-side-data">
						<?php
						$i = 0;
						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							if ( $i % 2 != 0 ) {
								$render_member_card();
							}
							$i++;
						}
						?>
					</div>
				</div>
			</div>
			<?php
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
