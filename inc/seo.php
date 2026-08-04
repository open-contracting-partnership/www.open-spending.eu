<?php
/**
 * Lightweight, theme-native SEO:
 *
 *   - <title>            -> WordPress core (add_theme_support('title-tag'))
 *   - meta description   -> here (excerpt / tagline)
 *   - robots / noindex   -> here (member singles, attachments, search, tags, 404) via wp_robots
 *   - attachment pages   -> here (redirected to their parent post)
 *   - canonical          -> core for singular; here for front page + archives
 *   - Open Graph/Twitter -> here (featured image, with a site-wide fallback)
 *   - JSON-LD schema     -> here (Organization + WebSite, front page only)
 *   - XML sitemap        -> WordPress core (wp-sitemap.xml), tuned here
 *
 * @package OpenSpendingCoalition
 */

/**
 * The site tagline is a full sentence — keep it out of the front-page <title>
 * so it reads "Open Spending EU Coalition" rather than name + whole tagline.
 */
add_filter(
	'document_title_parts',
	function ( $parts ) {
		if ( is_front_page() ) {
			unset( $parts['tagline'] );
		}
		return $parts;
	}
);

/**
 * Social profiles for schema `sameAs` (kept in sync with acf_block/contact-us.php).
 */
function theme_seo_social_profiles() {
	return array(
		'https://twitter.com/EuSpending',
		'https://linkedin.com/company/open-spending-eu-coalition/',
	);
}

/**
 * A clean ~160-char meta description for the current request.
 */
function theme_seo_description() {
	if ( is_front_page() ) {
		$desc = get_bloginfo( 'description', 'display' );
	} elseif ( is_singular() ) {
		$desc = get_the_excerpt();
	} elseif ( is_post_type_archive() ) {
		$obj  = get_queried_object();
		$desc = ( $obj && ! empty( $obj->description ) ) ? $obj->description : get_bloginfo( 'description', 'display' );
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$desc = term_description();
	} else {
		$desc = get_bloginfo( 'description', 'display' );
	}

	$desc = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $desc ) ) );
	if ( ! $desc ) {
		$desc = get_bloginfo( 'description', 'display' );
	}
	if ( mb_strlen( $desc ) > 160 ) {
		$desc = rtrim( mb_substr( $desc, 0, 157 ) ) . '…';
	}

	return $desc;
}

/**
 * The share image: the post's featured image, else a bundled site-wide fallback.
 *
 * @return array{url:string,w:int,h:int}
 */
function theme_seo_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		if ( $src ) {
			return array( 'url' => $src[0], 'w' => (int) $src[1], 'h' => (int) $src[2] );
		}
	}

	return array(
		'url' => get_template_directory_uri() . '/dist/images/hero.png',
		'w'   => 1440,
		'h'   => 903,
	);
}

/**
 * Canonical URL for the current request.
 */
function theme_seo_canonical() {
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_singular() ) {
		return get_permalink();
	}
	if ( is_post_type_archive() ) {
		return get_post_type_archive_link( get_post_type() );
	}
	if ( is_tax() || is_category() || is_tag() ) {
		$link = get_term_link( get_queried_object() );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}

	global $wp;
	return home_url( user_trailingslashit( $wp->request ?? '' ) );
}

/**
 * Noindex thin and non-content pages.
 *
 * Member singles and attachment pages are the same case: a permalink WordPress
 * publishes that the theme does not want published. Neither can be prevented at
 * the source — making the member post type non-public would break the /member/
 * archive, and core gives every upload a permalink regardless.
 */
add_filter(
	'wp_robots',
	function ( $robots ) {
		if ( is_singular( 'member' ) || is_attachment() || is_search() || is_404() || is_tag() ) {
			$robots['noindex'] = true;
		}
		return $robots;
	}
);

/**
 * Send attachment pages to the post that uses the file.
 *
 * An attachment page has no content of its own; it renders a bare media file in
 * the site's chrome.
 *
 * If the wp_attachment_pages_enabled option is off, core's redirect_canonical()
 * sends every attachment to its file URL. This runs ahead of that at priority 9.
 *
 * Attachments with no published parent (uploaded directly to the media library)
 * fall through to core's redirect_canonical().
 */
add_action(
	'template_redirect',
	function () {
		if ( ! is_attachment() ) {
			return;
		}

		$parent = get_post_parent();

		if ( $parent && 'publish' === $parent->post_status ) {
			wp_safe_redirect( get_permalink( $parent ), 301 );
			exit;
		}
	},
	9
);

/**
 * Meta description, canonical, Open Graph and Twitter Card tags.
 */
add_action(
	'wp_head',
	function () {
		$desc  = theme_seo_description();
		$url   = theme_seo_canonical();
		$img   = theme_seo_image();
		$title = wp_get_document_title();
		$type  = ( is_singular() && ! is_front_page() ) ? 'article' : 'website';

		echo "\n";
		if ( $desc ) {
			printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $desc ) );
		}

		// Core already prints rel_canonical for singular views.
		if ( ! is_singular() && $url ) {
			printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $url ) );
		}

		printf( '<meta property="og:locale" content="%s" />' . "\n", esc_attr( get_locale() ) );
		printf( '<meta property="og:type" content="%s" />' . "\n", esc_attr( $type ) );
		printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
		if ( $desc ) {
			printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $desc ) );
		}
		printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
		printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $img['url'] ) );
		printf( '<meta property="og:image:width" content="%s" />' . "\n", esc_attr( (string) $img['w'] ) );
		printf( '<meta property="og:image:height" content="%s" />' . "\n", esc_attr( (string) $img['h'] ) );

		if ( 'article' === $type ) {
			printf( '<meta property="article:published_time" content="%s" />' . "\n", esc_attr( get_the_date( 'c' ) ) );
			printf( '<meta property="article:modified_time" content="%s" />' . "\n", esc_attr( get_the_modified_date( 'c' ) ) );
		}

		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
		printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
		if ( $desc ) {
			printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $desc ) );
		}
		printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_url( $img['url'] ) );
	},
	2
);

/**
 * Organization + WebSite JSON-LD (front page only) — enables the sitelinks
 * search box and gives the org a knowledge-graph anchor.
 */
add_action(
	'wp_head',
	function () {
		if ( ! is_front_page() ) {
			return;
		}

		$site = home_url( '/' );
		$name = get_bloginfo( 'name' );

		$organization = array(
			'@type'  => 'Organization',
			'@id'    => $site . '#organization',
			'name'   => $name,
			'url'    => $site,
			'sameAs' => theme_seo_social_profiles(),
		);

		$logo = wp_get_attachment_image_url( (int) get_option( 'site_logo' ), 'full' );
		if ( $logo ) {
			$organization['logo'] = array( '@type' => 'ImageObject', 'url' => $logo );
		}

		$website = array(
			'@type'           => 'WebSite',
			'@id'             => $site . '#website',
			'name'            => $name,
			'url'             => $site,
			'publisher'       => array( '@id' => $site . '#organization' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => $site . '?s={search_term_string}',
				),
				'query-input' => 'required name=search_term_string',
			),
		);

		$graph = array( '@context' => 'https://schema.org', '@graph' => array( $organization, $website ) );

		echo "\n" . '<script type="application/ld+json">'
		. wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
		. '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode returns safe JSON.
	},
	3
);

/**
 * Tune WordPress core's built-in sitemap (wp-sitemap.xml):
 * drop the noindexed member post type, the users sitemap, and thin taxonomy
 * archives, and add the post-type archive landing pages that core omits (see
 * provider below).
 */
add_filter(
	'wp_sitemaps_post_types',
	function ( $post_types ) {
		unset( $post_types['member'] );
		return $post_types;
	}
);

add_filter(
	'wp_sitemaps_taxonomies',
	function ( $taxonomies ) {
		unset( $taxonomies['post_tag'] ); // Thin archives, and noindexed by the wp_robots filter above.
		return $taxonomies;
	}
);

add_filter(
	'wp_sitemaps_add_provider',
	function ( $provider, $name ) {
		return ( 'users' === $name ) ? false : $provider;
	},
	10,
	2
);

/**
 * Add post-type archive landing pages (e.g. /news/, /campaign/) to the sitemap.
 *
 * Core's post-type sitemaps list individual posts but never the archive index
 * pages, which are real, indexable landing pages. Expose them via a small custom
 * provider. The noindexed member archive is skipped.
 */
add_action(
	'init',
	function () {
		if ( ! function_exists( 'wp_register_sitemap_provider' ) || ! class_exists( 'WP_Sitemaps_Provider' ) ) {
			return;
		}

	// phpcs:disable PHPCompatibility.FunctionDeclarations.NewClosure.ThisFoundOutsideClass -- false positive: $this is valid inside this anonymous class.
	// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter -- Signatures are fixed by WP_Sitemaps_Provider, where get_url_list() is abstract.
		$provider = new class() extends WP_Sitemaps_Provider {
			/**
			 * Name the provider so core routes wp-sitemap-archives-archive-1.xml here.
			 */
			public function __construct() {
				$this->name        = 'archives';
				$this->object_type = 'archive';
			}

			/**
			 * Every public post-type archive link, minus the noindexed member archive.
			 *
			 * @param int    $page_num       Page number. Ignored; this provider has one page.
			 * @param string $object_subtype Subtype. Ignored — this provider has none.
			 * @return array List of array( 'loc' => URL ).
			 */
			public function get_url_list( $page_num, $object_subtype = '' ) {
				$urls       = array();
				$post_types = get_post_types( array( 'public' => true, 'has_archive' => true ), 'names' );
				unset( $post_types['member'] ); // Noindexed, so keep it out of the sitemap too.
				foreach ( $post_types as $post_type ) {
					$link = get_post_type_archive_link( $post_type );
					if ( $link ) {
						$urls[] = array( 'loc' => $link );
					}
				}
				return $urls;
			}

			/**
			 * How many pages this provider's sitemap spans.
			 *
			 * @param string $object_subtype Subtype. Ignored — this provider has none.
			 * @return int Always 1: a handful of archive links needs no paging.
			 */
			public function get_max_num_pages( $object_subtype = '' ) {
				return 1;
			}
		};
	// phpcs:enable Generic.CodeAnalysis.UnusedFunctionParameter
	// phpcs:enable PHPCompatibility.FunctionDeclarations.NewClosure.ThisFoundOutsideClass

		wp_register_sitemap_provider( 'archives', $provider );
	}
);
