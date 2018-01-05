<?php 
add_action ('wp_enqueue_scripts', 'my_enqueued_assets');
function my_enqueued_assets () {
	wp_enqueue_style ('style', plugin_dir_url(__FILE__).'/css/style.css');

	wp_enqueue_script ('jquery', plugin_dir_url(__FILE__).'/js/jquery-3.2.1.min.js');
	wp_enqueue_script ('main.js', plugin_dir_url(__FILE__).'/js/main.js');
}
 ?>