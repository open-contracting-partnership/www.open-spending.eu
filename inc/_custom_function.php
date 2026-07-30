<?php

// get limit value then return post excerpt value of certain limit
function excerpt($limit = 115)
{
	$excerpt = html_entity_decode( wp_strip_all_tags( get_the_excerpt() ), ENT_QUOTES, 'UTF-8' );
	if ( $excerpt ) {
		$excerpt = mb_substr( $excerpt, 0, $limit ) . '...';
	}
	return $excerpt;
}

// get post id and Taxonomy then return the string of all the taxonomy slug associated with post id
function taxoTermsSLug($id, $taxonomy_val)
{
	$termsArray = get_the_terms( $id, $taxonomy_val );
	$termsSLug = '';
	if ( $termsArray ) {
		foreach ( $termsArray as $term ) {
			$termsSLug .= $term->slug . ' ';
		}
	}
	return $termsSLug;
}

/**
 * Build the shared attribute array (class/alt/loading/fetchpriority/sizes) used by
 * the responsive image helpers below.
 */
function feature_image_attr($args)
{
	$attr = array(
		'class'   => $args['class'] ?? '',
		'alt'     => $args['alt'] ?? '',
		'loading' => ($args['lazy'] ?? true) ? 'lazy' : 'eager',
	);
	if ( ! empty( $args['priority'] ) ) {
		$attr['fetchpriority'] = 'high';
	}
	if ( ! empty( $args['sizes'] ) ) {
		$attr['sizes'] = $args['sizes'];
	}
	return $attr;
}

/**
 * Echo a plain <img> fallback (no srcset) for when a responsive source can't be resolved.
 */
function render_plain_image($src, $args)
{
	printf(
		'<img src="%s" alt="%s" class="%s" loading="%s"%s>',
		esc_url( $src ),
		esc_attr( $args['alt'] ?? '' ),
		esc_attr( $args['class'] ?? '' ),
		esc_attr( ($args['lazy'] ?? true) ? 'lazy' : 'eager' ),
		empty( $args['priority'] ) ? '' : ' fetchpriority="high"'
	);
}

/**
 * Echo a post's featured image as responsive markup (srcset/sizes/width/height via
 * get_the_post_thumbnail()), falling back to the theme's default image when the post
 * has no thumbnail. Saves callers from repeating the has_post_thumbnail() dance and
 * gives every feature image display-appropriate sizing.
 *
 * @param array $args {
 *     @type int|null $post_id  Post ID. Default null (current post in the loop).
 *     @type string   $size     Registered image size. Default 'large'.
 *     @type string   $class    <img> class attribute.
 *     @type string   $alt      Alt text. Default get_the_title( $post_id ).
 *     @type bool     $lazy     Lazy-load. Default true. Set false for the LCP hero.
 *     @type bool     $priority fetchpriority="high". Default false.
 *     @type string   $sizes    Explicit sizes attribute (for eager images). Optional.
 * }
 */
function render_feature_image($args = array())
{
	$post_id = $args['post_id'] ?? null;
	$size    = $args['size'] ?? 'large';
	if ( ! isset( $args['alt'] ) ) {
		$args['alt'] = get_the_title( $post_id );
	}

	if ( has_post_thumbnail( $post_id ) ) {
		echo get_the_post_thumbnail( $post_id, $size, feature_image_attr( $args ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_post_thumbnail() returns escaped markup.
		return;
	}

	render_plain_image( get_template_directory_uri() . '/dist/images/default-post-img.jpg', $args );
}

/**
 * Echo an ACF image (stored as a URL) as responsive markup. Resolves the URL back to an
 * attachment ID so wp_get_attachment_image() can emit srcset/sizes/width/height; falls
 * back to a plain <img> when the URL isn't a media-library attachment. The URL->ID lookup
 * is memoised per request to avoid repeated queries in member/logo loops.
 *
 * @param string $url  Image URL from an ACF field.
 * @param array  $args See render_feature_image(): size, class, alt, lazy, priority, sizes.
 */
function render_acf_image($url, $args = array())
{
	static $id_cache = array();

	$size = $args['size'] ?? 'large';

	if ( $url ) {
		if ( ! array_key_exists( $url, $id_cache ) ) {
			$id_cache[ $url ] = attachment_url_to_postid( $url );
		}
		$attachment_id = $id_cache[ $url ];
		if ( $attachment_id ) {
			echo wp_get_attachment_image( $attachment_id, $size, false, feature_image_attr( $args ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() returns escaped markup.
			return;
		}
	}

	render_plain_image( $url, $args );
}

// Display svg icons

if ( ! function_exists( 'useSvg' ) ) {
	function useSvg($filename = 'long-arrow-right')
	{
		$icon = get_stylesheet_directory() . '/dist/images/icons/' . $filename . '.svg';

		$svg_icon_content = @file_get_contents( $icon );

		return $svg_icon_content;
	}
}


// pagination
function main_query_pagination()
{

	global $wp_query, $svgIcon;

	$big = 999999999;

	$rightIcon = useSvg( 'page-navigation-next' );
	$leftIcon = useSvg( 'page-navigation-prev' );

	$paginate = paginate_links(array(
		'prev_text'     => $leftIcon,
		'next_text'     => $rightIcon,
	));

	$html_paginate = '';

	if ( $paginate ) {
		$html_paginate .= '<div class="navigation pagination" role="navigation">';
		$html_paginate .= '<div class="nav-links">';
		$html_paginate .= $paginate;
		$html_paginate .= '</div>';
		$html_paginate .= '</div>';
	}
	return $html_paginate;
}

function custom_query_pagination($query, $paged)
{

	global $wp_query, $svgIcon;

	$big = 999999999;
	$rightIcon = useSvg( 'page-navigation-next' );
	$leftIcon = useSvg( 'page-navigation-prev' );

	$paginate = paginate_links(array(
		'base'         => str_replace( $big, '%#%', html_entity_decode( get_pagenum_link( $big ) ) ),
		'total'        => $query->max_num_pages,
		'current'      => $paged,
		'format'       => '?paged=%#%',
		'show_all'     => false,
		'type'         => 'plain',
		'end_size'     => 4,
		'mid_size'     => 1,
		'prev_next'    => true,
		'prev_text'    => $leftIcon,
		'next_text'    => $rightIcon,
		'add_args'     => false,
		'add_fragment' => '',
	));

	$html_paginate = '';

	if ( $paginate ) {
		$html_paginate .= '<div class="navigation pagination" role="navigation">';
		$html_paginate .= '<div class="nav-links">';
		$html_paginate .= $paginate;
		$html_paginate .= '</div>';
		$html_paginate .= '</div>';
	}

	return $html_paginate;
}

function paged()
{
	if ( get_query_var( 'paged' ) ) {
		$paged = get_query_var( 'paged' );
	} elseif ( get_query_var( 'page' ) ) {
		$paged = get_query_var( 'page' );
	} else {
		$paged = 1;
	}
	return $paged;
}

/**
 * =========================================================
 * Breadcrumb
 * =========================================================
 */

function breadcrumb_section($id)
{
	$posttype = get_post_type( $id );
	$post_object = get_post_type_object( $posttype );
	$labels = $post_object->labels;
	ob_start();
?>
	<div class="breadcrumb bg-n-10">
		<div class="breadcrumb-menu container">
			<div class="breadcrumb-menu__item">
				<a href="<?php echo esc_url( home_url() ); ?>">Home</a>
			</div>
			<div class="breadcrumb-menu__item">
				<?php if ( is_singular() ) { ?>
					<a href="<?php echo esc_url( get_post_type_archive_link( $posttype ) ); ?>">
						<?php echo esc_html( $labels->name ); ?>
					</a>
				<?php } else {
					echo esc_html( $labels->name );
				} ?>
			</div>
			<?php if ( is_singular() ) { ?>
			<div class="breadcrumb-menu__item">
				<?php echo esc_html( get_the_title() ); ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<?php
	$outupt_breadcrumb_section = ob_get_contents();
	ob_end_clean();

	return $outupt_breadcrumb_section;
}

// get all the taxo for post types
function get_tax_post_type($posttype, $taxonomies)
{
	$args_campaign = array(
		'post_type'             => $posttype,
		'posts_per_page'        => '-1',
		'post_status'           => array('publish'),
	);
	$the_query = new WP_Query( $args_campaign );
	$tax_post_type_array = array();
	$tax_post_type = array();
	$i = 0;
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$id = get_the_ID();
			$termsArray = get_the_terms( $id, $taxonomies );
			if ( $termsArray ) {
				foreach ( $termsArray as $term ) {
					if ( ! in_array( $term->slug, $tax_post_type_array ) ) {
						array_push( $tax_post_type_array, $term->slug );
						$tax_post_type[$i]['slug'] = $term->slug;
						$tax_post_type[$i]['name'] = $term->name;
						$i = $i + 1;
					}
				}
			}
		}
	}
	wp_reset_postdata();

	return $tax_post_type;
}
