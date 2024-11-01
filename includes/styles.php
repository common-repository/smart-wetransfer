<?php
//Load css in admin pages
function wetransfer_admin_styles($hook){
wp_register_style('wetransfer-admin-css',WETRANSFER_PLUGIN_PATH.'/includes/bootstrap.min.css',[],time());
wp_register_script('wetransfer-boot-js',WETRANSFER_PLUGIN_PATH.'/includes/bootstrap.min.js',[],time());
wp_register_script('custom-js',WETRANSFER_PLUGIN_PATH.'/includes/custom.js',[],time());


if('toplevel_page_wetransfer'==$hook){   //conditionally call css so that no other plugin is affected
    wp_enqueue_style('wetransfer-admin-css');
}
}
add_action('admin_enqueue_scripts','wetransfer_admin_styles',100);

//Load css in front end

function wetransfer_front_end_styles(){
    wp_enqueue_style('wetransfer-front-css',WETRANSFER_PLUGIN_PATH.'/includes/front-style.css',[],time());
}

add_action('wp_enqueue_scripts','wetransfer_front_end_styles',100);