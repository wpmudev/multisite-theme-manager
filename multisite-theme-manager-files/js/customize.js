jQuery(document).ready(function() {
	if(wmd_msreader.current_theme.name != null)
		jQuery('#customize-info .theme-name').html(wmd_msreader.current_theme.name);
	if(wmd_msreader.current_theme.image_url != null)
		jQuery('#customize-info .theme-screenshot').attr('src', wmd_msreader.current_theme.image_url);
	if(wmd_msreader.current_theme.description != null)
		jQuery('#customize-info .theme-description').html(wmd_msreader.current_theme.description);
});