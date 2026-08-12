<?php
/**
 * Breadcrumb block for pages, honouring the per-page ACF breadcrumb settings.
 *
 * @package OpenSpendingCoalition
 */

$current_id = get_the_ID();
$posttype   = get_post_type( $current_id );

if ( $current_id && ( $posttype === 'page' ) ) {

	$display_breadcrumb    = theme_field( 'display_breadcrumb', $current_id );
	$default_breadcrumb    = theme_field( 'default_breadcrumb', $current_id );
	$add_custom_breadcrumb = theme_field( 'add_custom_breadcrumb', $current_id );

	if ( $display_breadcrumb && $default_breadcrumb ) {
		?>
		<nav class="breadcrumb bg-n-10" aria-label="Breadcrumb">
			<ol class="breadcrumb-menu container">
				<li class="breadcrumb-menu__item">
					<a href="<?php echo esc_url( home_url() ); ?>">Home</a>
				</li>
				<li class="breadcrumb-menu__item" aria-current="page"> <?php echo esc_html( get_the_title( $current_id ) ); ?> </li>
			</ol>
		</nav>
		<?php
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
				<li class="breadcrumb-menu__item" aria-current="page"> <?php echo esc_html( get_the_title( $current_id ) ); ?> </li>
			</ol>
		</nav>
		<?php
	}
}
