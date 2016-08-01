<?php
/*
Plugin Name: WordPress JackRabbit Schedule
Plugin URI: http://whoischris.com
Description:  Use settings and shortcodes to display JackRabbit Scheduler class registration scripts on pages and posts
Author: Chris Flannagan
Version: 1.0
Author URI: http://whoischris.com/
*/

if( !class_exists( 'WP_JackRabbit_Scheduler' ) ) {

    class WP_JackRabbit_Scheduler
    {
        const PLUGIN_SLUG = 'wp-jackrabbit-scheduler';
        const PLUGIN_ABBR = 'wpjrs';
        public $jrsc;

        /**
         * Construct the plugin object
         */
        public function __construct()
        {
            // register actions
            add_action( 'admin_init', array( &$this, 'admin_init' ) );
            add_action( 'admin_menu', array( &$this, 'add_controls' ) );
            // register shortcode
            include_once 'wp-jackrabbit-shortcode.php';
            $this->jrsc = new JR_SC();
            add_shortcode( 'wpjackrabbit', array( $this->jrsc, 'jackrabbit_shorcode_func' ) );

        } // END public function __construct

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            global $wpdb;
            $main_prefix = $wpdb->get_blog_prefix( BLOG_ID_CURRENT_SITE );

            $table_name = $main_prefix . self::PLUGIN_ABBR . 'blogdata';

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
  				  id mediumint(9) NOT NULL AUTO_INCREMENT,
				  blogid mediumint(9) DEFAULT '0' NOT NULL,
				  sname varchar(255) NOT NULL,
				  scode mediumint(9) DEFAULT '0' NOT NULL,
				  scat1 varchar(115) NOT NULL,
				  scat2 varchar(115) NOT NULL,
				  UNIQUE KEY id (id)
				) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );

            $table_name = $main_prefix . self::PLUGIN_ABBR . 'customattr';
            $sql = "CREATE TABLE $table_name (
				  id mediumint(9) NOT NULL AUTO_INCREMENT,
				  blogid mediumint(9) DEFAULT '0' NOT NULL,
				  scode mediumint(9) DEFAULT '0' NOT NULL,
				  sattr varchar(55) NOT NULL,
				  sval varchar(55) NOT NULL,
				  UNIQUE KEY id (id)
				) $charset_collate;";

            dbDelta( $sql );
        } // END public static function activate

        /**
         * Deactivate the plugin
         */
        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate

        public function add_controls()
        {

            wp_enqueue_style( 'style-name', plugin_dir_url( __FILE__ ) . 'css/admin-css.css' );
            wp_enqueue_script( 'wpjr-admin-js', plugin_dir_url( __FILE__ ) . 'js/admin-js.js', array( 'jquery' ), '1.0', true );
            //Place a link to our settings page under the Wordpress "Settings" menu
            add_menu_page( 'WP JackRabbit Scheduler', 'JackRabbit Scheduler', 'manage_options', self::PLUGIN_SLUG . '-tool', array( $this, 'tool_page' ) );

        }

        public function tool_page()
        {

            //Include our settings page template
            include(sprintf("%s/%s-tool.php", dirname(__FILE__), self::PLUGIN_SLUG));

        }
        
        public function display_tables()
        {
    
        }

        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()
        {
        } // END public static function activate

    }
} // END if(!class_exists('WP_Form_Import'));

// Add a link to the settings page onto the plugin page
if ( class_exists( 'WP_JackRabbit_Scheduler' ) )
{
    // Installation and uninstallation hooks
    register_activation_hook( __FILE__, array( 'WP_JackRabbit_Scheduler', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'WP_JackRabbit_Scheduler', 'deactivate' ) );

    // instantiate the plugin class
    $WP_JackRabbit_Scheduler = new WP_JackRabbit_Scheduler();
}