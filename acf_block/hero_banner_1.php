<?php

$heading     = (function_exists( 'get_field' ) && $heading = get_field( 'heading' )) ? $heading : '';
$paragraph   = (function_exists( 'get_field' ) && $paragraph = get_field( 'paragraph' )) ? $paragraph : '';
$button_text = (function_exists( 'get_field' ) && $button_text = get_field( 'button_text' )) ? $button_text : 'Why Open Spending?';

$button_url = home_url( '/about-the-organization/' );
$image      = get_template_directory_uri() . '/dist/images/hero.webp';

$id       = get_the_ID();
$posttype = get_post_type( $id );

?>

<?php if ( $image ) { ?>
<style>
header {
	background-image: url(<?php echo esc_url( $image ); ?>);
	background-attachment: fixed;
}

.block-editor-page .single-header h1 {
	display: none;
}
</style>
<?php } ?>

<canvas id="heroCanvas"></canvas>
<section class="hero_banner_1">
	<div class="container pb-8 sm:pb-16 h-[75vh] md:h-[65vh] lg:h-[55vh] flex items-center justify-center">
		<div class="max-w-[910px] text-center">
			<h1 class="font-bold text-n-0">
				<?php echo wp_kses_post( $heading ); ?>
			</h1>
			<div class="mt-4 text-base text-n-0 tracking-wider">
				<?php echo wp_kses_post( $paragraph ); ?>
			</div>
			<a class="text-lg !text-n-0 flex justify-center pointer-events-none" href="<?php echo esc_url( $button_url ); ?>">
				<div
					class="mt-11 py-2.5 px-4 md:py-5 md:px-8 border border-[#FFF200] rounded-lg flex gap-x-4 sm:gap-x-6 hero-btn max-w-fit items-center">
					<p class="text-lg text-n-0 transition-all duration-[400ms]"><?php echo esc_html( $button_text ); ?>
					</p>
					<?php echo useSvg( 'right-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
				</div>
			</a>
		</div>
	</div>
</section>

<?php
// archive page title and sub-title at header
if ( is_archive() ) {
	$post_object = get_post_type_object( $posttype );
	$labels = $post_object->labels;
	$sub_heading = (function_exists( 'get_field' ) && $sub_heading = get_field( 'sub_heading', $posttype . '_options' )) ? $sub_heading : '';
?>
<div class="archive-header container text-center text-n-0 pt-12 pb-10 md:pt-20 md:pb-16">
	<h1 class="font-bold"><?php echo esc_html( $labels->name ); ?></h1>
	<p class="text-lg mt-2"><?php echo wp_kses_post( $sub_heading ); ?></p>
</div>
<?php
}

// single page title at header
if ( is_search() ) {
?>
<div class="single-header text-n-0 pb-10">
	<h1 class="font-bold">Search results for: <?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only search query, input sanitized. ?></h1>
</div>
<?php
} else if ( (is_single() || ($posttype == 'page')) && ! is_front_page() && ! is_admin() ) {
?>
<div class="single-header text-n-0 pb-10">
	<h1 class="font-bold"><?php echo esc_html( get_the_title() ); ?></h1>
</div>
<?php
}
