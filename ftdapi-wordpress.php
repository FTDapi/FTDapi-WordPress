<?php
/*
 Plugin Name: FTDapi for WordPress
 Plugin URI: http://www.food-trucks-deutschland.de/
 Description: Implementation of the FTDapi for WordPress
 Version: 0.1
 Author: Tim Weisenberger
 Text Domain: ftdapiwordpress
 Author URI: http://www.tec-promotion.de/
 License: GPL2

 Copyright 2014 Tim Weisenberger (tw@tweis.de)

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
		}

		public static function activate() {
			// Dirty Workaround because WordPress requires an static function
			$FtdApiForWordPress = new FtdApiForWordPress();
			$FtdApiForWordPress -> createCacheDir();
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

		public function makeApiCall($params = array(), $cache = true) {
			$mergedParams = array_merge($this -> defautApiParams, $params);

			$query = http_build_query($mergedParams);

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
			add_shortcode('ftd-api', array(&$this, 'shortCodeAction'));
		}

		public function shortCodeAction() {
			//return WP_CONTENT_DIR . '/ftdapi-wordpress-cache/';

			return '' . print_r($this -> makeApiCall(array('tk' => '509ac615cd1bbbe88f043c4a8bd7eaa3')), true);
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
