<?php


if ( function_exists( 'acf_register_block_type' ) ) :

	acf_register_block_type(array(
		'name' => 'hero_banner_1',
		'title' => 'Hero Banner',
		'description' => '',
		'category' => 'home page',
		'keywords' => array(
			0 => 'hero_banner_1',
		),
		'post_types' => array(
		),
		'mode' => 'preview',
		'align' => '',
		'align_content' => NULL,
		'render_template' => 'acf_block/hero_banner_1.php',
		'render_callback' => '',
		'enqueue_style' => '',
		'enqueue_script' => '',
		'enqueue_assets' => '',
		'icon' => '',
		'supports' => array(
			'align' => false,
			'mode' => true,
			'multiple' => true,
			'jsx' => false,
			'align_content' => false,
			'anchor' => false,
		),
	));

	
	acf_register_block_type(array(
		'name' => 'campaigns_block',
		'title' => 'Campaigns Block',
		'description' => '',
		'category' => 'home page',
		'keywords' => array(
			0 => 'campaigns_block',
		),
		'post_types' => array(
		),
		'mode' => 'preview',
		'align' => '',
		'align_content' => NULL,
		'render_template' => 'acf_block/campaigns_block.php',
		'render_callback' => '',
		'enqueue_style' => '',
		'enqueue_script' => '',
		'enqueue_assets' => '',
		'icon' => '',
		'supports' => array(
			'align' => false,
			'mode' => true,
			'multiple' => true,
			'jsx' => false,
			'align_content' => false,
			'anchor' => false,
		),
	));

	acf_register_block_type(array(
		'name' => 'member_block',
		'title' => 'Member Experts',
		'description' => '',
		'category' => 'home page',
		'keywords' => array(
			0 => 'member_block',
		),
		'post_types' => array(
		),
		'mode' => 'preview',
		'align' => '',
		'align_content' => NULL,
		'render_template' => 'acf_block/member_block.php',
		'render_callback' => '',
		'enqueue_style' => '',
		'enqueue_script' => '',
		'enqueue_assets' => '',
		'icon' => '',
		'supports' => array(
			'align' => false,
			'mode' => true,
			'multiple' => true,
			'jsx' => false,
			'align_content' => false,
			'anchor' => false,
		),
	));


	acf_register_block_type(
		array(
			'name' => 'member_organization',
			'title' => 'Member Organization',
			'description' => '',
			'category' => 'home page',
			'keywords' => array(
				0 => 'member_organization',
			),
			'post_types' => array(
			),
			'mode' => 'preview',
			'align' => '',
			'align_content' => NULL,
			'render_template' => 'acf_block/member_organization.php',
			'render_callback' => '',
			'enqueue_style' => '',
			'enqueue_script' => '',
			'enqueue_assets' => '',
			'icon' => '',
			'supports' => array(
				'align' => false,
				'mode' => true,
				'multiple' => true,
				'jsx' => false,
				'align_content' => false,
				'anchor' => false,
			),
		)
	);

	acf_register_block_type(
		array(
			'name' => 'latest_resources',
			'title' => 'Latest Resources',
			'description' => '',
			'category' => 'page',
			'keywords' => array(
				0 => 'latest_resources',
			),
			'render_template' => 'acf_block/latest_resources.php',
			'enqueue_style' => '',
			'enqueue_script' => '',
		)
	);

	acf_register_block_type(
		array(
			'name' => 'news_block',
			'title' => 'News Block',
			'description' => '',
			'category' => 'page',
			'keywords' => array(
				0 => 'news_block',
			),
			'render_template' => 'acf_block/news_block.php',
			'enqueue_style' => '',
			'enqueue_script' => '',
		)
	);

	acf_register_block_type(
		array(
			'name' => 'page-breadcrumb',
			'title' => 'Page Breadcrumb',
			'description' => '',
			'category' => 'page',
			'keywords' => array(
				0 => 'page-breadcrumb',
			),
			
			'render_template' => 'acf_block/page-breadcrumb.php',
			'enqueue_style' => '',
			'enqueue_script' => '',
		)
	);

	acf_register_block_type(
		array(
			'name' => 'contact-us',
			'title' => 'contact us',
			'description' => '',
			'category' => 'page',
			'keywords' => array(
				0 => 'contact-us',
			),
			
			'render_template' => 'acf_block/contact-us.php',
			'enqueue_style' => '',
			'enqueue_script' => '',
		)
	);

	acf_register_block_type(
		array(
			'name' => 'page-pagination',
			'title' => 'Pagination',
			'description' => '',
			'category' => 'page',
			'keywords' => array(
				0 => 'page-pagination',
			),
			
			'render_template' => 'acf_block/page-pagination.php',
			'enqueue_style' => '',
			'enqueue_script' => '',
		)
	);

endif;
