<?php
/*
 * Plugin Name: Log Deprecated Notices Extender
 * Plugin URI: http://jkudish.com/log-deprecated-notices-extender/
 * Description: WordPress plugin that extends Andrew Nacin's Log Deprecated Notices to show a link in the WP 3.3+ Toolbar.
 * Version: 0.1.2
 * Author: Joachim Kudish
 * Author URI: http://jkudish.com
 * License: GPLv2 or later
 */

/**
 * @package Deprecated_Log_Extender
 * @author  Joachim Kudish <info@jkudish.com>
 * @link 		http://jkudish.com
 *
 * credit to:  Andrew Nacin (Log Deprecated Notices)
 *
 * @todo 	make notices show up in the Debug Bar
 * @todo  show error messages if Log Deprecated Notices isn't installed
 * @todo  show error messages if WP 3.3 isn't installed
 */


 // Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

if (!class_exists( 'Deprecated_Log_Extender' ) ) :

	class Deprecated_Log_Extender {

		public $count;
		public $title;

		/**
		 * class constructors, where we hook the other stuff
		 * @return void
		 */
		public function __construct() {
			add_action('admin_bar_menu', array( &$this, 'manageToolbar'), 1001); // run it late in the game
		}

		/**
		 * count how many of notices there are and setup the title accordingly
		 * @uses  $wdb
		 * @return void
		 */
		public function count() {
			global $wpdb;
			$this->count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = %s AND post_date > %s AND post_status = %s", Deprecated_Log::pt, Deprecated_Log::$instance->options['last_viewed'], 'publish' ) );
			$this->title = ( $this->count ) ? sprintf( __( 'Deprecated Calls <small>%s</small>', 'log-deprecated' ),number_format_i18n( $this->count ) ) : __( 'Deprecated Calls', 'log-deprecated' );
		}

		/**
		 * hooks into the toolbar to add our new menu item
		 * @param  $toolbar the toolbar object
		 * @return void
		 */
		public function manageToolbar($toolbar) {

			$classes = apply_filters( 'deprecated_log_extender_classses', array() );
			$classes = implode( " ", $classes );

			$this->count();

			$toolbar->add_node( array(
				'id'     => 'deprecated_log_extender',
				'parent' => 'top-secondary',
				'title'  => apply_filters( 'deprecated_log_extender_title', $this->title ),
				'href' => add_query_arg( 'post_type', Deprecated_Log::pt, admin_url('edit.php') ),
				'meta'   => array( 'class' => $classes ),
			) );
		}

	} // end class

	/**
	 * make sure to init the class after Deprecated_Log has loaded
	 * and only in the admin
	 */
	add_action('admin_init', 'DeprecatedLogExtenderInit');
	function DeprecatedLogExtenderInit() {
		if ( class_exists('Deprecated_Log') )
			new Deprecated_Log_Extender();
	}

endif;