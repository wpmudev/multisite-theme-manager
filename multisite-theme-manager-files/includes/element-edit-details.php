<table id="inlineedit" style="display: none">
<tbody>
	<tr style="display:none" id="theme-edit" class="inline-edit-row inline-edit-row-post inline-edit-post quick-edit-row quick-edit-row-post inline-edit-post alternate inline-editor">
		<td colspan="4" class="colspanchange">

			<fieldset class="inline-edit-col-left">
			<div class="inline-edit-col">
				<h4><?php _e('Edit Theme Details', 'wmd_multisitethememanager'); ?></h4>

				<label>
					<span class="title" id="name_tooltip"><?php _e('Name', 'wmd_multisitethememanager');?></span>
					<span class="input-text-wrap">
					<input type="text" name="theme_name" class="theme_name" value="">
					</span>
				</label>

				<label class="setting-disabled">
					<span class="title"><?php _e('Orginal Name', 'wmd_multisitethememanager');?></span>
					<span class="input-text-wrap">
					<input type="text" name="theme_name_orginal" class="theme_name_orginal" value="" disabled>
					</span>
				</label>

				<label>
					<span class="title" id="custom_url_tooltip"><?php _e('Custom URL', 'wmd_multisitethememanager');?></span>
					<span class="input-text-wrap"><input type="text" class="theme_custom_url" name="theme_custom_url" value=""></span>
				</label>

				<label>
					<span class="title" id="custom_url_label_tooltip"><?php _e('Custom URL Label', 'wmd_multisitethememanager');?></span>
					<span class="input-text-wrap"><input type="text" class="theme_custom_url_label" name="theme_custom_url_label" value=""></span>
				</label>

				<label>
					<span class="title" id="image_url_tooltip"><?php _e('Image URL', 'wmd_multisitethememanager');?></span>
					<span class="input-text-wrap">
						<input type="text" class="theme_image_url" name="theme_image_url" value="">
						<input type="hidden" class="theme_image_id" name="theme_image_id" value="">
						<a class="theme_image_upload_button button target" href="#"><?php _e('Choose Image', 'wmd_multisitethememanager');?></a>
						<a class="theme_image_edit_button button" href="#" target="_blank"><?php _e('Edit Image', 'wmd_multisitethememanager');?></a>
					</span>
				</label>
				<?php if(!apply_filters('wmd_prettythemes_deprecate_block' , false)) { ?>
					<label>
						<span class="title" id="deprecate_tooltip"><?php _e('Deprecation', 'wmd_multisitethememanager');?></span>
	                    <span class="input-text-wrap">
		                    <div class="timestamp-wrap">
		                        <?php
		                        global $wp_locale;

		                        $time_adj = current_time('timestamp');
		                        $cur_jj = gmdate( 'd', $time_adj );
		                        $cur_mm = gmdate( 'm', $time_adj );
		                        $cur_aa = gmdate( 'Y', $time_adj );

		                        $month = "";
		                        for ( $i = 1; $i < 13; $i = $i +1 ) {
		                            $monthnum = zeroise($i, 2);
		                            $month .= "\t\t\t" . '<option value="' . $monthnum . '"';
		                            if ( $i == $cur_mm )
		                                $month .= ' selected="selected"';
		                            /* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
		                            $month .= '>' . sprintf( __( '%1$s-%2$s' ), $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . "</option>\n";
		                        }
		                        ?>
		                        <select class="theme_deprecate_mm" name="theme_deprecate_mm">
		                            <?php echo $month; ?>
		                        </select>
		                        <input type="text" class="theme_deprecate_jj" name="theme_deprecate_jj" value="<?php echo $cur_jj; ?>" size="2" maxlength="2" autocomplete="off">,
		                        <input type="text" class="theme_deprecate_aa" name="theme_deprecate_aa" value="<?php echo $cur_aa; ?>" size="4" maxlength="4" autocomplete="off">
		                        <span class="theme_deprecate_on_off_holder"><label class="selectit"><input value="true" type="checkbox" class="theme_deprecate_on_off" name="theme_deprecate_on_off"> <?php _e('Enable deprecation', 'wmd_multisitethememanager');?></label></span>
		                    </div>
		                    <div class="default-theme">
		                    	<?php _e('This theme is default. You can not deprecate it.', 'wmd_multisitethememanager');?>
		                    </div>
	                    </span>
					</label>
				<?php } ?>
			</div>
			</fieldset>

			<fieldset class="inline-edit-col-center inline-edit-categories">
			<div class="inline-edit-col">
				<span class="title inline-edit-categories-label" id="categories_tooltip"><?php _e('Categories', 'wmd_multisitethememanager');?></span>
				<ul class="theme-categories-checklist cat-checklist category-checklist">
					<?php

					foreach ($this->themes_categories as $key => $value) {
					?>
						<li class="category-<?php echo $key; ?>">
							<label class="selectit">
								<input value="<?php echo $key; ?>" type="checkbox" name="theme_category[]">
								<span class="category-name"><?php echo $value; ?></span>
									<a href="#<?php echo $key; ?>" class="edit-category-show-form"> <small>(<?php _e('edit', 'wmd_multisitethememanager');?>)</small></a>
							</label>
						</li>
					<?php
					}

					foreach ($this->themes_categories_config as $key => $value) {
					?>
						<li class="category-<?php echo $key; ?>">
							<label class="selectit">
								<input value="<?php echo $key; ?>" type="checkbox" name="theme_category[]" disabled>
								<span class="category-name"><?php echo $value; ?></span>
							</label>
						</li>
					<?php
						}
					?>

				</ul>

				<span class="title inline-edit-categories-label">
					<a class="add-category-show-form" href="#"><?php _e('New category', 'wmd_multisitethememanager');?></a>
					<span class="edit-category" style="display:none;"><?php _e('Edit category', 'wmd_multisitethememanager');?> <span class="edit-category-name"></span></span>
				</span>
				<div class="theme-category-add-edit-holder" style="display:none;">
					<input type="hidden" name="theme_edit_category_key" class="theme-edit-category-key" value="0">
					<p><input type="text" name="theme_new_edit_category" class="theme-new-edit-category" value=""></p>

					<a href="#" title="<?php _e('Add category', 'wmd_multisitethememanager');?>" class="button-secondary category-button add-category-button alignright target"><?php _e('Add', 'wmd_multisitethememanager');?></a>
					<a href="#" title="<?php _e('Edit category', 'wmd_multisitethememanager');?>" class="button-secondary category-button edit-category-save-button alignright target" style="display:none;"><?php _e('Save', 'wmd_multisitethememanager');?> </a>
					<a href="#" title="<?php _e('Cancel', 'wmd_multisitethememanager');?>" class="button-secondary category-cancel-button alignright"><?php _e('Cancel', 'wmd_multisitethememanager');?></a>

					<span class="spinner spinner-add-category"></span>
				</div>
			</div>
			</fieldset>


			<fieldset class="inline-edit-col-right">
			<div class="inline-edit-col">
				<label class="inline-edit-tags">
					<span class="title" id="description_tooltip"><?php _e('Description', 'wmd_multisitethememanager');?></span>
					<textarea cols="22" rows="1" name="theme_description" class="theme_description" autocomplete="off"></textarea>
				</label>
				<label class="inline-edit-tags setting-disabled">
					<span class="title"><?php _e('Orginal Description', 'wmd_multisitethememanager');?></span>
					<textarea cols="22" rows="1" name="theme_description" class="theme_description_orginal" autocomplete="off" disabled></textarea>
				</label>
			</div>
			</fieldset>

			<?php if($this->options['setup_mode']) {?>
				<p class="submit setup-mode-reminder"><?php printf(__('You are in setup mode, go to <a href="%s">Multisite Theme Manager Settings</a> to activate the changes across all sites.', 'wmd_multisitethememanager'), admin_url('network/settings.php?page=multisite-theme-manager.php'));?></p>
			<?php } ?>

			<p class="submit inline-edit-save">
				<?php wp_nonce_field( 'wmd_prettythemes_edit_theme_details', '_wpnonce' ) ?>
				<a accesskey="c" href="#" title="Cancel" class="button-secondary theme-cancel alignleft target"><?php _e('Cancel', 'wmd_multisitethememanager');?></a>
				<a accesskey="s" href="#" title="Update" class="button-primary theme-save alignright target"><?php _e('Update', 'wmd_multisitethememanager');?></a>
				<span class="spinner spinner-save"></span>
				<span class="error" style="display:none"></span>
				<br class="clear">
			</p>
		</td>
	</tr>
</tbody>
</table>