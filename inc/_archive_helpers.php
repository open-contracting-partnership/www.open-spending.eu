<?php

/**
 * Render a filterable post-type archive (campaign + country filters, paginated grid).
 * Used by archives/news.php, archives/evidence.php, archives/best_practices.php.
 */
function render_filterable_archive( $posttype ) {
	$taxonomies = 'country';
	$terms      = get_tax_post_type( $posttype, $taxonomies );

	$get_campaign = isset( $_GET['campaign_post'] ) ? absint( $_GET['campaign_post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only archive filter, input sanitized.
	$get_country  = isset( $_GET['taxonomy_country'] ) ? sanitize_title( wp_unslash( $_GET['taxonomy_country'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only archive filter, input sanitized.

	// Build the list of campaigns referenced by any post of this type
	$campaign_filter_data = array();
	$filter_query         = new WP_Query(array(
		'post_type'      => $posttype,
		'posts_per_page' => -1,
		'post_status'    => array( 'publish' ),
		'fields'         => 'ids',
	));
	if ( $filter_query->have_posts() ) {
		foreach ( $filter_query->posts as $filter_post_id ) {
			$related = get_field( 'realted_campaign', $filter_post_id );
			$related = ( is_array( $related ) && isset( $related['realted_campaign'] ) ) ? $related['realted_campaign'] : null;
			if ( $related ) {
				foreach ( $related as $cid ) {
					if ( ! in_array( $cid, $campaign_filter_data, true ) ) {
						$campaign_filter_data[] = $cid;
					}
				}
			}
		}
	}
	wp_reset_postdata();

	echo breadcrumb_section( get_the_ID() );

	$posttype_label = str_replace( '_', ' ', $posttype );
	?>
	<div class="container">
		<div class="flex flex-wrap gap-x-6">
			<form method="GET" role="search" class="campaign-filter md:flex gap-x-8"
				action="<?php echo esc_url( home_url( '/' . $posttype ) ); ?>" id="filter-form">
				<?php if ( $campaign_filter_data ) { ?>
				<div class="mt-8">
					<p class="text-n-60">Filter <?php echo esc_html( $posttype_label ); ?> by campaign: </p>
					<div class="<?php echo esc_attr( $posttype ); ?>-campaign archive-accordion-campaign archive-accordion-category flex flex-wrap gap-2 sm:gap-4 mt-2">
						<p class="campaign-item category-item <?php echo $get_campaign ? '' : 'active'; ?>" data-filter="*">
							<label class="filter-input">
								<input class="yi-input-radio " type="radio" name="campaign_post" value="" style="display: none">
								<span>All</span>
							</label>
						</p>
						<?php
						foreach ( $campaign_filter_data as $value_id ) {
							$is_active = ( $get_campaign === (int) $value_id );
							?>
						<p class="campaign-item category-item <?php echo $is_active ? 'active' : ''; ?>"
							data-filter="<?php echo esc_attr( '.campaign-' . $value_id ); ?>">
							<label class="filter-input">
								<input class="yi-input-radio" type="radio" name="campaign_post"
									value="<?php echo esc_attr( $value_id ); ?>" <?php checked( $is_active ); ?> style="display: none">
								<span><?php echo esc_html( get_the_title( $value_id ) ); ?></span>
							</label>
						</p>
						<?php } ?>
					</div>
				</div>
				<?php } ?>

				<?php if ( $terms ) { ?>
				<div class="mt-6 md:mt-8">
					<p class="text-n-60">Filter <?php echo esc_html( $posttype_label ); ?> by country: </p>
					<div class="<?php echo esc_attr( $posttype ); ?>-country ">
						<select name="taxonomy_country" id="country-filter"
							class="mt-2 py-[2px] pl-2 pr-7 rounded-xl border border-teal transition-all duration-300 hover:border-n-60 cursor-pointer">
							<option value="" class="filter-input">Select a country</option>
							<?php foreach ( $terms as $term ) { ?>
							<option class="filter-input"
								data-filter="<?php echo esc_attr( '.' . $term['slug'] ); ?>"
								value="<?php echo esc_attr( $term['slug'] ); ?>"
								<?php selected( $get_country, $term['slug'] ); ?>>
								<?php echo esc_html( $term['name'] ); ?>
							</option>
							<?php } ?>
						</select>
					</div>
				</div>
				<?php } ?>
			</form>
		</div>
		<?php
		$paged = paged();
		$args  = array(
			'post_type'      => $posttype,
			'post_status'    => array( 'publish' ),
			'posts_per_page' => 12,
			'paged'          => $paged,
		);
		if ( $get_campaign ) {
			$args['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key'     => 'realted_campaign_realted_campaign',
					'value'   => $get_campaign,
					'compare' => 'LIKE',
				),
			);
		}
		if ( $get_country ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'country',
					'field'    => 'slug',
					'terms'    => $get_country,
				),
			);
		}
		$the_query = new WP_Query( $args );
		if ( $the_query->have_posts() ) {
			?>
		<div class="<?php echo esc_attr( $posttype ); ?> archive-accordion py-8 w-full">
			<?php
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$card_id      = get_the_ID();
				$permalink    = get_the_permalink( $card_id );
				$feature_img  = ( has_post_thumbnail() ) ? get_the_post_thumbnail_url() : get_template_directory_uri() . '/dist/images/default-post-img.jpg';
				$excerpt      = excerpt( 115 );
				$title        = get_the_title();
				$related      = get_field( 'realted_campaign' );
				$related      = ( is_array( $related ) && isset( $related['realted_campaign'] ) ) ? $related['realted_campaign'] : null;
				$card_classes = '';
				if ( $related ) {
					foreach ( $related as $cid ) {
						$card_classes .= 'campaign-' . (int) $cid . ' ';
					}
				}
				$card_classes .= taxoTermsSLug( $card_id, $taxonomies );
				?>
			<div class="<?php echo esc_attr( $card_classes ); ?> archive-accordion-items p-5 border border-n-40 rounded-3xl"
				data-id="<?php echo (int) $card_id; ?>">
				<div class="accordion-card-inside ">
					<div>
						<a href="<?php echo esc_url( $permalink ); ?>">
							<div class="pt-[63%] relative">
								<img src="<?php echo esc_url( $feature_img ); ?>" alt="<?php echo esc_attr( $title ); ?>"
									class="absolute h-full w-full object-cover top-0 rounded-[20px]">
							</div>
						</a>
					</div>
					<div>
						<div class="mt-6"> <a href="<?php echo esc_url( $permalink ); ?>"
								class="text-2xl font-bold card-title-hover leading-none sm:leading-normal">
								<?php echo esc_html( $title ); ?></a> </div>
						<div class="mt-2 text-n-60 text-sm"> <?php echo esc_html( $excerpt ); ?> </div>
						<div class="mt-4">
							<a href="<?php echo esc_url( $permalink ); ?>" class="flex gap-x-2.5 items-center learn-more-btn">
								Learn More
								<?php echo useSvg( 'right-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
							</a>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
			<?php
			echo custom_query_pagination( $the_query, $paged );
		} else {
			?>
		<div class="py-10 sm:py-14 lg:py-20">
			<?php
				$parts = array();
			if ( $get_campaign ) {
				$parts[] = 'campaign: "' . get_the_title( $get_campaign ) . '"';
			}
			if ( $get_country ) {
				$country_term = get_term_by( 'slug', $get_country, $taxonomies );
				if ( $country_term ) {
					$parts[] = 'country: "' . $country_term->name . '"';
				}
			}
				$no_result_msg = $parts ? esc_html( implode( ' and ', $parts ) ) : '';
			?>
			<h3 class="font-medium text-n-80">No data available for selected <?php echo $no_result_msg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped above. ?></h3>
		</div>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>

	<script>
	jQuery(function($) {
		$('.filter-input').click(function() {
			$('form').submit();
		});
		$("#country-filter").change(function() {
			$('form').submit();
		});
	});
	</script>
	<?php
}


/**
 * Render the "Other X" related-posts grid below singles/news.php and singles/evidence.php.
 */
function render_other_posts_grid( $posttype, $exclude_id, $heading, $excerpt_length = 115 ) {
	$args      = array(
		'post_type'      => $posttype,
		'posts_per_page' => 3,
		'post_status'    => array( 'publish' ),
		'post__not_in'   => array( $exclude_id ),
	);
	$the_query = new WP_Query( $args );
	if ( ! $the_query->have_posts() ) {
		wp_reset_postdata();
		return;
	}
	?>
	<div class="bg-n-10">
		<div class="container py-10 md:pt-16 md:pb-20">
			<h2 class="font-bold"><?php echo esc_html( $heading ); ?></h2>
			<div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
				<?php
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$card_id      = get_the_ID();
					$card_excerpt = excerpt( $excerpt_length );
					$feature_img  = ( has_post_thumbnail() ) ? get_the_post_thumbnail_url() : get_template_directory_uri() . '/dist/images/default-post-img.jpg';
					?>
				<div id="<?php echo esc_attr( $posttype ); ?>_<?php echo (int) $card_id; ?>" class="p-5 border border-n-30 rounded-3xl bg-n-0 news-card-hover">
					<div class="pt-[63.8%] relative">
						<a href="<?php echo esc_url( get_the_permalink() ); ?>">
							<img src="<?php echo esc_url( $feature_img ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class=" absolute top-0 h-full w-full object-cover rounded-2xl">
						</a>
					</div>
					<div class="pt-6">
						<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="text-heading-3 font-bold !text-n-100 card-title-hover leading-tight">
							<?php echo esc_html( get_the_title() ); ?></a>
						<p class="mt-2 text-sm text-n-60 mb-4"><?php echo esc_html( $card_excerpt ); ?></p>
						<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="flex gap-x-2.5 items-center learn-more-btn">
							Learn more
							<?php echo useSvg( 'right-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG file content. ?>
						</a>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php
	wp_reset_postdata();
}
