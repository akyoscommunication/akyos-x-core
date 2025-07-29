<?php

/**
 * Module Wireframe - Remplace les blocs Gutenberg par des rectangles gris
 * 
 * Ce module utilise des filtres WordPress pour intercepter le rendu des blocs
 * et les remplacer par des rectangles gris simples pour faciliter la mise en page.
 */

// Vérifier que le module est actif
if (!\Akyos\Core\Classes\ModuleManager::isModuleActive('Wireframe')) {
    return;
}
/**
 * Filtre pour les blocs ACF
 * Intercepte le rendu des blocs ACF personnalisés
 */
add_filter('render_block', function ($block_content, $block) {
    // Vérifier si nous sommes en mode wireframe
    if (!is_wireframe_mode_active()) {
        return $block_content;
    }

    // Remplacer le contenu du bloc par un rectangle gris
    // return generate_wireframe_block($block);

    return $block_content;
}, 999, 2);

/**
 * Vérifier si le mode wireframe est actif
 * Le mode est actif quand le module Wireframe est activé
 * Peut être temporairement activé via un paramètre GET ou une constante
 */
function is_wireframe_mode_active(): bool
{
    return \Akyos\Core\Classes\ModuleManager::isModuleActive('Wireframe');
}

/**
 * Ajouter un indicateur visuel quand le mode wireframe est actif
 */
add_action('wp_body_open', function () {
    if (!is_wireframe_mode_active()) {
        return;
    }

    echo '<div style="position: fixed; top: 10px; right: 10px; background: #ef4444; color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; z-index: 9999;">
        MODE WIREFRAME ACTIF
    </div>';
});
