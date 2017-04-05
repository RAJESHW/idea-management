<?php
/* 	Innovation Lite Theme's Functions
	Copyright: 2015-2016, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since Innovation Lite 1.0
*/
 

	require_once ( trailingslashit(get_template_directory()) . 'inc/customize.php' );
	function innovation_about_page() { 
	add_theme_page( 'D5 Creation Themes', 'D5 Creation Themes', 'edit_theme_options', 'd5-themes', 'innovation_d5_themes' );
	add_theme_page( 'Innovation Options', 'Innovation Options', 'edit_theme_options', 'theme-about', 'innovation_theme_about' ); 
	}
	add_action('admin_menu', 'innovation_about_page');
	function innovation_d5_themes() {  require_once ( trailingslashit(get_template_directory()) . 'inc/d5-themes.php' ); }
	function innovation_theme_about() {  require_once ( trailingslashit(get_template_directory()) . 'inc/theme-about.php' ); }	
	function innovationlite_setup() {
//	Theme TextDomain for the Language File
	load_theme_textdomain( 'innovation-lite', get_template_directory() . '/languages' );
	
	register_nav_menus( array( 'main-menu' => __('Main Menu', 'innovation-lite'), 'top-menu' => __('Top Menu', 'innovation-lite') ) );


//	Set the content width based on the theme's design and stylesheet.
	global $content_width; if ( ! isset( $content_width ) ) $content_width = 584;
	add_theme_support( 'title-tag' );

// 	Tell WordPress for the Feed Link
	add_theme_support( 'automatic-feed-links' );
	add_editor_style();	
// 	This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 150, 150, true ); // default Post Thumbnail dimensions (cropped)
	add_image_size( 'innovationlite-fpage-thumb', 1100, 600, array( 'left', 'top' ) ); 
	
		
// 	WordPress Custom Background Support	
	$innovationlite_custom_background = array( 'default-color' => 'AAAAAA', 'default-image'  => '', );
	add_theme_support( 'custom-background', $innovationlite_custom_background );
	
// 	WordPress Custom Header Support				
	$innovationlite_custom_header = array(
	'default-image'          => '',
	'random-default'         => false,
	'width'                  => 300,
	'height'                 => 90,
	'flex-height'            => false,
	'flex-width'             => false,
	'default-text-color'     => '0a96d8',
	'header-text'            => false,
	'uploads'                => true,
	'wp-head-callback' 		 => '',
	'admin-head-callback'    => '',
	'admin-preview-callback' => '',
	);
	add_theme_support( 'custom-header', $innovationlite_custom_header );
	
	}
	add_action( 'after_setup_theme', 'innovationlite_setup' );

// 	Functions for adding script
	function innovationlite_enqueue_scripts() { 
	wp_enqueue_style('innovationlite-style', get_stylesheet_uri(), false );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) { wp_enqueue_script( 'comment-reply' ); 	}

	wp_enqueue_script( 'innovationlite-menu-style', get_template_directory_uri(). '/js/menu.js', array( 'jquery' ) );
	
	wp_register_style('innovationlite-gfonts1', '//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800', false );
	wp_enqueue_style('innovationlite-gfonts1');
	wp_register_style('innovationlite-gfonts2', '//fonts.googleapis.com/css?family=Roboto+Condensed:300italic,400italic,700italic,400,300,700', false );
	wp_enqueue_style('innovationlite-gfonts2');
	
	wp_enqueue_style('innovationlite-font-awesome-css', get_template_directory_uri(). '/css/font-awesome.css' );
	wp_enqueue_script( 'innovationlite-html5', get_template_directory_uri().'/js/html5.js'); 
    wp_script_add_data( 'innovationlite-html5', 'conditional', 'lt IE 9' );
	
	if (is_front_page()): 
	wp_enqueue_style('innovationlite-slider-css', get_template_directory_uri(). '/css/flexslider.css' ); 
	wp_enqueue_script( 'innovationlite-slider-js', get_template_directory_uri(). '/js/jquery.flexslider.js', array( 'jquery' ) );
	wp_enqueue_style('innovationlite-portfolio-component-css', get_template_directory_uri(). '/css/portfolio-component.css' ); 
	wp_enqueue_style('innovationlite-staffs-css', get_template_directory_uri(). '/css/staffs.css' );
	wp_enqueue_script( 'innovationlite-jquery.hoverfold-js', get_template_directory_uri(). '/js/smart-image-effect.js', array( 'jquery' ) );
	wp_enqueue_script( 'innovationlite-modernizr.min-js', get_template_directory_uri(). '/js/modernizr.js', array( 'jquery' ) );
	endif; 
	if ( innovation_get_option('responsive', '1') == '1' ) : wp_enqueue_style('innovationlite-responsive', get_template_directory_uri(). '/style-responsive.css' ); endif;
	}
	add_action( 'wp_enqueue_scripts', 'innovationlite_enqueue_scripts' );
	
// 	Functions for adding script to Admin Area
	function innovation_admin_style() { wp_enqueue_style( 'innovation_admin_css', get_template_directory_uri() . '/inc/admin-style.css', false ); }
	add_action( 'admin_enqueue_scripts', 'innovation_admin_style' );


// 	Functions for adding some custom code within the head tag of site
	function innovationlite_custom_code() {
?>
	
	<style type="text/css">
	.site-title a, 
	.site-title a:active, 
	.site-title a:hover {
	color: #<?php echo get_header_textcolor(); ?>;
	}
			
	<?php if ( is_admin_bar_showing() ): echo '#header { top: 32px; }'; endif; ?>
	</style>
<?php 
	
	echo innovation_get_option('headcode');
	}
	
	add_action('wp_head', 'innovationlite_custom_code');


	function innovationlite_creditline () {
	$innovationlite_theme_data = wp_get_theme(); $innovationlite_author_uri = $innovationlite_theme_data->get( 'AuthorURI' );
	echo '&copy; ' . date("Y"). ': ' . get_bloginfo( 'name' ). '<span class="credit"> | Innovation ' . __('Theme by:', 'innovation-lite') . ' <a href="'. $innovationlite_author_uri .'" target="_blank"> D5 Creation</a> | ' . __('Powered by:', 'innovation-lite') . ' <a href="http://wordpress.org" target="_blank">WordPress</a>';
    }

//	function tied to the excerpt_more filter hook.
	function innovationlite_excerpt_length( $innovationlite_excerpt_length ) {
	global $innovationlite_excerpt_length;
	if ($innovationlite_excerpt_length) {
    return $innovationlite_excerpt_length;
	} else {
    return 50; //default value
    } }
	add_filter( 'excerpt_length', 'innovationlite_excerpt_length', 999 );
	
	function innovationlite_excerpt_more($more) {
    global $post;
	return '<a href="'. get_permalink($post->ID) . '" class="read-more">' . __('Read More', 'innovation-lite'). '</a>';
	}
	add_filter('excerpt_more', 'innovationlite_excerpt_more');
	
	// Content Type Showing
	function innovationlite_content() {
	if ( is_page() || is_single() ) : the_content('<span class="read-more">' . __('Read More', 'innovation-lite'). '</span>');
	else: the_excerpt();
	endif;	
	}

//	Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link
	function innovationlite_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
	}
	add_filter( 'wp_page_menu_args', 'innovationlite_page_menu_args' );
	
// 	Post Meta design
	function innovationlite_post_meta() { ?>
	<div class="post-meta"> <span class="post-edit"> <?php edit_post_link(__('Edit', 'innovation-lite'),'<span class="fa-edit">','</span>' ); ?></span> <span class="post-author fa-user-md"> <?php the_author_posts_link(); ?> </span> <span class="post-date fa-clock-o"><?php the_time('F j, Y'); ?></span>	<span class="post-tag fa-tags"> <?php the_tags('' , ', '); ?> </span> <span class="post-category fa-archive"> <?php the_category(', '); ?> </span> <span class="post-comments fa-comments"><?php comments_popup_link(__('No Comments', 'innovation-lite') . ' &#187;', __('One Comment', 'innovation-lite') . ' &#187;', '% ' . __('Comments', 'innovation-lite') . ' &#187;'); ?> </span>
	</div> 
	
	<?php
	}
	
//	Page Navigation
	function innovationlite_page_nav() { ?>
		<div id="page-nav">
			<div class="alignleft"><?php previous_posts_link('<span class="fa-arrow-left"></span>  ' .  __('NEWER ENTRIES', 'innovation-lite') ) ?></div>
			<div class="alignright"><?php next_posts_link(__('OLDER ENTRIES', 'innovation-lite') .' <span class="fa-arrow-right"></span>') ?></div>
		</div>
    <?php }
	
//	Page Navigation
	function innovationlite_not_found() { ?>
		<br /><br />
        <div class="searchinfo">
        <h1 class="page-title fa-times-circle"><?php echo __('SORRY, NOT FOUND ANYTHING', 'innovation-lite'); ?></h1>
		<h3 class="arc-src"><span><?php echo __('You Can Try Another Search...', 'innovation-lite'); ?></span></h3>
		<?php get_search_form(); ?>
		<p class="backhome"><a href="<?php echo esc_url(home_url()); ?>" >&laquo; <?php echo __('&laquo; Or Return to the Home Page', 'innovation-lite'); ?></a></p>
        </div>
        <br />
    <?php }	
	
	
//	Registers the Widgets and Sidebars for the site
	function innovationlite_widgets_init() {

	register_sidebar( array(
		'name' =>  __('Primary Sidebar', 'innovation-lite'), 
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' =>  __('Secondary Sidebar', 'innovation-lite'), 
		'id' => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	 
	register_sidebar( array(
		'name' => __('Footer Area One', 'innovation-lite'), 
		'id' => 'sidebar-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	    
	register_sidebar( array(
		'name' =>  __('Footer Area Two', 'innovation-lite'), 
		'id' => 'sidebar-4',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __('Footer Area Three', 'innovation-lite'), 
		'id' => 'sidebar-5',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	
	register_sidebar( array(
		'name' => __('Footer Area Four', 'innovation-lite'), 
		'id' => 'sidebar-6',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	
	}
	add_action( 'widgets_init', 'innovationlite_widgets_init' );
	
	
	add_filter('the_title', 'innovationlite_title');
	function innovationlite_title($title) {
        if ( '' == $title ) {
            return '(Untitled)';
        } else {
            return $title;
        }
    }
	
