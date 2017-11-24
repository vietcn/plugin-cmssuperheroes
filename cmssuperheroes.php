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
/**
 * Require functions on plugin
 */
require_once CMS_INCLUDES . "functions.php";
/**
 * Use CmssuperheroesCore class
 */
new CmssuperheroesCore();

/**
 * Cmssuperheroes Class
 *
 */
class CmssuperheroesCore
{
    public function __construct()
    {
        /**
         * Init function, which is run on site init and plugin loaded
         */
        add_action('init', array($this, 'cmsInit'));
        add_action('plugins_loaded', array($this, 'cmsActionInit'));
        add_filter('style_loader_tag', array($this, 'cmsValidateStylesheet'));


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
}

?>