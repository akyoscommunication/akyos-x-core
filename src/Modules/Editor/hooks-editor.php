<?php

use Akyos\Core\Wrappers\QueryBuilder;

/**
 * Redirection des utilisateurs non connectés vers /editor
 * Sauf pour les URLs WordPress essentielles
 */
add_action('template_redirect', function () {
    // Si l'utilisateur n'est pas connecté
    if (!is_user_logged_in()) {
        $current_url = $_SERVER['REQUEST_URI'];

        $allowed_urls = [
            '/editor/',
            '/editor',
            '/wp-login.php',
            '/wp-admin/admin-ajax.php',
            '/wp-json/',
            '/wp-admin/load-scripts.php',
            '/wp-admin/load-styles.php',
            '/wp-admin/admin-post.php',
            '/wp-includes/',
            '/wp-content/',
            '/wp-content/uploads/',
            '/favicon.ico',
            '/robots.txt',
            '/sitemap.xml'
        ];

        // Vérifier si l'URL actuelle est autorisée
        $is_allowed = false;

        // Vérifier les URLs exactes
        foreach ($allowed_urls as $allowed_url) {
            if (strpos($current_url, $allowed_url) === 0) {
                $is_allowed = true;
                break;
            }
        }

        // Vérifier les URLs /editor/{id} et /editor/logout
        if (
            preg_match('/^\/editor\/[0-9]+\/?$/', $current_url) ||
            preg_match('/^\/editor\/logout\/?$/', $current_url)
        ) {
            $is_allowed = true;
        }

        // Si l'URL n'est pas autorisée, rediriger vers /editor
        if (!$is_allowed) {
            wp_redirect(home_url('/editor/'));
            exit;
        }
    }
}, 2); // Priorité 2 pour s'exécuter après la vérification de connexion

/**
 * Restriction d'accès pour les utilisateurs admin-lite
 * Ils ne peuvent accéder qu'aux URLs /editor et /editor/{id}
 */
add_action('template_redirect', function () {
    // Vérifier si l'utilisateur est connecté et a le rôle admin-lite
    if (is_user_logged_in() && current_user_can('editor')) {
        $current_url = $_SERVER['REQUEST_URI'];
        $home_url = home_url();

        // URLs autorisées pour admin-lite
        $allowed_urls = [
            '/editor/',
            '/editor',
            '/wp-login.php',
            '/wp-admin/admin-ajax.php',
            '/wp-json/',
            '/wp-admin/load-scripts.php',
            '/wp-admin/load-styles.php',
            '/wp-admin/admin-ajax.php',
            '/wp-includes/',
            '/wp-content/',
            '/wp-admin/admin-post.php'
        ];

        // Vérifier si l'URL actuelle est autorisée
        $is_allowed = false;

        // Vérifier les URLs exactes
        foreach ($allowed_urls as $allowed_url) {
            if (strpos($current_url, $allowed_url) === 0) {
                $is_allowed = true;
                break;
            }
        }

        // Vérifier les URLs /editor/{id}
        if (preg_match('/^\/editor\/[0-9]+\/?$/', $current_url)) {
            $is_allowed = true;
        }

        // Si l'URL n'est pas autorisée, rediriger vers /editor
        if (!$is_allowed) {
            wp_redirect(home_url('/editor/'));
            exit;
        }
    }
}, 1); // Priorité 1 pour s'exécuter en premier

/**
 * Redirection des utilisateurs subscriber vers une page personnalisée
 */
add_filter('login_redirect', function ($redirect_to, $request, $user) {

    if ($user instanceof \WP_User) {
        if (in_array('subscriber', $user->roles)) {
            return home_url('/editor/');
        }
    }

    return $redirect_to;
}, 10, 3);

/**
 * Redirection des utilisateurs editor vers /editor lors de la déconnexion
 */
add_filter('logout_redirect', function ($redirect_to, $requested_redirect_to, $user) {

    if ($user instanceof \WP_User) {
        if (in_array('editor', $user->roles)) {
            return home_url('/editor/');
        }
    }

    return $redirect_to;
}, 10, 3);

add_filter('wp_login_failed', function ($username) {
    // Vérifier si la requête vient de notre formulaire d'éditeur
    if (isset($_POST['redirect_to']) && strpos($_POST['redirect_to'], '/editor/') !== false) {
        wp_redirect(home_url('/editor/?login=failed'));
        exit;
    }
}, 10, 1);

/**
 * Ajout de la route personnalisée /editor
 */
add_action('init', function () {
    add_rewrite_rule(
        '^editor/?$',
        'index.php?custom_editor=1',
        'top'
    );

    // Nouvelle règle pour l'édition des pages : /editor/{id}
    add_rewrite_rule(
        '^editor/([0-9]+)/?$',
        'index.php?custom_editor=1&post_id=$matches[1]',
        'top'
    );

    // Route pour la déconnexion directe : /editor/logout
    add_rewrite_rule(
        '^editor/logout/?$',
        'index.php?custom_editor_logout=1',
        'top'
    );
}, 300);

/**
 * Ajout du query var personnalisé
 */
add_filter('query_vars', function ($vars) {
    $vars[] = 'custom_editor';
    $vars[] = 'custom_editor_logout';
    $vars[] = 'post_id';
    return $vars;
}, 300);

/**
 * Gestion de la route /editor
 */
add_action('template_redirect', function () {

    // Gestion de la déconnexion directe
    if (get_query_var('custom_editor_logout') == '1') {
        if (is_user_logged_in()) {
            wp_logout();
            wp_redirect(home_url('/editor/'));
            exit;
        } else {
            wp_redirect(home_url('/editor/'));
            exit;
        }
    }

    if (get_query_var('custom_editor') == '1') {
        $post_id = get_query_var('post_id');

        // Si un post_id est spécifié, afficher l'interface d'édition personnalisée
        if ($post_id) {
            $post = get_post($post_id);
            if ($post && $post->post_type === 'page' || $post->post_type === 'post') {
                // Vérifier que l'utilisateur est connecté et a le rôle admin-lite
                if (!is_user_logged_in()) {
                    // Afficher le template de login personnalisé
                    echo \Roots\view('akyos-editor::editor.login');
                    exit;
                }

                if (!current_user_can('editor')) {
                    wp_redirect(home_url('/wp-login.php'));
                    exit;
                }

                // Charger les scripts et styles nécessaires pour l'éditeur
                wp_enqueue_editor();
                wp_enqueue_media();

                // Afficher l'interface d'édition personnalisée
                echo \Roots\view('akyos-editor::edit', [
                    'post' => $post,
                    'current_user' => wp_get_current_user(),
                ]);
                exit;
            } else {
                // Page non trouvée ou pas une page
                wp_redirect(home_url('/editor/'));
                exit;
            }
        }

        // Affichage du dashboard par défaut
        // Vérifier que l'utilisateur est connecté et a le rôle admin-lite
        if (!is_user_logged_in()) {
            // Afficher le template de login personnalisé
            echo \Roots\view('akyos-editor::login');
            exit;
        }

        if (!current_user_can('editor')) {
            wp_redirect(home_url('/wp-login.php'));
            exit;
        }

        $current_user = wp_get_current_user();

        echo \Roots\view('akyos-editor::dashboard', [
            'pages' => collect(QueryBuilder::make('page')->get('query')->posts),
            'posts' => collect(QueryBuilder::make('post')->get('query')->posts),
        ]);

        exit;
    }
});
