<?php
/**
 * Campaigns block: a heading and the campaign cards, in editor-set order.
 *
 * @package OpenSpendingCoalition
 */

$block_id    = $block['id'];
$heading     = theme_field( 'heading', false, 'Campaigns' );
$paragraph   = theme_field( 'paragraph' );
$button_text = theme_field( 'button_text', false, 'Learn more' );

$args = array(
	'post_type'      => 'campaign',
	'posts_per_page' => 3,
	'post_status'    => array( 'publish' ),
);

$the_query = new WP_Query( $args );

?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="campaigns_block py-10 sm:py-14 md:py-20">
	<div class="container">
		<div class="info">
			<?php if ( trim( (string) $heading ) !== '' ) : // Skip the heading element if it would be empty. ?>
			<h2 class="text-center text-n-100 font-bold">
				<?php echo wp_kses_post( $heading ); ?>
			</h2>
			<?php endif; ?>
			<div class="text-n-80 text-center max-w-[614px] mt-4 mx-auto text-base sm:text-lg">
				<?php echo wp_kses_post( $paragraph ); ?>
			</div>
		</div>
		<?php
		if ( $the_query->have_posts() ) {
			?>
			<div class="campaign-data mt-10 md:mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-7 gap-y-4">
				<?php
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$campaign_id = get_the_ID();
					$excerpt     = excerpt( 200 );
					?>
					<div id="campaign_<?php echo (int) $campaign_id; ?>" class="campaign-each bg-n-0 rounded-3xl card-subtle-hover">
						<div class="pt-[100%] relative card-image-container">
							<a href="<?php echo esc_url( get_the_permalink() ); ?>">
								<?php render_feature_image( array( 'class' => 'absolute top-0 h-full w-full object-cover rounded-t-3xl' ) ); ?>
							</a>
						</div>
						<div class="info bg-n-0 px-8 py-6 rounded-b-3xl">
							<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="text-lg font-bold !text-n-100 card-title-hover">
								<?php echo esc_html( get_the_title() ); ?></a>
							<p class="mt-2.5 text-sm text-n-60 mb-4"><?php echo esc_html( $excerpt ); ?></p>
							<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="flex gap-x-2.5 items-center learn-more-btn">
								<?php echo esc_html( $button_text ); ?>
								<?php echo inline_svg( 'right-arrow' ); ?>
							</a>
						</div>
					</div>
					<?php
				}
				?>
			</div>

			<?php
		}
		wp_reset_postdata();
		?>
	</div>
</section>
