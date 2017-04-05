<?php

function innovation_customize_register($wp_customize){

    
    $wp_customize->add_section('innovation_options', array(
        'priority' 		=> 10,
		'capability'     => 'edit_theme_options',
		'title'    		=> __('INNOVATION OPTIONS', 'innovation-lite'),
        'description'   => '<div class="infohead"><span class="donation">A Theme is an effort of many sleepless nights of the Developers.  If you like this FREEE Theme You can consider for a 5 star rating and honest review. Your review will inspire us. You can<a href="https://wordpress.org/support/view/theme-reviews/innovation-lite" target="_blank"> <strong>Review Here</strong></a>.</span><br /><br /><br /><span class="donation"> Need More Features and Options including Multi Layer Slides, Unlimited Slide Items, Links from Featured Boxes, Portfolio and 100+ Advanced Features and Controls? Try <a href="http://d5creation.com/theme/innovation/" target="_blank"><strong>Innovation Extend</strong></a></span><br /> <br /><br /><span class="donation"> You can Visit the Innovation Extend <a href="http://demo.d5creation.com/themes/?theme=Innovation" target="_blank"><strong>DEMO 1</strong></a> and <a href="http://demo.d5creation.com/themes/?theme=Innovation-Box" target="_blank"><strong>DEMO 2</strong></a></span></div>'
    ));
	

//  Responsive Layout
    $wp_customize->add_setting('innovation[responsive]', array(
        'default'        	=> '1',
    	'sanitize_callback' => 'esc_html',
        'capability'     	=> 'edit_theme_options',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_responsive', array(
        'label'      => __('Use Responsive Layout', 'innovation-lite'),
        'section'    => 'innovation_options',
        'settings'   => 'innovation[responsive]',
		'description' => __('Check the Box if you want the Responsive Layout of your Website','innovation-lite'),
		'type' 		 => 'checkbox'
    ));
	
	
	
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //

 $wp_customize->add_section('innovation_social', array(
        'priority' 		=> 11,
		'capability'     => 'edit_theme_options',
		'title'    		=> __('&nbsp;&nbsp;&nbsp;&nbsp; - Social Links', 'innovation-lite'),
        'description'   => ''
    ));	
	

//  Social Links
	foreach (range(1, 5 ) as $numslinksn) {
    $wp_customize->add_setting('innovation[sl' . $numslinksn .']', array(
        'default'        	=> 'https://wordpress.org/themes/author/d5creation',
    	'sanitize_callback' => 'esc_url',
        'capability'     	=> 'edit_theme_options',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_sl' . $numslinksn, array(
        'label'      => __('Social Link - ',  'innovation-lite'). $numslinksn,
        'section'    => 'innovation_social',
        'settings'   => 'innovation[sl' . $numslinksn .']',
		'description' => __('Input Your Social Page Link. Example: <b>http://facebook.com/d5creation</b>', 'innovation-lite'),
    ));	
	}
	
	
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //

 $wp_customize->add_section('innovation_slide', array(
        'priority' 		=> 12,
		'capability'     => 'edit_theme_options',
		'title'    		=> __('&nbsp;&nbsp;&nbsp;&nbsp; - Slide Images', 'innovation-lite'),
        'description'   => ''
    ));	
	
	
//  Banner Images/ Slide Images

	foreach (range(1,3) as $opsinumber) {
		
    $wp_customize->add_setting('innovation[slide-back' . $opsinumber .']', array(
        'default'           => get_template_directory_uri() . '/images/slide/slideback' . $opsinumber . '.jpg',
        'capability'        => 'edit_theme_options',
    	'sanitize_callback' => 'esc_url',
        'type'           	=> 'option'
		

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'slide-back' . $opsinumber, array(
        'label'    			=> __('Slide Image', 'innovation-lite') . '-' .$opsinumber ,
        'section'  			=> 'innovation_slide',
        'settings' 			=> 'innovation[slide-back' . $opsinumber .']',
		'description'   	=> __('Upload or Input the Slide Image URL Here. Recommended Size: 2300px X 700px','innovation-lite')
		
    )));
	}
	
	
	
	
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //

 $wp_customize->add_section('innovation_fbox', array(
        'priority' 		=> 13,
		'capability'     => 'edit_theme_options',
		'title'    		=> __('&nbsp;&nbsp;&nbsp;&nbsp; - Featured Boxes', 'innovation-lite'),
        'description'   => ''
    ));	
	
	
	foreach (range(1,4) as $fbsinumber ) {
	if ( $fbsinumber == 1 ): $innovation_bi = 'fa-asterisk'; endif; if ( $fbsinumber == 2 ): $innovation_bi = 'fa-university'; endif;  if ( $fbsinumber == 3 ): $innovation_bi = 'fa-heartbeat'; endif;  if ( $fbsinumber == 4 ): $innovation_bi = 'fa-cog'; endif;
		
// Featured Icon
    $wp_customize->add_setting('innovation[featured-icon' . $fbsinumber . ']', array(
        'default'        	=> $innovation_bi,
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_html',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_featured-icon' . $fbsinumber, array(
        'label'      => __('Icon Name', 'innovation-lite'). '-' . $fbsinumber,
        'section'    => 'innovation_fbox',
        'settings'   => 'innovation[featured-icon' . $fbsinumber .']',
		'description'   => __('Icon Name from', 'innovation-lite'). ' <a href="'.esc_url('http://fontawesome.io/icons').'" target="_blank" >Font Awesome</a>'
    ));	
	  
// Featured Title
    $wp_customize->add_setting('innovation[featured-title' . $fbsinumber . ']', array(
        'default'        	=> __('Innovation Responsive','innovation-lite'),
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_textarea',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_featured-title' . $fbsinumber, array(
        'label'      => __('Featured Title', 'innovation-lite'). '-' . $fbsinumber,
        'section'    => 'innovation_fbox',
        'settings'   => 'innovation[featured-title' . $fbsinumber .']'
    ));


// Featured Description
    $wp_customize->add_setting('innovation[featured-description' . $fbsinumber . ']', array(
        'default'        	=> __('The Color changing options of Innovation Lite will give the WordPress Driven Site an attractive look. Innovation Lite is super elegant and Professional Responsive Theme which will create the business widely expressed','innovation-lite'),
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_textarea',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_featured-description' . $fbsinumber  , array(
        'label'      => __('Featured Description', 'innovation-lite') . '-' . $fbsinumber,
        'section'    => 'innovation_fbox',
        'settings'   => 'innovation[featured-description' . $fbsinumber .']',
		'type' 		 => 'textarea'
    ));


// Featured Link
    $wp_customize->add_setting('innovation[featured-link' . $fbsinumber . ']', array(
        'default'        	=> '#',
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_url',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_featured-link' . $fbsinumber, array(
        'label'      => __('Featured Title', 'innovation-lite'). '-' . $fbsinumber,
        'section'    => 'innovation_fbox',
        'settings'   => 'innovation[featured-link' . $fbsinumber .']'
    ));	
	
	
  }
  
  
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //

 $wp_customize->add_section('innovation_pfbox', array(
        'priority' 		=> 14,
		'capability'     => 'edit_theme_options',
		'title'    		=> __('&nbsp;&nbsp;&nbsp;&nbsp; - Portfolio', 'innovation-lite'),
        'description'   => ''
    ));	

	
// Portfolio Heading
    $wp_customize->add_setting('innovation[portfolioboxes-heading]', array(
        'default'        	=> __('A FEW FROM OUR PORTFOLIO','innovation-lite'),
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_textarea',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_portfolioboxes-heading' , array(
        'label'      => __('Portfolio Boxes Heading', 'innovation-lite'),
        'section'    => 'innovation_pfbox',
        'settings'   => 'innovation[portfolioboxes-heading]'
    ));
	
// Portfolio Description
    $wp_customize->add_setting('innovation[portfolioboxes-heading-des]', array(
        'default'        	=> __('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua','innovation-lite'),
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_textarea',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_portfolioboxes-heading-des' , array(
        'label'      => __('Portfolio Boxes Heading Description', 'innovation-lite'),
        'section'    => 'innovation_pfbox',
        'settings'   => 'innovation[portfolioboxes-heading-des]',
		'type' 		 => 'textarea'
    ));

  
 	  
//  Portfolio Image

	foreach (range(1, 4 ) as $portfolioboxsnumber) {
		
    $wp_customize->add_setting('innovation[portfolioboxes-image' . $portfolioboxsnumber .']', array(
        'default'           => get_template_directory_uri() . '/images/pf'.  $portfolioboxsnumber . '.png',
        'capability'        => 'edit_theme_options',
    	'sanitize_callback' => 'esc_url',
        'type'           	=> 'option'
		

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'portfolioboxes-image' . $portfolioboxsnumber, array(
        'label'    			=> __('Portfolio Image', 'innovation-lite') . '-' .$portfolioboxsnumber ,
        'section'  			=> 'innovation_pfbox',
        'settings' 			=> 'innovation[portfolioboxes-image' . $portfolioboxsnumber .']',
		'description'   	=> __('Uoload the Portfolio Image. The Sample Image is 400px X 300px','innovation-lite')
		
    )));
	
// Portfolio Title
    $wp_customize->add_setting('innovation[portfolioboxes-title' . $portfolioboxsnumber . ']', array(
        'default'        	=> __('OUR PROJECT ',  'innovation-lite'). $portfolioboxsnumber,
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_textarea',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_portfolioboxes-title' . $portfolioboxsnumber, array(
        'label'      => __('Portfolio Title', 'innovation-lite'). '-' . $portfolioboxsnumber,
        'section'    => 'innovation_pfbox',
        'settings'   => 'innovation[portfolioboxes-title' . $portfolioboxsnumber .']'
    ));
	
	
// Portfolio Caption
    $wp_customize->add_setting('innovation[portfolioboxes-description' . $portfolioboxsnumber . ']', array(
        'default'        	=> __('Innovation Lite is a Professional Responsive Theme','innovation-lite'),
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_textarea',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_portfolioboxes-description' . $portfolioboxsnumber, array(
        'label'      => __('Portfolio Description', 'innovation-lite'). '-' . $portfolioboxsnumber,
        'section'    => 'innovation_pfbox',
        'settings'   => 'innovation[portfolioboxes-description' . $portfolioboxsnumber .']',
		'type' 		 => 'textarea'
    ));
	
// Portfolio Link
    $wp_customize->add_setting('innovation[portfolioboxes-link' . $portfolioboxsnumber . ']', array(
        'default'        	=> '#',
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_url',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_portfolioboxes-link' . $portfolioboxsnumber, array(
        'label'      => __('Portfolio Title', 'innovation-lite'). '-' . $portfolioboxsnumber,
        'section'    => 'innovation_pfbox',
        'settings'   => 'innovation[portfolioboxes-link' . $portfolioboxsnumber .']'
    ));
	
	
	}


  
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //

 $wp_customize->add_section('innovation_heading', array(
        'priority' 		=> 15,
		'capability'     => 'edit_theme_options',
		'title'    		=> __('&nbsp;&nbsp;&nbsp;&nbsp; - Front Page Heading', 'innovation-lite'),
        'description'   => ''
    ));	

	
// Heading
    $wp_customize->add_setting('innovation[heading_text1]', array(
        'default'        	=> __('WordPress is web <em>software you can use to create websites!</em>','innovation-lite'),
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_textarea',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_heading_text1' , array(
        'label'      => __('Heading', 'innovation-lite'),
        'section'    => 'innovation_heading',
        'settings'   => 'innovation[heading_text1]'
    ));
	
// Description
    $wp_customize->add_setting('innovation[heading_des1]', array(
        'default'        	=> __('It is Amazing!  <em>Over 60 million people</em> have chosen WordPress to power the place on the web','innovation-lite'),
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_textarea',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_heading_des1' , array(
        'label'      => __('Heading Description', 'innovation-lite'),
        'section'    => 'innovation_heading',
        'settings'   => 'innovation[heading_des1]',
		'type' 		 => 'textarea'
    ));

// Heading Link
    $wp_customize->add_setting('innovation[heading_btn1_link]', array(
        'default'        	=> __('https://wordpress.org/themes/author/d5creation','innovation-lite'),
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_url',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_heading_btn1_link' , array(
        'label'      => __('Button URL', 'innovation-lite'),
        'section'    => 'innovation_heading',
        'settings'   => 'innovation[heading_btn1_link]'
    ));
 
 
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //

 $wp_customize->add_section('innovation_stbox', array(
        'priority' 		=> 16,
		'capability'     => 'edit_theme_options',
		'title'    		=> __('&nbsp;&nbsp;&nbsp;&nbsp; - Staff Boxes', 'innovation-lite'),
        'description'   => ''
    ));	
 
 // Staff Box Heading
    $wp_customize->add_setting('innovation[staffboxes-heading]', array(
        'default'        	=> __('WE ARE INSIDE','innovation-lite'),
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_textarea',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_staffboxes-heading' , array(
        'label'      => __('Staff Boxes Heading', 'innovation-lite'),
        'section'    => 'innovation_stbox',
        'settings'   => 'innovation[staffboxes-heading]'
    ));
	
// Staff Box Description
    $wp_customize->add_setting('innovation[staffboxes-heading-des]', array(
        'default'        	=> __('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua','innovation-lite'),
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_textarea',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_staffboxes-heading-des' , array(
        'label'      => __('Staff Boxes Heading Description', 'innovation-lite'),
        'section'    => 'innovation_stbox',
        'settings'   => 'innovation[staffboxes-heading-des]',
		'type' 		 => 'textarea'
    ));

  
 	  
//  Staff Boxes

	foreach (range(1, 3 ) as $staffboxsnumber) {
	
//  Staff Box Image
    $wp_customize->add_setting('innovation[staffboxes-image' . $staffboxsnumber .']', array(
        'default'           => get_template_directory_uri() . '/images/stf'.  $staffboxsnumber . '.jpg',
        'capability'        => 'edit_theme_options',
    	'sanitize_callback' => 'esc_url',
        'type'           	=> 'option'
		

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'staffboxes-image' . $staffboxsnumber, array(
        'label'    			=> __('Staff Image', 'innovation-lite') . '-' .$staffboxsnumber ,
        'section'  			=> 'innovation_stbox',
        'settings' 			=> 'innovation[staffboxes-image' . $staffboxsnumber .']',
		'description'   	=> __('Uoload the Staff Image. The Sample Image is 300px X 200px','innovation-lite')
		
    )));
	
// Staff Box Title
    $wp_customize->add_setting('innovation[staffboxes-title' . $staffboxsnumber . ']', array(
        'default'        	=> __('OUR PROJECT ',  'innovation-lite'). $staffboxsnumber,
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_textarea',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_staffboxes-title' . $staffboxsnumber, array(
        'label'      => __('Staff Name', 'innovation-lite'). '-' . $staffboxsnumber,
        'section'    => 'innovation_stbox',
        'settings'   => 'innovation[staffboxes-title' . $staffboxsnumber .']'
    ));
	
	
// Staff Box Caption
    $wp_customize->add_setting('innovation[staffboxes-description' . $staffboxsnumber . ']', array(
        'default'        	=> __('Service Executive','innovation-lite'),
        'capability'     	=> 'edit_theme_options',
    	'sanitize_callback' => 'esc_textarea',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_staffboxes-description' . $staffboxsnumber, array(
        'label'      => __('Staff Designation', 'innovation-lite'). '-' . $staffboxsnumber,
        'section'    => 'innovation_stbox',
        'settings'   => 'innovation[staffboxes-description' . $staffboxsnumber .']',
		'type' 		 => 'textarea'
    ));
	
	$wp_customize->add_setting('innovation[staffboxes-linka' . $staffboxsnumber .']', array(
        'default'        	=> 'https://wordpress.org/themes/author/d5creation',
    	'sanitize_callback' => 'esc_url',
        'capability'     	=> 'edit_theme_options',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_staffboxes-linka' . $staffboxsnumber, array(
        'label'      => __('Staff Social Links - ',  'innovation-lite'). $staffboxsnumber,
        'section'    => 'innovation_stbox',
        'settings'   => 'innovation[staffboxes-linka' . $staffboxsnumber .']',
		'description' => __('Input Your Social Page Link. Example: <b>https://facebook.com/d5creation</b>', 'innovation-lite'),
    ));	
	
	$wp_customize->add_setting('innovation[staffboxes-linkb' . $staffboxsnumber .']', array(
        'default'        	=> 'https://wordpress.org/themes/author/d5creation',
    	'sanitize_callback' => 'esc_url',
        'capability'     	=> 'edit_theme_options',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_staffboxes-linkb' . $staffboxsnumber, array(
        'section'    => 'innovation_stbox',
        'settings'   => 'innovation[staffboxes-linkb' . $staffboxsnumber .']',

    ));	
	
	$wp_customize->add_setting('innovation[staffboxes-linkc' . $staffboxsnumber .']', array(
        'default'        	=> 'https://wordpress.org/themes/author/d5creation',
    	'sanitize_callback' => 'esc_url',
        'capability'     	=> 'edit_theme_options',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_staffboxes-linkc' . $staffboxsnumber, array(
        'section'    => 'innovation_stbox',
        'settings'   => 'innovation[staffboxes-linkc' . $staffboxsnumber .']',

    ));	
	
	$wp_customize->add_setting('innovation[staffboxes-link' . $staffboxsnumber .']', array(
        'default'        	=> 'https://wordpress.org/themes/author/d5creation',
    	'sanitize_callback' => 'esc_url',
        'capability'     	=> 'edit_theme_options',
        'type'           	=> 'option'

    ));

    $wp_customize->add_control('innovation_staffboxes-link' . $staffboxsnumber, array(
        'label'      => __('Profile Link - ',  'innovation-lite'). $staffboxsnumber,
        'section'    => 'innovation_stbox',
        'settings'   => 'innovation[staffboxes-link' . $staffboxsnumber .']',
		'description' => __('Input Your Profile Page Link here','innovation-lite'),
    ));	
	
	}

}


add_action('customize_register', 'innovation_customize_register');


	if ( ! function_exists( 'innovation_get_option' ) ) :
	function innovation_get_option( $innovation_name, $innovation_default = false ) {
	$innovation_config = get_option( 'innovation' );

	if ( ! isset( $innovation_config ) ) : return $innovation_default; else: $innovation_options = $innovation_config; endif;
	if ( isset( $innovation_options[$innovation_name] ) ):  return $innovation_options[$innovation_name]; else: return $innovation_default; endif;
	}
	endif;