<?php

/**
 * The template for displaying 404 pages (Not Found).
 */

get_header(); 

?>
	
<div class="container">			
		
	<h1>404 - Page Not Found</h1>

	<p>We couldn't find the page you were looking for.</p>
	<p>If you typed the URL, check for spelling errors. If you followed a link, try going back in your browser or refreshing the page.</p>

	<p>Search the website:</p>
	<?php get_search_form(); ?>

</div>

<?php get_footer(); ?>
