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


// Désactiver les commentaires pour les articles et les pages
function disable_comments_post_types_support() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'disable_comments_post_types_support');

// Masquer les options de commentaire dans l'administration
function disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'disable_comments_admin_menu');

// Rediriger tout accès aux pages de commentaires existantes vers la page d'accueil
function disable_comments_admin_menu_redirect() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'disable_comments_admin_menu_redirect');

// Masquer l'interface de commentaire dans la barre d'outils de l'administration
function disable_comments_admin_bar() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'disable_comments_admin_bar');


