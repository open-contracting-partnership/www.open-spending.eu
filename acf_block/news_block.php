<?php
/**
 * News block: a heading and a grid of the most recent news posts.
 *
 * @package OpenSpendingCoalition
 */

$heading        = theme_field( 'heading' );
$paragraph      = theme_field( 'paragraph' );
$btn_text       = theme_field( 'btn_text', false, 'Learn more' );
$number_of_news = theme_field( 'number_of_news', false, 6 );

$view_all_news_url   = get_post_type_archive_link( 'news' );
$view_all_news_label = 'View all news';

$args = array(
	'post_type'      => 'news',
	'posts_per_page' => (int) $number_of_news,
	'post_status'    => array( 'publish' ),
);

$the_query = new WP_Query( $args );

?>


<section class="news_block py-10 sm:py-14 md:py-20">
	<div class="container">
		<div>
			<?php if ( trim( (string) $heading ) !== '' ) : ?>
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
		<div class="mt-8 sm:mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
			<?php
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$current_id = get_the_ID();
				$excerpt    = excerpt( 100 );

				?>
			<div id="news_<?php echo (int) $current_id; ?>" class="p-5 border border-n-30 rounded-3xl news-card-hover">
				<div class="pt-[63.8%] relative">
					<a href="<?php echo esc_url( get_the_permalink() ); ?>">
						<?php render_feature_image( array( 'alt' => get_the_title(), 'class' => 'absolute top-0 h-full w-full object-cover rounded-2xl' ) ); ?>
					</a>
				</div>
				<div class="pt-6">
					<a href="<?php echo esc_url( get_the_permalink() ); ?>"
						class="text-heading-3 font-bold !text-n-100 card-title-hover leading-tight">
					<?php echo esc_html( get_the_title() ); ?></a>
					<p class="mt-2 text-sm text-n-60 mb-4"><?php echo esc_html( $excerpt ); ?></p>
					<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="flex gap-x-2.5 items-center learn-more-btn">
					<?php echo esc_html( $btn_text ); ?>
					<?php echo inline_svg( 'right-arrow' ); ?>
					</a>
				</div>
			</div>
				<?php
			}
			?>
		</div>
			<?php if ( $view_all_news_url ) { ?>
		<div class="flex justify-center mt-8">
			<a href="<?php echo esc_url( $view_all_news_url ); ?>"
				class="!text-n-100 font-bold py-2.5 px-4 border border-teal rounded-lg transition-all duration-300 hover:bg-teal hover:!text-n-0">
				<?php echo esc_html( $view_all_news_label ); ?>
			</a>
		</div>
		<?php } ?>

			<?php
		}
		wp_reset_postdata();
		?>
	</div>
</section>
