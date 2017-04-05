

<?php

/*
* @Author 		Jaed Mosharraf
* Copyright: 	2015 Jaed Mosharraf
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_wpp_functions{
	

	public function wpp_themes($themes = array())
	{
		$themes = array(
					'flat'=>'Flat',		
				);
		foreach(apply_filters( 'wpp_themes', $themes ) as $theme_key=> $theme_name)
			$theme_list[$theme_key] = $theme_name;
		return $theme_list;
	}
	
	public function wpp_themes_dir($themes_dir = array())
	{
		$main_dir = WPP_PLUGIN_DIR.'themes/show-poll/';
		$themes_dir = array(
						'flat'=>$main_dir.'flat',
					);
			
		foreach(apply_filters( 'wpp_themes_dir', $themes_dir ) as $theme_key=> $theme_dir)
			$theme_list_dir[$theme_key] = $theme_dir;
		return $theme_list_dir;
	}

	public function wpp_themes_url($themes_url = array())
	{
		$main_url = WPP_PLUGIN_URL.'themes/show-poll/';
		$themes_url = array(
						'flat'=>$main_url.'flat',
					);
			
		foreach(apply_filters( 'wpp_themes_url', $themes_url ) as $theme_key=> $theme_url)
			$theme_list_url[$theme_key] = $theme_url;
		return $theme_list_url;
	}
	
	public function wpp_single_themes_dir($themes_dir = array())
	{
		$main_dir = WPP_PLUGIN_DIR.'themes/single-poll/';
		$themes_dir = array(
						'flat'=>$main_dir.'flat',
					);
			
		foreach(apply_filters( 'wpp_single_themes_dir', $themes_dir ) as $theme_key=> $theme_dir)
			$theme_list_dir[$theme_key] = $theme_dir;
		return $theme_list_dir;
	}

	public function wpp_single_themes_url($themes_url = array())
	{
		$main_url = WPP_PLUGIN_URL.'themes/single-poll/';
		$themes_url = array(
						'flat'=>$main_url.'flat',
					);
			
		foreach(apply_filters( 'wpp_single_themes_url', $themes_url ) as $theme_key=> $theme_url)
			$theme_list_url[$theme_key] = $theme_url;
		return $theme_list_url;
	}
	
	public function wpp_list_themes_dir($themes_dir = array())
	{
		$main_dir = WPP_PLUGIN_DIR.'themes/poll-list/';
		$themes_dir = array(
						'flat'=>$main_dir.'flat',
					);
			
		foreach(apply_filters( 'wpp_list_themes_dir', $themes_dir ) as $theme_key=> $theme_dir)
			$theme_list_dir[$theme_key] = $theme_dir;
		return $theme_list_dir;
	}

	public function wpp_list_themes_url($themes_url = array())
	{
		$main_url = WPP_PLUGIN_URL.'themes/poll-list/';
		$themes_url = array(
						'flat'=>$main_url.'flat',
					);
			
		foreach(apply_filters( 'wpp_list_themes_url', $themes_url ) as $theme_key=> $theme_url)
			$theme_list_url[$theme_key] = $theme_url;
		return $theme_list_url;
	}
	
	
	
	
	
//=====  Extra Functions  ====================================//
	
	public function wpp_get_submit_percent( $wpp_id, $wpp_option )
	{
		wp_reset_query();
		
		$poll_submit_percent = '';
		$wp_query_get_total_submit = new WP_Query(
			array (
				'post_type' => 'wp_polled_by',
				'post_status' => 'publish',
				'meta_query' => array(
					array(
						'key' => 'wp_polled_post_id',
						'value' => $wpp_id,
							
						),	
					array(
						'key' => 'wp_polled_option',
						'value' => $wpp_option,
							
						),	
					
					),
			) );
		
		$poll_submit = $wp_query_get_total_submit->found_posts;
		wp_reset_query();
		
		$poll_total_submit = $this->wpp_get_total_submit( $wpp_id );
		if ( empty($poll_total_submit) )
			$poll_submit_percent = 0;
		else 
			$poll_submit_percent = ( $poll_submit * 100 ) / $poll_total_submit; 
		
		return (int)$poll_submit_percent;
	}
	
	public function wpp_get_total_submit( $wpp_id )
	{
		wp_reset_query();
		
		$total_poll_submitted = '';
		$wp_query_get_total_submit = new WP_Query(
			array (
				'post_type' => 'wp_polled_by',
				'post_status' => 'publish',
				'meta_query' => array(
					array(
						'key' => 'wp_polled_post_id',
						'value' => $wpp_id,
							
						),					
					),
			) );
		
		$total_poll_submitted = $wp_query_get_total_submit->found_posts;
		wp_reset_query();
		return $total_poll_submitted;
	}
	
	public function fn_check_already_ld( $wp_polled_post_id, $wp_polled_by )
	{
		$wp_query = new WP_Query(
			array (
			'post_type' => 'wp_polled_by',
			'post_status' => 'publish',					
			'meta_query' => array(
				array(
					'key' => 'wp_polled_post_id',
					'value' => $wp_polled_post_id,
				),
				array(
					'key' => 'wp_polled_by',
					'value' => $wp_polled_by,
				),
			) ));
				
		if ( $wp_query->found_posts > 0 ) return 1;
		else return 0;
	}
	
	public function getUserIP()
	{
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];

		if(filter_var($client, FILTER_VALIDATE_IP)) $ip = $client;
		elseif(filter_var($forward, FILTER_VALIDATE_IP)) $ip = $forward;
		else $ip = $remote;
		return $ip;
	}
	
	
		
} new class_wpp_functions();