<?php 

class WMD_PrettyThemesFEShowcase extends WMD_PrettyThemes_Functions {
	var $plugin;

	var $preview_blog_id;

	function __construct() {
		global $wmd_prettythemes;
		$this->plugin = $wmd_prettythemes;

		add_shortcode('wmd-theme-showcase', array($this,'display_theme_showcase'));

		add_action('init', array($this, 'set_theme_preview_data' ) );
		add_filter('stylesheet', array($this, 'get_stylesheet'));
		add_filter('template', array($this, 'get_template'));
	}

	function display_theme_showcase($atts) {
		global $post;

		$atts = shortcode_atts(array('preview_site_id' => '', 'themes' => false, 'hide_interface' => false, 'show_buttons' => 'hover', 'category' => ''), $atts, 'wmd-theme-showcase');
		$this->preview_blog_id = (int) $atts['preview_site_id'];

		$this->plugin->init_vars();
		$this->plugin->set_custom_theme_data();

		if(isset($_GET['wmd-fe-showcase-theme-details']) && $_GET['wmd-fe-showcase-theme-details']) {
			if(!function_exists('wp_prepare_themes_for_js'))
				require_once(ABSPATH . '/wp-admin/includes/theme.php');

			wp_enqueue_style('wmd-prettythemes-fe-theme', $this->plugin->plugin_dir_url.'multisite-theme-manager-files/includes/frontend-showcase-files/style.css', array(), 4);

			$this->plugin->themes_data = wp_prepare_themes_for_js( array( wp_get_theme($_GET['wmd-fe-showcase-theme-details']) ) );
			$theme = $this->plugin->get_merged_theme_data();
			$theme = $theme[0];
			ob_start();
			include($this->plugin->plugin_dir.'multisite-theme-manager-files/includes/frontend-showcase-files/single_theme.php');
			return ob_get_clean();			
		}
		else {
			add_filter('wmd_prettythemes_merged_theme_data', array($this, 'set_theme_data'));

			wp_enqueue_style('wmd-prettythemes-fe-theme', $this->plugin->plugin_dir_url.'multisite-theme-manager-files/includes/frontend-showcase-files/style.css', array(), 4);
			wp_enqueue_script('wmd-prettythemes-fe-theme', $this->plugin->plugin_dir_url.'multisite-theme-manager-files/includes/frontend-showcase-files/theme.js', array('jquery', 'backbone', 'wp-backbone'), 5);
			
			if($atts['themes']) {
				$themes_stylesheets = explode(',', str_replace(' ', '' , $atts['themes']));
				$themes = array();
				foreach ($themes_stylesheets as $stylesheet)
					$themes[] = wp_get_theme($stylesheet);
			}
			if(!isset($themes) || !$themes)
				$themes = false; 
			$this->plugin->enqueue_theme_showcase_script_data('wmd-prettythemes-fe-theme', parse_url( get_permalink($post->ID), PHP_URL_PATH ), true, $themes, true);

			ob_start();
			include($this->plugin->plugin_dir.'multisite-theme-manager-files/includes/frontend-showcase-files/theme_list.php');
			return ob_get_clean();
		}
	}

	function set_theme_data($theme) {
		global $post;
		
		$theme['live_preview_url'] = $this->get_live_preview_url($theme['id']);
		$theme['details_url'] = get_permalink($post->ID).'?wmd-fe-showcase-theme-details='.$theme['id'];


		return $theme;
	}

	function get_live_preview_url($theme_id, $preview_blog_id = false) {
		global $post;

		if(!$preview_blog_id)
			$preview_blog_id = $this->preview_blog_id;

		$url = apply_filters('wmd_prettythemes_showcase_theme_site', get_permalink($post->ID));

		return $url.'?wmd-fe-showcase-theme-preview='.$theme_id.'&wmd-fe-showcase-preview-blog-id='.$preview_blog_id;
	}


	function set_theme_preview_data() {
		if ( isset($_GET['wmd-fe-showcase-theme-preview']) && $_GET['wmd-fe-showcase-theme-preview'] && isset($_GET['wmd-fe-showcase-preview-blog-id']) && is_numeric($_GET['wmd-fe-showcase-preview-blog-id']) ) {
			$blog_url = get_site_url($_GET['wmd-fe-showcase-preview-blog-id']);
			if($blog_url) {
				setcookie( 'wmd-fe-showcase', json_encode(array('blog_id' => $_GET['wmd-fe-showcase-preview-blog-id'], 'theme' => esc_attr($_GET['wmd-fe-showcase-theme-preview']))), time() + 30000000, COOKIEPATH, COOKIE_DOMAIN );			
			
				wp_redirect($blog_url);
				exit();
			}
		}
	}
	function get_stylesheet( $stylesheet ) {

		if (is_admin())
			return $stylesheet;
		
        /* Get theme name */
		$theme = $this->get_preview_theme_name();
		if ( empty( $theme ) )
			return $stylesheet;
		
        /* Get theme by name */
		$theme = wp_get_theme( $theme );
        if ( empty( $theme ) )
			return $stylesheet;
		
		/* Don't let people peek at unpublished themes. */
		if ( isset( $theme['Status'] ) && $theme['Status'] != 'publish' )
			return $stylesheet;

		add_action('posts_selection', array($this, 'showcase_theme_tweaks'), 999);

		return $theme['Stylesheet'];
	}
	function get_template( $template ) {

		if (is_admin())
			return $template;
			
        /* Get theme name */
		$theme = $this->get_preview_theme_name();
		if ( empty( $theme ) )
			return $template;
		
        /* Get theme by name */
		$theme = wp_get_theme( $theme );
		if ( empty( $theme ) )
			return $template;

		/* Don't let people peek at unpublished themes. */
		if ( isset($theme['Status'] ) && $theme['Status'] != 'publish' )
			return $template;

		return $theme['Template'];
	}
	function get_preview_theme_name() {
		if ( !empty( $_COOKIE[ 'wmd-fe-showcase' ] ) ) {
			$cookie = json_decode(stripslashes($_COOKIE[ 'wmd-fe-showcase' ]), true);
			return $cookie['blog_id'] == get_current_blog_id() ? $cookie['theme'] : false;
		} else {
			return;
		}
	}

	function showcase_theme_tweaks() {
		$theme = $this->get_preview_theme_name();

		do_action('wmd_prettythemes_showcase_theme_tweaks', $theme);

		//some standard mods
		$tweaks = apply_filters('wmd_prettythemes_showcase_theme_buildin_tweaks', array(
			'remove_post_thumbnails' => array(),
			'remove_post_thumbnails_single' => array()
		));
		if(
			(isset($tweaks['remove_post_thumbnails']) && in_array($theme, $tweaks['remove_post_thumbnails'])) ||
			(is_singular() && isset($tweaks['remove_post_thumbnails_single']) && in_array($theme, $tweaks['remove_post_thumbnails_single']))
		) {
			remove_theme_support( 'post-thumbnails' );
			remove_post_type_support( 'post', 'thumbnail' );
			add_filter('get_post_metadata', array($this, 'showcase_theme_tweaks_remove_featured_image'), 999, 4);
		}
	}
	function showcase_theme_tweaks_remove_featured_image($value, $object_id, $meta_key, $single) {
		if($meta_key == '_thumbnail_id')
			return '';
		else
			return $value;
	}
}

global $wmd_prettythemes_fe_showcase;
$wmd_prettythemes = new WMD_PrettyThemesFEShowcase;