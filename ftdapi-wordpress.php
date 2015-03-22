<?php
/*
 Plugin Name: FTDapi for WordPress
 Plugin URI: http://ftdapi.de/
 Description: Implementation of the FTDapi for WordPress
 Version: 0.9.0
 Author: Tim Weisenberger
 Text Domain: ftdapiwordpress
 Author URI: http://www.tec-promotion.de/
 License: GPL2

 Copyright 2014 - 2015 Tim Weisenberger (tw@tweis.de)

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

defined('ABSPATH') or die('No script kiddies please!');

if (!class_exists('FtdApiForWordPress')) :
	class FtdApiForWordPress {
		public $ftdApiEndpoint = 'http://www.food-trucks-deutschland.de/api/v12.php';

		public $defautApiParams = array('tk' => '', 'dt' => 'week', 'tp' => 'tour');

		public $cacheLocation = '/ftdapi-wordpress-cache/';

		public function __construct() {
			add_action('admin_init', array(&$this, 'adminInit'));
			add_action('admin_menu', array(&$this, 'addMenu'));

			add_action('init', array(&$this, 'frontendInit'));

			add_action('plugins_loaded', function() {
				load_plugin_textdomain('ftdapiwordpress', false, dirname(plugin_basename(__FILE__)) . '/language');
			});
		}

		public static function activate() {
			$FtdApiForWordPress = new FtdApiForWordPress();
			$FtdApiForWordPress -> createCacheDir();

			if (wp_next_scheduled('ftdapi_cleanup_cachefolder') == false) :
				wp_schedule_event(time(), 'hourly', 'ftdapi_cleanup_cachefolder');
			endif;
		}

		public static function deactivate() {

		}

		public function adminInit() {
			$this -> initSettings();
			$this -> showCacheWarning();
		}

		public function createCacheDir() {
			if (wp_mkdir_p(WP_CONTENT_DIR . $this -> cacheLocation)) :
				update_option('ftdapi_cacheable', '1');
			else :
				update_option('ftdapi_cacheable', '0');
			endif;
		}

		public function showCacheWarning() {
			if (get_option('ftdapi_cacheable') != 1 && $_GET['page'] == 'ftdapi_for_wordpress') :
				add_action('admin_notices', function() {
					echo '<div class="error" style="display:block; border-left: 4px solid #ffba00;">';
					echo '<p><strong>' . __('WARNING: Your filesystem is not writable. Requests to FTDapi can not be cached!', 'ftdapiwordpress') . '</strong></p>';
					echo '</div>';
				});
			endif;
		}

		public function initSettings() {
			register_setting('ftdapi_for_wordpress-group', 'ftdapi_token', array(&$this, 'validateApiToken'));
			register_setting('ftdapi_for_wordpress-group', 'ftdapi_cache', 'intval');

			register_setting('ftdapi_for_wordpress-group', 'ftdapi_time_interval', 'wp_filter_nohtml_kses');
			register_setting('ftdapi_for_wordpress-group', 'ftdapi_selection', 'wp_filter_nohtml_kses');
			register_setting('ftdapi_for_wordpress-group', 'ftdapi_foodtruck_id', 'wp_filter_nohtml_kses');
			register_setting('ftdapi_for_wordpress-group', 'ftdapi_show_map', 'intval');
			register_setting('ftdapi_for_wordpress-group', 'ftdapi_map_dataprovider', 'wp_filter_nohtml_kses');
			register_setting('ftdapi_for_wordpress-group', 'ftdapi_map_height', 'intval');
		}

		public function addMenu() {
			add_options_page(__('FTDapi Settings', 'ftdapiwordpress'), __('FTDapi for WordPress', 'ftdapiwordpress'), 'manage_options', 'ftdapi_for_wordpress', array(&$this, 'pluginSettingsPage'));
		}

		public function pluginSettingsPage() {
			if (!current_user_can('manage_options'))
				wp_die(__('You do not have sufficient permissions to access this page.'));

			include (sprintf("%s/templates/settings.php", dirname(__FILE__)));
		}

		public function validateApiToken($input) {
			if ($input == '')
				return $input;

			$request = $this -> makeApiCall(array('tk' => $input), false);

			if (isset($request['error'])) :
				add_settings_error('ftdapiwordpress', esc_attr('settings_updated'), __('No valid API-Token set.'), 'error');

				return '';
			else :
				return $input;
			endif;
		}

		public function makeApiCall($params = array(), $cache = true, $debug = false) {
			$mergedParams = array_merge($this -> defautApiParams, $params);

			$query = http_build_query($mergedParams);

			if ($debug == true) :
				return $query;
			endif;

			$cacheFileName = WP_CONTENT_DIR . $this -> cacheLocation . md5($query) . '.json';

			if ($cache && get_option('ftdapi_cacheable') == 1) :
				if (file_exists($cacheFileName)) :
					if (filemtime($cacheFileName) > (time() - (60 * get_option('ftdapi_cache', '360')))) :
						return json_decode(file_get_contents($cacheFileName), true);
					endif;
				endif;
			endif;

			try {
				$request = wp_remote_get($this -> ftdApiEndpoint . '?' . $query);

				if ($request['response']['code'] == 200) :
					$responseData = json_decode($request['body'], true);

					if ($responseData['error'] == 1) :
						throw new Exception(__('FTDapi - No vaild request', 'ftdapiwordpress'));
					endif;
				else :
					throw new Exception(__('FTDapi - API is currently unavailable', 'ftdapiwordpress'));
				endif;

				// Process the Data
				if ($cache && get_option('ftdapi_cacheable') == 1) :
					@file_put_contents($cacheFileName, json_encode($responseData));
				endif;

				return $responseData;

			} catch (Exception $e) {
				return array('error' => 1, 'message' => $e -> getMessage());
			}
		}

		public function frontendInit() {
			add_action('wp_enqueue_scripts', array(&$this, 'registerAssets'));

			add_shortcode('ftd-api', array(&$this, 'shortCodeAction'));
			add_filter('widget_text', 'do_shortcode');

			add_action('ftdapi_cleanup_cachefolder', array(&$this, 'cleanUpCacheFolder'));
		}

		public function shortCodeAction($atts) {
			$atts = shortcode_atts(array('token' => get_option('ftdapi_token'), 'time_interval' => get_option('ftdapi_time_interval'), 'selection' => get_option('ftdapi_selection'), 'foodtruck_id' => get_option('ftdapi_foodtruck_id'), 'show_map' => get_option('ftdapi_show_map'), 'map_dataprovider' => get_option('ftdapi_map_dataprovider'), 'map_height' => get_option('ftdapi_map_height'), 'template' => false), $atts, 'ftd-api');
			
			setlocale(LC_ALL, get_locale());
			
			switch($atts['selection']) :
				case 'region' :
					$request = $this -> makeApiCall(array('tk' => $atts['token'], 'tp' => 'operatortour', 'dt' => $atts['time_interval']), true, false);

					if (isset($request['error']) || count($request) == 0)
						return __('FTDapi - Currently no dates available', 'ftdapiwordpress');

					if ($atts['template'] != false) :
						include ($this -> getTemplateFile($atts['template']));
					else :
						include ($this -> getTemplateFile('region'));
					endif;
					break;
				case 'provider' :
					$request = $this -> makeApiCall(array('tk' => $atts['token'], 'tp' => 'operatortour', 'dt' => $atts['time_interval']), true, false);

					if (isset($request['error']) || count($request) == 0)
						return __('FTDapi - Currently no dates available', 'ftdapiwordpress');

					if ($atts['template'] != false) :
						include ($this -> getTemplateFile($atts['template']));
					else :
						include ($this -> getTemplateFile('provider'));
					endif;
					break;
				case 'truck' :
					$request = $this -> makeApiCall(array('tk' => $atts['token'], 'tp' => 'operatortour', 'dt' => $atts['time_interval']), true, false);

					$request = $this -> filterTrucks($request, $atts['foodtruck_id']);

					if (isset($request['error']) || count($request) == 0)
						return __('FTDapi - Currently no dates available', 'ftdapiwordpress');

					if ($atts['template'] != false) :
						include ($this -> getTemplateFile($atts['template']));
					else :
						include ($this -> getTemplateFile('truck'));
					endif;
					break;
				default :
					return __('FTDapi - No display type selected', 'ftdapiwordpress');
			endswitch;
		}

		public function getTemplateFile($filename) {
			if (file_exists(get_template_directory() . '/ftdapi/' . $filename . '.php')) :
				return get_template_directory() . '/ftdapi/' . $filename . '.php';
			else :
				return sprintf("%s/templates/" . $filename . ".php", dirname(__FILE__));
			endif;
		}

		public function registerAssets() {
			wp_register_style('ftdapi-base', plugins_url('assets/css/ftdapi.css', __FILE__));

			wp_register_style('ftdapi-leaflet', 'http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css');
			wp_register_style('ftdapi-leaflet-markercluster', 'http://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/0.4.0/MarkerCluster.css');
			wp_register_style('ftdapi-leaflet-markercluster-default', 'http://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/0.4.0/MarkerCluster.Default.css');

			wp_register_script('ftdapi-leaflet', 'http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js', array(), '0.7.3', true);
			wp_register_script('ftdapi-leaflet-markercluster', 'http://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/0.4.0/leaflet.markercluster.js', array(), '0.4.0', true);
			wp_register_script('ftdapi-googlemaps', 'http://maps.google.com/maps/api/js?v=3.2&sensor=false', array(), '3.2', true);
			wp_register_script('ftdapi-leaflet-google', plugins_url('assets/js/leaflet.google.js', __FILE__), array(), '0.1', true);

			wp_register_script('ftdapi-map', plugins_url('assets/js/ftdapi-map.js', __FILE__), array(), '1.0', true);
		}

		public function filterTrucks($data, $truckId) {
			foreach ($data as $key => $value) :
				if (trim($value['truck']) != $truckId) :
					unset($data[$key]);
				endif;
			endforeach;

			return array_values($data);
		}

		public function cleanUpCacheFolder() {
			foreach (scandir(WP_CONTENT_DIR . $this -> cacheLocation) as $file) :
				$fileWithPath = WP_CONTENT_DIR . $this -> cacheLocation . $file;

				if (is_file($fileWithPath)) :
					if (time() - filemtime($fileWithPath) >= get_option('ftdapi_cache')) :
						unlink($fileWithPath);
					endif;
				endif;
			endforeach;
		}

	}

endif;

if (class_exists('FtdApiForWordPress')) :
	register_activation_hook(__FILE__, array('FtdApiForWordPress', 'activate'));
	register_deactivation_hook(__FILE__, array('FtdApiForWordPress', 'deactivate'));

	$FtdApiForWordPress = new FtdApiForWordPress();
endif;

if (isset($FtdApiForWordPress)) :
	add_filter("plugin_action_links_" . plugin_basename(__FILE__), function($links) {
		$settingsLink = '<a href="options-general.php?page=ftdapi_for_wordpress">' . __('Settings') . '</a>';
		array_unshift($links, $settingsLink);
		return $links;
	});
endif;
