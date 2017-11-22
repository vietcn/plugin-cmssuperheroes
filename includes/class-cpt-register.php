<?php
/**
 * Custom post types register.
 * @since   1.0
 * @author  KP
 *
 */

class EFramework_CPT_Register
{
    /**
     * Core singleton class
     *
     * @var self - pattern realization
     * @access private
     */
    private static $_instance;

    /**
     * Store supported post types in an array
     * @var array
     * @access private
     */
    private $post_types = array();

    /**
     * Constructor
     *
     * @access private
     */
    private function __construct()
    {
        add_action('init', array($this, 'init'), 0);
    }

    /**
     * init hook - 0
     */
    function init()
    {
        $this->post_types = apply_filters('cmssuperheroes_extra_post_types', array(
            'portfolio'   => false,
            'team_member' => false
        ));

        if (isset($this->post_types['portfolio']) && $this->post_types['portfolio']) {
            $this->type_portfolio_register();
            flush_rewrite_rules();
        }

        if (isset($this->post_types['team_member']) && $this->post_types['team_member']) {
            $this->type_team_member_register();
        }
    }

    /**
     * Registers portfolio post type, this fuction should be called in an init hook function.
     * @uses $wp_post_types Inserts new post type object into the list
     *
     * @access protected
     */
    protected function type_portfolio_register()
    {

        $portfolio_slug = function_exists('abtheme_get_opt') ? abtheme_get_opt('portfolio_slug','portfolio') : 'portfolio';
        $args = apply_filters('cmssuperheroes_portfolio_post_type_args', array(
            'labels'              => array(
                'name'                  => __('Portfolio', 'cmssuperheroes'),
                'singular_name'         => __('Portfolio Entry', 'cmssuperheroes'),
                'add_new'               => _x('Add New', 'add new on admin panel', 'cmssuperheroes'),
                'add_new_item'          => __('Add New Portfolio Entry', 'cmssuperheroes'),
                'edit_item'             => __('Edit Portfolio Entry', 'cmssuperheroes'),
                'new_item'              => __('New Portfolio Entry', 'cmssuperheroes'),
                'view_item'             => __('View Portfolio Entry', 'cmssuperheroes'),
                'view_items'            => __('View Portfolio Entries', 'cmssuperheroes'),
                'search_items'          => __('Search Portfolio Entries', 'cmssuperheroes'),
                'not_found'             => __('No Portfolio Entries Found', 'cmssuperheroes'),
                'not_found_in_trash'    => __('No Portfolio Entries Found in Trash', 'cmssuperheroes'),
                'parent_item_colon'     => __('Parent Portfolio Entry:', 'cmssuperheroes'),
                'all_items'             => __('All Entries', 'cmssuperheroes'),
                'archives'              => __('Portfolio Archives', 'cmssuperheroes'),
                'attributes'            => __('Portfolio Entry Attributes', 'cmssuperheroes'),
                'insert_into_item'      => __('Insert into Portfolio Entry', 'cmssuperheroes'),
                'uploaded_to_this_item' => __('Uploaded to this Portfolio Entry', 'cmssuperheroes'),
                'menu_name'             => __('Portfolio', 'cmssuperheroes'),
                'filter_items_list'     => __('Filter portfolio list', 'cmssuperheroes'),
                'items_list_navigation' => __('Portfolio list navigation', 'cmssuperheroes'),
                'items_list'            => __('Portfolio list', 'cmssuperheroes'),
                'name_admin_bar'        => _x('Portfolio', 'add new on admin bar', 'cmssuperheroes')
            ),
            'hierarchical'        => false,
            'description'         => '',
            'taxonomies'          => array('portfolio_category'),
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => null,
            'menu_icon'           => 'dashicons-feedback',
            'show_in_nav_menus'   => true,
            'publicly_queryable'  => true,
            'exclude_from_search' => false,
            'has_archive'         => true,
            'query_var'           => true,
            'can_export'          => true,
            'rewrite'             => array(
                'slug'       => $portfolio_slug,
                'with_front' => false,
                'pages'      => true
            ),
            'capability_type'     => 'post',
            'supports'            => array(
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'revisions'
            )
        ));
        register_post_type('cms-portfolio', $args);
    }

    /**
     * Registers team member post type, this fuction should be called in an init hook function.
     * @uses $wp_post_types Inserts new post type object into the list
     *
     * @access protected
     */
    protected function type_team_member_register()
    {
        $args = apply_filters('cmssuperheroes_team_member_post_type_args', array(
            'labels'              => array(
                'name'                  => __('Team', 'cmssuperheroes'),
                'singular_name'         => __('Team Member', 'cmssuperheroes'),
                'add_new'               => _x('Add New', 'add new on admin panel', 'cmssuperheroes'),
                'add_new_item'          => __('Add New Member', 'cmssuperheroes'),
                'edit_item'             => __('Edit Member', 'cmssuperheroes'),
                'new_item'              => __('New Member', 'cmssuperheroes'),
                'view_item'             => __('View Member', 'cmssuperheroes'),
                'view_items'            => __('View Members', 'cmssuperheroes'),
                'search_items'          => __('Search Members', 'cmssuperheroes'),
                'not_found'             => __('No Members Found', 'cmssuperheroes'),
                'not_found_in_trash'    => __('No Members Found in Trash', 'cmssuperheroes'),
                'parent_item_colon'     => __('Parent Member:', 'cmssuperheroes'),
                'all_items'             => __('All Members', 'cmssuperheroes'),
                'archives'              => __('Team Archives', 'cmssuperheroes'),
                'attributes'            => __('Member Attributes', 'cmssuperheroes'),
                'insert_into_item'      => __('Insert into Member', 'cmssuperheroes'),
                'uploaded_to_this_item' => __('Uploaded to this Member', 'cmssuperheroes'),
                'menu_name'             => __('Team', 'cmssuperheroes'),
                'filter_items_list'     => __('Filter Members list', 'cmssuperheroes'),
                'items_list_navigation' => __('Members list navigation', 'cmssuperheroes'),
                'items_list'            => __('Members list', 'cmssuperheroes'),
                'name_admin_bar'        => _x('Team', 'add new on admin bar', 'cmssuperheroes'),
            ),
            'hierarchical'        => false,
            'description'         => '',
            'taxonomies'          => array(),
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => null,
            'menu_icon'           => 'dashicons-groups',
            'show_in_nav_menus'   => true,
            'publicly_queryable'  => true,
            'exclude_from_search' => false,
            'has_archive'         => true,
            'query_var'           => true,
            'can_export'          => true,
            'rewrite'             => true,
            'capability_type'     => 'post',
            'supports'            => array(
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'revisions'
            ),
        ));

        register_post_type('eteam_member', $args);
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