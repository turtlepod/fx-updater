		<?php
		$text = file_get_contents( FX_UPDATER_PATH . 'assets/export-code/123.txt', true);
		$text = str_replace( '{{repo_uri}}', 'http://shellcreeper.com', $text );
		?>
		<div class="fxup-box">
			<div class="title">
				<?php _ex( 'PHP Code', 'settings', 'fx-updater' ); ?>
			</div><!-- .title -->
			<div class="inner">
				<p><?php _ex( 'Add this code in you main plugin file.', 'settings', 'fx-updater' ); ?></p>
				<textarea class="pre" readonly="readonly"><?php echo esc_textarea( $text ); ?></textarea>
			</div><!-- .inner -->
		</div><!-- .fxup-box -->

		<div class="fxup-box">
			<div class="title">
				<?php _ex( 'Updater Class', 'settings', 'fx-updater' ); ?>
			</div><!-- .title -->
			<div class="inner">
				<p><?php _ex( 'Create "includes/updater.php" in your plugin and add this code.', 'settings', 'fx-updater' ); ?></p>
				<textarea class="pre" readonly="readonly"><?php echo esc_textarea( $text ); ?></textarea>
			</div><!-- .inner -->
		</div><!-- .fxup-box -->
