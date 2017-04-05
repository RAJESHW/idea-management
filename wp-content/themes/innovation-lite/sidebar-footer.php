<?php
/* 	Innovation Lite Theme's Footer Sidebar Area
	Copyright: 2015-2016, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since Innovation Lite 1.0
*/
	
	
	if (   ! is_active_sidebar( 'sidebar-3'  )
		&& ! is_active_sidebar( 'sidebar-4' )
		&& ! is_active_sidebar( 'sidebar-5'  )
		&& ! is_active_sidebar( 'sidebar-6'  )
	)
		return;
		
	// If we get this far, we have widgets. Let do this.
?>
<div id="footer-sidebar">
		<div class="first-footer-widget">
		<?php if ( is_active_sidebar( 'sidebar-3' ) ) : dynamic_sidebar( 'sidebar-3' ); endif; ?>
    	</div><!-- #first .widget-area -->
        
		<div class="footer-widgets">
		<?php if ( is_active_sidebar( 'sidebar-4' ) ) : dynamic_sidebar( 'sidebar-4' );  endif; ?>
		</div><!-- #second .widget-area -->
    
    	<div class="footer-widgets">
		<?php if ( is_active_sidebar( 'sidebar-5' ) ) : dynamic_sidebar( 'sidebar-5' ); endif; ?>
    	</div><!-- #third .widget-area -->
    
      	<div class="footer-widgets">
    	<?php if ( is_active_sidebar( 'sidebar-6' ) ) : dynamic_sidebar( 'sidebar-6' ); endif; ?>
    	</div><!-- #fourth .widget-area -->
    
</div><!-- #footerwidget -->

