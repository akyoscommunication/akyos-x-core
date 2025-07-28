<?php

namespace Akyos\Core\Modules\Editor;

use Akyos\Core\Interface\IModule;

class EditorModule implements IModule
{
    public static function hook(): string
    {
        return 'init';
    }

    public static function boot(): void
    {
        if (self::isActive()) {
            $editor_file = __DIR__ . '/hooks-editor.php';
            if (file_exists($editor_file)) {
                require_once $editor_file;
                self::registerViews();
                flush_rewrite_rules();
            }
        }
    }

    public static function registerViews(): void
    {
        $view = \Roots\view();
        $view->addNamespace('akyos-editor', __DIR__ . '/resources/views');
    }

    public static function getName(): string
    {
        return 'Éditeur personnalisé';
    }

    public static function getDescription(): string
    {
        return 'Module qui active l\'éditeur personnalisé avec les routes /editor et les restrictions d\'accès';
    }

    public static function isActive(): bool
    {
        return \Akyos\Core\Classes\ModuleManager::isModuleActive('Editor');
    }

    public static function activate(): void
    {
        // Actions à effectuer lors de l'activation du module
        add_action('admin_notices', function () {
            echo '<div class="notice notice-success is-dismissible"><p>Module Éditeur personnalisé activé avec succès ! Les routes /editor sont maintenant disponibles.</p></div>';
        });

        // Rafraîchir les permaliens pour prendre en compte les nouvelles règles de réécriture
        flush_rewrite_rules();
    }

    public static function deactivate(): void
    {
        // Actions à effectuer lors de la désactivation du module
        add_action('admin_notices', function () {
            echo '<div class="notice notice-warning is-dismissible"><p>Module Éditeur personnalisé désactivé. Les routes /editor ne sont plus disponibles.</p></div>';
        });

        // Nettoyer les règles de réécriture personnalisées
        flush_rewrite_rules();
    }
}
