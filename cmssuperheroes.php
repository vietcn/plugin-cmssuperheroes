<?php
/**
 *
 * Plugin Name: Cmssuperheroes
 * Plugin URI: http://cmssuperheroes.com
 * Description: This plugin is package compilation some addons, which is developed by Cmssuperheroes Team for Visual Comporser plugin.
 * Version: 1.1
 * Author: CMS Hero
 * Author URI: http://cmssuperheroes.com
 * Copyright 2017 Cmssuperheroes.com. All rights reserved.
 * Text Domain: cmssuperheroes
 */
define('CMS_NAME', 'cmssuperheroes');
define('CMS_DIR', plugin_dir_path(__FILE__));
define('CMS_URL', plugin_dir_url(__FILE__));
define('CMS_LIBRARIES', CMS_DIR . "libraries" . DIRECTORY_SEPARATOR);
define('CMS_LANGUAGES', CMS_DIR . "languages" . DIRECTORY_SEPARATOR);
define('CMS_TEMPLATES', CMS_DIR . "templates" . DIRECTORY_SEPARATOR);
define('CMS_INCLUDES', CMS_DIR . "includes" . DIRECTORY_SEPARATOR);

define('CMS_CSS', CMS_URL . "assets/css/");
define('CMS_JS', CMS_URL . "assets/js/");
define('CMS_IMAGES', CMS_URL . "assets/images/");
define('CMS_TEXT_DOMAIN', 'cmssuperheroes');
/**
 * Require functions on plugin
 */
require_once CMS_INCLUDES . "functions.php";
//new CmssuperheroesCore();

/**
 * Cmssuperheroes Class
 *
 */
class CmssuperheroesCore
{
    /**
     * Core singleton class
     *
     * @var self - pattern realization
     * @access private
     */
    private static $_instance;

    /**
     * Store plugin paths
     *
     * @since 1.0
     * @access private
     * @var array
     */
    private $paths = array();

    protected $post_metabox = null;

    protected $post_format_metabox = null;

    protected $taxonomy_metabox = null;

    public function __construct()
    {
        $dir = untrailingslashit(plugin_dir_path(__FILE__));
        $url = untrailingslashit(plugin_dir_url(__FILE__));

        $this->set_paths(array(
            'APP_DIR' => $dir,
            'APP_URL' => $url
        ));

        /**
         * Init function, which is run on site init and plugin loaded
         */
        add_action('init', array($this, 'cmsInit'));
        add_action('plugins_loaded', array($this, 'cmsActionInit'));
        add_filter('style_loader_tag', array($this, 'cmsValidateStylesheet'));
        register_activation_hook(__FILE__, array($this, 'activation_hook'));

        if (!class_exists('ReduxFramework')) {
            add_action('admin_notices', array($this, 'redux_framework_notice'));
        } else {
            // Late at 30 to be sure that other extensions available via same hook.
            // Eg: Load extensions at 29 or lower.
            add_action("redux/extensions/before", array($this, 'redux_extensions'), 30);
        }
        if (!class_exists('EFramework_enqueue_scripts')) {
            require_once $this->path('APP_DIR', 'includes/class-enqueue-scripts.php');
        }

        if (!class_exists('EFramework_CPT_Register')) {
            require_once CMS_INCLUDES . 'class-cpt-register.php';
            EFramework_CPT_Register::get_instance();
        }

        if (!class_exists('EFramework_CTax_Register')) {
            require_once CMS_INCLUDES . 'class-ctax-register.php';
            EFramework_CTax_Register::get_instance();
        }

        if (!class_exists('EFramework_MegaMenu_Register')) {
            require_once CMS_INCLUDES . 'mega-menu/class-megamenu.php';
            EFramework_MegaMenu_Register::get_instance();
        }


        if (!class_exists('EFramework_menu_handle')) {
            require_once CMS_INCLUDES . 'class-menu-hanlde.php';
        }

        /**
         * Enqueue Scripts on plugin
         */
        add_action('wp_enqueue_scripts', array($this, 'cms_register_style'));
        add_action('wp_enqueue_scripts', array($this, 'cms_register_script'));
        add_action('admin_enqueue_scripts', array($this, 'cms_admin_script'));

        /**
         * Visual Composer action
         */
        add_action('vc_before_init', array($this, 'cmsShortcodeRegister'));
        add_action('vc_after_init', array($this, 'cmsShortcodeAddParams'), 11);

        /**
         * widget text apply shortcode
         */
        add_filter('widget_text', 'do_shortcode');
    }

    function cmsActionInit()
    {
        global $wp_filesystem;
        // Localization
        load_plugin_textdomain(CMS_NAME, false, CMS_LANGUAGES);

        /* Add WP_Filesystem. */
        if (!class_exists('WP_Filesystem')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            WP_Filesystem();
        }
    }

    function cmsInit()
    {
        if (apply_filters('abtheme_scssc_on', false)) {
            // scss compiler library
            if (!class_exists('scssc')) {
                require_once CMS_LIBRARIES . 'scss.inc.php';
            }
        }
    }

    function cmsShortcodeRegister()
    {
        //Load required libararies
        require_once CMS_INCLUDES . 'cms_shortcodes.php';
    }

    function cmsShortcodeAddParams()
    {
        $extra_params_folder = get_template_directory() . '/vc_params';
        $files = cmsFileScanDirectory($extra_params_folder, '/cms_.*\.php/');
        if (!empty($files)) {
            foreach ($files as $file) {
                if (WPBMap::exists($file->name)) {
                    if (isset($params)) {
                        unset($params);
                    }
                    include $file->uri;
                    if (isset($params) && is_array($params)) {
                        foreach ($params as $param) {
                            if (is_array($param)) {
                                $param['group'] = __('Template', CMS_NAME);
                                $param['edit_field_class'] = isset($param['edit_field_class']) ? $param['edit_field_class'] . ' cms_custom_param vc_col-sm-12 vc_column' : 'cms_custom_param vc_col-sm-12 vc_column';
                                $param['class'] = 'cms-extra-param';
                                if (isset($param['template']) && !empty($param['template'])) {
                                    if (!is_array($param['template'])) {
                                        $param['template'] = array($param['template']);
                                    }
                                    $param['dependency'] = array("element" => "cms_template", "value" => $param['template']);

                                }
                                vc_add_param($file->name, $param);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Function register stylesheet on plugin
     */
    function cms_register_style()
    {
        wp_enqueue_style('cms-plugin-stylesheet', CMS_CSS . 'cms-style.css');
    }

    /**
     * replace rel on stylesheet (Fix validator link style tag attribute)
     */
    function cmsValidateStylesheet($src)
    {
        if (strstr($src, 'widget_search_modal-css') || strstr($src, 'owl-carousel-css') || strstr($src, 'vc_google_fonts')) {
            return str_replace('rel', 'property="stylesheet" rel', $src);
        } else {
            return $src;
        }
    }

    /**
     * Function register script on plugin
     */
    function cms_register_script()
    {
        wp_register_script('modernizr', CMS_JS . 'modernizr.min.js', array('jquery'));
        wp_register_script('waypoints', CMS_JS . 'waypoints.min.js', array('jquery'));
        wp_register_script('imagesloaded', CMS_JS . 'jquery.imagesloaded.js', array('jquery'));
        wp_register_script('jquery-shuffle', CMS_JS . 'jquery.shuffle.js', array('jquery', 'modernizr', 'imagesloaded'));
        if (file_exists(get_template_directory() . '/assets/js/jquery.shuffle.cms.js')) {
            wp_register_script('cms-jquery-shuffle', get_template_directory_uri() . '/assets/js/jquery.shuffle.cms.js', array('jquery-shuffle'));
        } else {
            wp_register_script('cms-jquery-shuffle', CMS_JS . 'jquery.shuffle.cms.js', array('jquery-shuffle'));
        }
    }

    /**
     * Function register admin on plugin
     */
    function cms_admin_script()
    {
        wp_enqueue_style('admin-style', CMS_CSS . 'admin.css', array(), '1.0.0');
        wp_enqueue_style('font-awesome', CMS_CSS . 'font-awesome.min.css', array(), 'all');
    }

    /**
     * Setter for paths
     *
     * @since  1.0
     * @access protected
     *
     * @param array $paths
     */
    protected function set_paths($paths = array())
    {
        $this->paths = $paths;
    }

    /**
     * Gets absolute path for file/directory in filesystem.
     *
     * @since  1.0
     * @access public
     *
     * @param string $name - name of path path
     * @param string $file - file name or directory inside path
     *
     * @return string
     */
    function path($name, $file = '')
    {
        return $this->paths[$name] . (strlen($file) > 0 ? '/' . preg_replace('/^\//', '', $file) : '');
    }

    /**
     * Get url for asset files
     *
     * @since  1.0
     * @access public
     *
     * @param  string $file - filename
     * @return string
     */
    function get_url($file = '')
    {
        return esc_url($this->path('APP_URL', $file));
    }

    /**
     * Get template file full path
     * @param  string $file
     * @param  string $default
     * @return string
     */
    function get_template($file, $default)
    {
        $path = locate_template($file);
        if ($path) {
            return $path;
        }
        return $default;
    }

    function is_min()
    {
        $dev_mode = defined('WP_DEBUG') && WP_DEBUG;
        if ($dev_mode) {
            return '';
        } else {
            return '.min';
        }
    }


    /**
     * Redux Framework notices
     *
     * @since 1.0
     * @access public
     */
    function redux_framework_notice()
    {
        $plugin_name = '<strong>' . esc_html__("Cmssupperheroes", CMS_TEXT_DOMAIN) . '</strong>';
        $redux_name = '<strong>' . esc_html__("Redux Framework", CMS_TEXT_DOMAIN) . '</strong>';

        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p>';
        printf(
            esc_html__('%1$s require %2$s installed and activated. Please active %3$s plugin', CMS_TEXT_DOMAIN),
            $plugin_name,
            $redux_name,
            $redux_name
        );
        echo '</p>';
        printf('<button type="button" class="notice-dismiss"><span class="screen-reader-text">%s</span></button>', esc_html__('Dismiss this notice.', CMS_TEXT_DOMAIN));
        echo '</div>';
    }


    /**
     * Action handle when active plugin
     *
     * Check Redux framework active
     */
    function activation_hook()
    {
        if (is_admin()) {
            if (!is_plugin_active('redux-framework/redux-framework.php')) {
                deactivate_plugins(plugin_basename(__FILE__));

                $plugin_name = '<strong>' . esc_html__("Cmssupperheroes", CMS_TEXT_DOMAIN) . '</strong>';
                $redux_name = '<strong>' . esc_html__("Redux Framework", CMS_TEXT_DOMAIN) . '</strong>';
                ob_start();

                printf(
                    esc_html__('%1$s requires %2$s installed and activated. Currently it is either not installed or installed but not activated. Please follow these steps to get %1$s up and working:', CMS_TEXT_DOMAIN),
                    $plugin_name,
                    $redux_name
                );

                printf(
                    "<br/><br/>1. " . esc_html__('Go to %1$s to check if %2$s is installed. If it is, activate it then activate %3$s.', CMS_TEXT_DOMAIN),
                    sprintf('<strong><a href="%1$s">%2$s</a></strong>', esc_url(admin_url('plugins.php')), esc_html__('Plugins/Installed Plugins', CMS_TEXT_DOMAIN)),
                    $redux_name,
                    $plugin_name
                );

                printf(
                    "<br/><br/>2. " . esc_html__('If %1$s is not installed, go to %2$s, search for %1$s, install and activate %1$s, then activate %3$s.', CMS_TEXT_DOMAIN),
                    $redux_name,
                    sprintf('<strong><a href="%1$s">%2$s</a></strong>', esc_url(admin_url('plugin-install.php?s=Redux+Framework&tab=search&type=term')), esc_html__('Plugins/Add New')),
                    $plugin_name
                );

                $message = ob_get_clean();

                wp_die($message, esc_html__('Plugin Activation Error', CMS_TEXT_DOMAIN), array('back_link' => true));
            }
        }
    }


    /**
     * Load our ReduxFramework extensions
     *
     * @since 1.0
     * @param  ReduxFramework $redux
     */
    function redux_extensions($redux)
    {
        if (!class_exists('EFramework_Post_Metabox')) {
            require_once $this->path('APP_DIR', 'includes/class-post-metabox.php');

            if (empty($this->post_metabox)) {
                $this->post_metabox = new EFramework_Post_Metabox($redux);
            }
        }

        if (!class_exists('EFramework_Taxonomy_Metabox')) {
            require_once $this->path('APP_DIR', 'includes/class-taxonomy-metabox.php');

            if (empty($this->taxonomy_metabox)) {
                $this->taxonomy_metabox = new EFramework_Taxonomy_Metabox($redux);
            }
        }
    }

    /**
     * Get instance of the class
     *
     * @access public
     * @return object this
     */
    public static function get_instance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}


/**
 * Get instance of CmssuperheroesCore
 *
 * @since  1.0
 * @return CmssuperheroesCore instance
 */
function cmssuperheroes()
{
    return CmssuperheroesCore::get_instance();
}

$GLOBALS['cmssuperheroes'] = cmssuperheroes();

?>