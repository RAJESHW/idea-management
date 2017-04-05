<?php

/*
* @Author 		Jaed Mosharraf
* Copyright: 	2015 Jaed Mosharraf
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


	if ( ! function_exists( 'wpp_action_single_poll_main_function' ) ) {
		function wpp_action_single_poll_main_function() {
			
			do_action('wpp_action_single_poll_notice');
			do_action('wpp_action_single_poll_title');
			do_action('wpp_action_single_poll_message');
			do_action('wpp_action_single_poll_options');
			do_action('wpp_action_single_poll_results');
			do_action('wpp_action_single_poll_buttons');
			
		}
	}
	add_action( 'wpp_action_single_poll_main', 'wpp_action_single_poll_main_function', 10 );
	
	
	
	// Nested Actions 
	
	if ( ! function_exists( 'wpp_action_single_poll_title_function' ) ) {
		function wpp_action_single_poll_title_function($poll_id) {
			include( WPP_PLUGIN_DIR. 'templates/single-poll/title.php');			
		}
	}
	add_action( 'wpp_action_single_poll_title', 'wpp_action_single_poll_title_function', 10, 1 );
	
	
	if ( ! function_exists( 'wpp_action_single_poll_options_function' ) ) {
		function wpp_action_single_poll_options_function($poll_id) {
			include( WPP_PLUGIN_DIR. 'templates/single-poll/options.php');			
		}
	}
	add_action( 'wpp_action_single_poll_options', 'wpp_action_single_poll_options_function', 10, 1 );
	
	if ( ! function_exists( 'wpp_action_single_poll_notice_function' ) ) {
		function wpp_action_single_poll_notice_function($poll_id) {
			include( WPP_PLUGIN_DIR. 'templates/single-poll/notice.php');			
		}
	}
	add_action( 'wpp_action_single_poll_notice', 'wpp_action_single_poll_notice_function', 10, 1 );
	
	if ( ! function_exists( 'wpp_action_single_poll_message_function' ) ) {
		function wpp_action_single_poll_message_function($poll_id) {
			include( WPP_PLUGIN_DIR. 'templates/single-poll/message.php');			
		}
	}
	add_action( 'wpp_action_single_poll_message', 'wpp_action_single_poll_message_function', 10, 1 );
	
	if ( ! function_exists( 'wpp_action_single_poll_buttons_function' ) ) {
		function wpp_action_single_poll_buttons_function($poll_id) {
			include( WPP_PLUGIN_DIR. 'templates/single-poll/buttons.php');			
		}
	}
	add_action( 'wpp_action_single_poll_buttons', 'wpp_action_single_poll_buttons_function', 10, 1 );
	
	if ( ! function_exists( 'wpp_action_single_poll_results_function' ) ) {
		function wpp_action_single_poll_results_function($poll_id) {
			include( WPP_PLUGIN_DIR. 'templates/single-poll/results.php');			
		}
	}
	add_action( 'wpp_action_single_poll_results', 'wpp_action_single_poll_results_function', 10, 1 );
	
	
	
	
	