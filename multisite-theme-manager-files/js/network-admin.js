jQuery(document).ready(function() {
	theme_details_array = prettythemes_object_to_array(wmd_pl_na.theme_details);
	themes_categories_array = prettythemes_object_to_array(wmd_pl_na.theme_categories);

	table = jQuery('table.plugins'); table.show();
	table_theme_edit = jQuery('table#inlineedit tr');
	theme_edit_wpnonce = table_theme_edit.find('#_wpnonce').val();

	//add column for image
	table.find('thead tr, tfoot tr').append( '<th scope="col" id="image" class="manage-column column-image">'+wmd_pl_na.image+'</th>' );

	themes_table_array = [];


	//Prepare and add data for each theme
	table.find('tbody > tr:not(.theme-update-tr)').each(function() {
		themes_table = jQuery(this);
		theme_path = themes_table.find('th.check-column input').val();
		if(theme_path) {
			theme_theme_column = themes_table.find('td.theme-title');
			theme_description_column = themes_table.find('td.column-description');
			if(jQuery.inArray(theme_path,wmd_pl_na.network_only_themes) == -1) {
				theme_name = theme_theme_column.find('strong').text();
				theme_description = theme_description_column.find('p').html();

				themes_table_array[theme_path] = {
				    table : themes_table,
				    name : theme_name,
				    description : theme_description,
				    theme_column : theme_theme_column,
				    description_column : theme_description_column
				};
				themes_table.append( '<td class="column-image desc"><div class="theme-image"></div></td>' );

				prettythemes_theme_add_data(theme_path);
			}
			else {
				theme_theme_column.find('div.row-actions-visible .edit a').text(wmd_pl_na.edit_code);
				themes_table.append( '<td class="column-image desc"></td>' );
			}
		}
	});

	//get edit theme details screen on click
	jQuery('a.edit_details').click(function(event) {
		event.preventDefault();

		theme_path = jQuery(this).attr('href').substring(1);
		theme_edit_row = table_theme_edit.clone();
		themes_table_array[theme_path].edit_row = theme_edit_row;
		theme = themes_table_array[theme_path];

		theme.table.hide();
		theme.table.after(theme_edit_row.show());
		if(!!theme_details_array[theme_path]) {
			if(theme_details_array[theme_path].name != null)
				theme.edit_row.find('.theme_name').val(theme_details_array[theme_path].name);
			if(theme_details_array[theme_path].description != null)
				theme.edit_row.find('.theme_description').val(theme_details_array[theme_path].description);
			if(theme_details_array[theme_path].custom_url != null)
				theme.edit_row.find('.theme_custom_url').val(theme_details_array[theme_path].custom_url);
			if(theme_details_array[theme_path].custom_url_label != null)
				theme.edit_row.find('.theme_custom_url_label').val(theme_details_array[theme_path].custom_url_label);
			if(theme_details_array[theme_path].image_url != null)
				theme.edit_row.find('.theme_image_url').val(theme_details_array[theme_path].image_url);
			prettythemes_handle_image_id(theme.edit_row, theme_details_array[theme_path].image_id);
			if(wmd_pl_na.default_theme == theme_path && theme_details_array[theme_path].deprecate_on_off == null) {
				theme.edit_row.find('.default-theme').show();
				theme.edit_row.find('.timestamp-wrap').hide();
			}
			else if(theme_details_array[theme_path].deprecate_on_off != null) {
				theme.edit_row.find('.theme_deprecate_on_off').attr('checked', 'checked');
				theme.edit_row.find('.theme_deprecate_mm').val(theme_details_array[theme_path].deprecate_mm);
				theme.edit_row.find('.theme_deprecate_jj').val(theme_details_array[theme_path].deprecate_jj);
				theme.edit_row.find('.theme_deprecate_aa').val(theme_details_array[theme_path].deprecate_aa);
			}

			if(theme_details_array[theme_path].categories != null) {
				jQuery.each(theme_details_array[theme_path].categories, function( index, value ) {
						theme.edit_row.find('.category-'+value+' input').attr('checked', 'checked');
					});
			}
		}
		theme.edit_row.find('.theme_name_orginal').val(theme.name);
		theme.edit_row.find('.theme_description_orginal').val(theme.description);
		theme.edit_row.find('.target').attr('href','#'+theme_path);

		return false;
	});

	//show interface to add category on click
	table.on( 'click', 'a.add-category-show-form', function(event) {
		event.preventDefault();

		jQuery(this).parent().siblings( ".theme-category-add-edit-holder" ).toggle().find('.theme_new_category').focus();

		return false;
	});

	//makes adding category work
	table.on( 'click', 'a.add-category-button', function(event) {
		event.preventDefault();

		theme_path = jQuery(this).attr('href').substring(1);
		theme = themes_table_array[theme_path];

		theme_new_category = theme.edit_row.find('.theme-new-edit-category').val();

		if(theme_new_category != '') {
			theme.edit_row.find('.spinner-add-category').css('visibility', 'visible');

			var data = { //looks for and sets all variables used for export
				action: 'prettythemes_add_category_ajax',
				_wpnonce: theme_edit_wpnonce,
				theme_new_category: theme_new_category
			};

			jQuery.post(wmd_pl_na.ajax_url, data, function(data){ //post data to specified action trough special WP ajax page
				data = jQuery.parseJSON(data);
				if(data.error == 0)
					if(data.name == theme_new_category) {
						var theme_categories_checklist = theme.edit_row.find('.theme-categories-checklist');
					    theme_categories_checklist.animate({"scrollTop": theme_categories_checklist[0].scrollHeight}, "slow");

						jQuery('.theme-categories-checklist').append('<li  class="category-'+data.id+'"><label class="selectit"><input value="'+data.id+'" type="checkbox" name="theme_category[]"> <span class="category-name">'+data.name+'</span> <a href="#'+data.id+'" class="edit-category-show-form"> <small>('+wmd_pl_na.edit+')</small></a></label></li>');
						theme_categories_checklist.find('.category-'+data.id+' input').attr("checked","checked");

						themes_categories_array[data.id] = data.name;

						prettythemes_hide_new_edit_form(theme.edit_row);
					}
			});
		}

		return false;
	});

	//show interface for editing category
	table.on( 'click', 'a.edit-category-show-form', function(event) {
		event.preventDefault();

		category_id = jQuery(this).attr('href').substring(1);
		theme_category_div = jQuery(this).parents('.inline-edit-col');
		category_name = jQuery(this).parent().find('.category-name').text();

		theme_category_div.find('.add-category-show-form, .add-category-button').hide();

		theme_category_div.find('.edit-category, .edit-category-save-button').show();
		theme_category_div.find('.edit-category-name').text('"'+category_name+'"');
		theme_category_div.find('.theme-category-add-edit-holder').show().find('.theme-new-edit-category').val(category_name).focus();

		theme_category_div.find('.theme-edit-category-key').val(category_id);

		return false;
	});

	//makes adding category work
	table.on( 'click', 'a.edit-category-save-button', function(event) {
		event.preventDefault();

		theme_path = jQuery(this).attr('href').substring(1);
		theme = themes_table_array[theme_path];

		theme_edit_category = theme.edit_row.find('.theme-new-edit-category').val();
		theme_edit_category_key = theme.edit_row.find('.theme-edit-category-key').val();

		if(theme_edit_category != '') {
			theme.edit_row.find('.spinner-add-category').css('visibility', 'visible');

			var data = { //looks for and sets all variables used for export
				action: 'prettythemes_save_category_ajax',
				_wpnonce: theme_edit_wpnonce,
				theme_edit_category: theme_edit_category,
				theme_edit_category_key: theme_edit_category_key
			};

			jQuery.post(wmd_pl_na.ajax_url, data, function(data){ //post data to specified action trough special WP ajax page
				data = jQuery.parseJSON(data);
				if(data.error == 0)
					if(data.name == theme_edit_category) {
						jQuery('.theme-categories-checklist').find('.category-'+data.id+' .category-name').text(data.name);

						themes_categories_array[data.id] = data.name;

						prettythemes_hide_new_edit_form(theme.edit_row);
					}
			});
		}

		return false;
	});

	//cancel theme editing/adding
	table.on( 'click', 'a.category-cancel-button', function(event) {
		event.preventDefault();

		theme_category_div = jQuery(this).parents('.inline-edit-col');

		prettythemes_hide_new_edit_form(theme_category_div);

		return false;
	});

	//get edit theme details screen on click
	table.on( 'click', 'a.theme-save', function(event) {
		event.preventDefault();

		theme_path = jQuery(this).attr('href').substring(1);
		theme = themes_table_array[theme_path];

		var theme_categories_ready = new Array();
		jQuery.each(theme.edit_row.find( 'input[name="theme_category[]"]:checked' ), function() {
			theme_categories_ready.push(jQuery(this).val());
		});

		theme.edit_row.find('.spinner-save').css('visibility', 'visible');

		var data = {
			action: 'prettythemes_save_theme_details_ajax',
			_wpnonce: theme_edit_wpnonce,
			theme_path: theme_path,
			theme_name: theme.edit_row.find( 'input.theme_name' ).val(),
			theme_custom_url: theme.edit_row.find( 'input.theme_custom_url' ).val(),
			theme_custom_url_label: theme.edit_row.find( 'input.theme_custom_url_label' ).val(),
			theme_image_url: theme.edit_row.find( 'input.theme_image_url' ).val(),
			theme_image_id: theme.edit_row.find( 'input.theme_image_id' ).val(),
			theme_deprecate_date: theme.edit_row.find( 'input.theme_deprecate_jj' ).val()+'.'+theme.edit_row.find( 'select.theme_deprecate_mm' ).val()+'.'+theme.edit_row.find( 'input.theme_deprecate_aa' ).val(),
			theme_deprecate_on_off: theme.edit_row.find( 'input.theme_deprecate_on_off' ).is(':checked') ? 1 : 0,
			theme_description: theme.edit_row.find( 'textarea.theme_description' ).val(),
			theme_categories: theme_categories_ready
		};

		jQuery.post(wmd_pl_na.ajax_url, data, function(data){
			data = jQuery.parseJSON(data);
			if(data.error == 0) {
				theme_details_array[data.new_details.path] = data.new_details;

				jQuery.each(data.remove_categories, function(index, key) {
					jQuery('.theme-categories-checklist li.category-'+key).remove();
				});
				prettythemes_theme_add_data(theme_path);

				theme.table.show();
				theme.edit_row.remove();
			}
		});

		return false;
	});

	//cancel theme editing
	table.on( 'click', 'a.theme-cancel', function(event) {
		event.preventDefault();

		theme_path = jQuery(this).attr('href').substring(1);
		theme = themes_table_array[theme_path];

		if(theme) {
			theme.table.show();
			theme.edit_row.remove();
		}

		return false;
	});

	//handle enter pressing while editing details
	table.on( 'keyup keypress', '#theme-edit', function(event) {
		var code = event.keyCode || event.which;
		if (code  == 13) {
			event.preventDefault();
			if(jQuery('.theme-new-edit-category:focus').size() == 1)
			    jQuery('.category-button:visible').click();
			else
				jQuery('.theme-save').click();

			return false;
		}
	});

    var image_uploader;
    table.on( 'click', '.theme_image_upload_button', function(event) {
        event.preventDefault();

		theme_path = jQuery(this).attr('href').substring(1);
		theme = themes_table_array[theme_path];

        if (image_uploader) {
            image_uploader.open();
            return;
        }

        image_uploader = wp.media.frames.file_frame = wp.media({
            title: wmd_pl_na.choose_screenshot,
            button: {
                text: wmd_pl_na.select_image
            },
            multiple: false
        });

        image_uploader.on('select', function() {
            attachment = image_uploader.state().get('selection').first().toJSON();
            theme.edit_row.find('.theme_image_url').val(attachment.url);
            prettythemes_handle_image_id(theme.edit_row, attachment.id);
        });

        image_uploader.open();
    });

	table.on( 'change', '.theme_image_url', function(event) {
		prettythemes_hide_image_id_edit_button(jQuery(this).parent())
	});

	//change colspan for theme update message
	table.find('tbody > tr.plugin-update-tr').each(function() {
		jQuery(this).find('td').attr('colspan', '4');
	});
});

function prettythemes_handle_image_id(target, image_id) {
	if(image_id != null) {
		target.find('.theme_image_id').val(image_id);
		target.find('.theme_image_edit_button').attr('style', 'display: inline-block;').attr('href', wmd_pl_na.admin_url+'/post.php?post='+image_id+'&action=edit&image-editor');
	}
	else
		prettythemes_hide_image_id_edit_button(target)
}
function prettythemes_hide_image_id_edit_button(target) {
	target.find('.theme_image_id').val('');
	target.find('.theme_image_edit_button').hide().attr('href', '#');
}
function prettythemes_hide_new_edit_form(target) {
	target.find('.spinner-add-category').css('visibility', 'hidden');

	target.find('.add-category-show-form, .add-category-button').show();
	target.find('.edit-category, .edit-category-save-button').hide();

	target.find('.theme-category-add-edit-holder').hide().find('.theme_new_category').val('');
}

function prettythemes_theme_add_data(theme_path) {
	theme_image_url = '';
	if(!!theme_details_array[theme_path]) {
		theme = themes_table_array[theme_path];
		theme_details = theme_details_array[theme_path];

		if(theme_details.name != null) {
			var theme_name_custom = theme.theme_column.find('.theme-name-custom small');
			if(theme_name_custom.length)
				theme_name_custom.text(theme_details.name);
			else
				theme.theme_column.find('strong').after('<br/><strong class="theme-name-custom"><small>'+theme_details.name+'</small></strong>');
		}
		else
			theme.theme_column.find('.theme-name-custom').remove();

		if(theme_details.description != null) {
			var theme_description_custom = theme.description_column.find('.theme-description-custom');
			if(theme_description_custom.length)
				theme_description_custom.html('<small>'+theme_details.description+'</small>');
			else
				theme.description_column.find('p').after('<p class="theme-description-custom"><small>'+theme_details.description+'</small></p>');
		}
		else
			theme.description_column.find('.theme-description-custom').remove();

		if(theme_details.custom_url != null) {
			if(theme_details.custom_url_label != null)
				var current_url_label = theme_details.custom_url_label;
			else
				var current_url_label = wmd_pl_na.default_custom_url_label;

			var theme_custom_url = theme.description_column.find('.theme-custom-url');
			if(theme_custom_url.length) {
				theme_custom_url.attr('href', theme_details.custom_url);

				if(theme_details.custom_url_label != null)
					theme_custom_url.text(current_url_label);
			}
			else
				theme.description_column.find('.theme-version-author-uri:not(.theme-categories-holder)').append( '<span class="theme-custom-url-holder"> | <a class="theme-custom-url" href="'+theme_details.custom_url+'" title="'+wmd_pl_na.default_custom_url_label+'">'+current_url_label+'</a></span>' );
		}
		else
			theme.description_column.find('.theme-custom-url-holder').remove();

		if(theme_details.image_url_preview != null && theme_details.image_url_preview)
			theme.table.find('.theme-image')
				.css('background-image', "url('"+theme_details.image_url_preview+"')")
				.css('background-size', "100px auto")
				.css('background-repeat', "no-repeat");
		else
			theme.table.find('.theme-image').removeAttr('style');

		if(theme_details.categories != null) {
			theme_categories_names = [];
			jQuery.each(theme_details.categories, function( index, value ) {
					theme_categories_names.push(themes_categories_array[value]);
				});
			var theme_categories = theme.description_column.find('.theme-categories-list');
			if(theme_categories.length)
				theme_categories.text(theme_categories_names.join(', '));
			else
				theme.description_column.find('.theme-version-author-uri').before( '<div class="update second theme-version-author-uri theme-categories-holder">'+wmd_pl_na.categories+': <span class="theme-categories-list">'+theme_categories_names.join(', ')+'</span></div>' );
		}
		else
			theme.description_column.find('.theme-categories-holder').remove();

		if(theme_details.deprecate_on_off != null) {
			var theme_deprecation = theme.description_column.find('.theme-deprecation-holder');

			if(theme_deprecation.length)
				theme_deprecation.text(wmd_pl_na.deprecation_date+': '+theme_details.deprecate_jj+'.'+theme_details.deprecate_mm+'.'+theme_details.deprecate_aa);
			else
				theme.description_column.find('.theme-version-author-uri:last').after( '<div class="update second theme-version-author-uri theme-deprecation-holder">'+wmd_pl_na.deprecation_date+': '+theme_details.deprecate_jj+'.'+theme_details.deprecate_mm+'.'+theme_details.deprecate_aa+'</div>' );
		}
		else
			theme.description_column.find('.theme-deprecation-holder:last').remove();
	}
}


function prettythemes_object_to_array(object) {
	array = [];
	jQuery.each(object, function( index, value ) {
		array[index] = value;
	});

	return array;
}