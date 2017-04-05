<?php
/*
	Plugin Name: WP Poll
	Plugin URI: https://www.pluginbazar.net/product/wp-poll/
	Description: 	It allows user to poll in your website with many awesome feature.
	Version: 2.1.3
	Author: Pluginbazar
	Author URI: https://pluginbazar.net/
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class WPPollManager {
	
	public function __construct(){
	
		$this->wpp_define_constants();
		$this->wpp_define_classes();
		$this->wpp_define_actions();
		$this->wpp_define_shortcodes();
		$this->wpp_load_functions();
		$this->wpp_load_scripts();
		
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ));
	}
	
	public function load_textdomain() {

		load_plugin_textdomain( WPP_TEXT_DOMAIN, false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' ); 
	}
	
	
	public function wpp_load_scripts(){
		
		add_action( 'admin_enqueue_scripts', 'wp_enqueue_media' );
		add_action( 'wp_enqueue_scripts', array( $this, 'wpp_front_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wpp_admin_scripts' ) );
	}
	
	public function wpp_load_functions(){
		
		require_once( plugin_dir_path( __FILE__ ) . 'includes/functions.php');
	}
	
	public function wpp_define_actions(){
		require_once( plugin_dir_path( __FILE__ ) . 'includes/actions/action-single-poll.php');	
		require_once( plugin_dir_path( __FILE__ ) . 'includes/actions/action-admin-report.php');	
	}
	
	public function wpp_define_shortcodes(){
		
		require_once( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/shortcode-poll.php');	
		require_once( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/shortcode-poll-list.php');	
	}
	
	public function wpp_define_classes(){
		
		require_once( plugin_dir_path( __FILE__ ) . 'includes/classes/class-dynamic-css.php');	
		require_once( plugin_dir_path( __FILE__ ) . 'includes/classes/class-post-type-poll.php');	
		require_once( plugin_dir_path( __FILE__ ) . 'includes/classes/class-post-meta-poll.php');	
		require_once( plugin_dir_path( __FILE__ ) . 'includes/classes/class-menu.php');	
		require_once( plugin_dir_path( __FILE__ ) . 'includes/classes/class-poll-column.php');	
		require_once( plugin_dir_path( __FILE__ ) . 'includes/classes/class-update-db.php');	
	}
	
	public function wpp_define_constants(){
		
		define('WPP_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
		define('WPP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		define('WPP_TEXT_DOMAIN', 'wp-poll' );
	}
	
	public function wpp_front_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_style( 'dashicons' );
		
		wp_enqueue_script('wpp_js', plugins_url( 'assets/front/js/scripts.js' , __FILE__ ) , array( 'jquery' ));
		wp_localize_script( 'wpp_js', 'wpp_ajax', array( 'wpp_ajaxurl' => admin_url( 'admin-ajax.php')));

		wp_enqueue_style('wpp_style', WPP_PLUGIN_URL.'assets/front/css/style.css');

		wp_enqueue_style('font-awesome', WPP_PLUGIN_URL.'assets/global/css/font-awesome.css');
	}

	public function wpp_admin_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_style( 'jquery-ui' ); 
		wp_enqueue_script('jquery-ui-sortable');
		
		
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-datepicker-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');

		wp_enqueue_script('wpp_admin_js', plugins_url( 'assets/admin/js/scripts.js' , __FILE__ ) , array( 'jquery' ));
		// wp_localize_script('wpp_admin_js', 'wpp_ajax', array( 'wpp_ajaxurl' => admin_url( 'admin-ajax.php')));
		
		wp_enqueue_style('font-awesome', WPP_PLUGIN_URL.'assets/global/css/font-awesome.css');
		wp_enqueue_style('wpp_admin_style', WPP_PLUGIN_URL.'assets/admin/css/style.css');

		// BackAdmin
		wp_enqueue_style('BackAdmin_Style', WPP_PLUGIN_URL.'assets/BackAdmin/BackAdmin.css');		
		wp_enqueue_script('BackAdmin_JS', plugins_url( 'assets/BackAdmin/BackAdmin.js' , __FILE__ ) , array( 'jquery' ));
		
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'qa_color_picker', plugins_url('assets/admin/js/color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	
	
		wp_enqueue_script('jquery.canvasjs.min', plugins_url( '/assets/admin/js/jquery.canvasjs.min.js' , __FILE__ ) , array( 'jquery' ));							
	}
	
	
} new WPPollManager();