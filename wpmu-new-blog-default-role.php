<?php
/*
 * THIS PLUGIN MUST BE PLACED IN THE MU-PLUGINS DIRECTORY
 * 
Plugin Name: New Blog Default Role
Plugin URI: http://www.radiolivetransmission.com/category/wordpress/
Description: Allows admins to change the default role assigned to users who sign up for a blog
Author: Alex Brims - alex.brims@gmail.com
Version: 0.1
Author URI: http://www.radiolivetransmission.com/
Licence: GPL2 
*/

class NewBlogDefaultRole {

	function setup () {
		add_site_option( 'wpmu_new_blog_default_user_role', 'administrator' );
	}
	
	function wpmu_change_new_blog_user_role($blog_id, $user_id) {
		switch_to_blog($blog_id);
		$user = new WP_User($user_id);
		$user->set_role(get_site_option('wpmu_new_blog_default_user_role'));
		restore_current_blog();
	}
	
	function add_default_new_blog_submenu() {
		add_submenu_page('wpmu-admin.php', 'New Blog Default User role', 'New Blog Default User role', 10, 'new_blog_user_role_admin_option', array('NewBlogDefaultRole', 'new_blog_user_role_admin_option'));
	}
	
	function new_blog_user_role_admin_option() {
		
		if (!is_admin()) {
			print "Get off my land!";
			return false;
		}
		
		# Process post
		if ($_POST['new_blog_role_option']) {
			update_site_option ('wpmu_new_blog_default_user_role', $_POST['new_blog_role_option']);
		}
		
		$roles = new WP_Roles();
		$role_names = $roles->get_names();
	
		$selected_option = get_site_option('wpmu_new_blog_default_user_role');
		
		print "<h2>Default role for new blog signups</h2>";
		print "<form action='' method='post' name='form_new_blog_role_option'>";	
		print "<select name='new_blog_role_option'>";	
		
		foreach ($role_names as $key => $role_name) {
			if ($key == $selected_option) {
				$selected = " selected";
			} else {
				$selected = "";
			}
			print "<option value='".$key."'".$selected.">".$role_name."</option>";
		}
		print "</select>";	
		print "<input type='submit' name='submit'>";	 
		print "</form>";	
		
	} 

}

# Setup the option if it doesn't exist
if( get_site_option('wpmu_new_blog_default_user_role') == null || ($_GET['reset'] == 1 && $_GET['page'] == 'new_blog_user_role_admin_option')) {
	NewBlogDefaultRole::setup();
}

add_action('wpmu_activate_blog', array('NewBlogDefaultRole', 'wpmu_change_new_blog_user_role'), 300, 2);

# Add the site admin config page
add_action('admin_menu', array('NewBlogDefaultRole', 'add_default_new_blog_submenu'));
