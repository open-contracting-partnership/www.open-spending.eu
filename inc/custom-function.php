<?php
/**
 * Template helpers shared across the theme.
 *
 * Excerpts, taxonomy slug lists, inline SVG icons, pagination and breadcrumbs.
 *
 * @package OpenSpendingCoalition
 */

/**
 * Read an ACF field, falling back when ACF is unavailable or the value is empty.
 *
 * Every template needs the `function_exists( 'get_field' )` guard so the theme
 * still renders with the ACF plugin deactivated. This wraps that guard together
 * with the "empty means use the default" behaviour the templates all relied on.
 *
 * @param string     $selector Field name.
 * @param int|string $post_id  Post ID, or an ACF options page id such as
 *                             'member_options'. Default false (current post).
 * @param mixed      $fallback Returned when ACF is inactive or the value is empty.
 * @return mixed The field value, or $fallback.
 */
function theme_field( $selector, $post_id = false, $fallback = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $fallback;
	}

	$value = get_field( $selector, $post_id );

	return $value ? $value : $fallback;
}

/**
 * The current post's excerpt, hard-truncated to a character budget.
 *
 * @param int $limit Maximum characters before the ellipsis.
 * @return string The excerpt, or '' when the post has none.
 */
function excerpt( $limit = 115 ) {
	$excerpt = get_the_excerpt();
	if ( $excerpt ) {
		$excerpt = mb_substr( $excerpt, 0, $limit ) . '...';
	}
	return $excerpt;
}

/**
 * A post's term slugs for one taxonomy, space-separated for use in a class
 * attribute (the archive filters match on these).
 *
 * @param int    $id           Post ID.
 * @param string $taxonomy_val Taxonomy name.
 * @return string Space-separated slugs, with a trailing space; '' if none.
 */
function taxonomy_term_slugs( $id, $taxonomy_val ) {
	$terms_array = get_the_terms( $id, $taxonomy_val );
	$terms_slug  = '';
	if ( $terms_array ) {
		foreach ( $terms_array as $term ) {
			$terms_slug .= $term->slug . ' ';
		}
	}
	return $terms_slug;
}

if ( ! function_exists( 'inline_svg' ) ) {
	/**
	 * Inline the contents of one of the theme's bundled SVG icons.
	 *
	 * Returned markup is not escaped — it is raw file content, so callers are
	 * responsible for only passing icon names they control.
	 *
	 * @param string $filename Icon name, without the .svg extension, as found in
	 *                         dist/images/icons.
	 * @return string The SVG markup, or '' when there is no such readable file.
	 */
	function inline_svg( $filename = 'long-arrow-right' ) {
		$icon = get_stylesheet_directory() . '/dist/images/icons/' . $filename . '.svg';

		// A missing icon should render as nothing, not a warning. Checked
		// explicitly rather than silenced with @, so a genuine read error
		// (permissions, I/O) still surfaces in the log.
		if ( ! is_readable( $icon ) ) {
			return '';
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reads a bundled theme asset off local disk. WP_Filesystem is the abstraction for writable paths, isn't loaded on the front end, and can prompt for credentials; it would be both slower and wrong here.
		return file_get_contents( $icon );
	}
}


/**
 * Pagination for the main query, with the theme's arrow icons.
 *
 * @return string Pagination markup, already escaped, or '' on a single page.
 */
function main_query_pagination() {

	global $wp_query;

	$big = 999999999;

	$right_icon = inline_svg( 'page-navigation-next' );
	$left_icon  = inline_svg( 'page-navigation-prev' );

	$paginate = paginate_links(
		array(
			'prev_text' => $left_icon,
			'next_text' => $right_icon,
		)
	);

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

/**
 * Pagination for a secondary WP_Query, with the theme's arrow icons.
 *
 * @param WP_Query $query The query to paginate.
 * @param int      $paged The current page number.
 * @return string Pagination markup, already escaped, or '' on a single page.
 */
function custom_query_pagination( $query, $paged ) {

	global $wp_query;

	$big        = 999999999;
	$right_icon = inline_svg( 'page-navigation-next' );
	$left_icon  = inline_svg( 'page-navigation-prev' );

	$paginate = paginate_links(
		array(
			'base'         => str_replace( $big, '%#%', html_entity_decode( get_pagenum_link( $big ) ) ),
			'total'        => $query->max_num_pages,
			'current'      => $paged,
			'format'       => '?paged=%#%',
			'show_all'     => false,
			'type'         => 'plain',
			'end_size'     => 4,
			'mid_size'     => 1,
			'prev_next'    => true,
			'prev_text'    => $left_icon,
			'next_text'    => $right_icon,
			'add_args'     => false,
			'add_fragment' => '',
		)
	);

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

/**
 * The current page number, reading whichever query var applies.
 *
 * @return int Page number; 1 when unpaginated.
 */
function paged() {
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
 * Breadcrumb trail for a post or archive: Home / post type / title.
 *
 * @param int $id Post ID, used to resolve the post type and its labels.
 * @return string Breadcrumb markup, already escaped.
 */
function breadcrumb_section( $id ) {
	$posttype    = get_post_type( $id );
	$post_object = get_post_type_object( $posttype );
	$labels      = $post_object->labels;
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
					<?php
				} else {
					echo esc_html( $labels->name );
				}
				?>
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

/**
 * The distinct terms of one taxonomy actually used by posts of a post type,
 * for building an archive's filter controls.
 *
 * @param string $posttype   Post type to scan.
 * @param string $taxonomies Taxonomy name.
 * @return array List of array( 'slug' => ..., 'name' => ... ), in first-seen order.
 */
function get_tax_post_type( $posttype, $taxonomies ) {
	$args_campaign       = array(
		'post_type'      => $posttype,
		'posts_per_page' => '-1',
		'post_status'    => array( 'publish' ),
	);
	$the_query           = new WP_Query( $args_campaign );
	$tax_post_type_array = array();
	$tax_post_type       = array();
	$i                   = 0;
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$id          = get_the_ID();
			$terms_array = get_the_terms( $id, $taxonomies );
			if ( $terms_array ) {
				foreach ( $terms_array as $term ) {
					if ( ! in_array( $term->slug, $tax_post_type_array, true ) ) {
						array_push( $tax_post_type_array, $term->slug );
						$tax_post_type[ $i ]['slug'] = $term->slug;
						$tax_post_type[ $i ]['name'] = $term->name;
						++$i;
					}
				}
			}
		}
	}
	wp_reset_postdata();

	return $tax_post_type;
}
