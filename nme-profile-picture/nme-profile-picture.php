<?php
/**
 * Plugin Name: NME Profile Picture
 * Plugin URI: http://www.netmediaeurope.com/
 * Description: Add a profile picture to a Wordpress author
 * Version: 1.0
 * Author: David Beja
 * Author URI: http://dbeja.com
 * License: GPL2
 */
 
 /*  Copyright 2014  David Beja  (email : david.beja@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !class_exists('NME_Profile_Picture') ) {
	
	class NME_Profile_Picture {
	
		private $plugin_path = '';
		
		/*
		* Constructor
		*/
		public function __construct() {
		
			// Set plugin path
			$this->plugin_path = dirname(__FILE__) . '/';
			
			// Register action for enqueuing necessary media manager files 
			add_action( 'admin_enqueue_scripts', array( &$this, 'load_media_manager' ) );
			
			// Register actions for showing profile picture field in profile page administration
			add_action( 'show_user_profile', array( &$this, 'show_profile_picture_field' ) );
			add_action( 'edit_user_profile', array( &$this, 'show_profile_picture_field' ) );
			
			// Register actions for saving profile picture fields
			add_action( 'personal_options_update', array( &$this, 'save_profile_picture_field' ) );
			add_action( 'edit_user_profile_update', array( &$this, 'save_profile_picture_field' ) );
			
			// Register filter for showing the profile picture
			add_filter( 'the_content', array( &$this, 'show_profile_picture' ), 20 );
			
		}
		
		/*
		* Activate the plugin
		*/
		public static function activate() {}
		
		/*
		* Deactive the plugin
		*/
		public static function deactivate() {}
		
		/*
		* Load Media Manager
		*/
		public function load_media_manager( $hook ) {
			
			if( 'profile.php' != $hook && 'user-edit.php' != $hook )
				return;
        
			wp_enqueue_media();

		}
		
		/*
		* Show Profile Picture Field
		*/
		public function show_profile_picture_field( $user ) {
			
			// get current profile picture
			$current_profile_picture = esc_attr( get_the_author_meta( 'nme_profile_picture', $user->ID ) );
			
			// show template
			include( $this->plugin_path . 'templates/profile_picture_field.php' );

		}
		
		/*
		* Save Profile Picture Field
		*/
		public function save_profile_picture_field( $user_id ) {
			
			// check if the user has permissions
			if ( !current_user_can( 'edit_user', $user_id ) )
				return false;
 
			update_user_meta( $user_id, 'nme_profile_picture', $_POST['nme_profile_picture_url'] );

		}
		
		/*
		* Show Profile Picture
		*/
		public function show_profile_picture( $content ) {
			
			$extra = '';
			
			// only for singular items (post, page, attachment)
			if ( is_singular() ) {
				
				$profile_picture = get_the_author_meta( 'nme_profile_picture' );
				if( !empty( $profile_picture ) ) {
					$extra = sprintf( '<img src="%s"/>', $profile_picture );
				}
				
			}
			
			return $content . $extra;

		}
		
	}

}

if ( class_exists('NME_Profile_Picture') ) {
	
	// Activation and Deactivation hooks
	register_activation_hook( __FILE__, array( 'NME_Profile_Picture', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'NME_Profile_Picture', 'deactivate' ) );
	
	// Instantiate class
	$nme_profile_picture = new NME_Profile_Picture();

}