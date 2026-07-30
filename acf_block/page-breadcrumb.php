<?php
$current_id = get_the_ID();
$posttype   = get_post_type( $current_id );

if ( $current_id && ( $posttype === 'page' ) ) {

	$display_breadcrumb    = theme_field( 'display_breadcrumb', $current_id );
	$default_breadcrumb    = theme_field( 'default_breadcrumb', $current_id );
	$add_custom_breadcrumb = theme_field( 'add_custom_breadcrumb', $current_id );

	if ( $display_breadcrumb && $default_breadcrumb ) {
		?>
		<div class="breadcrumb bg-n-10">
			<div class="breadcrumb-menu container">
				<div class="breadcrumb-menu__item">
					<a href="<?php echo esc_url( home_url() ); ?>">Home</a>
				</div>
				<div class="breadcrumb-menu__item"> <?php echo esc_html( get_the_title( $current_id ) ); ?> </div>
			</div>
		</div>
		<?php
	}

	if ( $display_breadcrumb && ! $default_breadcrumb && $add_custom_breadcrumb ) {
		?>
		<div class="breadcrumb bg-n-10">
			<div class="breadcrumb-menu container">
				<?php foreach ( $add_custom_breadcrumb as $value ) { ?>
					<div class="breadcrumb-menu__item">
						<a href="<?php echo esc_url( $value['link'] ); ?>"> <?php echo esc_html( $value['item'] ); ?> </a>
					</div>
				<?php } ?>
				<div class="breadcrumb-menu__item"> <?php echo esc_html( get_the_title( $current_id ) ); ?> </div>
			</div>
		</div>
		<?php
	}
}
