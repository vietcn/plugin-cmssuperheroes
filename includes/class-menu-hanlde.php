<?php

if (!defined('ABSPATH')) {
    die();
}
if (!class_exists('EFramework_menu_handle')) {
    class EFramework_menu_handle
    {
        /**
         * EFramework_menu_handle->theme_name
         * private
         * @var string
         */
        protected $theme_name = '';

        /**
         * EFramework_menu_handle->theme_text_domain
         * private
         * @var string
         */
        protected $theme_text_domain = '';

        public function __construct()
        {
            $current_theme = wp_get_theme();
            $this->theme_name = $current_theme->get('Name');
            $this->theme_text_domain = $current_theme->get('TextDomain');

            add_action('admin_menu', array($this, 'abtheme_add_menu'));

            add_action('admin_bar_menu', array($this, 'abtheme_add_admin_bar_menu'), 100);

            add_filter('abtheme_export_mode', function () {
                return true;
            });
        }

        public function abtheme_add_menu()
        {
            add_menu_page($this->theme_name, $this->theme_name, 'manage_options', $this->theme_text_domain, array($this, 'abtheme_create_theme_dashboard'), 'dashicons-admin-generic', 3);

            add_submenu_page($this->theme_text_domain, $this->theme_name, esc_html__('Dashboard', 'abtheme'), 'manage_options', $this->theme_text_domain, array($this, 'abtheme_create_theme_dashboard'));

            if (is_plugin_active('theme-core-import-export/theme-core-import-export.php')) {
                add_submenu_page($this->theme_text_domain, esc_html__('Import Demos', 'abtheme'), esc_html__('Import Demos', 'abtheme'), 'manage_options', 'abtheme-import', array($this, 'abtheme_import_demo_page'));
            }

        }

        public function abtheme_create_theme_dashboard()
        {
            include_once abtheme()->path('APP_DIR', 'templates/dashboard/dashboard.php');
        }

        public function abtheme_import_demo_page()
        {
            $export_mode = $this->abtheme_enable_export_mode();
            include_once abtheme()->path('APP_DIR', 'templates/dashboard/import-page.php');
        }

        function abtheme_enable_export_mode()
        {
            return apply_filters('abtheme_export_mode', false);
        }

        function abtheme_add_admin_bar_menu($wp_admin_bar)
        {
            $theme = wp_get_theme();
            /**
             * Add "Theme Name" parent node
             */
            $opt_name = abtheme_get_opt_name();
            $args = array(
                'id'    => $theme->get("TextDomain"),
                'title' => '<span class="ab-icon dashicons-smiley"></span>' . $theme->get("Name"),
                'href'  => admin_url('admin.php?page=' . $theme->get("TextDomain")),
                'meta'  => array(
                    'class' => 'dashicons dashicons-admin-generic',
                    'title' => $theme->get("TextDomain"),
                )
            );
            $wp_admin_bar->add_node($args);
            /**
             * Add Dashboard children node
             */
            $args = array(
                'id'     => 'dashboard',
                'title'  => esc_html__('Dashboard', 'abtheme'),
                'href'   => admin_url('admin.php?page=' . $theme->get("TextDomain")),
                'parent' => $theme->get("TextDomain"),
                'meta'   => array(
                    'class' => '',
                    'title' => esc_html__('Dashboard', 'abtheme'),
                )
            );
            $wp_admin_bar->add_node($args);

            /**
             * Add Import Export children node
             */
            if (is_plugin_active('theme-core-import-export/theme-core-import-export.php')) {
                $args = array(
                    'id'     => 'abtheme-import',
                    'title'  => esc_html__('Import Demos', 'abtheme'),
                    'href'   => admin_url('admin.php?page=abtheme-import'),
                    'parent' => $theme->get("TextDomain"),
                    'meta'   => array(
                        'class' => '',
                        'title' => esc_html__('Import Demos', 'abtheme'),
                    )
                );
                $wp_admin_bar->add_node($args);
            }

            /**
             * Add Theme options children node
             */
            $args = array(
                'id'     => 'theme-options',
                'title'  => esc_html__('Theme Options', 'abtheme'),
                'href'   => admin_url('admin.php?page=theme-options'),
                'parent' => $theme->get("TextDomain"),
                'meta'   => array(
                    'class' => '',
                    'title' => esc_html__('Import Demos', 'abtheme'),
                )
            );
            $wp_admin_bar->add_node($args);
        }
    }
}
new EFramework_menu_handle();