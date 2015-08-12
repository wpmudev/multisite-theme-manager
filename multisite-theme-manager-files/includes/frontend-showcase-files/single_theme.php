<div class="wmd-themes-showcase-single">
	<h2><?php echo $theme['name']; ?></h2>
	<p><strong><a target="_blank" href="<?php echo $this->get_live_preview_url($theme['id']); ?>"><?php _e( 'Live Preview', 'wmd_multisitethememanager' ); ?></a></strong></p>

	<?php if ( count($theme['screenshot']) > 0 ) { ?>
		<div class="theme-screenshots">
		<?php foreach ($theme['screenshot'] as $screenshot) { ?>
			<p class="theme-screenshot"><img src="<?php echo $screenshot; ?>"/></p>
		<?php } ?>
		</div>
	<?php } ?>

	<p class="theme-description-link">
		<span class="theme-description"><?php echo $theme['description']; ?></span>
		<?php if ( ! empty( $theme['customLinkAndUri'] ) ) { ?>
			<span class="theme-custom-link"><br/><?php echo $theme['customLinkAndUri']; ?></span>
		<?php } ?>
	</p>
	<p class="theme-details">
		<?php if ( ! empty( $theme['authorAndUri'] ) ) { ?>
			<span class="theme-author"><strong><?php _e( 'Author:', 'wmd_multisitethememanager' ); ?></strong> <?php echo $theme['authorAndUri']; ?></strong></span>
		<?php } ?>
		<?php if ( ! empty( $theme['categories'] ) ) { ?>
			<span class="theme-categories"><br/><strong><?php _e( 'Categories:', 'wmd_multisitethememanager' ); ?></strong> <?php echo $theme['categories']; ?></strong></span>
		<?php } ?>
		<?php if ( ! empty( $theme['tags'] ) ) { ?>
			<span class="theme-tags"><br/><strong><?php _e( 'Tags:', 'wmd_multisitethememanager' ); ?></strong> <?php echo $theme['tags']; ?></strong></span>
		<?php } ?>
	</p>

	<p class="theme-showcase-link">
		<strong><a href="<?php echo get_permalink(); ?>"><?php _e( 'Show all available themes', 'wmd_multisitethememanager' ); ?></a></strong>
	</p>

</div>