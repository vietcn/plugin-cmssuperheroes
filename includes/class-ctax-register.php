<?php
/**
 * Custom taxonomies register
 *
 * @package eFramework
 * @since   1.0
 */

class EFramework_CTax_Register
{
    /**
     * Core singleton class
     * 
     * @var self - pattern realization
     * @access private
     */
    private static $_instance;

    /**
     * Store supported taxonomies in an array
     * @var array
     * @access private
     */
    private $taxonomies = array();

    /**
     * Constructor
     *
     * @access private
     */
    function __construct()
    {
        add_action( 'init', array( $this, 'init' ), 0 );
    }

    /**
     * init hook - 0
     */
    function init()
    {
        $this->taxonomies = apply_filters( 'abtheme_extra_taxonomies', array(
            'portfolio_category' => false,
            'team_member_position' => false
        ) );

        if ( isset( $this->taxonomies['portfolio'] ) && $this->taxonomies['portfolio'] )
        {
            $this->type_portfolio_register();
        }

        if ( isset( $this->taxonomies['team_member'] ) && $this->taxonomies['team_member'] )
        {
            $this->type_team_member_register();
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
        if ( ! ( self::$_instance instanceof self ) )
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}