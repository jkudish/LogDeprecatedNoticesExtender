<?php
/*
 * Plugin Name: Log Deprecated Notices Extender
 * Plugin URI: http://wordpress.org/extend/plugins/log-deprecated-notices/
 * Description: Extends Andrew Nacin's Log Deprecated Notices to show a link in the WP 3.3+ Toolbar. Upcoming version will also show notices in the Debug Bar
 * Version: 0.1
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
		 * @return null
		 */
		public function __construct() {
			$this->count();
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


		public function manageToolbar($toolbar) {

			$classes = apply_filters( 'deprecated_log_extender_classses', array() );
			$classes = implode( " ", $classes );

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
	 * make sure to init the class after other plugins have loaded
	 * and only in the admin
	 */
	add_action('admin_init', 'DeprecatedLogExtenderInit');
	function DeprecatedLogExtenderInit() {
		if ( class_exists('Deprecated_Log') )
			new Deprecated_Log_Extender();
	}

endif;