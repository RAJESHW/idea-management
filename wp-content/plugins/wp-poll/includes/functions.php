<?php
/*
* @Author 		Jaed Mosharraf
* Copyright: 	2015 Jaed Mosharraf
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

	function wpp_ajax_add_new_option() {
		
		$response 		= array();
		$poll_id 		= (int)sanitize_text_field($_POST['poll_id']);
		$option_val 	= sanitize_text_field($_POST['option_val']);
		
		if( empty( $poll_id ) || empty( $option_val ) ) die();
		
		$poll_meta_options 	= get_post_meta( $poll_id, 'poll_meta_options', true );
		if( empty( $poll_meta_options ) ) $poll_meta_options = array();
		
		$poll_meta_options[ time() ] = $option_val;
		
		
		update_post_meta( $poll_id, 'poll_meta_options', $poll_meta_options );
		
		echo 'ok';
		die();
	}
	add_action('wp_ajax_wpp_ajax_add_new_option', 'wpp_ajax_add_new_option');
	add_action('wp_ajax_nopriv_wpp_ajax_add_new_option', 'wpp_ajax_add_new_option');	
	
	
	
	function wpp_ajax_submit_poll() {
		
		$response 		= array();
		$poll_id 		= (int)sanitize_text_field($_POST['poll_id']);
		$checked_opts	= $_POST['checked'];
		
		$polled_data 	= array();
		$polled_data	= get_post_meta( $poll_id, 'polled_data', true );
		$poller 		= get_poller();
		
		if( array_key_exists( $poller, $polled_data ) ) {
			
			$response['status'] = 0;
			$response['notice'] = '<i class="fa fa-exclamation-triangle"></i> You have reached the Maximum Polling quota !';
		}
		else {
			
			foreach( $checked_opts as $option ) {
				$polled_data[$poller][] = $option;
			}
			update_post_meta( $poll_id, 'polled_data', $polled_data );
			
			$response['status'] = 1;
			$response['notice'] = '<i class="fa fa-check"></i> Successfully Polled on this.';
		}

		echo json_encode($response);
		die();
	}
	add_action('wp_ajax_wpp_ajax_submit_poll', 'wpp_ajax_submit_poll');
	add_action('wp_ajax_nopriv_wpp_ajax_submit_poll', 'wpp_ajax_submit_poll');	
	
	
	
	function show_notice( $type = 1 ){
		
		if( $type == 1 ) $notice_type = 'wpp_success';
		if( $type == 0 ) $notice_type = 'wpp_error';
		
		return "<div class='wpp_notice $notice_type'>Warning: You have already Polled !</div>";
	}
	
	function get_poller(){
		
		$user = wp_get_current_user();
		if( $user->ID != 0 ) return $user->ID;
		
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}


		return $ip;
	}

	function qa_single_poll_template($single_template) {
		 global $post;
		 if ($post->post_type == 'poll') {
			  $single_template = WPP_PLUGIN_DIR . 'templates/single-poll/single-poll.php';
		 }
		 return $single_template;
	}
	add_filter( 'single_template', 'qa_single_poll_template' );
	
	
	// function qa_single_poll_template($content) {
		 // global $post;
		 
		 // if ($post->post_type == 'poll') {
			 
			// ob_start();		
			// include WPP_PLUGIN_DIR . 'templates/single-poll/single-poll.php';
			// return ob_get_clean();		
		 // }
		 // return $content;
	// }
	// add_filter( 'the_content', 'qa_single_poll_template' );
	
	
	function wpp_dark_color($rgb, $darker=2) {

		$hash = (strpos($rgb, '#') !== false) ? '#' : '';
		$rgb = (strlen($rgb) == 7) ? str_replace('#', '', $rgb) : ((strlen($rgb) == 6) ? $rgb : false);
		if(strlen($rgb) != 6) return $hash.'000000';
		$darker = ($darker > 1) ? $darker : 1;

		list($R16,$G16,$B16) = str_split($rgb,2);

		$R = sprintf("%02X", floor(hexdec($R16)/$darker));
		$G = sprintf("%02X", floor(hexdec($G16)/$darker));
		$B = sprintf("%02X", floor(hexdec($B16)/$darker));

		return $hash.$R.$G.$B;
	}