<?php
/**
 * Archive template.
 *
 * Renders the block header and footer around a per-post-type partial from
 * archives/, falling back to a core block query layout when there isn't one.
 *
 * Template Name: Archive Default
 *
 * @package OpenSpendingCoalition
 */

$block_content = do_blocks(
	'
    <!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
    <main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--70);margin-bottom:var(--wp--preset--spacing--70)">
        <!-- wp:query-title {"type":"archive","align":"wide","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}}} /-->
    
        <!-- wp:query {"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"displayLayout":{"type":"flex","columns":3},"align":"wide","layout":{"type":"default"}} -->
        <div class="wp-block-query alignwide">
            <!-- wp:post-template {"align":"wide"} -->
                <!-- wp:post-featured-image {"isLink":true,"width":"100%","height":"max(15vw, 30vh)","align":"wide"} /-->
                <!-- wp:post-title {"isLink":true} /-->
                <!-- wp:post-excerpt /-->
                <!-- wp:post-date {"isLink":true} /-->
    
                <!-- wp:spacer {"height":"var(--wp--preset--spacing--50)"} -->
                <div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
                <!-- /wp:spacer -->
            <!-- /wp:post-template -->
    
            <!-- wp:query-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"space-between"}} -->
                <!-- wp:query-pagination-previous {"label":"Newer Posts"} /-->
                <!-- wp:query-pagination-next {"label":"Older Posts"} /-->
            <!-- /wp:query-pagination -->
        </div>
        <!-- /wp:query -->
    </main>
    <!-- /wp:group -->
'
);

/*
 * Render the header and footer parts up front, even though they're echoed further
 * down. Doing it before wp_head() is what lets WordPress collect their blocks'
 * styles in time to print them in <head>; calling block_header_area() inline
 * instead moves that CSS down next to the footer.
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

		<div class="archive-page">
			<?php
				$posttype = get_post_type();
				$filepath = __DIR__ . '/archives/' . $posttype . '.php';
			if ( file_exists( $filepath ) ) {
				include_once $filepath;
			} else {
				echo $block_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered block markup from do_blocks().
			}
			?>
		</div>


		<footer class="wp-block-template-part">
			<?php echo $block_footer_area; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output of block_footer_area(). ?>
		</footer>
	</div>
	<?php wp_footer(); ?>

</body>

</html>