<?php
/*
* Plugin Name: Simple Google reCAPTCHA
* Description: Simply protect your WordPress against spam comments and brute-force attacks, thanks to Google reCAPTCHA!
* Version: 2.1
* Author: Michal Nov&aacute;k
* Author URI: https://www.novami.cz
* License: GPL3
* Text Domain: simple-google-recaptcha
* Domain Path: /languages
*/

function sgr_add_plugin_action_links($links) {
	return array_merge(array("settings" => "<a href=\"options-general.php?page=sgr-options\">".__("Settings", "simple-google-recaptcha")."</a>"), $links);
}
add_filter("plugin_action_links_".plugin_basename(__FILE__), "sgr_add_plugin_action_links");

function sgr_options_page() {
	echo "<div class=\"wrap\">
	<h1>".__("reCAPTCHA Options", "simple-google-recaptcha")."</h1>
	<form method=\"post\" action=\"options.php\">";
	settings_fields("sgr_header_section");
	do_settings_sections("sgr-options");
	submit_button();
	echo "</form>
	</div>";
}

function sgr_menu() {
	add_submenu_page("options-general.php", "reCAPTCHA", "reCAPTCHA", "manage_options", "sgr-options", "sgr_options_page");
}
add_action("admin_menu", "sgr_menu");

function sgr_display_content() {
	echo "<p>".__("You have to <a href=\"https://www.google.com/recaptcha/admin\" rel=\"external\">register your domain</a> first, get required keys from Google and save them bellow.", "simple-google-recaptcha")."</p>";
}

function sgr_display_site_key_element() {
	echo "<input type=\"text\" name=\"sgr_site_key\" class=\"regular-text\" id=\"sgr_site_key\" value=\"".get_option("sgr_site_key")."\" />";
}

function sgr_display_secret_key_element() {
	echo "<input type=\"text\" name=\"sgr_secret_key\" class=\"regular-text\" id=\"sgr_secret_key\" value=\"".get_option("sgr_secret_key")."\" />";
}

function sgr_display_comments_disable() {
	echo "<input type=\"checkbox\" name=\"sgr_comments_disable\" id=\"sgr_comments_disable\" value=\"1\" ".checked(1, get_option("sgr_comments_disable"), false)." />";
}

function sgr_display_forms_disable() {
	echo "<input type=\"checkbox\" name=\"sgr_forms_disable\" id=\"sgr_forms_disable\" value=\"1\" ".checked(1, get_option("sgr_forms_disable"), false)." />";
}

function sgr_display_options() {
	add_settings_section("sgr_header_section", __("What first?", "simple-google-recaptcha"), "sgr_display_content", "sgr-options");
	
	add_settings_field("sgr_site_key", __("Site Key", "simple-google-recaptcha"), "sgr_display_site_key_element", "sgr-options", "sgr_header_section");
	add_settings_field("sgr_secret_key", __("Secret Key", "simple-google-recaptcha"), "sgr_display_secret_key_element", "sgr-options", "sgr_header_section");
	add_settings_field("sgr_comments_disable", __("Disable reCAPTCHA for comments", "simple-google-recaptcha"), "sgr_display_comments_disable", "sgr-options", "sgr_header_section");
	add_settings_field("sgr_forms_disable", __("Disable reCAPTCHA for basic forms", "simple-google-recaptcha"), "sgr_display_forms_disable", "sgr-options", "sgr_header_section");

	register_setting("sgr_header_section", "sgr_site_key");
	register_setting("sgr_header_section", "sgr_secret_key");
	register_setting("sgr_header_section", "sgr_comments_disable");
	register_setting("sgr_header_section", "sgr_forms_disable");
}
add_action("admin_init", "sgr_display_options");

function load_language_sgr() {
	load_plugin_textdomain("sgr", false, dirname(plugin_basename(__FILE__))."/languages/");
}
add_action("plugins_loaded", "load_language_sgr");

function frontend_sgr_script() {
	if ((did_action("login_init") && get_option("sgr_forms_disable") != 1) || (!is_user_logged_in() && is_single() && comments_open() && get_option("sgr_comments_disable") != 1)) {
		wp_register_script("sgr_recaptcha", "https://www.google.com/recaptcha/api.js?hl=".get_locale());
		wp_enqueue_script("sgr_recaptcha");
		wp_register_script("sgr_recaptcha_check", plugin_dir_url(__FILE__)."check.js");
		wp_enqueue_script("sgr_recaptcha_check");
		wp_localize_script("sgr_recaptcha_check", "sgr_recaptcha_trans", array("title" => __("Are you a Robot?", "simple-google-recaptcha")));
		wp_enqueue_style("style", plugin_dir_url(__FILE__)."style.css");
	}
}

function sgr_display() {
	echo "<div class=\"g-recaptcha\" data-sitekey=\"".get_option("sgr_site_key")."\" data-callback=\"enableBtn\"></div>";
}

function sgr_verify($input) {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_POST["g-recaptcha-response"])) {
			$recaptcha_response = sanitize_text_field($_POST["g-recaptcha-response"]);
			$recaptcha_secret = get_option("sgr_secret_key");
			$response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=".$recaptcha_secret."&response=".$recaptcha_response);
			$response = json_decode($response["body"], true);
			
			if ($response["success"] == true) {
				return $input;
			} else {
				wp_die("<p><strong>".__("ERROR:", "simple-google-recaptcha")."</strong> ".__("Google reCAPTCHA verification failed.", "simple-google-recaptcha")."</p>\n\n<p><a href=".wp_get_referer().">&laquo; ".__("Back", "simple-google-recaptcha")."</a>");
				return null;
			}
		} else {
			wp_die("<p><strong>".__("ERROR:", "simple-google-recaptcha")."</strong> ".__("Google reCAPTCHA verification failed.", "simple-google-recaptcha")." ".__("Do you have JavaScript enabled?", "simple-google-recaptcha")."</p>\n\n<p><a href=".wp_get_referer().">&laquo; ".__("Back", "simple-google-recaptcha")."</a>");
			return null;
		}
	}
}

function sgr_check() {
	if (get_option("sgr_site_key") != "" && get_option("sgr_secret_key") != "") {
		
		add_action("login_enqueue_scripts", "frontend_sgr_script");
		add_action("wp_enqueue_scripts", "frontend_sgr_script");
		
		if (get_option("sgr_comments_disable") != 1 && !is_user_logged_in()) {
			add_action("comment_form_after_fields", "sgr_display");
			add_action("preprocess_comment", "sgr_verify");
		}
		
		if (get_option("sgr_forms_disable") != 1) {
			add_action("login_form", "sgr_display");
			add_action("wp_authenticate_user", "sgr_verify");

			add_action("register_form", "sgr_display");
			add_action("registration_errors", "sgr_verify");		
	
			add_action("lostpassword_form", "sgr_display");
			add_action("lostpassword_post", "sgr_verify");
	
			add_action("resetpass_form", "sgr_display");
			add_action("resetpass_post", "sgr_verify");
		}
	}
}
add_action("init", "sgr_check");