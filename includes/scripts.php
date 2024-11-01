<?php
//load scripts in adnin pages
function wetransfer_load_admin_scripts(){
wp_enqueue_script('jquery');
wp_enqueue_script('wetransfer-boot-js',WETRANSFER_PLUGIN_PATH.'/includes/bootstrap.min.js',['jquery'],time());
wp_enqueue_script('custom-js',WETRANSFER_PLUGIN_PATH.'/includes/custom.js',[],time());
}
add_action('admin_enqueue_scripts','wetransfer_load_admin_scripts',100);
function wetransfer_delete_action_fun() { ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
        jQuery(".delete-single").click(function(e){
            var selected_id =jQuery(this).attr('id');
		var data = {
			'action': 'delete_action',
            'delete_id': selected_id
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			$("#message").html("<h3>"+response+"</h3>");
		});
    });
});
	</script> <?php
}
add_action( 'admin_footer', 'wetransfer_delete_action_fun' ); //load our js in footer
function wetransfer_delete_action() {
	global $wpdb;
    $table =  $wpdb->prefix . 'wetransfer';
	$delete_id = intval( sanitize_key($_POST['delete_id'] )); //defined in above function delete_action_fun
    $qry = $wpdb->delete( $table, array( 'id' => $delete_id ) );
    if($qry){
        echo "Deleted";
    }else{
        echo "Delete Failed!";
    }

	wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_delete_action', 'wetransfer_delete_action' ); // notice wp_ajax_delete_action