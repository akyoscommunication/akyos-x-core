<?php

namespace Akyos\Core\Modules\Wireframe;

use Akyos\Core\Interface\IModule;

class WireframeModule implements IModule
{
    public static function hook(): string
    {
        return 'init';
    }

    public static function boot(): void
    {
        if (self::isActive()) {
            self::loadAssets();

            $wireframe_file = __DIR__ . '/hooks-wireframe.php';
            if (file_exists($wireframe_file)) {
                require_once $wireframe_file;
            }
        }
    }

    public static function getName(): string
    {
        return 'Mode Wireframe';
    }

    public static function getDescription(): string
    {
        return 'Module qui remplace tous les blocs Gutenberg par des rectangles gris pour faciliter la mise en page';
    }

    public static function isActive(): bool
    {
        return \Akyos\Core\Classes\ModuleManager::isModuleActive('Wireframe');
    }

    public static function loadAssets(): void
    {
        if (self::isActive() && !is_admin()) {
            $themeUrl = get_template_directory_uri();
            $assetsUrl = $themeUrl . '/vendor/akyos/akyos-x-core/';

            wp_enqueue_style('wireframe', $assetsUrl . 'dist/css/wireframe.css', [], '1.0.0');
            wp_enqueue_script('wireframe', $assetsUrl . 'dist/js/wireframe.js', [], '1.0.0', true);
        }
    }

    public static function activate(): void
    {
        // Actions à effectuer lors de l'activation du module
        add_action('admin_notices', function () {
            echo '<div class="notice notice-success is-dismissible"><p>Module Mode Wireframe activé avec succès ! Tous les blocs Gutenberg sont maintenant affichés comme des rectangles gris.</p></div>';
        });
    }

    public static function deactivate(): void
    {
        // Actions à effectuer lors de la désactivation du module
        add_action('admin_notices', function () {
            echo '<div class="notice notice-warning is-dismissible"><p>Module Mode Wireframe désactivé. Les blocs Gutenberg sont maintenant affichés normalement.</p></div>';
        });
    }
}
