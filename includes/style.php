<?php
//Load css in admin pages
function wetransfer_admin_styles($hook){
wp_enqueue_style('wetransfer-admincustom-css',WETRANSFER_PLUGIN_PATH.'/includes/style.css',[],1.0);
wp_register_style('wetransfer-admin-css',WETRANSFER_PLUGIN_PATH.'/includes/bootstrap.min.css',[],4.0);
if('toplevel_page_wetransfer'==$hook){   //conditionally call css so that no other plugin is affected
    wp_enqueue_style('wetransfer-admin-css');
    wp_enqueue_style('wetransfer-admincustom-css');
}
}
add_action('admin_enqueue_scripts','wetransfer_admin_styles',100);

//Load css in front end

function wetransfer_front_end_styles(){
    wp_enqueue_style('wetransfer-front-css',WETRANSFER_PLUGIN_PATH.'/includes/front-style.css',[],1.0);
}

add_action('wp_enqueue_scripts','wetransfer_front_end_styles',100);