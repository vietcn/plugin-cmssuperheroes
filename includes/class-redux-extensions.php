<?php
/**
 * @Template: class-redux-extensions.php
 * @since: 1.0.0
 * @author: KP
 * @descriptions:
 * @create: 23-Dec-17
 */
if (!defined('ABSPATH')) {
    die();
}
if (!class_exists('CMS_Redux_Extensions')) {
    class CMS_Redux_Extensions
    {
        public static $instance = null;
        public $file;
        public $basename;
        public $plugin_directory;
        public $plugin_directory_uri;
        public $extensions;
        public $extensions_url;

        public static function instance()
        {
            if (!class_exists('ReduxFramework')) {
                return;
            }
            if (is_null(self::$instance)) {
                self::$instance = new CMS_Redux_Extensions();
                self::$instance->define_variable();
                self::$instance->extensions_actions();
            }

            return self::$instance;
        }

        private function define_variable()
        {
            $this->extensions = cmssuperheroes()->path('APP_DIR') . '/includes/extensions/';
            $this->extensions_url = cmssuperheroes()->path('APP_URL') . '/includes/extensions/';
        }

        private function extensions_actions()
        {
            add_action('redux/extensions/before', array($this, 'cms_register_extensions'));
        }

        function cms_register_extensions($ReduxFramework)
        {
            $path = $this->extensions;
            $folders = scandir($path, 1);

            foreach ($folders as $folder) {
                if ($folder === '.' or $folder === '..' or !is_dir($path . $folder)) {
                    continue;
                }
                $extension_class = 'CMS_Redux_Extensions_' . $folder;

                if (!class_exists($extension_class)) {
                    // In case you wanted override your override, hah.
                    $class_file = $path . $folder . '/extension_' . $folder . '.php';
                    $class_file = apply_filters('redux/extension/' . $ReduxFramework->args['opt_name'] . '/' . $folder, $class_file);
                    if ($class_file) {
                        require_once($class_file);
                    }
                }

                if (!isset($ReduxFramework->extensions[$folder])) {
                    $ReduxFramework->extensions[$folder] = new $extension_class($ReduxFramework);
                }
            }
        }
    }
}

if (!function_exists('cms_redux_extensions')) {
    function cms_redux_extensions()
    {
        return CMS_Redux_Extensions::instance();
    }
}

$GLOBALS['cms_redux_extensions'] = cms_redux_extensions();