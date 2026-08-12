<?php
/**
 * Single template.
 *
 * Renders the block header and footer around a per-post-type partial from
 * singles/, falling back to a core block layout when there isn't one.
 *
 * Template Name: Single Default
 *
 * @package OpenSpendingCoalition
 */

// The main landmark is the wrapper below, which both branches share.
$block_content = do_blocks(
	'
    <!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
    <div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--50)">
        <!-- wp:group {"layout":{"type":"constrained"}} -->
        <div class="wp-block-group">
            <!-- wp:post-featured-image {"overlayColor":"contrast","dimRatio":50,"align":"wide","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50","top":"calc(-1 * var(--wp--preset--spacing--50))"}}}} /-->
            <!-- wp:post-title {"level":1,"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} /-->
        </div>
        <!-- /wp:group -->

        <!-- wp:post-content {"layout":{"type":"constrained"}} /-->
    </div>
    <!-- /wp:group -->
'
);

/*
 * Render these before wp_head() so WordPress collects their blocks' styles in time
 * to print them in <head>. Calling block_header_area() inline where it's echoed
 * moves that CSS down beside the footer.
 */
ob_start();
block_header_area();
$block_header_area = ob_get_clean();

ob_start();
block_footer_area();
$block_footer_area = ob_get_clean();

?>

<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div class="wp-site-blocks">
		<header class="wp-block-template-part">
			<?php echo $block_header_area; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output of block_header_area(). ?>
		</header>

		<main class="single-page wp-block-post-content">
			<?php
				$posttype = get_post_type();
				$filepath = __DIR__ . '/singles/' . $posttype . '.php';
			if ( file_exists( $filepath ) ) {
				include_once $filepath;
			} else {
				echo $block_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered block markup from do_blocks().
			}
			?>
		</main>

		<footer class="wp-block-template-part">
			<?php echo $block_footer_area; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output of block_footer_area(). ?>
		</footer>
	</div>
	<?php wp_footer(); ?>

</body>

</html>