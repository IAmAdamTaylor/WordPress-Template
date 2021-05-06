<?php

/**
 * The front page template file
 *
 * If the user has selected a static page for their homepage, 
 * this is what will appear.
 * Learn more: https://codex.wordpress.org/Template_Hierarchy
 */

get_header(); 

?>

<?php
	/**
	 * Since the home page is normally quite dynamic,
	 * and we can not accurately predict what is needed,
	 * pass this off to the default page template instead.
	 * 
	 * Should be overwritten in child theme.
	 */
	get_template_part( 'page' );
?>

<?php get_footer(); ?>
