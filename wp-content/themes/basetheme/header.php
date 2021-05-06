<?php

/**
 * The template for displaying the header.
 */

?><!DOCTYPE html>
<html <?php html_class(); ?> <?php language_attributes(); ?>>
<head>
  <title><?php trim(wp_title('')); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> >

	<header class="site-header" role="banner">
		
	</header> <!-- /.site-header -->
  
 	<?php get_template_part( 'template-parts/wrapper/start' ); ?>    
