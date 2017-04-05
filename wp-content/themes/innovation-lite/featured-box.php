<?php
/* 	Innovation Lite Theme's Featured Box to show the Featured Items of Front Page
	Copyright: 2015-2016, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since Innovation Lite 1.0
*/
?>

<div class="box90">
	<div class="featured-boxs">
		<?php  foreach (range(1, 4) as $fboxn) { if ( $fboxn == 1 ): $innovation_bi = 'fa-asterisk'; endif; if ( $fboxn == 2 ): $innovation_bi = 'fa-university'; endif;  if ( $fboxn == 3 ): $innovation_bi = 'fa-heartbeat'; endif;  if ( $fboxn == 4 ): $innovation_bi = 'fa-cog'; endif; ?>
				<span class="featured-box" >
                	<a href="<?php echo esc_url(innovation_get_option('featured-link'. $fboxn, '#')); ?>"> 
					<div class="box-icon <?php echo esc_html(innovation_get_option('featured-icon' . $fboxn, $innovation_bi )); ?>"></div>
						<h3 class="ftitle"><?php echo esc_attr(innovation_get_option('featured-title' . $fboxn, 'Innovation Lite Responsive')); ?></h3></a>
						<p><?php echo esc_textarea(innovation_get_option('featured-description' . $fboxn , 'The Color changing options of Innovation Lite will give the WordPress Driven Site an attractive look. Innovation Lite is super elegant and Professional Responsive Theme which will create the business widely expressed.')); ?></p>
				</span>
		<?php } ?>

	</div> <!-- featured-boxs -->

</div>
<div class="lsep"></div>
