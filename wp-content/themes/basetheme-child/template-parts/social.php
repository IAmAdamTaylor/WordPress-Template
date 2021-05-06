<?php
	
/**
 * Template part showing the social icons for the header.
 */

$social_map = array(
	'facebook' => get_field( 'facebook', 'options' ),
	'twitter' => get_field( 'twitter', 'options' ),
	'instagram' => get_field( 'instagram', 'options' ),
	'linkedin' => get_field( 'linkedin', 'options' ),
);	

?>

<?php if ( array_filter( $social_map ) ): ?>
	
	<ul class="social-wrapper">

		<?php if ( $social_map['facebook'] ): ?>
			<li>
				<a class="social" href="<?php echo esc_url( $social_map['facebook'] ); ?>" target="_blank" rel="noopener">
					<?php include_asset( 'static/social/facebook.svg' ); ?>
					<span class="sr-only">Like us on Facebook</span>
				</a>
			</li>
		<?php endif; ?>

		<?php if ( $social_map['twitter'] ): ?>
			<li>
				<a class="social" href="<?php echo esc_url( $social_map['twitter'] ); ?>" target="_blank" rel="noopener">
					<?php include_asset( 'static/social/twitter.svg' ); ?>
					<span class="sr-only">Follow us on Twitter</span>
				</a>
			</li>
		<?php endif; ?>

		<?php if ( $social_map['instagram'] ): ?>
			<li>
				<a class="social" href="<?php echo esc_url( $social_map['instagram'] ); ?>" target="_blank" rel="noopener">
					<?php include_asset( 'static/social/instagram.svg' ); ?>
					<span class="sr-only">Follow us on Instagram</span>
				</a>
			</li>
		<?php endif; ?>

		<?php if ( $social_map['linkedin'] ): ?>
			<li>
				<a class="social" href="<?php echo esc_url( $social_map['linkedin'] ); ?>" target="_blank" rel="noopener">
					<?php include_asset( 'static/social/linkedin.svg' ); ?>
					<span class="sr-only">Follow us on LinkedIn</span>
				</a>
			</li>
		<?php endif; ?>
		
	</ul>

<?php endif; ?>
