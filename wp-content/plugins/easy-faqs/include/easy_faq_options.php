<?php
/*
This file is part of Easy FAQs.

Easy FAQs is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Easy FAQs is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with The Easy FAQs.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once("easy_faqs_config.php");
require_once('lib/GP_Sajak/gp_sajak.class.php');
require_once("lib/easy_faqs_importer.php");
require_once("lib/easy_faqs_exporter.php");

class easyFAQOptions
{
	var $textdomain = 'easy-faqs';
	var $shed = false;
	var $root = false;

	function __construct($root = false){
		//may be running in non WP mode (for example from a notification)
		if(function_exists('add_action')){
			//add a menu item
			add_action('admin_menu', array($this, 'add_admin_menu_item'));		
		}
		
		// create the BikeShed object now, so that BikeShed can add its hooks
		$this->shed = new Easy_FAQs_GoldPlugins_BikeShed();
		
		if($root) {
			$this->root = $root;
		}
	}
	
	function add_admin_menu_item(){
		$title = "Easy FAQs Settings";
		$page_title = "Easy FAQs Settings";
		$top_level_slug = "easy-faqs-settings";
		
		//create new top-level menu
		add_menu_page($page_title, $title, 'administrator', $top_level_slug , array($this, 'basic_settings_page'));
		add_submenu_page($top_level_slug , 'Basic Options', 'Basic Options', 'administrator', $top_level_slug, array($this, 'basic_settings_page'));
		add_submenu_page($top_level_slug , 'Themes', 'Themes', 'administrator', 'easy-faqs-themes', array($this, 'themes_page'));
		if (isValidFAQKey()) {
			add_submenu_page($top_level_slug , 'Question Form Options', 'Question Form', 'administrator', 'easy-faqs-submission-form-options', array($this, 'submission_form_options'));
			add_submenu_page($top_level_slug , 'Import & Export', 'Import & Export', 'administrator', 'easy-faqs-import-export', array($this, 'import_export_page'));
			add_submenu_page($top_level_slug , 'Recent Searches', 'Recent Searches', 'administrator', 'easy-faqs-recent-searches', array($this, 'recent_searches_page'));
		} else {
			add_submenu_page($top_level_slug , 'Recent Searches (Pro)', 'Recent Searches (Pro)', 'administrator', 'easy-faqs-recent-searches', array($this, 'recent_searches_page'));
		}
		add_submenu_page($top_level_slug , 'Shortcode Generator', 'Shortcode Generator', 'administrator', 'easy-faqs-shortcode-generator', array($this, 'shortcode_generator_page'));
		add_submenu_page($top_level_slug , 'Help & Instructions', 'Help & Instructions', 'administrator', 'easy-faqs-help', array($this, 'render_help_page'));

		//call register settings function
		add_action( 'admin_init', array($this, 'admin_init') );	
	}
	
	//admin init functions
	function admin_init( $hook ) 
	{
		//register plugin settings
		$this->register_settings();
	}
	
	//function to produce tabs on admin screen
	function admin_tabs( $current = 'homepage' ) {
	
		if (isValidFAQKey()) {
			$tabs = array( 'easy-faqs-settings' => __('Basic Options', $this->textdomain),
						   'easy-faqs-themes' => __('Themes', $this->textdomain),
						   'easy-faqs-submission-form-options' => __('Question Form', $this->textdomain)
						 );
		} else {
			$tabs = array( 'easy-faqs-settings' => __('Basic Options', $this->textdomain),
						   'easy-faqs-themes' => __('Themes', $this->textdomain)
						 );
		}
		
		echo '<div id="icon-themes" class="icon32"><br></div>';
		echo '<h2 class="nav-tab-wrapper">';
			foreach( $tabs as $tab => $name ){
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab$class' href='?page=$tab'>$name</a>";
			}
		echo '</h2>';
	}

	function register_settings(){
		//register our settings
		register_setting( 'easy-faqs-settings-group', 'faqs_link' );
		register_setting( 'easy-faqs-settings-group', 'faqs_read_more_text' );
		register_setting( 'easy-faqs-settings-group', 'faqs_image' );
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_custom_css' );	
		
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_question_font_size' );	
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_question_font_color' );	
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_question_font_style' );	
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_question_font_family' );	
		
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_answer_font_size' );	
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_answer_font_color' );	
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_answer_font_style' );	
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_answer_font_family' );	
		
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_read_more_link_font_size' );	
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_read_more_link_font_color' );	
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_read_more_link_font_style' );	
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_read_more_link_font_family' );			
		
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_registered_name' );
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_registered_url' );
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_registered_key' );
		
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_excerpt_text', array($this, 'easy_faqs_excerpt_text') );
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_excerpt_length', array($this, 'easy_faqs_excerpt_length') );
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_link_excerpt_to_full' );
		register_setting( 'easy-faqs-settings-group', 'easy_faqs_use_custom_excerpt' );
		
		// theme options
		register_setting( 'easy-faqs-theme-settings-group', 'faqs_style' );
		register_setting( 'easy-faqs-theme-settings-group', 'easy_faqs_preview_window_background' );

		//submission form options
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_use_captcha' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_submit_notification_address' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_question_label' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_question_description' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_name_label' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_name_description' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_submit_label' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_submit_success_redirect_url' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_submit_success_message' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_submit_notification_address' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_submit_notification_include_question' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_submit_button_label' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_name_error_message' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_question_error_message' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_captcha_error_message' );
		register_setting( 'easy-faqs-submission-form-options-group', 'easy_faqs_general_error_message' );
	}

	function easy_faqs_excerpt_text($val){
		//if nothing set, default to Continue Reading
		if(strlen($val)<1){
			return "Continue Reading";
		} else {
			return $val;
		}
	}
	
	function easy_faqs_excerpt_length($val){
		//if nothing set, default to 55
		if(strlen($val)<1){
			return 55;
		} else {
			return intval($val);
		}
	}
	
	function settings_page_top( $show_tabs = true ){
		$message = "Easy FAQs Settings Updated.";
		
		global $pagenow;
	?>	
		<?php if(isValidFAQKey()): ?>
		<div class="wrap easy_faqs_wrapper gold_plugins_settings">
		<?php else: ?>
		<div class="wrap easy_faqs_wrapper gold_plugins_settings not-pro">
		<?php endif; ?>
		<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') : ?>
		<div class="gp_updated"><p><?php echo $message; ?></p></div>
		<?php endif;
		
		if( $show_tabs ){
			$this->get_and_output_current_tab($pagenow);	
		}
	}
	
	function settings_page_bottom(){
		if(!isValidFAQKey()): ?>		
			<?php $this->output_sidebar_coupon_form(); ?>
		<?php endif; ?>
		</div>
		<?php
	}
	
	function output_sidebar_coupon_form()
	{
		global $pagenow;
		$current_user = wp_get_current_user();
?>
	<script type="text/javascript">
	jQuery(function () {
		if (typeof(gold_plugins_init_coupon_box) == 'function') {
			gold_plugins_init_coupon_box();
		}
	});
	</script>
		<?php if(!isValidFAQKey()): ?>
			<!-- Begin MailChimp Signup Form -->
			<style type="text/css">
			</style>
			<div id="signup_wrapper">
				<div class="topper purple">
					<h3><span>Upgrade To</span> Easy FAQs Pro!</h3>
					<p class="pitch" style="font-size: 14px">When you upgrade, you'll instantly unlock Accordion Style FAQs, the Submit A Question Form, 100+ professionally designed themes, Import&nbsp;&amp;&nbsp;Export, FAQs Search Features, personalized support, and more!</p>
					<a class="upgrade_link" href="https://goldplugins.com/our-plugins/easy-faqs-details/?utm_source=cpn_box&utm_campaign=upgrade&utm_banner=learn_more" title="Learn More">Learn More About Easy FAQs Pro &raquo;</a>
				</div>
				<div id="mc_embed_signup">
					<div class="save_now">
						<h3>Save 10% Now!</h3>
						<p class="pitch">Subscribe to our newsletter now, and we’ll send you a coupon for 10% off your upgrade to the Pro version.</p>
					</div>
					<form action="" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						<div class="fields_wrapper">
							<label for="mce-NAME">Your Name:</label>
							<input type="text" value="<?php echo (!empty($current_user->display_name) ?  $current_user->display_name : ''); ?>" name="NAME" class="name" id="mce-NAME" placeholder="Your Name">
							<label for="mce-EMAIL">Your Email:</label>
							<input type="email" value="<?php echo (!empty($current_user->user_email) ?  $current_user->user_email : ''); ?>" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
							<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
							<div style="position: absolute; left: -5000px;"><input type="text" name="b_403e206455845b3b4bd0c08dc_6ad78db648" tabindex="-1" value=""></div>
						</div>
						<div class="clear"><input type="submit" value="Send Me The Coupon Now" name="subscribe" id="mc-embedded-subscribe" class="smallBlueButton"></div>
						<p class="secure"><img src="<?php echo plugins_url( 'img/lock.png', __FILE__ ); ?>" alt="Lock" width="16px" height="16px" />We respect your privacy.</p>
						
						<input type="hidden" name="PRODUCT" value="Easy FAQs Pro" />
						<input type="hidden" id="mc-upgrade-plugin-name" value="Easy FAQs Pro" />
						<input type="hidden" id="mc-upgrade-link-per" value="https://goldplugins.com/purchase/easy-faqs/single?promo=newsub10" />
						<input type="hidden" id="mc-upgrade-link-biz" value="https://goldplugins.com/purchase/easy-faqs/business?promo=newsub10" />
						<input type="hidden" id="mc-upgrade-link-dev" value="https://goldplugins.com/purchase/easy-faqs/developer?promo=newsub10" />
					</form>
					<div class="gp_logo">
						<a href="https://goldplugins.com/?utm_source=easy_faqs_coupon_box&utm_campaign=gp_logo" target="_blank">
							<img src="<?php echo plugins_url( 'img/logo.png', __FILE__ ); ?>">
						</a>
					</div>
				</div>
				<p class="u_to_p"><a href="https://goldplugins.com/our-plugins/easy-faqs-details/upgrade-to-easy-faqs-pro/">Upgrade to Easy FAQs Pro now</a> to remove banners like this one.</p>
			</div>
			<!--End mc_embed_signup-->
		<?php endif;
	}
	
	function get_and_output_current_tab($pagenow){
		$tab = $_GET['page'];
		
		$this->admin_tabs($tab); 
				
		return $tab;
	}
	
	//loads all options
	//builds array of options matching our prefix
	//returns our array
	private function load_all_options(){
		$my_options = array();
		$all_options = wp_load_alloptions();
		
		$patterns = array(
			'faqs_link',
			'faqs_read_more_text',
			'faqs_image',
			'faqs_style',
			'easy_faqs_submit_notification_address',
			'easy_faqs_(.*)',
		);
		
		foreach ( $all_options as $name => $value ) {
			if ( $this->preg_match_array( $name, $patterns ) ) {
				$my_options[ $name ] = $value;
			}
		}
		
		return $my_options;
	}
	
	function preg_match_array( $candidate, $patterns )
	{
		foreach ($patterns as $pattern) {
			$p = sprintf('#%s#i', $pattern);
			if ( preg_match($p, $candidate, $matches) == 1 ) {
				return true;
			}
		}
		return false;
	}
	
	function basic_settings_page()
	{
		$this->settings_page_top();		
		$this->basic_settings_page_tabs();		
		$this->settings_page_bottom();
	}
	
	function basic_settings_page_tabs(){
		//add upgrade button if free version
		$extra_buttons = array();
		if(!isValidFAQKey()){
			$extra_buttons = array(
				array(
					'class' => 'btn-purple',
					'label' => 'Upgrade To Pro',
					'url' => 'https://goldplugins.com/our-plugins/easy-faqs-details/upgrade-to-easy-faqs-pro/'
				)
			);
		}
		
		$tabs = new GP_Sajak( array(
			'header_label' => 'Basic Options',
			'settings_field_key' => 'easy-faqs-settings-group', // can be an array	
			'extra_buttons_header' => $extra_buttons,
			'extra_buttons_footer' => $extra_buttons
		) );
		
		$tabs->add_tab(
			'display_options', // section id, used in url fragment
			'Display Options', // section label
			array($this, 'output_display_options'), // display callback
			array(
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->add_tab(
			'excerpt_options', // section id, used in url fragment
			'Excerpt Options', // section label
			array($this, 'output_excerpt_options'), // display callback
			array(
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->add_tab(
			'font_options', // section id, used in url fragment
			'Font Options', // section label
			array($this, 'output_font_options'), // display callback
			array(
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->add_tab(
			'pro_registration', // section id, used in url fragment
			'Pro Registration', // section label
			array($this, 'output_pro_registration_fields'), // display callback
			array(
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->display();
	}
	
	function output_pro_registration_fields(){
		include('registration_options.php');
	}
	
	function output_excerpt_options(){
		?>
		<h3>FAQ Excerpt Options</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="easy_faqs_excerpt_length">Excerpt Length</label></th>
					<td><input type="text" name="easy_faqs_excerpt_length" id="easy_faqs_excerpt_length" value="<?php echo get_option('easy_faqs_excerpt_length', 55); ?>"  style="" />
					<p class="description">This is the number of words to use in a shortened answer.  The default value is 55 words.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="easy_faqs_excerpt_text">Excerpt Text</label></th>
					<td><input type="text" name="easy_faqs_excerpt_text" id="easy_faqs_excerpt_text" value="<?php echo get_option('easy_faqs_excerpt_text', 'Continue Reading'); ?>"  style="" />
					<p class="description">The text used after the Excerpt.  If you are linking your Excerpts to Full Answers, this text is used in the Link.  This defaults to "Continue Reading".</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="easy_faqs_link_excerpt_to_full">Link Excerpts to Full Answer</label></th>
					<td><input type="checkbox" name="easy_faqs_link_excerpt_to_full" id="easy_faqs_link_excerpt_to_full" value="1" <?php if(get_option('easy_faqs_link_excerpt_to_full', true)){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description">If checked, shortened answers will end with a link that goes to the full length Answer.</p>
					</td>
				</tr>
			</table>
		<?php
	}
	
	function output_display_options(){
		?>
		<h3>Display Options</h3>
			<table class="form-table">
		<?php
		// Custom CSS (textarea)
		$this->shed->textarea( array('name' => 'easy_faqs_custom_css', 'label' =>'Custom CSS', 'value' => get_option('easy_faqs_custom_css'), 'description' => 'Input any Custom CSS you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though.') );
		
		// FAQS - View All Link (text)
		$this->shed->text( array('name' => 'faqs_link', 'label' =>'FAQs View All Link', 'value' => get_option('faqs_link'), 'description' => 'Enter the URL of your FAQs page here. If you do, a \'View All\' link will displayed after each of your FAQs which directs visitors to this URL. ') );

		// FAQS - View All Text (text)
		$this->shed->text( array('name' => 'faqs_read_more_text', 'label' =>'FAQs View All Text', 'value' => get_option('faqs_read_more_text'), 'description' => 'This is the Text of the \'View All\' Link.  Default text is "View All."  This is only displayed if a URL is set in the above field, FAQs View All Link.') );
		
		// FAQ show featured image (checkbox)
		$checked = (get_option('faqs_image') == '1');
		$this->shed->checkbox( array('name' => 'faqs_image', 'label' =>'Show FAQ Images', 'value' => 1, 'checked' => $checked, 'description' => 'If checked, the Featured Image for each FAQ will be shown before the FAQ\'s answer.', 'inline_label' => 'Show FAQ Images') );
		?>
		</table>
		<?php
	}
	
	function output_font_options(){
		?>
		<h3>Font Options</h3>
			<table class="form-table">
		<?php
		$faqs_themes_page_url = admin_url('admin.php?page=easy-faqs-themes');
		$themes_moved_msg = 'Themes now have their own tab!';
		$themes_moved_link_text = 'Click here to check them out.';
		$message_html = sprintf('<em>%s <a href="%s">%s</a></em>', $themes_moved_msg, $faqs_themes_page_url, $themes_moved_link_text);
		$this->shed->message( array('label' =>'FAQs Theme', 'message' => $message_html ) );

		// Question Font (typography)
		$values = array(
			'font_size' => get_option('easy_faqs_question_font_size'),
			'font_family' => get_option('easy_faqs_question_font_family'),
			'font_style' => get_option('easy_faqs_question_font_style'),
			'font_color' => get_option('easy_faqs_question_font_color'),
		);
		$this->shed->typography( array('name' => 'easy_faqs_question_*', 'label' =>'Question Font', 'description' => 'Choose a font size, family, style, and color.', 'google_fonts' => true, 'default_color' => '#878787', 'values' => $values) );

		// Answer Font (typography)
		$values = array(
			'font_size' => get_option('easy_faqs_answer_font_size'),
			'font_family' => get_option('easy_faqs_answer_font_family'),
			'font_style' => get_option('easy_faqs_answer_font_style'),
			'font_color' => get_option('easy_faqs_answer_font_color'),
		);
		$this->shed->typography( array('name' => 'easy_faqs_answer_*', 'label' =>'Answer Font', 'description' => 'Choose a font size, family, style, and color.', 'google_fonts' => true, 'default_color' => '#878787', 'values' => $values) );

		// View All Link Font (typography)
		$values = array(
			'font_size' => get_option('easy_faqs_read_more_link_font_size'),
			'font_family' => get_option('easy_faqs_read_more_link_font_family'),
			'font_style' => get_option('easy_faqs_read_more_link_font_style'),
			'font_color' => get_option('easy_faqs_read_more_link_font_color'),
		);
		$this->shed->typography( array('name' => 'easy_faqs_read_more_link_*', 'label' =>'View All Link Font', 'description' => 'Choose a font size, family, style, and color.', 'google_fonts' => true, 'default_color' => '#878787', 'values' => $values) );
		?>
		</table>
		<?php
	}
	
	function themes_page()
	{
		wp_enqueue_style( 'easy_faqs_style' );
		
		$this->settings_page_top(); 
		$this->themes_page_tabs();
		$this->settings_page_bottom();
	}
	
	function themes_page_tabs(){
		//add upgrade button if free version
		$extra_buttons = array();
		if(!isValidFAQKey()){
			$extra_buttons = array(
				array(
					'class' => 'btn-purple',
					'label' => 'Upgrade To Pro',
					'url' => 'https://goldplugins.com/our-plugins/easy-faqs-details/upgrade-to-easy-faqs-pro/'
				)
			);
		}
		
		$tabs = new GP_Sajak( array(
			'header_label' => 'Themes',
			'settings_field_key' => 'easy-faqs-theme-settings-group', // can be an array	
			'extra_buttons_header' => $extra_buttons,
			'extra_buttons_footer' => $extra_buttons
		) );
		
		$tabs->add_tab(
			'theme_selector', // section id, used in url fragment
			'Themes', // section label
			array($this, 'output_themes_selector'), // display callback
			array(
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->display();
	}
	
	function output_themes_selector(){
		?>					
			<h3>Themes</h3>			
			<p>Please select a theme to use with your FAQs. This theme will become  your default choice, but you can always specify a different theme for each widget if you like!</p>
			<table class="form-table easy-faqs-options-table">
				<?php
					// FAQs Theme (select)
					$themes = array(
						'default_style' => 'Default Theme',
						'no_style' => 'No Theme',
					);
					$current_theme = get_option('faqs_style');
					$themes = EasyFAQs_Config::all_themes(isValidFAQKey(), false);
					$desc = '';
					if (!isValidFAQKey())
					{
						$desc = '';						
					}
					$this->shed->grouped_select( array('name' => 'faqs_style', 'options' => $themes, 'label' =>'FAQs Theme', 'value' => $current_theme, 'description' => $desc) );

				?>
			</table>
			
			<div id="easy_faqs_theme_preview">			
				<h4 id="easy_faqs_theme_preview_title">Preview:</h4>
				<div id="easy_faqs_theme_preview_color_picker">
					<table class="form-table">
					<?php
						$cur_prev_bg = get_option('easy_faqs_preview_window_background', '#fff');
						$this->shed->color( array('name' => 'easy_faqs_preview_window_background', 'label' =>'Set Background Color:', 'value' => $cur_prev_bg, 'description' => '') );
					?>
					</table>
				</div>
				<div id="easy_faqs_theme_preview_browser"></div>
				<div id="easy_faqs_theme_preview_content">
					<div class="easy-faqs-wrapper easy-faqs-theme-office-red easy-faqs-theme-office easy-faqs-accordion">
						<div class="easy-faq" id="easy-faq-20141">
							<h3 class="easy-faq-title" style=""><span class="easy-faqs-title-before"></span><span class="easy-faqs-title-text">How do I upgrade to Easy FAQs Pro?</span><span class="easy-faqs-title-after"></span></h3>
							<div class="easy-faq-body" style="">
								<p>Its easy! Simply visit <a href="http://goldplugins.com/our-plugins/easy-faqs-details/upgrade-to-easy-faqs-pro/?utm_source=plugin_settings&utm_campaign=theme_preview_faqs_first_faq_in_the_list" target="_blank">our website</a> and purchase the license of your choice. Right away, you'll receive an API key that you can enter into the settings page here, instantly unlocking all of the Pro features.</p>
							</div>
						</div>
						<div class="easy-faq" id="easy-faq-20140">
							<h3 class="easy-faq-title" style=""><span class="easy-faqs-title-before"></span><span class="easy-faqs-title-text">What will I recevive when I upgrade?</span><span class="easy-faqs-title-after"></span></h3>
							<div class="easy-faq-body" style="">
								<p>When you upgrade, you'll instantly unlock:</p>
								<ul>
									<li>All 100+ Professionally Designed Themes</li>
									<li>Accordion Style FAQs</li>
									<li>The Submit A Question Form</li>
									<li>Import & Export functonality</li>
									<li>Personalized support</li>
									<li>And more! You'll receive free updates and new features for a year after your upgrade.</li>
								</ul>
							</div>
						</div>
						<div class="easy-faq" id="easy-faq-20142">
							<h3 class="easy-faq-title" style=""><span class="easy-faqs-title-before"></span><span class="easy-faqs-title-text">What will happen to my data and settings when I upgrade?</span><span class="easy-faqs-title-after"></span></h3>
							<div class="easy-faq-body" style="">
								<p>Nothing at all! All of your FAQs, settings, and other data will be preserved when you upgrade.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<?php if(!isValidFAQKey()): ?>			
			<div id="easy_faqs_themes_pro_warning">
				<h3>This Theme Requires Easy FAQs Pro</h3>
				<p>You can preview it here, but you must upgrade before you can use it on your website.</p>
				<p class="click_to_upgrade">
					<a class="button" target="_blank" href="http://goldplugins.com/our-plugins/easy-faqs-details/upgrade-to-easy-faqs-pro/?utm_source=plugin_settings&utm_campaign=themes_upgrade_box">Upgrade Now</a>
				</p>
			</div>
			<?php endif; ?>
		<?php
	}
	
	function submission_form_options() {
		$this->settings_page_top();
		$this->question_form_tabs();
		$this->settings_page_bottom();
	}
	
	function question_form_tabs(){
		//add upgrade button if free version
		$extra_buttons = array();
		if(!isValidFAQKey()){
			$extra_buttons = array(
				array(
					'class' => 'btn-purple',
					'label' => 'Upgrade To Pro',
					'url' => 'https://goldplugins.com/our-plugins/easy-faqs-details/upgrade-to-easy-faqs-pro/'
				)
			);
		}
		
		$tabs = new GP_Sajak( array(
			'header_label' => 'Question Form Settings',
			'settings_field_key' => 'easy-faqs-submission-form-options-group', // can be an array	
			'extra_buttons_header' => $extra_buttons,
			'extra_buttons_footer' => $extra_buttons
		) );
		
		$tabs->add_tab(
			'labels_and_descriptions', // section id, used in url fragment
			'Field Labels &amp; Descriptions', // section label
			array($this, 'output_labels_and_descriptions'), // display callback
			array(
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->add_tab(
			'submission_options', // section id, used in url fragment
			'Submission Options', // section label
			array($this, 'output_submission_options'), // display callback
			array(
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->add_tab(
			'spam_prevention', // section id, used in url fragment
			'Spam Prevention', // section label
			array($this, 'output_spam_prevention_options'), // display callback
			array(
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->add_tab(
			'error_messages', // section id, used in url fragment
			'Error Messages', // section label
			array($this, 'output_error_messages'), // display callback
			array(
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->display();
	}
	
	function output_labels_and_descriptions(){
		?>
		<h3>Field Labels &amp; Descriptions</h3>
			
		<p>Use the below options to control the look and feel of the question submission form.</p>
	
		<fieldset>
			<legend>Name Field</legend>			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="easy_faqs_name_label">Label</label></th>
					<td><input type="text" name="easy_faqs_name_label" id="easy_faqs_name_label" <?php if(!isValidFAQKey()): ?>disabled="disabled"<?php endif; ?> value="<?php echo get_option( 'easy_faqs_name_label', $this->root->get_str('FAQ_FORM_NAME') ); ?>" />
					<p class="description">This is the label of the first field in the form, which defaults to "Your Name".</p>
					</td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="easy_faqs_name_description">Description</label></th>
					<td><textarea name="easy_faqs_name_description" id="easy_faqs_name_description" <?php if(!isValidFAQKey()): ?>disabled="disabled"<?php endif; ?>><?php echo get_option('easy_faqs_name_description', $this->root->get_str('FAQ_FORM_NAME_DESCRIPTION') ); ?></textarea>
					<p class="description">This is the description below the first field in the form, which defaults to "Please enter your name".</p>
					</td>
				</tr>
			</table>
		</fieldset>
					
		<fieldset>
			<legend>Question Field</legend>			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="easy_faqs_question_label">Label</label></th>
					<td><input type="text" name="easy_faqs_question_label" id="easy_faqs_question_label" <?php if(!isValidFAQKey()): ?>disabled="disabled"<?php endif; ?> value="<?php echo get_option('easy_faqs_question_label', $this->root->get_str('FAQ_FORM_QUESTION') ); ?>" />
					<p class="description">This is the label of the second field in the form, which defaults to "Your Question".</p>
					</td>
				</tr>
			</table>
						
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="easy_faqs_question_description">Description</label></th>
					<td><textarea name="easy_faqs_question_description" id="easy_faqs_question_description" <?php if(!isValidFAQKey()): ?>disabled="disabled"<?php endif; ?>><?php echo get_option('easy_faqs_question_description', $this->root->get_str('FAQ_FORM_QUESTION_DESCRIPTION') ); ?></textarea>
					<p class="description">This is the description below the second field in the form, which defaults to "Please enter your Question".</p>
					</td>
				</tr>
			</table>
		</fieldset>		
					
		<fieldset>
			<legend>Submit Button</legend>	
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="easy_faqs_submit_button_label">Submit Button Label</label></th>
					<td><input type="text" name="easy_faqs_submit_button_label" id="easy_faqs_submit_button_label" <?php if(!isValidFAQKey()): ?>disabled="disabled"<?php endif; ?> value="<?php echo get_option('easy_faqs_submit_button_label', $this->root->get_str('FAQ_SUBMIT_QUESTION_BUTTON') ); ?>" />
					<p class="description">This is the label of the submit button at the bottom of the form.</p>
					</td>
				</tr>
			</table>
		</fieldset>
		<?php
	}
	
	function output_submission_options(){
		?>
		<h3>Submission Options</h3>
					
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="easy_faqs_submit_success_message">Submission Success Message</label></th>
				<td><textarea name="easy_faqs_submit_success_message" id="easy_faqs_submit_success_message" <?php if(!isValidFAQKey()): ?>disabled="disabled"<?php endif; ?>><?php echo get_option('easy_faqs_submit_success_message', $this->root->get_str('FAQ_SUBMIT_SUCCESS') ); ?></textarea>
				<p class="description">This is the text that appears after a successful submission.</p>
				</td>
			</tr>
		</table>
					
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="easy_faqs_submit_success_redirect_url">Submission Success Redirect URL</label></th>
				<td><input type="text" name="easy_faqs_submit_success_redirect_url" id="easy_faqs_submit_success_redirect_url" <?php if(!isValidFAQKey()): ?>disabled="disabled"<?php endif; ?> value="<?php echo get_option('easy_faqs_submit_success_redirect_url', ''); ?>"/>
				<p class="description">If you want the user to be taken to a specific URL on your site after asking their Question, enter it into this field.  If the field is empty, they will stay on the same page and see the Success Message, instead.</p>
				</td>
			</tr>
		</table>
		
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="easy_faqs_submit_notification_address">Submission Success Notification E-Mail Address</label></th>
				<td><input type="text" name="easy_faqs_submit_notification_address" id="easy_faqs_submit_notification_address" <?php if(!isValidFAQKey()): ?>disabled="disabled"<?php endif; ?> value="<?php echo get_option('easy_faqs_submit_notification_address'); ?>" />
				<p class="description">If set, we will attempt to send an e-mail notification to this address upon a successful submission.  If not set, submission notifications will be sent to the site's Admin E-mail address.  You can include multiple, comma-separated e-mail addresses here.</p>
				</td>
			</tr>
		</table>
		
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="easy_faqs_submit_notification_include_question">Include Question In Notification E-mail</label></th>
				<td><input type="checkbox" name="easy_faqs_submit_notification_include_question" id="easy_faqs_submit_notification_include_question" <?php if(!isValidFAQKey()): ?>disabled="disabled"<?php endif; ?> value="1" <?php if(get_option('easy_faqs_submit_notification_include_question')){ ?> checked="CHECKED" <?php } ?>/>
				<p class="description">If checked, the notification e-mail will include the Question asked.</p>
				</td>
			</tr>
		</table>
		<?php
	}
	
	function output_spam_prevention_options(){
		?>
		<h3>Spam Prevention</h3>
		<table class="form-table">
		<?php
				// Submission Form CAPTCHA (checkbox)
				$desc = 'If checked, and a compatible plugin is installed (such as <a href="https://wordpress.org/plugins/really-simple-captcha/" target="_blank">Really Simple Captcha</a>) then we will output a Captcha on the Submission Form.  This is useful if you are having SPAM problems.';
				$disabled =  !isValidFAQKey();
				if(!class_exists('ReallySimpleCaptcha')) {
					$desc .= '</p><p class="alert"><strong>ALERT: Really Simple Captcha is NOT active.  Captcha feature will not function.</strong>';
				}
				$checked = (get_option('easy_faqs_use_captcha') == '1');
				$this->shed->checkbox( array('name' => 'easy_faqs_use_captcha', 'label' =>'Enable Really Simple Captcha', 'value' => 1, 'checked' => $checked, 'description' => $desc, 'inline_label' => 'Show a CAPTCHA on form submissions to prevent spam', 'disabled' => $disabled) );
		?>
		</table>
		<?php
	}
	
	function output_error_messages(){
		?>
		<h3>Error Messages</h3>
				
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="easy_faqs_name_error_message">Name Error Message</label></th>
				<td><textarea name="easy_faqs_name_error_message" id="easy_faqs_name_error_message" <?php if(!isValidFAQKey()): ?>disabled="disabled"<?php endif; ?>><?php echo get_option('easy_faqs_name_error_message',  $this->root->get_str('FAQ_FORM_ERROR_NAME') ); ?></textarea>
				<p class="description">This is the message shown when this field isn't filled out correctly.</p>
				</td>
			</tr>
		</table>
		
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="easy_faqs_question_error_message">Question Error Message</label></th>
				<td><textarea name="easy_faqs_question_error_message" id="easy_faqs_question_error_message" <?php if(!isValidFAQKey()): ?>disabled="disabled"<?php endif; ?>><?php echo get_option('easy_faqs_question_error_message',  $this->root->get_str('FAQ_FORM_ERROR_QUESTION') ); ?></textarea>
				<p class="description">This is the message shown when this field isn't filled out correctly.</p>
				</td>
			</tr>
		</table>
				
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="easy_faqs_captcha_error_message">Captcha Error Message</label></th>
				<td><textarea name="easy_faqs_captcha_error_message" id="easy_faqs_captcha_error_message" <?php if(!isValidFAQKey()): ?>disabled="disabled"<?php endif; ?>><?php echo get_option('easy_faqs_captcha_error_message',  $this->root->get_str('FAQ_FORM_ERROR_CAPTCHA') ); ?></textarea>
				<p class="description">This is the message shown when this field isn't filled out correctly.</p>
				</td>
			</tr>
		</table>
		
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="easy_faqs_general_error_message">General Error Message</label></th>
				<td><textarea name="easy_faqs_general_error_message" id="easy_faqs_general_error_message" <?php if(!isValidFAQKey()): ?>disabled="disabled"<?php endif; ?>><?php echo get_option('easy_faqs_general_error_message',  $this->root->get_str('FAQ_FORM_ERROR_SUBMISSION') ); ?></textarea>
				<p class="description">This is the message shown when this field isn't filled out correctly.</p>
				</td>
			</tr>
		</table>
		<?php
	}
		
	function shortcode_generator_page() {
		$this->settings_page_top( false );
		$this->shortcode_generator_page_tabs();
		$this->settings_page_bottom();
	}
	
	function shortcode_generator_page_tabs()
	{
		//add upgrade button if free version
		$extra_buttons = array();
		if(!isValidFAQKey()){
			$extra_buttons = array(
				array(
					'class' => 'btn-purple',
					'label' => 'Upgrade To Pro',
					'url' => 'https://goldplugins.com/our-plugins/easy-faqs-details/upgrade-to-easy-faqs-pro/'
				)
			);
		}
		
		$tabs = new GP_Sajak( array(
			'header_label' => 'Shortcode Generator',
			'settings_field_key' => 'easy-faqs-shortcode-generator-group', // can be an array
			'show_save_button' => false, // hide save buttons for all panels 		
			'extra_buttons_header' => $extra_buttons,
			'extra_buttons_footer' => $extra_buttons
		) );
		
		$tabs->add_tab(
			'shortcode_generator', // section id, used in url fragment
			'Shortcode Generator', // section label
			array($this, 'output_shortcode_generator'), // display callback
			array(
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->display();
	}
	
	function output_shortcode_generator(){
		?>
		<div id="gold_plugins_shortcode_generator wp-content-wrap">
			<h3>Shortcode Generator</h3>
		
			<p>Using the buttons below, select your desired method and options for displaying FAQs.</p>
			<p>Instructions:</p>
			<ol>
				<li>Click the FAQs button, below,</li>
				<li>Pick from the available display methods listed, such as List of FAQs,</li>
				<li>Set the options for your desired method of display,</li>
				<li>Click "Insert Now" to generate the shortcode.</li>
				<li>The generated shortcode will appear in the textarea below - simply copy and paste this into the Page or Post where you would like FAQs to appear!</li>
			</ol>
			
			<div id="easy-faqs-shortcode-generator">
			<?php 
				$content = "";//initial content displayed in the editor_id
				$editor_id = "easy_faqs_shortcode_generator";//HTML id attribute for the textarea NOTE hyphens will break it
				$settings = array(
					//'tinymce' => false,//don't display tinymce
					'quicktags' => false,
				);
				wp_editor($content, $editor_id, $settings); 
			?>
			</div>
		</div><!-- end #gold_plugins_shortcode_generator -->
		<?php
	}
	
	function import_export_page() {
		$this->settings_page_top(false);
		$this->import_export_tabs();
		$this->settings_page_bottom();
	}

	function import_export_tabs(){
		//add upgrade button if free version
		$extra_buttons = array();
		if(!isValidFAQKey()){
			$extra_buttons = array(
				array(
					'class' => 'btn-purple',
					'label' => 'Upgrade To Pro',
					'url' => 'https://goldplugins.com/our-plugins/easy-faqs-details/upgrade-to-easy-faqs-pro/'
				)
			);
		}
		
		$tabs = new GP_Sajak( array(
			'header_label' => 'Import &amp; Export',
			'settings_field_key' => 'easy-import-export-group', // can be an array
			'show_save_button' => false, // hide save buttons for all panels 	
			'extra_buttons_header' => $extra_buttons,
			'extra_buttons_footer' => $extra_buttons
		) );
		
		$tabs->add_tab(
			'faqs_importer', // section id, used in url fragment
			'Import FAQs', // section label
			array($this, 'output_faqs_importer'), // display callback
			array(
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->add_tab(
			'faqs_exporter', // section id, used in url fragment
			'Export FAQs', // section label
			array($this, 'output_faqs_exporter'), // display callback
			array(
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->display();
	}

	function output_faqs_importer(){
		if( !isValidFAQKey() ): // not pro ?>
			<h3>Import FAQs</h3>	
			<p class="easy_faqs_not_registered"><strong>These features require Easy FAQs Pro.</strong>&nbsp;&nbsp;&nbsp;<a class="button" target="blank" href="https://goldplugins.com/our-plugins/easy-faqs-details/upgrade-to-easy-faqs-pro/?utm_campaign=upgrade&utm_source=plugin&utm_banner=import_upgrade">Upgrade Now</a></p>		
		<?php else: //is pro ?>
			<form method="POST" action="" enctype="multipart/form-data">
				<h3>FAQs Importer</h3>	
				<?php 
					//CSV Importer
					$importer = new FAQsPlugin_Importer($this);
					$importer->csv_importer(); // outputs form and handles input. TODO: break into 2 functions (one to show form, one to process input)
				?>
			</form>
		<?php endif;
	}
	
	function output_faqs_exporter(){
		if( !isValidFAQKey() ): // not pro ?>
			<h3>Export FAQs</h3>		
			<p class="easy_faqs_not_registered"><strong>These features require Easy FAQs Pro.</strong>&nbsp;&nbsp;&nbsp;<a class="button" target="blank" href="https://goldplugins.com/our-plugins/easy-faqs-details/upgrade-to-easy-faqs-pro/?utm_campaign=upgrade&utm_source=plugin&utm_banner=export_upgrade">Upgrade Now</a></p>		
		<?php else: //is pro ?>
			<form method="POST" action="" enctype="multipart/form-data">
				<h3>FAQs Exporter</h3>	
				<?php 
					//CSV Exporter
					FAQsPlugin_Exporter::output_form();
				?>
			</form>
		<?php endif;
	}
	
	//output the help page
	function render_help_page(){		
		//load additional label string based upon pro status
		$pro_string = isValidFAQKey() ? "" : " (Pro)";
		
		//add upgrade button if free version
		$extra_buttons = array();
		if(!isValidFAQKey()){
			$extra_buttons = array(
				array(
					'class' => 'btn-purple',
					'label' => 'Upgrade To Pro',
					'url' => 'https://goldplugins.com/our-plugins/easy-faqs-details/upgrade-to-easy-faqs-pro/'
				)
			);
		}
		
		//instantiate tabs object for output basic settings page tabs
		$tabs = new GP_Sajak( array(
			'header_label' => 'Help &amp; Instructions',
			'settings_field_key' => 'easy-faqs-help-settings-group', // can be an array	
			'show_save_button' => false, // hide save buttons for all panels   		
			'extra_buttons_header' => $extra_buttons, // extra header buttons
			'extra_buttons_footer' => $extra_buttons // extra footer buttons
		) );
		
		$this->settings_page_top(false);
	
		$tabs->add_tab(
			'help', // section id, used in url fragment
			'Help Center', // section label
			array($this, 'output_help_page'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'life-buoy' // icons here: http://fontawesome.io/icons/
			)
		);
	
		$tabs->add_tab(
			'contact', // section id, used in url fragment
			'Contact Support' . $pro_string, // section label
			array($this, 'output_contact_page'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'envelope-o' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->display();
		
		$this->settings_page_bottom();
	}
	
	function output_contact_page(){
		if(isValidFAQKey()){		
			//load all plugins on site
			$all_plugins = get_plugins();
			//load current theme object
			$the_theme = wp_get_theme();
			//load current easy t options
			$the_options = $this->load_all_options();
			//load wordpress area
			global $wp_version;
			
			$site_data = array(
				'plugins'	=> $all_plugins,
				'theme'		=> $the_theme,
				'wordpress'	=> $wp_version,
				'options'	=> $the_options
			);
			
			$current_user = wp_get_current_user();
			?>
			<h3>Contact Support</h3>
			<p>Would you like personalized support? Use the form below to submit a request!</p>
			<p>If you aren't able to find a helpful answer in our Help Center, go ahead and send us a support request!</p>
			<p>Please be as detailed as possible, including links to example pages with the issue present and what steps you've taken so far.  If relevant, include any shortcodes or functions you are using.</p>
			<p>Thanks!</p>
			<div class="gp_support_form_wrapper">
				<div class="gp_ajax_contact_form_message"></div>
				
				<div data-gp-ajax-form="1" data-ajax-submit="1" class="gp-ajax-form" method="post" action="https://goldplugins.com/tickets/galahad/catch.php">
					<div style="display: none;">
						<textarea name="your-details" class="gp_galahad_site_details">
							<?php
								echo htmlentities(json_encode($site_data));
							?>
						</textarea>
						
					</div>
					<div class="form_field">
						<label>Your Name (required)</label>
						<input type="text" aria-invalid="false" aria-required="true" size="40" value="<?php echo (!empty($current_user->display_name) ?  $current_user->display_name : ''); ?>" name="your_name">
					</div>
					<div class="form_field">
						<label>Your Email (required)</label>
						<input type="email" aria-invalid="false" aria-required="true" size="40" value="<?php echo (!empty($current_user->user_email) ?  $current_user->user_email : ''); ?>" name="your_email"></span>
					</div>
					<div class="form_field">
						<label>URL where problem can be seen:</label>
						<input type="text" aria-invalid="false" aria-required="false" size="40" value="" name="example_url">
					</div>
					<div class="form_field">
						<label>Your Message</label>
						<textarea aria-invalid="false" rows="10" cols="40" name="your_message"></textarea>
					</div>
					<div class="form_field">
						<input type="hidden" name="include_wp_info" value="0" />
						<label for="include_wp_info">
							<input type="checkbox" id="include_wp_info" name="include_wp_info" value="1" />Include information about my WordPress environment (server information, installed plugins, theme, and current version)
						</label>
					</div>					
					<p><em>Sending this data will allow the Gold Plugins can you help much more quickly. We strongly encourage you to include it.</em></p>
					<input type="hidden" name="plugin" value="easy_faqs_pro" />
					<input type="hidden" name="registered_email" value="<?php echo htmlentities(get_option('easy_faqs_registered_name')); ?>" />
					<input type="hidden" name="site_url" value="<?php echo htmlentities(site_url()); ?>" />
					<input type="hidden" name="challenge" value="<?php echo substr(md5(sha1('bananaphone' . get_option('easy_faqs_registered_key') )), 0, 10); ?>" />
					<div class="submit_wrapper">
						<input type="submit" class="button submit" value="Send">			
					</div>
				</div>
			</div>
			<?php
		} else {
			?>
			<h3>Contact Support</h3>
			<p>Would you like personalized support? Upgrade to Pro today to receive hands on support and access to all of our Pro features!</p>
			<p><a class="button upgrade" href="https://goldplugins.com/special-offers/upgrade-to-easy-faqs-pro/?utm_source=easy_faqs_freep&utm_campaign=galahad_support_tab&utm_content=learn_more_button_1">Click Here To Learn More</a></p>			
			<?php
		}
	}
	
	function output_help_page(){
		?>
		<h3>Help Center</h3>
		<div class="help_box">
			<h4>Have a Question?  Check out our FAQs!</h4>
			<p>Our FAQs contain answers to our most frequently asked questions.  This is a great place to start!</p>
			<p><a class="easy_faqs_support_button" target="_blank" href="https://goldplugins.com/documentation/easy-faqs-documentation/frequently-asked-questions/">Click Here To Read FAQs</a></p>
		</div>
		<div class="help_box">
			<h4>Looking for Instructions? Check out our Documentation!</h4>
			<p>For a good start to finish explanation of how to add FAQs and then display them on your site, check out our Documentation!</p>
			<p><a class="easy_faqs_support_button" target="_blank" href="https://goldplugins.com/documentation/easy-faqs-documentation/?utm_source=help_page">Click Here To Read Our Docs</a></p>
		</div>
		<?php		
	}	
	
	function recent_searches_page()
	{
		$this->settings_page_top(false);		
		$this->recent_searches_page_tabs();		
		$this->settings_page_bottom();
	}
	
	function recent_searches_page_tabs() {
		//add upgrade button if free version
		$extra_buttons = array();
		if(!isValidFAQKey()){
			$extra_buttons = array(
				array(
					'class' => 'btn-purple',
					'label' => 'Upgrade To Pro',
					'url' => 'https://goldplugins.com/our-plugins/easy-faqs-details/upgrade-to-easy-faqs-pro/'
				)
			);
		}
		
		$tabs = new GP_Sajak( array(
			'header_label' => 'Recent Searches',
			'settings_field_key' => 'easy-faqs-recent-searches-group', // can be an array
			'show_save_button' => false, // hide save buttons for all panels 	
			'extra_buttons_header' => $extra_buttons,
			'extra_buttons_footer' => $extra_buttons	
		) );
		
		$tabs->add_tab(
			'recent_searches', // section id, used in url fragment
			'Recent Searches', // section label
			array($this, 'output_recent_searches'), // display callback
			array(
				'icon' => 'gear' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$tabs->display();
	}
	
	function output_recent_searches(){		
		$categories = get_terms( 'easy-faq-category', 'orderby=title&hide_empty=0' );
		?>
		<div id="easy_faqs_recent_searches">
			<h3>Recent Searches</h3>
			<?php if (isValidFAQKey()): ?>
			<?php
				global $wpdb;		
				$table_name = $wpdb->prefix . 'easy_faqs_search_log';
				$limit = 25;
				$page = (isset($_GET['results_page']) && intval($_GET['results_page']) > 0) ? intval($_GET['results_page']) : 1;
				$offset = $page > 1 ? ( ($page - 1) * $limit ) : 0;
				$sql_template = 'SELECT * from %s ORDER BY time DESC LIMIT %d,%d';
				$sql = sprintf($sql_template, $table_name, $offset, $limit);				
				$recent_searches = $wpdb->get_results($sql);
				
				
				// get the total count				
				$count_sql_template = 'SELECT count(id) from %s';
				$count_sql = sprintf($count_sql_template, $table_name);
				$record_count = $wpdb->get_var($count_sql);
				
				if (is_array($recent_searches)) {
					echo '<table id="easy_faqs_recent_searches" class="wp-list-table widefat fixed pages">';
						echo '<thead>';
							echo '<tr>';
								echo '<th>Time</th>';
								echo '<th>Query</th>';
								echo '<th>Results</th>';
								echo '<th>Visitor IP</th>';
								echo '<th>Location</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
							foreach($recent_searches as $i => $search)
							{
								$row_class = ($i % 2 == 0) ? 'alternate' : '';
							echo '<tr class="'.$row_class.'">';
								$friendly_time = date('Y-m-d H:i:s', strtotime($search->time));
								if ($this->root !== false) {
									$friendly_time = $this->root->time_elapsed_string($friendly_time);
								}
								printf ('<td>%s</td>', htmlentities($friendly_time));
								printf ('<td>%s</td>', htmlentities($search->query));
								printf ('<td>%s</td>', htmlentities($search->result_count));
								printf ('<td>%s</td>', htmlentities($search->ip_address));
								printf ('<td>%s</td>', htmlentities($search->friendly_location));
							echo '</tr>';				
							}
						echo '</tbody>';
					echo '</table>';
					
					if ($record_count > $limit)
					{
						$link_template = '<li><a href="%s">%s</a></li>';
						$href_template = admin_url('admin.php?page=easy-faqs-recent-searches&results_page=') . '%d';
						$last_page = ceil($record_count / $limit);
						echo '<div class="tablenav bottom">';
						echo '<div class="tablenav-pages">';
						echo '<ul class="search_result_pages">';

						// first page link
						$href = sprintf($href_template, 1);
						printf($link_template, $href, '&laquo;');

						// output page links
						for($i = 1; $i <= $last_page; $i++)
						{
							$href = sprintf($href_template, ($i));
							printf($link_template, $href, $i);
						}						
						
						// last page link
						$href = sprintf($href_template, $last_page);
						printf($link_template, $href, '&raquo;');						
						
						echo '</ul>';
						echo '</div>'; // end tablenav-pages
						echo '</div>'; // end tablenav
					}
				}		
			?>	
		<?php else: ?>
			<p class="easy_faqs_not_registered"><strong>This feature requires Easy FAQs Pro.</strong>&nbsp;&nbsp;&nbsp;<a class="button" target="blank" href="https://goldplugins.com/our-plugins/easy-faqs-details/upgrade-to-easy-faqs-pro/?utm_campaign=upgrade_search&utm_source=plugin&utm_banner=recent_searches">Upgrade Now</a></p>
		<?php endif; ?>
			</div><!-- end #easy_faqs_recent_searches -->
		<?php
	}
	
} // end class