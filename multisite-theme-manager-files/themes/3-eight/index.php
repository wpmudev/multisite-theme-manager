
<div class="wrap">
	<h2><?php echo stripslashes( $this->options['themes_page_title'] ); ?>
		<span class="theme-count"><?php echo count( $this->themes_data ); ?></span>
	</h2>
	<?php if($this->options['themes_page_description']) { ?>
	<p class="page-description">
		<?php echo stripslashes($this->options['themes_page_description']); ?>
	</p>
	<?php } ?>

<div class="theme-browser">
	<div class="themes">

<?php
foreach ( $this->themes_data as $theme ) :
	$aria_action = esc_attr( $theme['id'] . '-action' );
	$aria_name   = esc_attr( $theme['id'] . '-name' );
	?>
<div class="theme<?php if ( $theme['active'] ) echo ' active'; ?>" tabindexx="0" aria-describedby="<?php echo $aria_action . ' ' . $aria_name; ?>">
	<?php if ( ! empty( $theme['screenshot'][0] ) ) { ?>
		<div class="theme-screenshot">
			<img src="<?php echo $theme['screenshot'][0]; ?>" alt="" />
		</div>
	<?php } else { ?>
		<div class="theme-screenshot blank"></div>
	<?php } ?>
	<span class="more-details" id="<?php echo $aria_action; ?>"><?php _e( 'Theme Details' ); ?></span>
	<?php if($theme['author']) { ?>
	<div class="theme-author"><?php printf( __( 'By %s' ), $theme['author'] ); ?></div>
	<?php } ?>

	<?php if ( $theme['active'] ) { ?>
		<h3 class="theme-name" id="<?php echo $aria_name; ?>"><span><?php _ex( 'Active:', 'theme' ); ?></span> <?php echo $theme['name']; ?></h3>
	<?php } else { ?>
		<h3 class="theme-name" id="<?php echo $aria_name; ?>"><?php echo $theme['name']; ?></h3>
	<?php } ?>

	<div class="theme-actions">

	<?php if ( $theme['active'] ) { ?>
		<?php if ( $theme['actions']['customize'] ) { ?>
			<a class="button button-primary customize load-customize hide-if-no-customize" href="<?php echo $theme['actions']['customize']; ?>"><?php _e( 'Customize' ); ?></a>
		<?php } ?>
	<?php } else { ?>
		<a class="button button-primary activate" href="<?php echo $theme['actions']['activate']; ?>"><?php _e( 'Activate' ); ?></a>
		<a class="button button-secondary load-customize hide-if-no-customize" href="<?php echo $theme['actions']['customize']; ?>"><?php _e( 'Live Preview' ); ?></a>
		<a class="button button-secondary hide-if-customize" href="<?php echo $theme['actions']['preview']; ?>"><?php _e( 'Preview' ); ?></a>
	<?php } ?>

	</div>

	<?php if ( $theme['hasUpdate'] ) { ?>
		<div class="theme-update"><?php _e( 'Update Available' ); ?></div>
	<?php } ?>
</div>
<?php endforeach; ?>
	<br class="clear" />
	</div>
</div>
<div class="theme-overlay"></div>

</div><!-- .wrap -->

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
	<span class="more-details" id="{{ data.id }}-action"><?php _e( 'Theme Details' ); ?></span>
	<# if ( data.author ) { #>
		<div class="theme-author"><?php printf( __( '%s' ), '{{{ data.author }}}' ); ?></div>
	<# } #>

	<# if ( data.active ) { #>
		<h3 class="theme-name" id="{{ data.id }}-name"><span><?php _ex( 'Active:', 'theme' ); ?></span> {{{ data.name }}}</h3>
	<# } else { #>
		<h3 class="theme-name" id="{{ data.id }}-name">{{{ data.name }}}</h3>
	<# } #>

	<div class="theme-actions">

	<# if ( data.active ) { #>
		<# if ( data.actions.customize ) { #>
			<a class="button button-primary customize load-customize hide-if-no-customize" href="{{ data.actions.customize }}"><?php _e( 'Customize' ); ?></a>
		<# } #>
	<# } else { #>
		<a class="button button-primary activate" href="{{{ data.actions.activate }}}"><?php _e( 'Activate' ); ?></a>
		<a class="button button-secondary load-customize hide-if-no-customize" href="{{{ data.actions.customize }}}"><?php _e( 'Live Preview' ); ?></a>
		<a class="button button-secondary hide-if-customize" href="{{{ data.actions.preview }}}"><?php _e( 'Preview' ); ?></a>
	<# } #>

	</div>

	<# if ( data.hasUpdate ) { #>
		<div class="theme-update"><?php _e( 'Update Available' ); ?></div>
	<# } #>
</script>

<script id="tmpl-theme-single" type="text/template">
	<div class="theme-backdrop"></div>
	<div class="theme-wrap">
		<div class="theme-header">
			<button alt="<?php _e( 'Show previous theme' ); ?>" class="left dashicons dashicons-no"></button>
			<button alt="<?php _e( 'Show next theme' ); ?>" class="right dashicons dashicons-no"></button>
			<button alt="<?php _e( 'Close overlay' ); ?>" class="close dashicons dashicons-no"></button>
		</div>
		<div class="theme-about">
			<div class="theme-screenshots">
			<# if ( data.screenshot[0] ) { #>
				<div class="screenshot"><img src="{{ data.screenshot[0] }}" alt="" /></div>
			<# } else { #>
				<div class="screenshot blank"></div>
			<# } #>
			</div>

			<div class="theme-info">
				<# if ( data.active ) { #>
					<span class="current-label"><?php _e( 'Current Theme' ); ?></span>
				<# } #>
				<h3 class="theme-name">
					{{{ data.name }}}
					<# if ( data.version ) { #>
						<span class="theme-version"><?php printf( __( 'Version: %s' ), '{{{ data.version }}}' ); ?></span>
					<# } #>
				</h3>
				<# if ( data.authorAndUri ) { #>
				<h4 class="theme-author"><?php printf( __( 'By %s' ), '{{{ data.authorAndUri }}}' ); ?></h4>
				<# } #>
				<# if ( data.customLinkAndUri ) { #>
				<h4 class="theme-author">{{{ data.customLinkAndUri }}}</h4>
				<# } #>

				<# if ( data.hasUpdate ) { #>
				<div class="theme-update-message">
					<h4 class="theme-update"><?php _e( 'Update Available' ); ?></h4>
					{{{ data.update }}}
				</div>
				<# } #>
				<p class="theme-description">{{{ data.description }}}</p>

				<# if ( data.parent ) { #>
					<p class="parent-theme"><?php printf( __( 'This is a child theme of %s.' ), '<strong>{{{ data.parent }}}</strong>' ); ?></p>
				<# } #>

				<# if ( data.categories ) { #>
					<p class="theme-tags"><span><?php _e( 'Categories:' ); ?></span> {{{ data.categories }}}</p>
				<# } #>

				<# if ( data.tags ) { #>
					<p class="theme-tags"><span><?php _e( 'Tags:' ); ?></span> {{{ data.tags }}}</p>
				<# } #>
			</div>
		</div>

		<div class="theme-actions">
			<div class="active-theme">
				<a href="{{{ data.actions.customize }}}" class="button button-primary customize load-customize hide-if-no-customize"><?php _e( 'Customize' ); ?></a>
				<?php echo implode( ' ', $current_theme_actions ); ?>
			</div>
			<div class="inactive-theme">
				<# if ( data.actions.activate ) { #>
					<a href="{{{ data.actions.activate }}}" class="button button-primary activate"><?php _e( 'Activate' ); ?></a>
				<# } #>
				<a href="{{{ data.actions.customize }}}" class="button button-secondary load-customize hide-if-no-customize"><?php _e( 'Live Preview' ); ?></a>
				<a href="{{{ data.actions.preview }}}" class="button button-secondary hide-if-customize"><?php _e( 'Preview' ); ?></a>
			</div>

			<# if ( ! data.active && data.actions['delete'] ) { #>
				<a href="{{{ data.actions['delete'] }}}" class="button button-secondary delete-theme"><?php _e( 'Delete' ); ?></a>
			<# } #>
		</div>
	</div>
</script>