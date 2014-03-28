<?php
/**
 * Plugin Name: NME City Post
 * Plugin URI: http://www.netmediaeurope.com/
 * Description: Add a city name to a post
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

if ( !class_exists('NME_City_Post') ) {
	
	class NME_City_Post {
	
		const WEATHER_URL = 'http://api.openweathermap.org/data/2.5/weather?mode=xml&units=metric&q=';
	
		private $plugin_path = '';
		
		/*
		* Constructor
		*/
		public function __construct() {
		
			// Set plugin path
			$this->plugin_path = dirname(__FILE__) . '/';
			
			// create cities taxonomy on init
			add_action( 'init', array( &$this, 'create_cities_taxonomy' ), 0 );
			
			// create shortcode to show city and weather
			add_shortcode( 'nme-city-weather', array( &$this, 'show_city_weather' ) );
			
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
		* Create Cities Taxonomy
		*/
		public static function create_cities_taxonomy() {
			
			$labels = array(
				'name'                       => _x( 'Cities', 'taxonomy general name', 'nme-city-post' ),
				'singular_name'              => _x( 'City', 'taxonomy singular name', 'nme-city-post' ),
				'search_items'               => __( 'Search Cities' ),
				'popular_items'              => __( 'Popular Cities' ),
				'all_items'                  => __( 'All Cities' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => __( 'Edit City' ),
				'update_item'                => __( 'Update City' ),
				'add_new_item'               => __( 'Add New City' ),
				'new_item_name'              => __( 'New City Name' ),
				'separate_items_with_commas' => __( 'Separate cities with commas' ),
				'add_or_remove_items'        => __( 'Add or remove cities' ),
				'choose_from_most_used'      => __( 'Choose from the most used cities' ),
				'not_found'                  => __( 'No cities found.' ),
				'menu_name'                  => __( 'Cities' ),
			);
			
			$args = array(
				'public'				=> true,
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_admin_column'     => true,
				'rewrite'               => array( 'slug' => 'city' ),
			);
			
			register_taxonomy( 'nme-city', 'post', $args );

		}
		
		/*
		* Show City Weather
		*/
		public function show_city_weather( $atts, $content = null ) {

			// get cities of the current post
			$cities = get_the_terms( get_the_ID(), 'nme-city' );
			
			// if more than one city, read only the first one
			if( is_array( $cities ) ) {
				$city = array_shift( array_values( $cities ) );
			}
			
			// read temperature from openweathermap.org api
			if( !empty( $city ) ) {
				$response = wp_remote_get( self::WEATHER_URL . $city->name );
				$response_xml = simplexml_load_string( $response['body'] );
				$temperature = $response_xml->temperature->attributes()->value;
			}
			
			// output buffering of city weather template
			ob_start();
			include( $this->plugin_path . 'templates/city_weather.php' );
			return ob_get_clean();

		}
		
	}

}

if ( class_exists('NME_City_Post') ) {
	
	// Activation and Deactivation hooks
	register_activation_hook( __FILE__, array( 'NME_City_Post', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'NME_City_Post', 'deactivate' ) );
	
	// Instantiate class
	$nme_city_post = new NME_City_Post();

}