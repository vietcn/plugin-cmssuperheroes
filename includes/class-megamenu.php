<?php
/**
 * Mega menu register
 *
 * @since   1.0
 * @author  KP
 *
 */

class EFramework_MegaMenu_Register
{
    /**
     * Core singleton class
     *
     * @var self - pattern realization
     * @access private
     */
    private static $_instance;


    /**
     * Constructor
     *
     * @access private
     */
    function __construct()
    {
        add_action('init', array($this, 'init'), 0);
        // Custom Fields - Add
        add_filter('wp_setup_nav_menu_item', array($this, 'setup_nav_menu_item'));

        // Custom Fields - Save
//        add_action('wp_update_nav_menu_item', array($this, 'update_nav_menu_item'), 100, 3);

        // Custom Walker - Edit
        add_filter('wp_edit_nav_menu_walker', array($this, 'edit_nav_menu_walker'), 100, 2);
    }

    function init()
    {
        $labels = array(
            'name'                  => _x('Mega Menus', 'Post Type General Name', 'cmssuperheroes'),
            'singular_name'         => _x('Mega Menu', 'Post Type Singular Name', 'cmssuperheroes'),
            'menu_name'             => __('Mega Menus', 'cmssuperheroes'),
            'name_admin_bar'        => __('Mega Menus', 'cmssuperheroes'),
            'archives'              => __('Item Archives', 'cmssuperheroes'),
            'parent_item_colon'     => __('Parent Item:', 'cmssuperheroes'),
            'all_items'             => __('All Items', 'cmssuperheroes'),
            'add_new_item'          => __('Add New Mega Menu', 'cmssuperheroes'),
            'add_new'               => __('Add New', 'cmssuperheroes'),
            'new_item'              => __('New Mega Menu', 'cmssuperheroes'),
            'edit_item'             => __('Edit Mega Menu', 'cmssuperheroes'),
            'update_item'           => __('Update Mega Menu', 'cmssuperheroes'),
            'view_item'             => __('View Mega Menu', 'cmssuperheroes'),
            'search_items'          => __('Search Mega Menu', 'cmssuperheroes'),
            'not_found'             => __('Not found', 'cmssuperheroes'),
            'not_found_in_trash'    => __('Not found in Trash', 'cmssuperheroes'),
            'featured_image'        => __('Featured Image', 'cmssuperheroes'),
            'set_featured_image'    => __('Set featured image', 'cmssuperheroes'),
            'remove_featured_image' => __('Remove featured image', 'cmssuperheroes'),
            'use_featured_image'    => __('Use as featured image', 'cmssuperheroes'),
            'insert_into_item'      => __('Insert into item', 'cmssuperheroes'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'cmssuperheroes'),
            'items_list'            => __('Items list', 'cmssuperheroes'),
            'items_list_navigation' => __('Items list navigation', 'cmssuperheroes'),
            'filter_items_list'     => __('Filter items list', 'cmssuperheroes'),
        );
        $args = array(
            'label'               => __('Mega Menu', 'cmssuperheroes'),
            'labels'              => $labels,
            'supports'            => array('title', 'editor', 'revisions',),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 25,
            'menu_icon'           => 'dashicons-align-center',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'rewrite'             => false,
            'capability_type'     => 'page',
        );
        register_post_type('cms-mega-menu', $args);
    }


    // Custom Fields - Add
    function setup_nav_menu_item( $menu_item ) {

        $menu_item->cms_megaprofile = get_post_meta( $menu_item->ID, '_menu_item_cms_megaprofile', true );
        $menu_item->cms_icon = get_post_meta( $menu_item->ID, '_menu_item_cms_icon', true );
        $menu_item->cms_onepage = get_post_meta( $menu_item->ID, '_menu_item_cms_onepage', true );
        return $menu_item;
    }

    // Custom Fields - Save
    function update_nav_menu_item( $menu_id, $menu_item_db_id, $menu_item_data ) {

        if ( isset( $_REQUEST['menu-item-rella-megaprofile'][$menu_item_db_id]) ) {
            update_post_meta($menu_item_db_id, '_menu_item_cms_megaprofile', $_REQUEST['menu-item-rella-megaprofile'][$menu_item_db_id]);
        }
        if ( isset( $_REQUEST['menu-item-rella-icon'][$menu_item_db_id]) ) {
            update_post_meta($menu_item_db_id, '_menu_item_cms_icon', $_REQUEST['menu-item-rella-icon'][$menu_item_db_id]);
        }

        if ( isset( $_REQUEST['menu-item-rella-icon-position'][$menu_item_db_id]) ) {
            update_post_meta($menu_item_db_id, '_menu_item_cms_onepage', $_REQUEST['menu-item-rella-icon-position'][$menu_item_db_id]);
        }
    }

    // Custom Backend Walker - Edit
    function edit_nav_menu_walker( $walker, $menu_id ) {

        if ( ! class_exists( 'EFramework_Mega_Menu_Edit_Walker' ) ) {
            require_once( CMS_INCLUDES . 'class-mage-menu-edit.php' );
        }

        return 'EFramework_Mega_Menu_Edit_Walker';
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