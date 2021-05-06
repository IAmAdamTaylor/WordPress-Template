<?php

/**
 * The template used for displaying generic post content in the loop.
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
					
	<h1><?php echo get_the_title(); ?></h1>
	<?php the_excerpt(); ?>

	<a href="<?php echo get_the_permalink(); ?>">View post</a>

</article><!-- #post -->
