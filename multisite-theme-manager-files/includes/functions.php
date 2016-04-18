<?php
class WMD_PrettyThemes_Functions {


	//Helpers


	//check if page that requires data is displayed
	function is_prettythemes_data_required() {
		global $pagenow;

		if(
			(
				$pagenow == 'settings.php' &&
				isset($_REQUEST['page']) &&
				$_REQUEST['page'] == 'multisite-theme-manager.php'
			)
			||
			(
				$pagenow == 'themes.php' &&
				isset($_REQUEST['page']) &&
				$_REQUEST['page'] == 'multisite-theme-manager.php'
			)
			||
			(
				$pagenow == 'admin.php' &&
				isset($_REQUEST['page']) &&
				$_REQUEST['page'] == 'multisite-theme-manager.php'
			)
			||
			(
				$pagenow == 'themes.php' &&
				is_network_admin()
			)
			||
			(
				$pagenow == 'customize.php'
			)
		)
			return true;
		else
			return false;
	}
    //get theme array for select option
    function get_themes() {
    	$themes_dirs = $themes = array();
    	$themes_dirs_paths = array(
    			'standard' => $this->plugin_dir.'multisite-theme-manager-files/themes/',
    			'custom' => $this->plugin_dir_custom.'themes/'
    		);
    	if(is_dir($themes_dirs_paths['standard']))
			$themes_dirs['standard'] = scandir($themes_dirs_paths['standard']);
		if(is_dir($themes_dirs_paths['custom']))
			$themes_dirs['custom'] = scandir($themes_dirs_paths['custom']);

		foreach ($themes_dirs as $type => $themes_dir)
			foreach ($themes_dir as $theme_dir) {
				$theme_dir = str_replace('.', '', $theme_dir);
				if(!empty($theme_dir))
				    if (is_dir($themes_dirs_paths[$type].'/'.$theme_dir))
				        if(file_exists($themes_dirs_paths[$type].'/'.$theme_dir.'/index.php')) {
				        	$theme_dir_name = ucwords(str_replace('-', ' ', $theme_dir));
				        	$type_name = ($type == 'custom') ? __( 'Custom', 'wmd_multisitethememanager' ) : __( 'Standard', 'wmd_multisitethememanager' );
				        	$themes[$type.'/'.$theme_dir] = $theme_dir_name.' ('.$type_name.')';
				    	}
			}

		return $themes;
    }

    function get_current_theme_details() {
    	$theme = array('url' => '', 'dir' => '');
    	$theme_details = explode('/', $this->options['theme']);
    	if($theme_details[0] == 'standard') {
    		$theme['dir_url'] = $this->plugin_dir_url.'multisite-theme-manager-files/themes/'.$theme_details[1].'/';
    		$theme['dir'] = $this->plugin_dir.'multisite-theme-manager-files/themes/'.$theme_details[1].'/';
    		$theme['type'] = 'standard';
    	}
    	elseif($theme_details[0] == 'custom' && !empty($this->plugin_dir_url_custom)) {
    		$theme['dir_url'] = $this->plugin_dir_url_custom.'themes/'.$theme_details[1].'/';
    		$theme['dir'] = $this->plugin_dir_custom.'themes/'.$theme_details[1].'/';
    		$theme['type'] = 'custom';
    	}

    	return $theme;
    }

    function get_screenshot_url($screenshot_value, $theme_path) {
    	$theme_path_slug = str_replace('/', '-', $theme_path);

		if(!empty($screenshot_value) && count(explode('/', $screenshot_value)) == 1 && file_exists($this->plugin_dir_custom.'screenshots/'.$screenshot_value))
			$screenshot_value = $this->plugin_dir_url_custom.'multisite-theme-manager-files/screenshots/'.$screenshot_value;
		elseif(empty($screenshot_value) && $this->options['themes_auto_screenshots_by_name'] && file_exists($this->plugin_dir_custom.'screenshots/'.$theme_path_slug.'.png'))
			$screenshot_value = $this->plugin_dir_url_custom.'multisite-theme-manager-files/screenshots/'.$theme_path_slug.'.png';

    	return (is_ssl()) ? str_replace('http://', 'https://', $screenshot_value) : str_replace('https://', 'http://', $screenshot_value);
    }

	function get_merged_themes_categories($themes = false) {
		if(!isset($this->themes_categories_config) || !is_array($this->themes_categories_config))
			$this->themes_categories_config = array();
		if(!isset($this->themes_categories) || !is_array($this->themes_categories))
			$this->themes_categories = array();

		//remove categories with same label
		$unique_config_categories = array();
		foreach ($this->themes_categories_config as $key => $value)
			if(!in_array($value, $this->themes_categories) )
				$unique_config_categories[$key] = $value;

		$categories = array_merge($unique_config_categories, $this->themes_categories);
		asort($categories);

		//lets remove cats that are not assigned to any theme
		if($themes) {
			foreach ($categories as $category_key => $category_name) {
				$category_valid = false;
				foreach ($themes as $theme_path => $theme_details) {
					if(isset($theme_details['categories_keys']) && is_array($theme_details['categories_keys']) && in_array($category_key, $theme_details['categories_keys'])) {
						$category_valid = true;
						break;
					}
				}

				if(!$category_valid)
					unset($categories[$category_key]);
			}
		}

		return $categories;
	}

	function get_merged_themes_custom_data() {
		if(!isset($this->themes_custom_data_config) || !is_array($this->themes_custom_data_config))
			$this->themes_custom_data_config = get_site_option('wmd_prettythemes_themes_custom_data_config', array());
		if(!isset($this->themes_custom_data) || !is_array($this->themes_custom_data))
			$this->themes_custom_data = get_site_option('wmd_prettythemes_themes_custom_data', array());

		$themes = array_replace_recursive($this->themes_custom_data_config, $this->themes_custom_data);

		//properly merge config categories
		foreach ($themes as $path => $values) {
			$categories = (isset($this->themes_custom_data[$path]['Categories']) && is_array($this->themes_custom_data[$path]['Categories'])) ? $this->themes_custom_data[$path]['Categories'] : array();
			$config_categories = (isset($this->themes_custom_data_config[$path]['Categories'])) ? $this->themes_custom_data_config[$path]['Categories'] : array();
			if(count($categories) || count($config_categories))
				$themes[$path]['Categories'] = array_merge($categories, $config_categories);
		}

		ksort($themes);

		return $themes;
	}

	function get_last_category_id() {
		if($this->themes_categories) {
			end($this->themes_categories);
			$last_category = key($this->themes_categories);
			return substr($last_category, 8);
		}
		else
			return 0;
	}

	function get_validated_options($input) {
		if(is_array($input)) {
			foreach (array('author_link', 'custom_link', 'author_link_target', 'custom_link_target', 'tags', 'version') as $type) {
				if(isset($input['themes_options'][$type]) && $input['themes_options'][$type])
					$this->options['themes_options'][$type] = '1';
				else
					$this->options['themes_options'][$type] = '0';
			}


			$possible_themes = $this->get_themes();
			if(isset($input['theme']) && array_key_exists($input['theme'], $possible_themes))
				$this->options['theme'] = $input['theme'];
			else
				$this->options['theme'] = 'standard/quick-sand';

			$standard_options = array('themes_link_label' => 'strip_tags', 'themes_page_title' => 'strip_tags', 'themes_page_description' => '', 'themes_auto_screenshots' => '', 'setup_mode' => '', 'themes_hide_descriptions' => '', 'themes_auto_screenshots_by_name' => '', 'author_link_target' => '', 'custom_link_target' => '');
			foreach ($standard_options as $option => $action) {
				if(isset($input[$option])) {
					if($action == 'strip_tags')
						$input[$option] = strip_tags($input[$option]);
					$this->options[$option] = $input[$option];
				}
				elseif(!isset($this->options[$option]))
					$this->options[$option] = $this->default_options[$option];
			}
		}

		return $this->options;
	}

	function get_converted_themes_data_for_js($themes_custom_data_source = array()) {
		$themes_custom_data_ready = array();
		foreach ($themes_custom_data_source as $path => $details) {
			$possible_data = array('Name', 'Description', 'Categories', 'CustomLink', 'CustomLinkLabel', 'ScreenShot', 'ScreenShotID');
			$strip_slashes = array('Name', 'Description', 'CustomLinkLabel');
			foreach ($possible_data as $possible_data_name) {
				if(in_array($possible_data_name, $strip_slashes))
					$details[$possible_data_name] = stripslashes($details[$possible_data_name]);

				$details[$possible_data_name] = (isset($details[$possible_data_name]) && !empty($details[$possible_data_name])) ? $details[$possible_data_name] : null;
			}

			$details['ScreenShotPreview'] = $this->get_screenshot_url($details['ScreenShot'], $path);

			$details['deprecateDate'] = (isset($details['deprecateDate']) && $details['deprecateDate']) ? $details['deprecateDate'] : null;

			$themes_custom_data_ready[$path] = array(
					'path' => $path,
					'name' => $details['Name'],
					'description' => $details['Description'],
					'categories' => $details['Categories'],
					'custom_url' => $details['CustomLink'],
					'custom_url_label' => $details['CustomLinkLabel'],
					'image_url' => $details['ScreenShot'],
					'image_url_preview' => $details['ScreenShotPreview'],
					'image_id' => $details['ScreenShotID'],
					'deprecate_on_off' => $details['deprecateDate'],
					'deprecate_jj' => $details['deprecateDate'] ? date('d', $details['deprecateDate']) : null,
					'deprecate_mm' => $details['deprecateDate'] ? date('m', $details['deprecateDate']) : null,
					'deprecate_aa' => $details['deprecateDate'] ? date('Y', $details['deprecateDate']) : null
				);
		}

		return $themes_custom_data_ready;
	}

	function get_merged_theme_data($fe = false) {
		$themes_categories = $this->get_merged_themes_categories();

		$themes_default_data = $this->themes_data;
		$themes_custom_data = $this->get_merged_themes_custom_data();

		$themes = array();
		foreach($themes_default_data as $key => $theme) {
			if(!isset($theme['id']))
				continue;

			if($fe == true) {
				$allowed_themes = get_site_option('allowedthemes');
				if(!isset($allowed_themes[$theme['id']]))
					continue;
			}

			//lets skip this theme if it is deprecated
			if(isset($themes_custom_data[$theme['id']]['deprecateDate']) && $themes_custom_data[$theme['id']]['deprecateDate'])
				continue;

			//first lets do stuff independend from custom data

			$theme['version'] = $this->options['themes_options']['version'] ? $theme['version'] : false;
			$theme['tags'] = $this->options['themes_options']['tags'] ? $theme['tags'] : false;
			$theme['authorAndUri'] = $this->options['themes_options']['author_link'] ? $theme['authorAndUri'] : false;


			//lets check if there is custom data to apply

			$target = (isset($this->options['themes_options']['author_link_target']) && $this->options['themes_options']['author_link_target']) ? ' target="_blank"' : '';
			$theme['authorAndUri'] = str_replace('<a href=', '<a'.$target.' href=', $theme['authorAndUri']);

			if(!isset($themes_custom_data[$theme['id']])) {
				$themes[$key] = apply_filters('wmd_prettythemes_merged_theme_data', $theme);
				continue;
			}

			$theme_path = $theme['id'];
			$theme_custom_data = $themes_custom_data[$theme['id']];

			//replace name and description
			$data_merge = array('Name' => 'name', 'Description' => 'description');
			foreach ($data_merge as $source => $target)
				if(isset($theme_custom_data[$source]) && $theme_custom_data[$source])
					$theme[$target] = stripslashes($theme_custom_data[$source]);

			//set correct screenshot
			if(isset($theme_custom_data['ScreenShot']) && $theme_custom_data['ScreenShot']) {
				$screenshot = $this->get_screenshot_url($theme_custom_data['ScreenShot'], $theme['id']);
				if($screenshot)
					$theme['screenshot'] = array($screenshot);
			}

			$link_label = (isset($theme_custom_data['CustomLinkLabel']) && $theme_custom_data['CustomLinkLabel']) ? $theme_custom_data['CustomLinkLabel'] : $this->options['themes_link_label'];
			$target = (isset($this->options['themes_options']['custom_link_target']) && $this->options['themes_options']['custom_link_target']) ? ' target="_blank"' : '';
			$theme['customLinkAndUri'] = (isset($this->options['themes_options']['custom_link']) && isset($theme_custom_data['CustomLink']) && $this->options['themes_options']['custom_link'] && $link_label) ? '<a'.$target.' href="'.$theme_custom_data['CustomLink'].'">'.stripslashes($link_label).'</a>' : false;

			if(isset($theme_custom_data['Categories']) && count($theme_custom_data['Categories']) > 0) {
				$categories = $categories_keys = array();
				foreach ($theme_custom_data['Categories'] as $theme_category_key) {
					//check if its missing because we wanted to remove duplicated categories from config
					if(!isset($themes_categories[$theme_category_key]))
						$theme_category_key = array_search($this->themes_categories_config[$theme_category_key], $this->themes_categories);

					if(isset($themes_categories[$theme_category_key])) {
						$categories[] = $themes_categories[$theme_category_key];
						$categories_keys[] = $theme_category_key;
						$theme['categories'] = implode(', ', $categories);
						$theme['categories_keys'] = $categories_keys;
					}
				}
			}

			$themes[$key] = apply_filters('wmd_prettythemes_merged_theme_data', $theme);
		}

		//sort and recreate keys
		uasort($themes, array($this,'compare_by_name'));
		$themes = array_values($themes);

		return $themes;
	}

	//theme deprecation

	function get_deprecation_date($stylesheet = false) {
		if(!$stylesheet) 
			$stylesheet = get_stylesheet();
		$themes = $this->get_merged_themes_custom_data();

		return (isset($themes[$stylesheet]['deprecateDate']) && $themes[$stylesheet]['deprecateDate']) ? $themes[$stylesheet]['deprecateDate'] : false;
	} 

	function deprecate_engine() {
		global $mtm_current_theme_expire_status;
		
		$current_stylesheet = get_stylesheet();

		$mtm_current_theme_expire_status = $this->get_deprecation_date($current_stylesheet);

		if($mtm_current_theme_expire_status) {
			if(is_numeric($mtm_current_theme_expire_status) && $mtm_current_theme_expire_status < time()) {
				//get default theme
				$default_theme = get_site_option('default_theme', 0);
				if(!$default_theme)
					$default_theme = (defined('WP_DEFAULT_THEME')) ? WP_DEFAULT_THEME : 0;
				
				//make sure that deprecated theme is not default theme
				if($default_theme != $current_stylesheet) {
					$default_theme = wp_get_theme($default_theme);
					switch_theme(esc_html($default_theme->template), esc_html($default_theme->stylesheet));

					/*
					//lets network deactivate this theme so it wont be used again - its not needed anymore since we are blocking it with the plugin anyway
					$network_allowed_themes = get_site_option('allowedthemes');
					if(isset($network_allowed_themes[$current_theme->stylesheet])) {
						unset($network_allowed_themes[$current_theme->stylesheet]);
						update_site_option('allowedthemes', $network_allowed_themes);
					}
					*/

					wp_redirect($_SERVER['REQUEST_URI']);
					exit();
				}
			}
			else
				add_action('admin_notices', array($this, 'theme_deprecated_warning'), $count);
		}
	}

	function theme_deprecated_warning() {
		global $mtm_current_theme_expire_status, $pagenow;

		$seconds_left = $mtm_current_theme_expire_status - time();

		$time_left = floor($seconds_left/86400);
		if($time_left <= 1) {
			$time_left = floor($seconds_left/3600);
			$type = 'hours';
		}
		else
			$type = 'days';

		$link_text = $pagenow != 'themes.php' ? ' <a href="'.admin_url('themes.php').'">'.__( 'here', 'theme-expire' ).'</a>' : '';
    ?>
	    <div class="error">
	        <p><?php printf(__( 'The theme you are using is being retired and will be removed in %s %s. Please change to a newer theme%s.', 'theme-expire' ), $time_left, $type, $link_text); ?></p>
	    </div>
    <?php
	}


	//Actions


	function import_xml_data_setting_file($file_path, $config = 0) {
	    $xml = simplexml_load_string(str_replace(array("\n", "\r"), "", file_get_contents($file_path) ));
	    $xml_json = json_encode($xml);
	    $xml_import_data = json_decode($xml_json,TRUE);

		if(isset($xml_import_data['Categories'])) {
			$themes_categories_to_import = array();

			//replace names for config categories
			if($config) {
				//rename categories so they have "config" at the beginning
				foreach ($xml_import_data['Categories'] as $key => $value) {
					$new_key = str_replace('category', 'configcategory', $key);

					$themes_categories_replace[$key] = $new_key;
					$themes_categories_to_import[$new_key] = $value;
				}
			}
			//looks for different keyes with same value and creates new key for them
			elseif(!empty($this->themes_categories)) {
				$themes_categories_replace = array();
				$last_category = 0;
				foreach ($xml_import_data['Categories'] as $key => $value) {
					$category_key = array_search($value, $this->themes_categories);
					if(isset($category_key) && $category_key)
						$themes_categories_replace[$key] = $category_key;
					elseif(isset($this->themes_categories[$key]) && $this->themes_categories[$key] != $value) {
						if(!$last_category) {
							end($this->themes_categories);
							$last_category = key($this->themes_categories);
							$last_category = substr($last_category, 8);
						}

						$last_category ++;
						$new_last_category = 'category'.$last_category;
						$themes_categories_replace[$key] = $new_last_category;
						$themes_categories_to_import[$new_last_category] = $value;
					}
					else
						$themes_categories_to_import[$key] = $value;
				}
			}
			else
				$themes_categories_to_import = $xml_import_data['Categories'];

			if($config) {
				$this->themes_categories_config = $themes_categories_to_import;
				update_site_option('wmd_prettythemes_themes_categories_config', $this->themes_categories_config);
			}
			else {
				$this->themes_categories = array_replace_recursive($this->themes_categories, $themes_categories_to_import);
				update_site_option('wmd_prettythemes_themes_categories', $this->themes_categories);
			}
		}

		if(isset($xml_import_data['Themes']['Theme'])) {
			//fix for single theme in xml
			if(!isset($xml_import_data['Themes']['Theme'][0]))
				$xml_import_data['Themes']['Theme'] = array(0 => $xml_import_data['Themes']['Theme']);

			$theme_custom_data_to_import = array();
			foreach($xml_import_data['Themes']['Theme'] as $key => $value) {
				if(isset($value['Path'])) {
					$path = $value['Path'];
					unset($value['Path']);
					unset($value['ScreenShotID']);

					//fix for single theme category in xml
					if(isset($value['Categories']['item']) && !is_array($value['Categories']['item']))
						$value['Categories']['item'] = array(0 => $value['Categories']['item']);

					//Merges old categories with new one
					if(isset($value['Categories']['item']) && isset($themes_categories_replace) && $themes_categories_replace) {
						//configure theme categories
						$new_categories = array();
						foreach ($value['Categories']['item'] as $id => $category) {
							if(array_key_exists($category, $themes_categories_replace))
								$new_categories[] = $themes_categories_replace[$category];
							else
								$new_categories[] = $category;
						}

						if(!$config && isset($this->themes_custom_data[$path]['Categories'])) {
							$value['Categories'] = array_merge_recursive($this->themes_custom_data[$path]['Categories'], $new_categories);
							$value['Categories'] = array_unique($value['Categories']);
						}
						else
							$value['Categories'] = $new_categories;
					}
					elseif(isset($value['Categories']['item']))
						$value['Categories'] = $value['Categories']['item'];
					else
						unset($value['Categories']);

					if(!empty($value))
						$theme_custom_data_to_import[$path] = $value;
				}
			}
			if(!empty($theme_custom_data_to_import)) {
				if($config) {
					$this->themes_custom_data_config = $theme_custom_data_to_import;
					update_site_option('wmd_prettythemes_themes_custom_data_config', $this->themes_custom_data_config);
				}
				else {
					$this->themes_custom_data = array_replace_recursive($this->themes_custom_data, $theme_custom_data_to_import);
					ksort($this->themes_custom_data);
					update_site_option('wmd_prettythemes_themes_custom_data', $this->themes_custom_data);
				}
			}
		}

		if(isset($xml_import_data['Options'])) {
			$validated = $this->get_validated_options($xml_import_data['Options']);

			update_site_option( 'wmd_prettythemes_options', $validated );
		}
	}

	function export_xml_data_setting_file() {
		if(wp_verify_nonce($_REQUEST['_wpnonce'], 'wmd_prettythemes_options')) {
			//rename categories to remove "config" part and merges
			$themes_categories_xml = array();
			$themes_categories_config_ready = array();
			$themes_categories_replace = array();
			$last_category = 0;
			foreach ($this->themes_categories_config as $key => $value) {
				if(!empty($this->themes_categories))
					$category_key = array_search($value, $this->themes_categories);
				if(isset($category_key) && $category_key)
					$themes_categories_replace[$key] = $category_key;
				else {
					if(!$last_category)
						$last_category = $this->get_last_category_id();
					$last_category ++;
					$new_last_category = 'category'.$last_category;
					$themes_categories_replace[$key] = $new_last_category;
					$themes_categories_config_ready[$new_last_category] = $value;
				}
			}
			$themes_categories_xml = array_merge($themes_categories_config_ready, $this->themes_categories);

			$themes_custom_data_xml = array();
			foreach ($this->get_merged_themes_custom_data() as $path => $value) {
				//replace themes categories keys to match new ones(without config in name)
				if(isset($value['Categories']) && $themes_categories_replace) {
					$new_categories = array();
					foreach ($value['Categories'] as $id => $category) {
						if(array_key_exists($category, $themes_categories_replace))
							$new_categories[] = $themes_categories_replace[$category];
						else
							$new_categories[] = $category;
					}
					if(isset($this->themes_custom_data[$path]['Categories'])) {
						$value['Categories'] = array_merge_recursive($this->themes_custom_data[$path]['Categories'], $new_categories);
						$value['Categories'] = array_unique($value['Categories']);
					}
					else
						$value['Categories'] = $new_categories;
				}

				//move path from key to array
				$themes_custom_data_xml[] = array_merge(array('Path' => $path), $value);
			}

			$filename = 'config.xml';

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ), true );

			$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";

			$xml .= '<Themes-data-settings>'."\n";
				$xml .= '<Themes>';
					$xml .= $this->get_array_as_xml($themes_custom_data_xml, 'Theme');
				$xml .= '</Themes>'."\n";

				$xml .= '<Categories>';
					$xml .= $this->get_array_as_xml($themes_categories_xml);
				$xml .= '</Categories>'."\n";

				$xml .= '<Options>';
					$xml .= $this->get_array_as_xml($this->options);
				$xml .= '</Options>'."\n";
			$xml .= '</Themes-data-settings>';

			echo $xml;

			die();
		}
	}


	//themes integration


	function prosite_theme_available($theme_file) {
		$psts_themes = $this->pro_site_settings['pp_themes'];
		if(isset($psts_themes[$theme_file]['level']) && $psts_themes[$theme_file]['level'] != 0 && is_numeric($psts_themes[$theme_file]['level']) && !is_super_admin())
			if((function_exists('is_pro_site') && is_pro_site($this->blog_id, $psts_themes[$theme_file]['level'])))
				return true;
			else
				return false;
		else
			return true;
	}

	function prosite_theme_required_level_name($theme_file) {
		global $psts;
		$psts_themes = $this->pro_site_settings['pp_themes'];

		if(isset($psts_themes[$theme_file]['level']) && $psts_themes[$theme_file]['level'] != 0 && is_numeric($psts_themes[$theme_file]['level'])) {
			return $psts->get_level_setting($psts_themes[$theme_file]['level'], 'name');
		}
		else
			return true;
	}


	//Functions


	function get_resized_attachment_url($attachment_id, $width = '880', $height = '660', $crop = true, $suffix = "-theme-screenshot") {
		$attachment_url = wp_get_attachment_url($attachment_id);
		if($attachment_url) {
			$attachment_meta = wp_get_attachment_metadata($attachment_id);
			if($attachment_meta['width'] > $width || $attachment_meta['height'] > $height) {
				$old_image_details = array('path' => get_attached_file($attachment_id), 'url' => $attachment_url);
				foreach ($old_image_details as $type => $address) {
					$path_parts = pathinfo($address);
					$filename = $path_parts['filename'];
					$new_filename = $filename.$suffix.'.'.$path_parts['extension'];
					$new_detail = $path_parts['dirname'].'/'.$new_filename;

					$new_image_details[$type] = $new_detail;
				}

				if(!file_exists($new_image_details['path'])) {
					$image = wp_get_image_editor($old_image_details['path']);
					if (!is_wp_error($image)) {
					    $image->resize($width, $height, $crop);
					    $image->save($new_image_details['path']);
					}
				}

				if(file_exists($new_image_details['path']))
					return $new_image_details['url'];
				else
					return false;
			}
			else
				return  $attachment_url;
		}
		else
			return false;
	}

	//Converts array to xml
	function get_array_as_xml($array, $node_name = 'item') {
		$xml = "\n";

		if (is_array($array) || is_object($array)) {
			foreach ($array as $key => $value) {
				if (is_numeric($key)) {
					$key = $node_name;
				}
				$xml .= '<'.$key.'>'.$this->get_array_as_xml($value).'</'.$key.'>'."\n";
			}
		} else {
			$xml = "\n".htmlspecialchars($array, ENT_QUOTES) . "\n";
		}

		return $xml;
	}

	//used to sort themes by name
	function compare_by_name($a, $b) {
		return strtolower($a['name']) > strtolower($b['name']);
	}

	function the_select_options($array, $current) {
		if(empty($array))
			$array = array( 1 => 'True', 0 => 'False' );

		foreach( $array as $name => $label ) {
			$selected = selected( $current, $name, false );
			echo '<option value="'.$name.'" '.$selected.'>'.$label.'</option>';
		}
	}
}

//Compatibility with older PHP
if (!function_exists('array_replace_recursive')) {
	function array_replace_recursive() {
	    $arrays = func_get_args();

	    $original = array_shift($arrays);

	    foreach ($arrays as $array) {
	        foreach ($array as $key => $value) {
	            if (is_array($value)) {
	                $original[$key] = array_replace_recursive($original[$key], $array[$key]);
	            }

	            else {
	                $original[$key] = $value;
	            }
	        }
	    }

	    return $original;
	}
}