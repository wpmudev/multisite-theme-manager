<div class="wrap">

	<?php screen_icon('tools'); ?>
	<h2><?php _e('Multisite Theme Manager Settings', 'wmd_multisitethememanager') ?></h2>
	<p class="configuration-steps">
		<img src="<?php echo $this->plugin_dir_url.'multisite-theme-manager-files/images/configuration-tips.jpg'; ?>" alt="<?php echo esc_attr(__('This page lets you control Multisite Theme Manager. You can modify the details for every theme in your network by clicking Edit Details for each theme in Network Admin > Themes > Installed Themes. Go to Main Site Dashboard > Appearance > '.$this->options['themes_page_title'].' to see how it currently looks like on the main site.', 'wmd_multisitethememanager')) ?>"/>
	</p>
	<form action="settings.php?page=multisite-theme-manager.php" method="post" enctype="multipart/form-data">

		<?php
		settings_fields('wmd_prettythemes_options');
		?>

		<h3><?php _e('General Settings', 'wmd_multisitethememanager') ?></h3>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettythemes_options[setup_mode]"><?php _e('Setup Mode', 'wmd_multisitethememanager') ?></label>
				</th>

				<td>
					<select name="wmd_prettythemes_options[setup_mode]">
						<?php $this->the_select_options(array( 1 => 'Setup Mode Enabled', 0 => 'Setup Mode Disabled' ), $this->options['setup_mode']); ?>
					</select>
					<p class="description"<?php echo ($this->options['setup_mode'] == 1) ? ' style="color:red;"' : ''; ?>><?php _e('When set to "Setup Mode Enabled", the Multisite Theme Manager theme page will be visible only on the main site. This mode is useful for configuring theme details before enabling the Multisite Theme Manager features on all sites in the network.', 'wmd_multisitethememanager') ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettythemes_options[theme]"><?php _e('Select Theme For Theme Page', 'wmd_multisitethememanager') ?></label>
				</th>

				<td>
					<?php
					$select_options = $this->get_themes();
					?>
					<select name="wmd_prettythemes_options[theme]">
						<?php $this->the_select_options($select_options, $this->options['theme']); ?>
					</select>
					<p class="description"><?php _e('Choose the theme that you want to use to display your theme page. You can add your own themes into "wp-content/uploads/multisite-theme-manager/your-theme/" folder. (Tip: duplicate the default theme from "wp-content/plugins/multisite-theme-manager/multisite-theme-manager-files/themes/" to get started.)', 'wmd_multisitethememanager') ?></p>
				</td>
			</tr>

		</table>

		<h3><?php _e('Theme and Appearance Settings', 'wmd_multisitethememanager'); ?></h3>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettythemes_options[themes_options]"><?php _e('Select details that you want to show for themes', 'wmd_multisitethememanager') ?></label>
				</th>
				<?php $select_options = array('' => __('Open in the same window', 'wmd_multisitethememanager'), '1' => __('Open in new window', 'wmd_multisitethememanager')); ?>
				<td>
					<label><input name="wmd_prettythemes_options[themes_options][author_link]" type="checkbox" id="registrationnotification" value="1" <?php checked( '1', $this->options['themes_options']['author_link']); ?>>Author Link</label> 
						<label>
							<select name="wmd_prettythemes_options[themes_options][author_link_target]">
								<?php $this->the_select_options($select_options, $this->options['themes_options']['author_link_target']); ?>
							</select>
						</label>
						<br/>
					<label><input name="wmd_prettythemes_options[themes_options][custom_link]" type="checkbox" id="registrationnotification" value="1" <?php checked( '1', $this->options['themes_options']['custom_link']); ?>>Custom Link</label>
						<label>
							<select name="wmd_prettythemes_options[themes_options][custom_link_target]">
								<?php $this->the_select_options($select_options, $this->options['themes_options']['custom_link_target']); ?>
							</select>
						</label>
						<br/>
					<label><input name="wmd_prettythemes_options[themes_options][tags]" type="checkbox" id="registrationnotification" value="1" <?php checked( '1', $this->options['themes_options']['tags']); ?>>Tags</label><br/>
					<label><input name="wmd_prettythemes_options[themes_options][version]" type="checkbox" id="registrationnotification" value="1" <?php checked( '1', $this->options['themes_options']['version']); ?>>Version</label>
					<p class="description"><?php _e('Choose which links will be displayed for each theme', 'wmd_multisitethememanager') ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettythemes_options[themes_auto_screenshots_by_name]"><?php _e('Auto Load Screenshot With Correct Name', 'wmd_multisitethememanager') ?></label>
				</th>

				<td>
					<select name="wmd_prettythemes_options[themes_auto_screenshots_by_name]">
						<?php $this->the_select_options(array(), $this->options['themes_auto_screenshots_by_name']); ?>
					</select>
					<p class="description"><?php _e('If the featured image for a theme has not been set and there is an image located in "wp-content/uploads/multisite-theme-manager/screenshots/" with the correct name (example: theme location - "wp-content/themes/twentythirteen", image file - "twentythirteen.png".), it will autoload. Only PNG files will work in this method', 'wmd_multisitethememanager') ?></p>
				</td>
			</tr>
		</table>

		<h3><?php _e('Labels', 'wmd_multisitethememanager') ?></h3>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettythemes_options[themes_link_label]"><?php _e('Theme Page Title', 'wmd_multisitethememanager') ?></label>
				</th>

				<td>
					<input type="text" class="regular-text" name="wmd_prettythemes_options[themes_page_title]" value="<?php echo stripslashes(esc_attr($this->options['themes_page_title'])); ?>"/>
					<p class="description"><?php _e('This is what you call the "Themes" menu item. Call it "Themes", "Design", "Page Look" or whatever you\'d like.', 'wmd_multisitethememanager') ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettythemes_options[themes_page_description]"><?php _e('Theme Page Description', 'wmd_multisitethememanager') ?></label>
				</th>

				<td>
					<input type="text" class="regular-text" style="width:95%;" name="wmd_prettythemes_options[themes_page_description]" value="<?php echo stripslashes(esc_attr($this->options['themes_page_description'])); ?>"/>
					<p class="description"><?php _e('This text will be visible at the top of the themes page. Tell your users what you have to offer.', 'wmd_multisitethememanager') ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettythemes_options[themes_link_label]"><?php _e('Default Custom Link Label', 'wmd_multisitethememanager') ?></label>
				</th>

				<td>
					<input type="text" class="regular-text" name="wmd_prettythemes_options[themes_link_label]" value="<?php echo stripslashes(esc_attr($this->options['themes_link_label'])); ?>"/>
					<p class="description"><?php _e('This will be default label for custom link that you can add for each theme when editing details (for example, a link to support documents for the theme). You can also overwrite label per theme.', 'wmd_multisitethememanager') ?></p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" name="save_settings" class="button-primary" value="<?php _e('Save Changes', 'wmd_multisitethememanager') ?>" />
		</p>

		<h3><?php _e('Export and Import', 'wmd_multisitethememanager'); ?></h3>
		
		<?php $prettythemes_options_nonce = wp_create_nonce('wmd_prettythemes_options'); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label><?php _e('Export', 'wmd_multisitethememanager') ?></label>
				</th>

				<td>
					<a href="<?php echo add_query_arg(array('prettythemes_action' => 'export', '_wpnonce' => $prettythemes_options_nonce)); ?>" class="button"><?php _e('Download Export File', 'wmd_multisitethememanager') ?></a>
					<p class="description">
						<?php _e('Export data and settings for later import or use as a configuration file. You can put exported file named "config.xml" into "wp-content/uploads/multisite-theme-manager/" folder to autoload data and settings.', 'wmd_multisitethememanager') ?> <small><?php _e('Keep in mind that data from current config file (if exists) will also be exported.', 'wmd_multisitethememanager') ?></small>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettythemes_options[themes_link_label]"><?php _e('Import:', 'wmd_multisitethememanager') ?></label>
				</th>

				<td>
					<input type="file" name="config_file" id="upload" size="25">
					<input type="submit" name="import_config" class="button" value="<?php _e('Upload file and import', 'wmd_multisitethememanager'); ?>"/>
					<p class="description"><?php _e('Choose an export file (correctly formatted XML file) to import data and settings. This action will replace any existing data and settings.', 'wmd_multisitethememanager') ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label><?php _e('Reset:', 'wmd_multisitethememanager') ?></label>
				</th>

				<td>
					<a onclick="return confirm('<?php _e('Are you sure?', 'wmd_multisitethememanager'); ?>')" href="<?php echo add_query_arg(array('prettythemes_action' => 'delete_custom_data', '_wpnonce' => $prettythemes_options_nonce)); ?>" class="button"><?php _e('Delete all custom theme data', 'wmd_multisitethememanager') ?></a>
					<a onclick="return confirm('<?php _e('Are you sure?', 'wmd_multisitethememanager'); ?>')" href="<?php echo add_query_arg(array('prettythemes_action' => 'reset_settings', '_wpnonce' => $prettythemes_options_nonce)); ?>" class="button"><?php _e('Reset all settings', 'wmd_multisitethememanager') ?></a>

					<p class="description">
						<?php _e('This action will permanently delete choosen data.', 'wmd_multisitethememanager') ?></br>
					</p>
				</td>
			</tr>
		</table>
	</form>

</div>