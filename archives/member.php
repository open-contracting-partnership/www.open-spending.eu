<?php
/**
 * Archive listing for members: experts and organizations, as two tabs.
 *
 * @package OpenSpendingCoalition
 */

$current_id               = get_the_ID();
$posttype                 = get_post_type( $current_id );
$post_object              = get_post_type_object( $posttype );
$labels                   = $post_object->labels;
$person_member_data       = array();
$organization_member_data = array();

if ( have_posts() ) {
	$i = 0;
	while ( have_posts() ) {
		the_post();
		$member_id     = get_the_ID();
		$post_title    = get_the_title();
		$content       = get_the_content();
		$member_fields = get_field( 'members' );
		if ( ! is_array( $member_fields ) ) {
			continue;
		}
		$member_terms      = get_the_terms( $member_id, 'type_of_member' );
		$type_of_member    = ( is_array( $member_terms ) && $member_terms ) ? $member_terms[0]->name : '';
		$logoprofile_photo = $member_fields['logoprofile_photo'] ?? '';
		$address           = $member_fields['address'] ?? '';
		$email             = $member_fields['email'] ?? '';
		$photo_src         = $logoprofile_photo ? $logoprofile_photo : get_template_directory_uri() . '/dist/images/default-post-img.jpg';

		if ( $type_of_member === 'Person' ) {
			$person_member_data[ $i ] = array(
				'id'                => $member_id,
				'title'             => $post_title,
				'content'           => $content,
				'logoprofile_photo' => $photo_src,
				'designation'       => $member_fields['designation'] ?? '',
				'address'           => $address,
				'email'             => $email,
				'quotes'            => $member_fields['quotes'] ?? '',
			);
		} elseif ( $type_of_member === 'Organization' ) {
			$organization_member_data[ $i ] = array(
				'id'                => $member_id,
				'title'             => $post_title,
				'content'           => $content,
				'logoprofile_photo' => $photo_src,
				'phone'             => $member_fields['phone'] ?? '',
				'address'           => $address,
				'email'             => $email,
				'website'           => $member_fields['website'] ?? '',
			);
		}
		++$i;
	}
}

// Breadcrumb: render the member archive's breadcrumb from its per-archive options.
$display_breadcrumb    = theme_field( 'display_breadcrumb', $posttype . '_options', '0' );
$default_breadcrumb    = theme_field( 'default_breadcrumb', $posttype . '_options', '0' );
$add_custom_breadcrumb = theme_field( 'add_custom_breadcrumb', $posttype . '_options' );

if ( $display_breadcrumb && $default_breadcrumb ) {
	echo breadcrumb_section( $current_id );
}
if ( $display_breadcrumb && ! $default_breadcrumb && $add_custom_breadcrumb ) {
	?>
	<nav class="breadcrumb bg-n-10" aria-label="Breadcrumb">
		<ol class="breadcrumb-menu container">
			<?php foreach ( $add_custom_breadcrumb as $value ) { ?>
				<li class="breadcrumb-menu__item">
					<?php // Plain text when the row has no link, since an empty href has no accessible name. ?>
					<?php if ( $value['link'] ) { ?>
						<a href="<?php echo esc_url( $value['link'] ); ?>"> <?php echo esc_html( $value['item'] ); ?> </a>
					<?php } else { ?>
						<?php echo esc_html( $value['item'] ); ?>
					<?php } ?>
				</li>
			<?php } ?>
			<li class="breadcrumb-menu__item" aria-current="page"> <?php echo esc_html( $labels->name ); ?> </li>
		</ol>
	</nav>
	<?php
}
?>

<div class="container">
	<div class="members-tab-menu">
		<div class="max-w-fit mx-auto mt-8 sm:mt-10 lg:mt-12 flex border rounded-3xl">
			<button type="button" class="active members-tab-item members-experts-btn" aria-pressed="true"
				aria-controls="members-experts">Experts</button>
			<button type="button" class="members-tab-item members-organizations-btn" aria-pressed="false"
				aria-controls="members-organizations">Organizations</button>
		</div>
	</div>

	<!-- List of experts members -->
	<div id="members-experts" class="members-experts-list">
		<div class="<?php echo esc_attr( $posttype ); ?> grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7 pt-10 sm:pt-12 lg:pt-16 pb-10 sm:pb-14 lg:pb-32">
			<?php foreach ( $person_member_data as $member_data ) { ?>
			<div data-id="<?php echo (int) $member_data['id']; ?>" class="relative member-card">
				<div class="pt-[120%] relative members-list-image">
					<?php render_acf_image( $member_data['logoprofile_photo'], array( 'alt' => $member_data['title'], 'class' => 'absolute top-0 h-full w-full object-cover rounded-3xl' ) ); ?>
				</div>
				<div class="absolute bottom-10 left-8">
					<div class="member-quote hidden opacity-0 font-medium text-lg text-n-0 mb-8 pr-8">
						<?php echo inline_svg( 'quote' ); ?>
						<?php echo wp_kses_post( $member_data['quotes'] ); ?>
					</div>
					<div class="font-bold text-heading-3 text-n-0"><?php echo esc_html( $member_data['title'] ); ?></div>
					<div class="text-n-30 mt-2"><?php echo esc_html( $member_data['address'] ); ?></div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>

	<!-- List of organizations members -->
	<div id="members-organizations" class="members-organizations-list hidden">
		<div class="pt-10 sm:pt-12 lg:pt-14 pb-10 sm:pb-14 lg:pb-20 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
			<?php foreach ( $organization_member_data as $organization_member ) { ?>
			<a href="<?php echo esc_url( $organization_member['website'] ); ?>" target="_blank" rel="noopener noreferrer">
				<div class="member-org-item">
					<?php render_acf_image( $organization_member['logoprofile_photo'], array( 'alt' => $organization_member['title'], 'class' => 'w-auto' ) ); ?>
				</div>
			</a>
			<?php } ?>
		</div>
	</div>

</div>
