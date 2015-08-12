jQuery(document).ready(function() {
	//this code is mostly for compatibility with < WP 4.2
	if(typeof(prettythemes_customize.themes_custom_data[prettythemes_customize.current_theme_path]) != "undefined") {
		var current_theme = prettythemes_customize.themes_custom_data[prettythemes_customize.current_theme_path];
		if(current_theme.name != null) {
			jQuery('#customize-info .theme-name').html(current_theme.name);
			if(typeof _wpCustomizeSettings['sections']['themes'] != 'undefined') {
				_wpCustomizeSettings['sections']['themes'].title = current_theme.name;
				_wpCustomizeSettings['sections']['themes'].content = "<li id=\"accordion-section-themes\" class=\"accordion-section control-section control-section-themes\">\n\t\t\t<h3 class=\"accordion-section-title\">\n\t\t\t\t<span>Previewing theme<\/span> "+current_theme.name+"\n\t\t\t\t<button type=\"button\" class=\"button change-theme\">Change<\/button>\n\t\t\t<\/h3>\n\t\t\t<div class=\"customize-themes-panel control-panel-content themes-php\">\n\t\t\t\t<h2>\n\t\t\t\t\tThemes\t\t\t\t\t<span class=\"title-count theme-count\">21<\/span>\n\t\t\t\t<\/h2>\n\n\t\t\t\t<h3 class=\"accordion-section-title customize-section-title\">\n\t\t\t\t\t<span>Previewing theme<\/span> "+current_theme.name+"\t\t\t\t\t<button type=\"button\" class=\"button customize-theme\">Customize<\/button>\n\t\t\t\t<\/h3>\n\n\t\t\t\t<div class=\"theme-overlay\" tabindex=\"0\" role=\"dialog\" aria-label=\"Theme Details\"><\/div>\n\n\t\t\t\t<div id=\"customize-container\"><\/div>\n\t\t\t\t\t\t\t\t\t<p><label for=\"themes-filter\">\n\t\t\t\t\t\t<span class=\"screen-reader-text\">Search installed themes...<\/span>\n\t\t\t\t\t\t<input type=\"text\" id=\"themes-filter\" placeholder=\"Search installed themes...\" />\n\t\t\t\t\t<\/label><\/p>\n\t\t\t\t\t\t\t\t<div class=\"theme-browser rendered\">\n\t\t\t\t\t<ul class=\"themes accordion-section-content\">\n\t\t\t\t\t<\/ul>\n\t\t\t\t<\/div>\n\t\t\t<\/div>\n\t\t<\/li>";
			}
		}
		if(current_theme.image_url != null)
			jQuery('#customize-info .theme-screenshot').attr('src', current_theme.image_url);
		if(current_theme.description != null)
			jQuery('#customize-info .theme-description').html(current_theme.description);
	}

	jQuery.each(prettythemes_customize.themes_custom_data, function(path, data) {
		var theme_setting_slug = false;
		if(typeof _wpCustomizeSettings['controls']['theme_'+path] != 'undefined')
			theme_setting_slug = 'theme_'+path;
		if(!theme_setting_slug && typeof _wpCustomizeSettings['controls'][path] != 'undefined')
			theme_setting_slug = path;

		if(theme_setting_slug) {
			if(data.deprecate_on_off) {
				delete _wpCustomizeSettings['controls'][theme_setting_slug];
			}
			else {
				if(data.name != null)
					_wpCustomizeSettings['controls'][theme_setting_slug]['theme'].name = data.name;
				if(data.image_url != null)
					_wpCustomizeSettings['controls'][theme_setting_slug]['theme'].screenshot[0] = data.image_url;
				if(data.description != null)
					_wpCustomizeSettings['controls'][theme_setting_slug]['theme'].description = data.description;
			}
		}
	});
});