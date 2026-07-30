<?php

// get limit value then return post excerpt value of certain limit
function excerpt($limit = 115)
{
	$excerpt = get_the_excerpt();
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
					if ( ! in_array( $term->slug, $tax_post_type_array, true ) ) {
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
