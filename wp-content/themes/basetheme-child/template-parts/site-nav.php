<?php
	
/**
 * Template part for the main navigation menu.
 */

try {
	$menu = new WP_Menu_Query( array(
		'location' => 'header-menu',
	) );
} catch (Exception $e) {
	return;	
}

if ( !$menu->have_items() ) {
	return;
}

?>

<nav class="site-nav js-site-nav" aria-labelledby="site-nav-label">

	<h2 id="site-nav-label" class="sr-only">Main Menu</h2>

	<button class="site-nav__toggle js-site-nav-toggle" type="button">
		<span class="icon-hamburger">
			<span class="icon-hamburger__line icon-hamburger__line--top"></span>
			<span class="icon-hamburger__line icon-hamburger__line--middle"></span>
			<span class="icon-hamburger__line icon-hamburger__line--bottom"></span>
		</span>
		<span class="sr-only">Menu</span>
	</button>
	
	<ul class="site-nav__list">
		
		<?php while ( $menu->have_items() ): ?>

			<?php
				$item = $menu->the_item();
				$classes = $item->classes;

				if ( $item->is_current() || $item->has_current_child() ) {
					$classes[] = 'is-current';
				}

				if ( $item->has_children() ) {
					$classes[] = 'has-children';
				}
			?>
			<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
				
				<a 
					class="site-nav__item" 
					href="<?php echo esc_url( $item->url ); ?>" 
					<?php echo ( $item->target ? 'target="' . $item->target . '"' : '' ); ?> 
				>
					<?php echo $item->title; ?>
				</a>

			</li>

		<?php endwhile; ?>
		
	</ul>

</nav>
