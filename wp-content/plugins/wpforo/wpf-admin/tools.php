<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
	if( !current_user_can('administrator') ) exit;
?>

<?php $plugins = true; ?>
<div class="wrap"><h2 style="padding:0px 0px 30px 0px;line-height: 20px;"><?php _e('Forum Tools') ?></h2></div>
<?php $wpforo->notice->show(FALSE) ?>
<?php do_action('wpforo_tools_page_top') ?>
<div id="wpf-admin-wrap" class="wrap"><div id="icon-users" class="icon32"><br /></div>
<?php
	$tabs = array( 
		'antispam' => __('Antispam', 'wpforo'), 
		//'cleanup' => __('Cleanup', 'wpforo')
	);
	wpforo_admin_tools_tabs( $tabs, ( isset($_GET['tab']) ? $_GET['tab'] : 'antispam' ) ); 
	?>
    <div class="wpf-info-bar" style="padding:1% 2%;">
		<?php 
			if(isset($_GET['tab'])){
				switch($_GET['tab']){
					case 'antispam':
						include( 'tools-tabs/antispam.php' );
					break;
					case 'cleanup':
						include( 'tools-tabs/cleanup.php' );
					break;
					default:
					include_once( 'tools-tabs/antispam.php' );
				}
			}else{
				include_once( 'tools-tabs/antispam.php' );
			}
		?>
	</div>
</div>