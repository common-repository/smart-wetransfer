<?php
/*
Plugin Name: Smart WeTransfer
Plugin URI: https://grittechnologies.com/plugins
Version: 1.3
Author: Mrityunjay Kumar
Author URI: https://profiles.wordpress.org/mrityunjay/
Description: Transfer Large Files using WeTransfer Embed API.
Text Domain: wetransfer
License: GPLv2
Domain Path: /languages
Tags: Large Files Upload, Transfer Big Files, Wetransfer
*/
// If this file is called directly, abort.
if(!defined('WPINC')){
	die;
}
define('WETRANSFER_PLUGIN_PATH',plugin_dir_url(__FILE__)); //plugin directory path
include(plugin_dir_path(__FILE__).'includes/style.php');
include(plugin_dir_path(__FILE__).'includes/scripts.php');

//Get Form shortcode to display upload form

function wetransfer_getForm(){
	ob_start();
	if(isset($_SESSION['grit_msg'])){
	    echo "<h3 style='color:green'>".$_SESSION['grit_msg']."</h3>";
	}
	unset($_SESSION['grit_msg']);
	?>
	
	<form method="post" action="#">
	<?php wp_nonce_field('update-options') ?>
	<p><?php _e('Your Name', 'wetransfer');?>:<br/><input type='text' name='your_name' class='form-control' placeholder='<?php _e('Your Name', 'wetransfer');?>'><br/>
	<?php
if (isset($_SESSION['error_empty_name'] )) {
echo $_SESSION['error_empty_name'];
unset($_SESSION['error_empty_name']);
}
	
	?></p>
	<p><?php _e('Your Email', 'wetransfer');?>:<br/><input type='email' name='your_email' class='form-control' placeholder='<?php _e('Your Email', 'wetransfer');?>' required><br/>
	<?php
if (isset($_SESSION['error_empty_email'] )) {
echo $_SESSION['error_empty_email'];
unset($_SESSION['error_empty_email']);
}
	
	?>
	<?php echo $_SESSION['email_not_valid'];
	unset($_SESSION['email_not_valid']);
	?>
	</p>
	<p><?php _e('File Name', 'wetransfer');?>:<br/><input type='text' name='your_file' class='form-control'  placeholder='<?php _e('File Name', 'wetransfer');?>'><br/>
	<?php
	if (isset($_SESSION['error_empty_file'] )) {
	echo $_SESSION['error_empty_file'];
	unset($_SESSION['error_empty_file']);
}
	
	?>
	</p>
	
	
	
	<div data-widget-host="habitat" id="wt_embed">
	
  <script type="text/props">
    {
      "wtEmbedKey": "<?php echo get_option('wetransfer_key'); ?>",
      "wtEmbedOutput": ".wt_embed_output",
      "wtEmbedLanguage": "en"
    }
  </script>
</div>
<script async src="https://prod-embed-cdn.wetransfer.net/v1/latest.js"></script>
<!--
  The next input element will hold the transfer link. For testing purposes, you
  could change the type attribute to "text", instead of "hidden".
-->
<input type="hidden" name="wt_embed_output" class="wt_embed_output" />
<br/>
<?php
	if (isset($_SESSION['error_empty_url'] )) {
	echo $_SESSION['error_empty_url'];
	unset($_SESSION['error_empty_url']);
}
	
	?>
	
	<p><br/><input type='submit' class='btn btn-primary' name='submit_file' value='<?php _e('Submit', 'wetransfer');?>'></p>
	</form><?php
	
	return ob_get_clean();
}
	
add_shortcode('smartTransfer','wetransfer_getForm');

//save form data to database
function wetransfer_save_form_data(){
if(isset($_POST['submit_file'])){	
global $wpdb;
$dataArray = array(
	
	'yname' => sanitize_user($_POST['your_name']),
	'yemail' => sanitize_email($_POST['your_email']),
	'yfile' => sanitize_text_field($_POST['your_file']),
	'yurl' => esc_url_raw($_POST['wt_embed_output']),
	

	
);
 if(empty($dataArray['yname'])){
    $error_empty_name = __('<span class="error">Please enter name.</span>', 'wetransfer');
	$dataArray['error_empty_name'] =  $error_empty_name;
 }
 if(empty($dataArray['yemail'])){
    $error_empty_email = __('<span class="error">Please enter email.</span>', 'wetransfer');
	$dataArray['error_empty_email'] = $error_empty_email;
 }
 if(empty($dataArray['yfile'])){
    $error_empty_file = __('<span class="error">Add file name(s).</span>', 'wetransfer');
	$dataArray['error_empty_file'] = $error_empty_file;
 }
 if(empty($dataArray['yurl'])){
    $error_empty_url = __('<span class="error">Please add files to upload.</span>', 'wetransfer');
	$dataArray['error_empty_url'] = $error_empty_url;
 }
 if(!is_email($dataArray['yemail'])){
    $email_not_valid = __('<span class="error">Please use a valid email address.</span>', 'wetransfer');
	$dataArray['email_not_valid'] = $email_not_valid;
 }
 if(empty($dataArray['error_empty_data']) AND empty($dataArray['email_not_valid']) ){
$table =  $wpdb->prefix . 'wetransfer';
$insert = $wpdb->insert($table, $dataArray);
if(!$insert){
$save_failed = __('<span class="error">Data did not save.</span>', 'wetransfer');
$dataArray['save_failed'] =  $save_failed;
}else{
	
add_action('init','smart_wetransfer_send_notification');
function smart_wetransfer_send_notification(){
//php mailer variables
  $to = get_option('admin_email');
  $subject = "New file submitted to wetransfer account";
  $message = "A new file submitted to your wetransfer account by ".$_POST['your_email'].". Link to the file is ".$_POST['wt_embed_output'];
  $headers = 'From: '. $_POST['your_email'] . "\r\n" .
  'Reply-To: ' . $_POST['your_email'] . "\r\n";


//Here put your Validation and send mail
$sent = wp_mail($to, $subject, strip_tags($message), $headers);

$_SESSION['grit_msg'] = 'Success';

}	
	
	
}
}
$_SESSION['email_not_valid'] = $dataArray['email_not_valid'];
$_SESSION['error_empty_name']=  $dataArray['error_empty_name'];
$_SESSION['error_empty_email']=  $dataArray['error_empty_email'];
$_SESSION['error_empty_file']=  $dataArray['error_empty_file'];
$_SESSION['error_empty_url']=  $dataArray['error_empty_url'];
$_SESSION['save_failed'] = $dataArray['save_failed'];


}	
}
wetransfer_save_form_data();
//create plugin table
function wetransfer_jal_install() {
	global $wpdb;
	global $jal_db_version;
	$jal_db_version = '1.0';

	$table_name = $wpdb->prefix . 'wetransfer';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time timestamp DEFAULT current_timestamp NOT NULL,
		yname text NOT NULL,
		yemail text NOT NULL,
		yfile text NOT NULL,
		yurl varchar(55) DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'jal_db_version', $jal_db_version );
}
register_activation_hook( __FILE__, 'wetransfer_jal_install' );
//Add admin menu and create page

function wetransfer_settings_page(){
	add_menu_page(
	__('Smart WeTransfer','wetransfer'),  //page name
	__('Smart WeTransfer','wetransfer'),  // menu name
	'manage_options', //To show menu in dashboard menu
	'wetransfer',  //slug
	'wetransfer_setting_page_markup',  // callback function
	'dashicons-email-alt2',  //Icon
	100  //Position
	);
	add_submenu_page(
		'wetransfer', //Parent Slug
		__('settings','wetransfer'), //Page name 
		__('Settings','wetransfer'), //Menu name
		  'manage_options',
		  'wetransfer_settings', // slug
		   'wetransfer_subpage_markup' //callback function
	);
}
add_action('admin_menu','wetransfer_settings_page');

function wetransfer_setting_page_markup(){

	if ( !current_user_can('manage_options') ) {
		return;
	}
	
	global $wpdb;
	$table =  $wpdb->prefix . 'wetransfer';
	$results = $wpdb->get_results("select * from $table");
echo "<div class='wrap grit-style'>";	

_e('<h2>Your Data</h2><p>Your uploads will be automatically deleted after 7 days from wetransfer in free plan!</p>', 'wetransfer');

echo "<form>";
echo "<div id='message'></div>";
echo "<br/>";?>
<table class='table'><tr><th><?php _e('Name', 'wetransfer');?></th><th><?php _e('Email', 'wetransfer');?></th><th><?php _e('File', 'wetransfer');?></th><th><?php _e('Url', 'wetransfer');?></th><th><?php _e('Upload Date', 'wetransfer');?></th><th><?php _e('Delete', 'wetransfer');?></th></tr>
<?php
foreach($results as $result){
$date=date_create($result->time);
$date = date_format($date,"Y/m/d H:i:s");
echo "<tr><td>".esc_html($result->yname)."</td><td>".esc_html($result->yemail)."</td><td>".esc_html($result->yfile)."</td><td><a href='".esc_url($result->yurl)."' target='_blank'>".esc_url($result->yurl)."</a></td><td>".esc_html($date)."</td><td><a href='#' class='btn btn-danger delete-single'  id='".esc_html($result->id)."'>x</a></td></tr>";
}
echo "</table></form></div>";

}
function wetransfer_subpage_markup(){
	
	?>
	<div class='wrap grit-style'>
	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options') ?>
	<p><strong><?php _e('Add Wetransfer API Key here', 'wetransfer');?></strong><br />
	<input type="text" class='form-control' name="wetransfer_key" size="45" value="<?php echo get_option('wetransfer_key'); ?>" /></p>
	<p><input type="submit" class='btn btn-primary' name="Submit" value="<?php _e('Update Key', 'wetransfer');?>" /></p>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="wetransfer_key" />
	
	</form>
	
	
<h3><?php _e('How To Use', 'wetransfer');?></h3><p><?php _e('Use Shortcode', 'wetransfer');?> [smartTransfer] <?php _e('in your Post or Page or ', 'wetransfer');?> &lt;?php echo do_shortcode('[smartTransfer]');?&gt; <?php _e('in your template. Get WeTransfer API key from ', 'wetransfer');?> <a target='_blank' href="https://developers.wetransfer.com/">https://developers.wetransfer.com/</a>
<?php _e('where you can send 2GB data for free and it will be saved for 7 days in free account. ', 'wetransfer');?></p>
</div>
<?php
}
//Add link to setting page after plugin gets activated

function wetransfer_add_setting_link($links){
	$settings_link = '<a href="admin.php?page=wetransfer_settings">'.__('Settings','wetransfer'). '</a>';
	array_push($links,$settings_link);
	return $links;
}
$filter_name = "plugin_action_links_".plugin_basename(__FILE__);
add_filter($filter_name,'wetransfer_add_setting_link');
?>