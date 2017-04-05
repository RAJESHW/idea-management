var EasyFAQs_scroll_if_anchor = function (href, scroll_offset, push_state) {

    if ( typeof(push_state) == 'undefined' ) {
		push_state = true;
	}
	
	// If our Href points to a valid, non-empty anchor, and is on the same page (e.g. #foo)
    // Legacy jQuery and IE7 may have issues: http://stackoverflow.com/q/1593174
    var $target = jQuery(href);

    // Older browsers without pushState might flicker here, as they momentarily
    // jump to the wrong position (IE < 10)
    if($target.length) {
        jQuery('html, body').animate({ scrollTop: $target.offset().top - scroll_offset });
        if(push_state && history && "pushState" in history) {
            history.pushState({}, document.title, window.location.pathname + href);
            return false;
        }
    }
};  


var initEasyFAQs = function ()
{	
	/* src: http://james.padolsey.com/javascript/parsing-urls-with-the-dom/ */
	var parse_url = function (url) {
		var a =  document.createElement('a');
		a.href = url;
		return {
			source: url,
			protocol: a.protocol.replace(':',''),
			host: a.hostname,
			query: a.search,
			path: a.pathname.replace(/^([^/])/,'/$1'),
		};
	};

	var url_links_to_self = function(url) {
		if ( /^#/.test(url) === true ) {
			return true; // starts with a hash, nothing else to check
		}
		
		// now compare the hostname, path, and query of the 2 URLs
		var p1 = parse_url(url);
		var p2 = parse_url(window.location.href);

		if ( p1.host == p2.host && p1.path == p2.path && p1.query == p2.query ) {
			return true;
		}
		
		// no match
		return false;
	};
	
	var open_faq_by_id = function (id) {		
		var faq = jQuery(id);
		if ( faq.length > 0 && faq.hasClass('easy-faq') ) {
			var faq_header = faq.find('h3.easy-faq-title');
			if ( !faq_header.hasClass('ui-state-active') ) {
				faq_header.trigger("click");
			}
		}		
	};
	
	var update_hash = function ( event, ui ) {

		if ( ui.newHeader.length && history && ("pushState" in history) ) {
			var faq = jQuery (ui.newHeader).parents('.easy-faq:first');
			var faq_id = faq.attr('id');			
			if ( ('#' + faq_id) !== window.location.hash ) {
				history.pushState({}, document.title, window.location.pathname + '#' + faq_id);
			}
		}
	};

	jQuery(window).on("hashchange", function(e) {
		open_faq_by_id(window.location.hash);
		
		setTimeout( function () { EasyFAQs_scroll_if_anchor(window.location.hash, scroll_offset, false); }, 500 );
	});
	
	var options = {
		header: '.easy-faq-title',
		collapsible: true,
		heightStyle: "content",
		beforeActivate: update_hash
	};
	jQuery( ".easy-faqs-accordion" ).accordion(options);
	var options = {
		header: '.easy-faq-title',
		active: false,
		collapsible: true,
		heightStyle: "content",
		beforeActivate: update_hash
	};
	jQuery( ".easy-faqs-accordion-collapsed" ).accordion(options);
	
	// init category accordions
	jQuery('.easy_faqs_category_wrapper.categories_accordion').each ( function () {
		var collapsed = jQuery(this).hasClass('categories_accordion_collapsed');
		var options = {
			header: '.easy-faqs-category-heading',
			collapsible: true,
			heightStyle: "content",
		};
		if (collapsed) {
			options['active'] = false;
		}
		jQuery(this).accordion(options);	
	});

	//quicklinks
	var quicklinks = jQuery(".faq-questions");
	var scroll_offset = quicklinks.data('scroll_offset');
	if (!scroll_offset) {
		scroll_offset = 0;
	}	
	
	jQuery('.easy-faqs-wrapper').on('click', 'a', function() {		
		// disable on quicklinks
		if (jQuery(this).is('.faq-questions a')) {
			return true;
		}
		
		var href = jQuery(this).attr('href');
		var hash = href.replace(/^.*?#/,''); // remove everything before the #, leaving the hash		
		var is_easy_faqs_anchor = ( hash.indexOf('easy-faq-') > -1 );
		
		if( url_links_to_self(href) && is_easy_faqs_anchor ) {
				EasyFAQs_scroll_if_anchor('#' + hash, scroll_offset, false);
				open_faq_by_id('#' + hash);
				return false; // prevent default
		}
	});
		
	// wire up quicklinks to jump to their anchors
	quicklinks.on('click', 'a', function() {
		
		var faq_header = jQuery("#easy-faq-" + jQuery(this).parent("li").attr("id") + " h3");
		var href = jQuery(this).attr('href');
		var faq_body = faq_header.parents('.easy-faq:first').find('.easy-faq-body');		
		var needs_open = ( !faq_body.hasClass('ui-accordion-content-active') );
		
		// if an FAQ is already being shown, collapse it first
		jQuery('.easy-faqs-wrapper .ui-accordion-content-active').not(faq_body).css('display', 'none').removeClass('ui-accordion-content-active');
		
		// scroll to the anchor, accounting for offsets
		setTimeout(function () {
			EasyFAQs_scroll_if_anchor(href, scroll_offset);
			// then, only if the FAQ is not already expanded, expend it now
		}, 1);
				
		if ( needs_open ) {
			faq_header.trigger("click");
		}
		return false;
	});
	
	// if the URL's hash matches an FAQ, expand it and scroll to it
	var hash = window.location.hash;
	if (typeof(hash) == 'string' && hash.length > 0 && hash != '#' && hash.indexOf('easy-faq-') >= 0) {
		var starting_faq = jQuery(hash);
		if (starting_faq.length > 0 && starting_faq.hasClass('easy-faq')){
			var starting_header = starting_faq.find('h3.easy-faq-title');
			if (!starting_header.hasClass('ui-state-active')) {
				starting_header.trigger("click");
				EasyFAQs_scroll_if_anchor(hash, scroll_offset);
			}
		}
	}
	
}

jQuery(initEasyFAQs);