<?php

/* 	Innovation Lite Theme's Single Page to display Single Page or Post
	Copyright: 2015-2016, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since Innovation Lite 1.0
*/


get_header(); ?>
<div id="container">
<?php get_template_part( 'post-content' ); ?>
<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>