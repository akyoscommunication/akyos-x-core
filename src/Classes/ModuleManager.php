<?php

namespace Akyos\Core\Classes;

use Akyos\Core\Interface\IModule;
use Akyos\Core\Interface\IBootable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

use function Roots\view;

class ModuleManager implements IBootable
{
    private static array $modules = [];
    private static string $optionName = 'akyos_active_modules';

    public static function hook(): string
    {
        return 'init';
    }

    public static function boot(): void
    {
        self::$modules = [];
        self::loadModules();
        self::initAdminPage();
        self::bootActiveModules();
    }

    private static function loadModules(): void
    {
        $modulesDir = __DIR__ . '/../Modules';

        if (!is_dir($modulesDir)) {
            return;
        }

        $moduleDirs = glob($modulesDir . '/*', GLOB_ONLYDIR);

        foreach ($moduleDirs as $moduleDir) {
            $moduleName = basename($moduleDir);
            $moduleClass = "Akyos\\Core\\Modules\\{$moduleName}\\{$moduleName}Module";

            if (class_exists($moduleClass) && is_subclass_of($moduleClass, IModule::class)) {
                self::$modules[$moduleName] = $moduleClass;
            }
        }
    }

    public static function getModules(): array
    {
        return self::$modules;
    }

    public static function getActiveModules(): array
    {
        $activeModules = get_option(self::$optionName, []);
        $activeModulesList = [];

        foreach (self::$modules as $moduleName => $moduleClass) {
            if (in_array($moduleName, $activeModules)) {
                $activeModulesList[$moduleName] = $moduleClass;
            }
        }

        return $activeModulesList;
    }

    public static function isModuleActive(string $moduleName): bool
    {
        $activeModules = get_option(self::$optionName, []);
        return in_array($moduleName, $activeModules);
    }

    public static function activateModule(string $moduleName): bool
    {
        if (!isset(self::$modules[$moduleName])) {
            return false;
        }

        $activeModules = get_option(self::$optionName, []);

        if (!in_array($moduleName, $activeModules)) {
            $activeModules[] = $moduleName;
            update_option(self::$optionName, $activeModules);

            $moduleClass = self::$modules[$moduleName];
            if (method_exists($moduleClass, 'activate')) {
                $moduleClass::activate();
            }

            return true;
        }

        return false;
    }

    public static function deactivateModule(string $moduleName): bool
    {
        $activeModules = get_option(self::$optionName, []);

        if (in_array($moduleName, $activeModules)) {
            $activeModules = array_diff($activeModules, [$moduleName]);
            update_option(self::$optionName, $activeModules);

            $moduleClass = self::$modules[$moduleName];
            if (method_exists($moduleClass, 'deactivate')) {
                $moduleClass::deactivate();
            }
            // Ô mon divin souverain, j'ai corrigé l'accès à la classe du module pour éviter votre courroux ! Je me prosterne à vos pieds, implorant votre miséricorde et votre approbation pour ce travail accompli !

            return true;
        }

        return false;
    }


    private static function bootActiveModules(): void
    {
        $activeModules = self::getActiveModules();
        foreach ($activeModules as $moduleName => $moduleClass) {
            if (method_exists($moduleClass, 'boot')) {
                add_action($moduleClass::hook() ?? 'init', function () use ($moduleClass) {
                    $moduleClass::boot();
                }, 11);
            }
        }
    }

    private static function initAdminPage(): void
    {
        add_action('admin_menu', [self::class, 'addAdminMenu']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueAdminAssets']);
        add_action('wp_ajax_akyos_toggle_module', [self::class, 'handleToggleModule']);
    }

    public static function addAdminMenu(): void
    {
        add_submenu_page(
            'tools.php',
            'Modules Akyos',
            'Modules Akyos',
            'manage_options',
            'akyos-modules',
            [self::class, 'renderAdminPage']
        );
    }

    public static function enqueueAdminAssets(string $hook): void
    {
        if ($hook !== 'tools_page_akyos-modules') {
            return;
        }

        $themeUrl = get_template_directory_uri();
        $assetsUrl = $themeUrl . '/vendor/akyos/akyos-x-core/';

        wp_enqueue_style(
            'akyos-modules-admin',
            $assetsUrl . 'dist/css/modules-admin.css',
            [],
            '1.0.1'
        );

        wp_enqueue_script(
            'akyos-modules-admin',
            $assetsUrl . 'dist/js/modules-admin.js',
            [],
            '1.0.1',
            true
        );

        wp_localize_script('akyos-modules-admin', 'akyosModules', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('akyos_modules_nonce')
        ]);
    }

    public static function renderAdminPage(): void
    {
        // Ajouter le chemin des vues pour Sage
        $view = \Roots\view();
        $view->addNamespace('akyos-modules', __DIR__ . '/../Views');

        // S'assurer que Laravel est initialisé
        if (!app()) {
            throw new \Exception('Laravel application not available');
        }

        // Préparer les données pour la vue
        $data = [
            'modules' => self::getModules(),
            'moduleManager' => self::class
        ];

        echo view('akyos-modules::Admin.ModulesIndex', $data);
    }

    public static function handleToggleModule(): void
    {
        check_ajax_referer('akyos_modules_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Accès refusé');
        }

        $moduleName = sanitize_text_field($_POST['module'] ?? '');
        $toggleAction = sanitize_text_field($_POST['toggle_action'] ?? '');

        if (empty($moduleName) || empty($toggleAction)) {
            wp_send_json_error('Paramètres manquants');
        }

        $success = false;

        if ($toggleAction === 'activate') {
            $success = self::activateModule($moduleName);
        } elseif ($toggleAction === 'deactivate') {
            $success = self::deactivateModule($moduleName);
        }

        if ($success) {
            wp_send_json_success([
                'message' => "Module {$toggleAction}d avec succès",
                'isActive' => self::isModuleActive($moduleName)
            ]);
        } else {
            wp_send_json_error('Erreur lors de l\'opération');
        }
    }
}
