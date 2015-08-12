<div<?php if ( $atts['category'] ) echo ' data-category="'.$atts['category'].'"';?> class="wmd-themes-showcase theme-browser<?php if ( $atts['hide_interface'] ) echo ' hide-interface'; if ( $atts['show_buttons'] == 'always' ) echo ' always-show-buttons'; ?>">
	<div class="themes">

<?php
foreach ( $this->plugin->themes_data as $theme ) :
	$aria_action = esc_attr( $theme['id'] . '-action' );
	$aria_name   = esc_attr( $theme['id'] . '-name' );
	?>
<div class="theme<?php if ( $theme['active'] ) echo ' active'; ?>" tabindex="0" aria-describedby="<?php echo $aria_action . ' ' . $aria_name; ?>">

	<?php if ( ! empty( $theme['screenshot'][0] ) ) { ?>
		<div class="theme-screenshot">
			<img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo $theme['screenshot'][0]; ?>" alt="" />
		</div>
	<?php } else { ?>
		<div class="theme-screenshot blank"></div>
	<?php } ?>
	<div class="theme-links">
		<a class="more-details" href="<?php echo $theme['details_url']; ?>"><?php echo $theme['name']; ?>: <?php _e( 'Details', 'wmd_multisitethememanager' ); ?></a>
		<a class="more-details live-preview" target="_blank" href="<?php echo $theme['live_preview_url']; ?>"><?php _e( 'Live Demo', 'wmd_multisitethememanager' ); ?></a>
	</div>
</div>
<?php endforeach; ?>
	<br class="clear" />
	</div>
</div>

<script id="tmpl-category" type="text/template">
	<a data-sort="{{ data.name }}" class="theme-section theme-category" href="#">{{ data[0] }}</a>
</script>

<script id="tmpl-theme" type="text/template">
	<# if ( data.screenshot[0] ) { #>
		<div class="theme-screenshot">
			<img src="{{ data.screenshot[0] }}" alt="" />
		</div>
	<# } else { #>
		<div class="theme-screenshot blank"></div>
	<# } #>
	<div class="theme-links">
		<a class="more-details" href="{{{ data.details_url }}}">{{{ data.name }}}: <?php _e( 'Details', 'wmd_multisitethememanager' ); ?></a>
		<a class="more-details live-preview" target="_blank" href="{{{ data.live_preview_url }}}"><?php _e( 'Live Preview', 'wmd_multisitethememanager' ); ?></a>
	</div>
</script>