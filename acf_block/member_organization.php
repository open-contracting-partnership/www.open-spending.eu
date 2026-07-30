<?php
/**
 * Member organizations block: the organization members' logos.
 *
 * @package OpenSpendingCoalition
 */

$heading   = theme_field( 'heading' );
$paragraph = theme_field( 'paragraph' );

$args = array(
	'post_type'      => 'member',
	'posts_per_page' => -1,
	'post_status'    => array( 'publish' ),
	// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Selects one of two member types from a small, bounded post set. get_objects_in_term() with post__in avoids the join but trades it for a second query and an ID list, which is not a win at this size.
	'tax_query'      => array(
		array(
			'taxonomy' => 'type_of_member',
			'field'    => 'slug',
			'terms'    => 'organization', // Labelled "Organizations" in the admin.
		),
	),
);

$the_query = new WP_Query( $args );
?>

<section class="member-organization who_we_are_block pt-[1px] pb-10 sm:pb-14 md:pb-20">
	<div class="container">
		<h2 class="text-center text-n-100 font-bold">
			<?php echo wp_kses_post( $heading ); ?>
		</h2>
		<div class="slider-container">
			<div class="my-10 sm:my-14 md:my-20 flex justify-around members-slider">
				<?php
				if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$member_id         = get_the_ID();
						$members           = get_field( 'members', $member_id );
						$logoprofile_photo = is_array( $members ) ? ( $members['logoprofile_photo'] ?? '' ) : '';
						$website           = is_array( $members ) ? ( $members['website'] ?? '' ) : '';
						$logo_src          = ( $logoprofile_photo ) ? $logoprofile_photo : get_template_directory_uri() . '/dist/images/default-post-img.jpg';
						?>
				<div>
					<a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center">
						<?php render_acf_image( $logo_src, array( 'alt' => get_the_title(), 'class' => 'max-h-14 w-auto transition-all duration-300', 'size' => 'medium' ) ); ?>
					</a>
				</div>
						<?php
					}
				}
				wp_reset_postdata();
				?>
			</div>
			<div class="slider-navigation">
				<div class="slick-arrow prev"></div>
				<div class="slick-arrow next"></div>
			</div>
		</div>
		<div class="text-n-50 text-center max-w-[748px] mt-4 mx-auto text-base sm:text-lg">
			<?php echo wp_kses_post( $paragraph ); ?>
		</div>
	</div>
</section>
