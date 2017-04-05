jQuery(document).ready(function(){ jQuery('#header').css('width', jQuery('body').outerWidth(true) ); jQuery("#main-menu-con ul ul").css({display: "none"}); jQuery('#main-menu-con ul li').hover( function() { jQuery(this).find('ul:first').slideDown(200).css('visibility', 'visible'); jQuery(this).addClass('selected'); }, function() { jQuery(this).find('ul:first').slideUp(200); jQuery(this).removeClass('selected'); }); });

jQuery(window).resize(function(){ jQuery('#header').css('width', jQuery('body').outerWidth(true) ); });

		jQuery(document).ready(function() { 
		
		jQuery('.mobile-menu').click(function(){ jQuery('#main-menu-con').toggle(); });
		
		jQuery('.headerheight').css('marginTop', jQuery('#header').outerHeight(true) );
		
			jQuery(window).scroll(function() {
				jQuery('#header').css('left','-'+jQuery(window).scrollLeft()+'px');
				if (jQuery(this).scrollTop() > jQuery('#header').outerHeight(true)) {
					jQuery(".innovationlite-top-menu-container").hide();
					jQuery('.go-top').fadeIn(150);

				} else {
					jQuery(".innovationlite-top-menu-container").show();;
					jQuery('.go-top').fadeOut(150);
				}
			});
			
			// Animate the scroll to top
			jQuery('.go-top').click(function(event) {
				event.preventDefault();
				jQuery('html, body').animate({scrollTop: 0}, 500);
			})
		
		});

	

	
		
	












