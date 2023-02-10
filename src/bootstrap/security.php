<?php
/* ------------------------------------------------------ *
 * Créer le role pour les clients                        *
 * ------------------------------------------------------ */
function add_role_admin_lite()
{
	global $wp_roles;
	if (!isset($wp_roles)) {
		$wp_roles = new WP_Roles();
	}
	$adm = $wp_roles->get_role('administrator');
	// Adding a new role with all admin caps.
	$wp_roles->add_role('admin-lite', 'Admin Lite', $adm->capabilities);
}
add_action('init', 'add_role_admin_lite');

/* ------------------------------------------------------ *
 * Disable errors when connecting                         *
 * ------------------------------------------------------ */
add_filter('login_errors', function ($a) {
	return null;
});

/* ------------------------------------------------------ *
 * Hide WordPress version                                 *
 * ------------------------------------------------------ */
remove_action("wp_head", "wp_generator");

/* ------------------------------------------------------ *
 * Disable comments                                       *
 * ------------------------------------------------------ */
function wpc_comments_closed($open, $post_id)
{
	return false;
}
add_filter('comments_open', 'wpc_comments_closed', 10, 2);

/* ------------------------------------------------------ *
 * Hide scripts version                                   *
 * ------------------------------------------------------ */
function remove_cssjs_ver($src)
{
	if (strpos($src, '?ver=')) {
		$src = remove_query_arg('ver', $src);
	}
	return $src;
}
add_filter('style_loader_src', 'remove_cssjs_ver', 10, 2);
add_filter('script_loader_src', 'remove_cssjs_ver', 10, 2);


/* -------------------------------------------*
 * Allow .cco files *
 * -------------------------------------------*/

add_filter('upload_mimes', function($mime_types){
	
	$mime_types['cco'] = 'application/x-cocoa';
	return $mime_types;
	
});
