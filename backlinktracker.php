<?php
/*
Plugin Name: Back Link Tracker
Plugin URI:  http://www.lemosys.com/
Description: This Plugin will Track all the backlink in your wordpress site and redirect where you want to redirect it .
Version: 1.0.0
Author: Bhaskar Dhote
Author URI: http://www.lemosys.com/
*/

define( 'WP_BACKLINK_URL', plugin_dir_url(__FILE__) );
define( 'WP_BACKLINK_PATH', plugin_dir_path(__FILE__) );
define( 'WP_BACKLINK_SLUG','wp_members' );
$plugin_dir = plugin_dir_url( __FILE__ ).'images/';
$lipl_label = "lipl";
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
global $wpdbb_content_dir;
if(!function_exists('wp_get_current_user')){
	include(ABSPATH."wp-includes/pluggable.php") ; // Include pluggable.php for current user	
}
/*
 * Method :-- installation_plugin
 * Task   :-- Install the plugin and create the table for it
 * Hook   :-- register_activation_hook
*/
function installation_plugin() {
	global $wpdb;	
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "back_link";
    $sql = "CREATE TABLE IF NOT EXISTS ". $table_name ."(
            id bigint(20) NOT NULL AUTO_INCREMENT,
            refer_url text,
			redirect_url text,
            status varchar(255), 
            date timestamp Default CURRENT_TIMESTAMP,
			is_active tinyint(1),
            PRIMARY KEY  (id));";
            dbDelta($sql);
}
register_activation_hook( __FILE__, 'installation_plugin' );

/*
 * Method :-- deactivate
 * Task   :-- Deactivate the plugin and remove all the data of it.
 * Hook   :-- register_deactivation_hook
*/

function deactivate() {
	global $wpdb;
    $table_name = $wpdb->prefix . "back_link";
    $wpdb->query("DROP TABLE IF EXISTS $table");
	include('uninstall.php');
}
register_deactivation_hook( __FILE__, 'deactivate' );


/*
 * Method :-- custom_style
 * Task   :-- Include All the style and script into the plugin.
 * Action   :-- wp_head
*/
add_action('admin_enqueue_scripts','custom_style');
function custom_style(){
	wp_enqueue_style('thickbox');
	wp_enqueue_style('jquery_custom_styles',WP_BACKLINK_URL.'css/jquery_custom.css');
	wp_enqueue_style('back_styles',WP_BACKLINK_URL.'css/back_styels.css');
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('thickbox');
}

/*
 * Method :-- getRefer
 * Task   :-- Get the refrence link and reditrect ,Insert Detail if not found.
 * Action   :-- wp_head
*/
add_action('wp_head','getRefer');
function getRefer(){
	$referer="";	 
	if (isset($_SERVER['HTTP_REFERER'])){ // check if referrer is set
		$referer= (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Unknown' ); // echo referrer
	}
	//check if refer url already available then redirect to set url.
	global $wpdb;
    $table_name = $wpdb->prefix . "back_link";
	$refer_result=$wpdb->get_results( "SELECT * FROM $table_name where refer_url='$referer' and is_active=true" );
	if($refer_result){
	 $redirect_url=$refer_result[0]->redirect_url;
		$status=$refer_result[0]->status;
		if($status=="301"){
			wp_redirect($redirect_url); exit;					
		}else if($status=="404"){
			wp_redirect($redirect_url); exit;
		}else if($status=="custom"){
			wp_redirect($redirect_url); exit;						
		}
	}else{
		if($referer !==""){
			$wpdb->insert( $table_name, array( 'refer_url' => $referer, 'is_active' => 1 ) );
		}
	}
 }
/*
 * Method :-- add_menu
 * Task   :-- Add menu to the admin 
 * Action   :-- admin_menu
*/ 
add_action('admin_menu', 'add_menu');
function add_menu() {
   /* add_menu_page('Backlink','Backlist','manage_options','backlinktracker.php','backlink_list',plugin_dir_url( __FILE__ ).'images/icon.png');
	add_submenu_page( 'backlinktracker.php','Edit', 'Edit', 'manage_options', 'edit_detail', 'edit_detail' ); */
	
	add_menu_page('Back Link', 'Back Link', 'add_users', 'manage_link', 'backlink_list',plugin_dir_url( __FILE__ ).'images/icon.png');
	add_submenu_page('manage_link', 'Manage BackLink','Manage BackLink','add_users','manage_link','backlink_list');
	add_submenu_page('manage_link', 'Edit Link','Edit Link','add_users','edit_link','edit_linkdetail');
	

}
/*
 * Method :-- backlink_list
 * Task   :-- list all the backlinks that are save in the database 
 * Note   :-- Include the backlink_list.php
*/
function backlink_list() {
    global $menu, $submenu;
    require('backlink_list.php');
}
/*
 * Method :-- edit_detail
 * Task   :-- Edit the detail of the Back link 
 * Note   :-- Include the backlink_list.php
*/
function edit_linkdetail(){
		include_once('edit_link.php');
}
?>