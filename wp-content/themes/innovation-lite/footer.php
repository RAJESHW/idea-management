<?php
/* 	Innovation Lite Theme's Footer
	Copyright: 2015-2016, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since Innovation Lite 1.0
*/
?>
<div class="clear"></div>
<div id="footer">

<div class="versep"></div>
<div id="footer-content">

<div class="social social-link">
	  <?php foreach (range(1, 5 ) as $numslinksn) { 
	  if ( innovation_get_option('sl' . $numslinksn, '#') != '' ): echo '<a href="'. esc_url(innovation_get_option('sl' . $numslinksn, '#')) .'" target="_blank"> </a>'; endif;
	  } ?>
</div>

<?php
   	get_sidebar( 'footer' );
?>

<div id="creditline"><?php innovationlite_creditline(); ?></div>

</div> <!-- footer-content -->
</div> <!-- footer -->
<a href="#" class="go-top"></a>
<div class="clear"> </div>
</div>
<?php wp_footer(); ?>
</body>
</html>