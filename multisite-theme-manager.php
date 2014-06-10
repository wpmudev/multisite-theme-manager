<?php
/*
Plugin Name: Multisite Theme Manager
Plugin URI: http://premium.wpmudev.org/multisite-theme-manager/
Description: Take control of the theme admin page for your multisite network. Categorize your themes into groups, modify the name, description, and screenshot used for themes.
Version: 1.0.0.2
Network: true
Text Domain: wmd_multisitethememanager
Author: WPMU DEV
Author URI: http://premium.wpmudev.org/
WDP ID: 883804
*/

/*
Copyright Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define( 'PRETTYTHEMES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

include_once(PRETTYTHEMES_PLUGIN_DIR.'multisite-theme-manager-files/includes/functions.php');

class WMD_PrettyThemes extends WMD_PrettyThemes_Functions {

	var $plugin_main_file;
	var $plugin_dir_url;
	var $plugin_dir;
	var $plugin_basename;
	var $plugin_rel;
	var $plugin_dir_custom;
	var $plugin_dir_url_custom;

	var $blog_id;
	var $pro_site_plugin_active;
	var $pro_site_settings;

	var $themes_data;
	var $themes_custom_data;
	var $themes_custom_data_config;
	var $themes_categories;
	var $themes_categories_config;

	var $default_options;
	var $options;
	var $current_theme_details;
	var $config_version;

	function __construct() {
		//loads dashboard stuff
		global $wpmudev_notices;
		$wpmudev_notices[] = array( 'id'=> 852474, 'name'=> 'Multisite Theme Manager', 'screens' => array( 'settings_page_multisite-theme-manager-network' ) );
		include_once(PRETTYTHEMES_PLUGIN_DIR.'multisite-theme-manager-files/external/dash-notice/wpmudev-dash-notification.php');

		//plugin only works on admin
		if(is_admin()) {
			$this->init_vars();

			register_activation_hook($this->plugin_main_file, array($this, 'do_activation'));

			add_action('init', array($this,'init'));

			//if in setup mode, disable everything for other sites then main.
			if( isset($this->options['setup_mode']) && ($this->options['setup_mode'] == 0 || ($this->blog_id == 1 && $this->options['setup_mode'] == 1)) ) {
				add_action('plugins_loaded', array($this,'plugins_loaded'));

				add_action('admin_enqueue_scripts', array($this,'register_scripts_styles_admin'), 11);

				add_action('admin_menu', array($this,'admin_page'), 20);
				add_action('network_admin_menu', array($this,'network_admin_page'), 20);
				add_action('contextual_help', array($this,'network_plugins_help'), 10, 2);
				add_action('network_admin_notices', array($this,'options_page_validate_save_notices'));
				add_action('admin_notices', array($this,'theme_page_notice'), 11);
				add_filter('theme_action_links', array($this,'network_admin_theme_action_links'), 10, 3);
				add_filter('network_admin_plugin_action_links', array($this,'network_admin_plugin_action_links'), 10, 3);
				add_filter('admin_body_class', array($this,'admin_body_class'), 10, 1);

				add_action('admin_footer-themes.php', array($this,'prettythemes_edit_html'));

				add_action('wp_ajax_prettythemes_add_category_ajax', array($this,'add_category_ajax'));
				add_action('wp_ajax_prettythemes_save_category_ajax', array($this,'save_category_ajax'));
				add_action('wp_ajax_prettythemes_save_theme_details_ajax', array($this,'save_theme_details_ajax'));
			}
		}
	}

    function init_vars() {
    	//config version is only used for high traffic sites
    	$this->config_version = 0;
    	$this->blog_id = get_current_blog_id();

		$this->plugin_main_file = __FILE__;
		$this->plugin_dir = PRETTYTHEMES_PLUGIN_DIR;
		$this->plugin_dir_url = plugin_dir_url($this->plugin_main_file);
		$this->plugin_basename = plugin_basename($this->plugin_main_file);
		$this->plugin_rel = dirname($this->plugin_basename).'/';

		$wp_upload_dir = wp_upload_dir();
		if($this->blog_id != 1)
			foreach ($wp_upload_dir as $type => $value)
				if($type == 'basedir' || $type == 'baseurl') {
					$parts = explode('/', $value);
					if(is_numeric(end($parts))) {
						array_pop($parts);
						array_pop($parts);
						$wp_upload_dir[$type] = implode('/', $parts);
					}
				}

		$this->plugin_dir_custom = $wp_upload_dir['basedir'].'/multisite-theme-manager/';
		$this->plugin_dir_url_custom = $wp_upload_dir['baseurl'].'/multisite-theme-manager/';

		$this->default_options = array(
			'setup_mode' => '1',
			'theme' => 'standard/3-eight',
			'themes_options' => array('author_link' => '1', 'custom_link' => '1', 'tags' => '1', 'version' => '1'),
			'themes_auto_screenshots_by_name' => '0',
			'themes_page_title' => __('Themes', 'wmd_multisitethememanager'),
			'themes_page_description' => '',
			'themes_link_label' => __('Learn more about theme', 'wmd_multisitethememanager')
		);

		//load options
		$this->options = get_site_option('wmd_prettythemes_options', $this->default_options);
    }

    function do_activation() {
    	if(!is_multisite())
    		trigger_error(sprintf(__('Multisite Theme Manager only works in multisite configuration. You can read more about it <a href="%s" target="_blank">here</a>.', 'wmd_multisitethememanager'), 'http://codex.wordpress.org/Create_A_Network'),E_USER_ERROR);
    	else {
	        //create folder for custom themes
	        if (!$this->config_version && !is_dir($this->plugin_dir_custom)) {
	            mkdir($this->plugin_dir_custom);

	            if (!is_dir($this->plugin_dir_custom.'themes/'))
	            	mkdir($this->plugin_dir_custom.'themes/');
	        	if (!is_dir($this->plugin_dir_custom).'screenshots/')
	            	mkdir($this->plugin_dir_custom.'screenshots/');
	        }

	        //save default options
			if(get_site_option('wmd_prettythemes_options', 0) == 0)
				update_site_option('wmd_prettythemes_options', $this->default_options);
		}
    }

	function plugins_loaded() {
		global $pagenow;

		//delete_site_option( 'wmd_prettythemes_options');
		load_plugin_textdomain( 'wmd_multisitethememanager', false, $this->plugin_rel.'languages/' );
	}

	function init(){
		global $pagenow;

		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
		$default = isset($_REQUEST['default']) ? $_REQUEST['default'] : 0;
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 0;

		//load stuff when on correct page
		if($this->is_prettythemes_data_required()) {
			$this->themes_custom_data = get_site_option('wmd_prettythemes_themes_custom_data', array());
			$this->current_theme_details = $this->get_current_theme_details();

			//Check if prosite and theme module is active
			$this->pro_site_settings = get_site_option( 'psts_settings' );
			if(function_exists('is_pro_site') && isset($this->pro_site_settings['modules_enabled']) && in_array('ProSites_Module_Plugins', $this->pro_site_settings['modules_enabled']))
				$this->pro_site_plugin_active = true;
			else {
				$this->pro_site_plugin_active = false;
				$this->pro_site_settings = false;
			}

			//load config file if exists
			$config_file_path = '';
			if($this->config_version) {
				$config_file_m_time = $this->config_version;
				$config_file_path = $this->plugin_dir.'multisite-theme-manager-files/data/config.xml';
			}
			elseif(!$this->config_version && file_exists($this->plugin_dir_custom.'config.xml')) {
				$config_file_m_time = filemtime($this->plugin_dir_custom.'config.xml');
				$config_file_path = $this->plugin_dir_custom.'config.xml';
			}

			//check last modified time(or config version), load file if new
			if($config_file_path && file_exists($config_file_path)) {
				if($config_file_m_time != get_site_option('wmd_prettythemes_last_config_file_m_time', 0)) {
					update_site_option('wmd_prettythemes_last_config_file_m_time', $config_file_m_time);
					$this->import_xml_data_setting_file($config_file_path, 1);
				}
				else {
					$this->themes_categories_config = get_site_option('wmd_prettythemes_themes_categories_config', array());
					$this->themes_custom_data_config = get_site_option('wmd_prettythemes_themes_custom_data_config', array());
				}
			}
			else {
				$this->themes_categories_config = array();
				$this->themes_custom_data_config = array();
			}
			//load data
			$this->themes_categories = get_site_option('wmd_prettythemes_themes_categories', array());
		}

		//controlls welcome/setup notice
		if(current_user_can('manage_network_options') && (!isset($_POST['wmd_prettythemes_options']['setup_mode']) && $this->options['setup_mode'] == 1 || (isset($_POST['wmd_prettythemes_options']['setup_mode']) && $_POST['wmd_prettythemes_options']['setup_mode'] != 0)) && $this->is_prettythemes_data_required())
			add_action( 'all_admin_notices', array( $this, 'setup_mode_welcome_notice' ), 12 );

		//check if stuff are being exported
		if(isset($_REQUEST['prettythemes_action']) && $_REQUEST['prettythemes_action'] == 'export')
			add_action('wp_loaded', array($this,'export_data_settings'), 1);

		//Fix for first standard menu sub item being replced
		if($page === 'multisite-theme-manager.php' && $pagenow == 'admin.php') {
			wp_redirect( admin_url('themes.php?page=multisite-theme-manager.php') );
			exit();
		}

		//Redirect old themes page to new if parameter is not set
		if(!is_network_admin() && !$action && $default != 1 && $page === 0 && $pagenow == 'themes.php') {
			wp_redirect( add_query_arg(array('page' => 'multisite-theme-manager.php')) );
			exit();
		}
	}

	function register_scripts_styles_admin($hook) {
		global $pagenow;

		//register scripts and styles for theme page
		if( $hook == 'appearance_page_multisite-theme-manager' ) {
			wp_register_style('wmd-prettythemes-theme', $this->current_theme_details['dir_url'].'style.css', array(), 3);
			wp_enqueue_style('wmd-prettythemes-theme');


			wp_register_script('wmd-prettythemes-theme', $this->current_theme_details['dir_url'].'theme.js', array('jquery', 'backbone', 'wp-backbone'), 3, true);

			if ( current_user_can( 'switch_themes' ) ) {
				$this->themes_data = wp_prepare_themes_for_js();
			} else {
				$this->themes_data = wp_prepare_themes_for_js( array( wp_get_theme() ) );
			}
			wp_reset_vars( array( 'theme', 'search' ) );

			$this->themes_data = $this->get_merged_theme_data();
			$themes_categories = array_merge(array('all' => 'All'), $this->get_merged_themes_categories());
			$theme = isset($_GET['theme']) ? $_GET['theme'] : '';
			$search = isset($_GET['search']) ? $_GET['search'] : '';
			$category = isset($_GET['category']) ? $_GET['category'] : '';

			wp_localize_script( 'wmd-prettythemes-theme', '_wpThemeSettings', array(
				'themes'   => $this->themes_data,
				'categories'   => $themes_categories,

				'settings' => array(
					'canInstall'    => ( ! is_multisite() && current_user_can( 'install_themes' ) ),
					'installURI'    => ( ! is_multisite() && current_user_can( 'install_themes' ) ) ? admin_url( 'theme-install.php' ) : null,
					'confirmDelete' => __( "Are you sure you want to delete this theme?\n\nClick 'Cancel' to go back, 'OK' to confirm the delete.", 'wmd_multisitethememanager' ),
					'root'          => parse_url( admin_url( 'themes.php' ), PHP_URL_PATH ).'?page=multisite-theme-manager.php',
					'theme'         => esc_html( $theme ),
					'search'        => esc_html( $search ),
					'category'        => esc_html( $category ),
				),
			 	'l10n' => array(
			 		'search'  => __( 'Search Installed Themes', 'wmd_multisitethememanager' ),
			 		'searchPlaceholder' => __( 'Search installed themes...', 'wmd_multisitethememanager' ),
			 		'categories' => __( 'Categories:', 'wmd_multisitethememanager' ),
			  	),
			) );

			add_thickbox();
			//wp_enqueue_script( 'theme' );
			wp_enqueue_script('wmd-prettythemes-theme');
			wp_enqueue_script( 'customize-loader' );
		}
		//register scripts and styles for network theme page
		elseif($hook == 'themes.php' && is_network_admin()) {
			wp_register_style('wmd-prettythemes-network-admin', $this->plugin_dir_url.'multisite-theme-manager-files/css/network-admin.css');
			wp_enqueue_style('wmd-prettythemes-network-admin');

			wp_register_script('wmd-prettythemes-network-admin', $this->plugin_dir_url.'multisite-theme-manager-files/js/network-admin.js', false, true);
			wp_enqueue_script('wmd-prettythemes-network-admin');

			$themes_custom_data_ready = $this->get_converted_themes_data_for_js($this->get_merged_themes_custom_data());
			$themes_categories_ready = $this->get_merged_themes_categories();
			$protocol = isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://'; //This is used to set correct adress if secure protocol is used so ajax calls are working
			$params = array(
				'ajax_url' => admin_url( 'admin-ajax.php', $protocol ),
				'admin_url' => admin_url( '', $protocol ),
				'prettythemes_url' => $this->plugin_dir_url,
				'theme_url' => $this->current_theme_details['dir_url'],
				'image' => __('Custom Image', 'wmd_multisitethememanager'),
				'edit_code' => __('Edit Code', 'wmd_multisitethememanager'),
				'edit' => __('edit', 'wmd_multisitethememanager'),
				'orginal_description' => __('Show/hide orginal description', 'wmd_multisitethememanager'),
				'default_custom_url_label' => $this->options['themes_link_label'],
				'categories' => __('Categories', 'wmd_multisitethememanager'),
				'choose_screenshot' => __('Choose image for theme screenshot (recommended size: 880px on 660px)', 'wmd_multisitethememanager'),
				'select_image' => __('Select Image', 'wmd_multisitethememanager'),
				'theme_details' => $themes_custom_data_ready,
				'theme_categories' => $themes_categories_ready
			);
			wp_localize_script( 'wmd-prettythemes-network-admin', 'wmd_pl_na', $params );

			wp_enqueue_media();
		}
		//load stuff to replace details on customize page
		elseif( $pagenow == 'customize.php') {
			$theme = 0;
			$themes_custom_data_ready = $this->get_converted_themes_data_for_js($this->get_merged_themes_custom_data());

			if(isset($_REQUEST['theme']))
				$theme_path = $_REQUEST['theme'];
			else
				$theme_path = get_stylesheet();

			if(isset($theme_path) && isset($themes_custom_data_ready[$theme_path]))
				$theme = $themes_custom_data_ready[$theme_path];

			if($theme) {
				wp_register_script('wmd-prettythemes-customize', $this->plugin_dir_url.'multisite-theme-manager-files/js/customize.js', false, true);
				wp_enqueue_script('wmd-prettythemes-customize');
				wp_localize_script( 'wmd-prettythemes-customize', 'wmd_msreader', array('current_theme' => $theme) );
			}
		}
	}

	//Replaces themes page with custom (bit hacky so new theme page is first)
	function admin_page() {
		global $submenu, $parent_file;
		
		remove_submenu_page('themes.php', 'themes.php');

		add_theme_page(stripslashes($this->options['themes_page_title']), stripslashes($this->options['themes_page_title']), 'switch_themes', 'multisite-theme-manager.php', array($this,'new_theme_page') );

		if(isset($submenu['themes.php'])) {
			foreach ($submenu['themes.php'] as $key => $value) {
				if($value[2] == 'multisite-theme-manager.php') {
					$theme_page = $submenu['themes.php'][$key];
					unset($submenu['themes.php'][$key]);
					break;
				}
			}
			
			$submenu['themes.php'] = array_merge(array('5' => $theme_page), $submenu['themes.php']);
		}
	}

	function admin_body_class($classes) {
		global $pagenow;

		if(!is_network_admin() && $pagenow == 'themes.php')
			return ($classes) ? $classes.' themes-php' : 'themes-php';
	}

	function network_plugins_help($contextual_help, $screen_id) {
		if($screen_id == 'themes-network') {
			//Adds new help tab
			$screen = get_current_screen();
		    $screen->add_help_tab( 
		    	array(
			        'id'	=> 'edit_details',
			        'title'	=> __('Editing Theme Details', 'wmd_multisitethememanager'),
			        'content'	=> '
			        	<p>'.sprintf(__( 'You can edit theme details for each theme by clicking "Edit Details". All new details will be visible on <a href="%s">the themes page</a> available for all network sites. It is also possible to control aditional settings on "Network Admin" > "Settings" > "<a href="%s">Multisite Theme Manager</a>.','wmd_multisitethememanager'),  admin_url('themes.php?page=multisite-theme-manager.php'), admin_url('network/settings.php?page=multisite-theme-manager.php')).'</p>
			        	<p>'.__( '<strong>Name</strong> - Replace the name of the theme with one of your choice. Leave blank to use the original name.','wmd_multisitethememanager').'</p>
			        	<p>'.__( '<strong>Custom URL</strong> - Create an external theme link to any URL of your choice, for support documentation for example.','wmd_multisitethememanager').'</p>
			        	<p>'.__( 'Set label for custom url. Leave blank to use default label configured in Multisite Theme Manager settings page.', 'wmd_multisitethememanager').'</p>
			        	<p>'.__( '<strong>Image URL</strong> - Set image for this theme. You can choose an image from your media gallery or upload it to "wp-content/uploads/multisite-theme-manager/screenshots/" and input file name as "Custom URL". Alternatively, a file with the correct name will be autoloaded even when this field is empty (example: theme location - "wp-content/themes/twentythirteen/", image file - "akismet.png". Only PNG files will work with this method.). "Auto load screenshot with correct name" setting needs to be set to true for it to work. Recommended dimensions are 880px on 660px.','wmd_multisitethememanager').'</p>
			        	<p>'.__( '<strong>Categories</strong> - Allows you to set categories that the theme will be assigned to. Unused categories will be automatically deleted.','wmd_multisitethememanager').'</p>
			        	<p>'.__( '<strong>Description</strong> - Replace the original description of the theme with your own. Leave blank to use the original.','wmd_multisitethememanager').'</p>
			        '
		    	) 
			);


			//load tooltips for admin themes page
			if(!class_exists('WpmuDev_HelpTooltipsDyn'))
				include($this->plugin_dir.'multisite-theme-manager-files/external/wpmudev-help-tooltips.php');
			$tips = new WpmuDev_HelpTooltipsDyn();
			$tips->set_icon_url($this->plugin_dir_url.'multisite-theme-manager-files/images/tooltip.png');
			$tips->set_use_notice(false);

			$tips->bind_tip(__('Replace the name of the theme with one of your choice. Leave blank to use the original name.', 'wmd_multisitethememanager'), '#name_tooltip');
			$tips->bind_tip(__('Create an external theme link to any URL of your choice, for support documentation for example.', 'wmd_multisitethememanager'), '#custom_url_tooltip');
			$tips->bind_tip(__('Set label for custom url. Leave blank to use default label configured in Multisite Theme Manager settings page.', 'wmd_multisitethememanager'), '#custom_url_label_tooltip');
			$tips->bind_tip(__('Set the featured image for this theme. Recommended dimensions are 880px on 660px. Use help tab (top right corner) to get info about advanced usage.', 'wmd_multisitethememanager'), '#image_url_tooltip');
			$tips->bind_tip(__('Allows you to set categories that the theme will be assigned to. Unused categories will be automatically deleted.', 'wmd_multisitethememanager'), '#categories_tooltip');
			$tips->bind_tip(__('Replace the original description of the theme with your own. Leave blank to use the original.', 'wmd_multisitethememanager'), '#description_tooltip');
		}
	}

	function setup_mode_welcome_notice() {
		echo '<div class="updated fade"><p>'.sprintf(__('Multisite Theme Manager is in "Setup Mode". Test your updates on your main site\'s <a href="%s">Theme</a> page in this mode. This reminder will disappear and theme details will function on your sites once Setup Mode is disabled on the <a href="%s">Settings</a> page. Modify themes using "Edit Details" at <a href="%s">Network Admin - Themes</a>.', 'wmd_multisitethememanager'), admin_url('themes.php?page=multisite-theme-manager.php'), admin_url('network/settings.php?page=multisite-theme-manager.php'), admin_url('network/themes.php')).'</p></div>';
	}

	function theme_page_notice() {
		global $pagenow;

		if ( $pagenow == 'themes.php' && isset($_REQUEST['page']) && $_REQUEST['page'] == 'multisite-theme-manager.php' && is_super_admin() && !is_network_admin() )
			echo '<div class="updated"><p>'.sprintf(__('Super Admin, please note that standard theme page can still be accessed at <a href="%s">this URL</a>.', 'wmd_multisitethememanager'), admin_url('themes.php?default=1')).'</p></div>';
	}

	function network_admin_page() {
		add_submenu_page('settings.php', __( 'Multisite Theme Manager', 'wmd_multisitethememanager' ), __( 'Multisite Theme Manager', 'wmd_multisitethememanager' ), 'manage_network_options', basename($this->plugin_main_file), array($this,'network_option_page'));
	}

	function network_option_page() {
		include($this->plugin_dir.'multisite-theme-manager-files/includes/page-network-admin.php');
	}

	function network_admin_theme_action_links($actions, $theme_file, $theme_data) {
		if(is_network_admin()) {
			//adds "edit details" link
			array_splice($actions, 1, 0, '<a href="#'.$theme_file->stylesheet.'" title="'.__('Edit theme details like title, discription, image and categories', 'wmd_multisitethememanager').'" class="edit_details">'.__('Edit Details', 'wmd_multisitethememanager').'</a>');

			//changes "edit" link to "edit code" for clarity
			if(isset($actions['edit']))
				$actions['edit'] = str_replace(__('Edit'), __( 'Edit Code', 'wmd_multisitethememanager' ), $actions['edit']);

			return $actions;
		}
	}
	function network_admin_plugin_action_links($actions, $plugin_file, $plugin_data) {
		if($plugin_file == 'multisite-theme-manager/multisite-theme-manager.php')
			$actions['settings'] = '<a href="'.admin_url('network/settings.php?page=multisite-theme-manager.php').'" title="'.__('Go to the Multisite Theme Manager settings page', 'wmd_multisitethememanager').'">'.__('Settings', 'wmd_multisitethememanager').'</a>';

		return $actions;
	}


	function options_page_validate_save_notices() {
		//default save
		if(isset($_POST['option_page']) && $_POST['option_page'] == 'wmd_prettythemes_options' && isset($_POST['save_settings']) && wp_verify_nonce($_POST['_wpnonce'], 'wmd_prettythemes_options-options')) {
			$validated = $this->get_validated_options($_POST['wmd_prettythemes_options']);

			update_site_option( 'wmd_prettythemes_options', $validated );

			echo '<div id="message" class="updated"><p>'.__( 'Successfully saved', 'wmd_multisitethememanager' ).'</p></div>';
		}
		elseif(isset($_REQUEST['prettythemes_action'], $_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'wmd_prettythemes_options')) {
			//delete custom data
			if($_REQUEST['prettythemes_action'] == 'delete_custom_data') {
				echo '<div id="message" class="updated"><p>'.__( 'All custom theme data deleted sucessfully.', 'wmd_multisitethememanager' ).'</p></div>';
				delete_site_option('wmd_prettythemes_themes_custom_data');
				delete_site_option('wmd_prettythemes_themes_custom_data_config');
				delete_site_option('wmd_prettythemes_themes_categories');
				delete_site_option('wmd_prettythemes_themes_categories_config');
				delete_site_option('wmd_prettythemes_last_config_file_m_time');
			}
			//reset settings
			if($_REQUEST['prettythemes_action'] == 'reset_settings') {
				echo '<div id="message" class="updated"><p>'.__( 'All settings reset sucessfully.', 'wmd_multisitethememanager' ).'</p></div>';
				update_site_option('wmd_prettythemes_options', $this->default_options);
				$this->options = $this->default_options;
			}
		}
		//try to import config file
		elseif(isset($_POST['option_page'], $_POST['import_config'], $_POST['_wpnonce']) && $_POST['option_page'] == 'wmd_prettythemes_options' && wp_verify_nonce($_POST['_wpnonce'], 'wmd_prettythemes_options-options')) {
			if (!isset($_FILES['config_file']) && $_FILES['config_file']['error'] > 0) {
				echo '<div id="message" class="error"><p>'.__( 'There was a problem while uploading file.', 'wmd_multisitethememanager' ).'</p></div>';
			}
			else {
				$this->import_xml_data_setting_file($_FILES['config_file']["tmp_name"]);
				echo '<div id="message" class="updated"><p>'.__( 'Themes data and settings imported successfully.', 'wmd_multisitethememanager' ).'</p></div>';
			}
		}
	}

	function export_data_settings() {
		$this->export_xml_data_setting_file();
	}

	function prettythemes_edit_html() {
		include($this->plugin_dir.'multisite-theme-manager-files/includes/element-edit-details.php');
	}

	function add_category_ajax() {
		error_reporting(0);
		$error = 0;

		//loads variables for ajax call
		$this->themes_categories = get_site_option('wmd_prettythemes_themes_categories', array());

		if(wp_verify_nonce($_POST['_wpnonce'], 'wmd_prettythemes_edit_theme_details')) {
			$last_category = $this->get_last_category_id();
			$last_category++;
			$new_key = 'category'.$last_category;

			$this->themes_categories[$new_key] = $_POST['theme_new_category'];

			if(!empty($this->themes_categories[$new_key]) && !empty($_POST['theme_new_category']))
				update_site_option('wmd_prettythemes_themes_categories', $this->themes_categories);
			else
				$error = 1;
		}
		else
			$error = 1;

		echo json_encode(array('id' => $new_key, 'name' => $_POST['theme_new_category'], 'error' => $error));
		die();
	}

	function save_category_ajax() {
		error_reporting(0);
		$error = 0;

		//loads variables for ajax call
		$this->themes_categories = get_site_option('wmd_prettythemes_themes_categories', array());

		if(wp_verify_nonce($_POST['_wpnonce'], 'wmd_prettythemes_edit_theme_details')) {
			if(isset($this->themes_categories[$_POST['theme_edit_category_key']]) && !empty($_POST['theme_edit_category']) && $_POST['theme_edit_category_key']) {
				$this->themes_categories[$_POST['theme_edit_category_key']] = $_POST['theme_edit_category'];
				update_site_option('wmd_prettythemes_themes_categories', $this->themes_categories);
			}
			else
				$error = 1;
		}
		else
			$error = 1;

		echo json_encode(array('id' => $_POST['theme_edit_category_key'], 'name' => $_POST['theme_edit_category'], 'error' => $error));
		die();
	}

	function save_theme_details_ajax() {
		error_reporting(0);
		$error = 0;

		//loads variables for ajax call
		$this->themes_categories = get_site_option('wmd_prettythemes_themes_categories', array());
		$this->themes_custom_data = get_site_option('wmd_prettythemes_themes_custom_data', array());
		$this->themes_custom_data_config = get_site_option('wmd_prettythemes_themes_custom_data_config', array());

		if(wp_verify_nonce($_POST['_wpnonce'], 'wmd_prettythemes_edit_theme_details')) {
			if(is_numeric($_POST['theme_image_id']))
				$_POST['theme_image_url'] = $this->get_resized_attachment_url( $_POST['theme_image_id'] );

			foreach($_POST['theme_categories'] as $key => $category)
				if(strpos($category, 'config') !== false)
					unset($_POST['theme_categories'][$key]);

			if(!isset($this->themes_custom_data[$_POST['theme_path']]))
				$this->themes_custom_data[$_POST['theme_path']] = array();

			$data = array(
				'Categories' => $_POST['theme_categories'],
				'ScreenShot' => $_POST['theme_image_url'],
				'ScreenShotID' => $_POST['theme_image_id'],
				'CustomLink' => $_POST['theme_custom_url'],
				'CustomLinkLabel' => $_POST['theme_custom_url_label'],
				'Description' => $_POST['theme_description'],
				'Name' => $_POST['theme_name'],
			);
			foreach ($data as $name => $value)
				if(!empty($data[$name]))
					$this->themes_custom_data[$_POST['theme_path']][$name] = $value;
				else
					unset($this->themes_custom_data[$_POST['theme_path']][$name]);

			//empty categories fix
			if(count($this->themes_custom_data[$_POST['theme_path']]['Categories']) < 1)
				unset($this->themes_custom_data[$_POST['theme_path']]['Categories']);

			//adds http to custom link
			if(isset($this->themes_custom_data[$_POST['theme_path']]['CustomLink']))
				if (strpos($this->themes_custom_data[$_POST['theme_path']]['CustomLink'], '://') === false)
					$this->themes_custom_data[$_POST['theme_path']]['CustomLink'] = 'http://'.$this->themes_custom_data[$_POST['theme_path']]['CustomLink'];

			//remove unused and categories if necessary
			$removed_categories = $all_used_categories = array();
			foreach ($this->themes_custom_data as $path => $data)
				if(isset($data['Categories']))
					$all_used_categories = array_merge($all_used_categories, $data['Categories']);
			foreach ($this->themes_categories as $key => $category_name)
				if(!in_array($key, $all_used_categories)) {
					$update_categories = 1;
					unset($this->themes_categories[$key]);
					$removed_categories[] = $key;
				}
			if(isset($update_categories))
				update_site_option('wmd_prettythemes_themes_categories', $this->themes_categories);

			$themes_custom_data_ready = $this->get_converted_themes_data_for_js($this->get_merged_themes_custom_data());
			if(empty($themes_custom_data_ready[$_POST['theme_path']]))
				$error = 1;

			if(empty($this->themes_custom_data[$_POST['theme_path']]))
				unset($this->themes_custom_data[$_POST['theme_path']]);

			ksort($this->themes_custom_data);
			update_site_option('wmd_prettythemes_themes_custom_data', $this->themes_custom_data);
		}
		else
			$error = 1;

		echo json_encode(array('new_details' => $themes_custom_data_ready[$_POST['theme_path']], 'remove_categories' => $removed_categories, 'error' => $error));
		die();
	}

	function new_theme_page() {
		global $submenu, $self;

		if ( !current_user_can('switch_themes') && !current_user_can('edit_theme_options') )
			wp_die( __( 'Cheatin&#8217; uh?' ) );

		if ( ! validate_current_theme() || isset( $_GET['broken'] ) ) : ?>
		<div id="message1" class="updated"><p><?php _e('The active theme is broken. Reverting to the default theme.', 'wmd_multisitethememanager'); ?></p></div>
		<?php elseif ( isset($_GET['activated']) ) :
				if ( isset( $_GET['previewed'] ) ) { ?>
				<div id="message2" class="updated"><p><?php printf( __( 'Settings saved and theme activated. <a href="%s">Visit site</a>', 'wmd_multisitethememanager' ), home_url( '/' ) ); ?></p></div>
				<?php } else { ?>
		<div id="message2" class="updated"><p><?php printf( __( 'New theme activated. <a href="%s">Visit site</a>', 'wmd_multisitethememanager' ), home_url( '/' ) ); ?></p></div><?php
				}
			elseif ( isset($_GET['deleted']) ) : ?>
		<div id="message3" class="updated"><p><?php _e('Theme deleted.', 'wmd_multisitethememanager') ?></p></div>
		<?php
		endif;

		$ct = wp_get_theme();

		if ( $ct->errors() && current_user_can( 'manage_network_themes' ) ) {
			echo '<p class="error-message">' . sprintf( __( 'ERROR: %s', 'wmd_multisitethememanager' ), $ct->errors()->get_error_message() ) . '</p>';
		}

		//setup action links for current theme
		$parent_file = 'themes.php';
		$current_theme_actions = array();
		if ( is_array( $submenu ) && isset( $submenu['themes.php'] ) ) {
			foreach ( (array) $submenu['themes.php'] as $item) {
				$class = '';
				if ( 'themes.php' == $item[2] || 'theme-editor.php' == $item[2] || 'customize.php' == $item[2] || 'multisite-theme-manager.php' == $item[2] )
					continue;
				// 0 = name, 1 = capability, 2 = file
				if ( ( strcmp($self, $item[2]) == 0 && empty($parent_file)) || ($parent_file && ($item[2] == $parent_file)) )
					$class = ' class="current"';
				if ( !empty($submenu[$item[2]]) ) {
					$submenu[$item[2]] = array_values($submenu[$item[2]]); // Re-index.
					$menu_hook = get_plugin_page_hook($submenu[$item[2]][0][2], $item[2]);
					if ( file_exists(WP_PLUGIN_DIR . "/{$submenu[$item[2]][0][2]}") || !empty($menu_hook))
						$current_theme_actions[] = "<a class='button button-secondary' href='admin.php?page={$submenu[$item[2]][0][2]}'$class>{$item[0]}</a>";
					else
						$current_theme_actions[] = "<a class='button button-secondary' href='{$submenu[$item[2]][0][2]}'$class>{$item[0]}</a>";
				} else if ( current_user_can($item[1]) ) {
					$menu_file = $item[2];
					if ( false !== ( $pos = strpos( $menu_file, '?' ) ) )
						$menu_file = substr( $menu_file, 0, $pos );
					if ( file_exists( ABSPATH . "wp-admin/$menu_file" ) ) {
						$current_theme_actions[] = "<a class='button button-secondary' href='{$item[2]}'$class>{$item[0]}</a>";
					} else {
						$current_theme_actions[] = "<a class='button button-secondary' href='themes.php?page={$item[2]}'$class>{$item[0]}</a>";
					}
				}
			}
		}

		include($this->current_theme_details['dir'].'index.php');
	}
}

global $wmd_prettythemes;
$wmd_prettythemes = new WMD_PrettyThemes;
