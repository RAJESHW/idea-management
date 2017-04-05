<?php
/**
*	Plugin Name:       Podamibe Custom User Gravatar
*	Plugin URI:        http://podamibenepal.com/wordpress-plugins/
*	Description:       Allows users to use pictures in the media gallery as gravatar.
*	Version:           1.0.3
*	Author:            Podamibe Nepal
*	Author URI:        http://podamibenepal.com/ 
*	License:           GPLv2 or later
*	Text Domain:       pcg
**/

if( ! defined( "ABSPATH" ) ){
	exit();
}

/* plugin directory path */
define( "PCG_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__) );

/* plugin directory url */
define("PCG_PLUGIN_URL", plugin_dir_url( __FILE__ ) );

/* text domain */
define( "PCG_TEXT_DOMAIN", "pcg" );


class PCG_User_Gravatar {

	/**
	* A reference to an instance of this class.
	* @private
	* @var   pcg_User_Gravatar
	*/
	private static $_instance = null;
	
	/**
	*	@private
	*	constructor of this class
	**/
	private function __construct(){
		/**
		*	add custom gravatar section
		*	in user profile, edit profile and add user forms
		**/
		add_action( "show_user_profile", array( $this, "add_change_gravatar_section" ) );
		add_action( "edit_user_profile", array( $this, "add_change_gravatar_section" ) );		
		add_action( 'user_new_form', array( $this, 'add_change_gravatar_section' ) );
		
		/**
		*	update user meta on add user or edit user profile
		*	sets if the user will use custom gravatar
		**/
		add_action( 'personal_options_update', array( $this,'save_gravatar_options' ) );
		add_action( 'edit_user_profile_update', array( $this,'save_gravatar_options' ) );		
		add_action( 'user_register', array( $this, 'save_gravatar_options' ) );
		
		/**
		*	enqueue script
		**/
		add_action( "admin_enqueue_scripts", array( $this , "upload_picture_script" ));
		
		/**
		*	apply filter to get_avatar() function
		**/
		add_filter( "get_avatar", array( $this, "get_custom_avatar" ), 10, 6 );
	}
	
	/**
	* Returns an instance of this class
	* @public
	* @return   pcg_User_Gravatar
	*/
	public static function init(){
		if( is_null( self::$_instance ) ){
			self::$_instance = new self();
		}		
		return self::$_instance;
	}
	
	/**
	*	enqueue script
	*	@public
	**/
	public function upload_picture_script(){
        global $pagenow;
        if($pagenow == "user-new.php" || $pagenow == "profile.php" || $pagenow == "user-edit.php" ){
            wp_enqueue_media();
            wp_register_script( "pcg-custom-gravatar",  PCG_PLUGIN_URL . "assets/js/pcg-custom-gravatar.js", array('jquery'), null, true);
            wp_enqueue_script("pcg-custom-gravatar");
        }
    }
	
	/**
	*	add custom gravatar section
	*	@public
	**/
	public function add_change_gravatar_section( $user ){
?>
		<h3><?php esc_html_e('Custom Gravatar', PCG_TEXT_DOMAIN);?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( "Use Custom Gravatar", PCG_TEXT_DOMAIN ); ?></th>
				
				<td>
					<?php if( is_object( $user ) ){ ?>
						<label for="pcg-use-custom-gravatar"><input name="pcg_use_custom_gravatar" id="pcg-use-custom-gravatar" value="1" type="checkbox" <?php if ( get_user_meta( $user->ID, "pcg_use_custom_gravatar", true ) == 1 ) echo "checked"; ?> /></label>
					<?php }else{ ?>
						<label for="pcg-use-custom-gravatar"><input name="pcg_use_custom_gravatar" id="pcg-use-custom-gravatar" value="1" type="checkbox" /></label>
					<?php
					}
				?>
				</td>
				
			</tr>
			<tr>
				<th scope="row"></th>
				<td>
					<?php if( is_object( $user ) ){ ?>
						<div id="pcg-custom-gravatar-pic"><?php echo get_avatar( $user->ID ); ?></div>
						<label for="pcg-upload-profile-pic"><a href="javascript:void(0);" class="button" id="pcg-upload-profile-pic"><?php esc_html_e("Select Picture", PCG_TEXT_DOMAIN ); ?></a></label>
						<input name="pcg_custom_gravatar" id="pcg-custom-gravatar" value="<?php echo get_user_meta( $user->ID, "pcg_custom_gravatar", true ); ?>" type="hidden" />
					<?php }else{?>
						<div id="pcg-custom-gravatar-pic"><?php echo get_avatar(NULl); ?></div>
						<label for="pcg-upload-profile-pic"><a href="javascript:void(0);" class="button" id="pcg-upload-profile-pic"><?php esc_html_e("Select Picture", PCG_TEXT_DOMAIN ); ?></a></label>
						<input name="pcg_custom_gravatar" id="pcg-custom-gravatar" value="" type="hidden" />
					<?php
					} ?>
				</td>
			</tr>			
			
		</table>
<?php
	}
	
	/**
	*	save gravatar options
	*	@public
	**/
	public function save_gravatar_options( $user_id ){
		if( isset( $_REQUEST["pcg_use_custom_gravatar"] ) ){
			if( isset( $_REQUEST["pcg_custom_gravatar"] ) && $_REQUEST["pcg_custom_gravatar"] != "" ){
				update_user_meta( $user_id, "pcg_use_custom_gravatar", 1 );
				update_user_meta( $user_id, "pcg_custom_gravatar", sanitize_text_field($_REQUEST["pcg_custom_gravatar"]) );
			}
			else{
				update_user_meta( $user_id, "pcg_use_custom_gravatar", 0 );
			}			
		}
		else{
			update_user_meta( $user_id, "pcg_use_custom_gravatar", 0 );
		}
	}
	
	/**
	*	get custom avatar
	*	returns default gravatar if custom gravatar is not set
	*	returns custom gravatar if custom gravatar is not set
	*	@return string
	**/
	public function get_custom_avatar( $avatar, $id_or_email, $size, $default, $alt, $args ){
		if( $args["force_default"] ){
			return $avatar;
		}
	    if ( is_object( $id_or_email ) && isset( $id_or_email->comment_ID ) ) {
            $id_or_email = $id_or_email->user_id;
        }
		
		if( is_email( $id_or_email ) ){
			$user = get_user_by( "email", $id_or_email );
			$user_id = $user->ID;
		}
		else{
			$user_id = $id_or_email;
		}
		
		if( get_user_meta( $user_id, "pcg_use_custom_gravatar", true ) == 1 ){
			if( get_user_meta( $user_id, "pcg_custom_gravatar", true ) ){
				$avatar_pic = wp_get_attachment_image_src( get_user_meta( $user_id, "pcg_custom_gravatar", true ) );
				$avatar_url = $avatar_pic[0];
				$avatar = "<img alt='{$alt}' src='{$avatar_url}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
				return $avatar;
			}
			else{
				return $avatar;
			}
		}
		else{
			return $avatar;
		}
	}
}

PCG_User_Gravatar::init();