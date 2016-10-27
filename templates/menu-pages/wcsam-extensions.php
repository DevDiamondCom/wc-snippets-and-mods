<?php

$dir = glob( WCSAM_MODULES_DIR . '*', GLOB_ONLYDIR);
if ( ! is_array($dir)  )
	return;

$modules = (array) wcsam_get_option('wcsam', 'extensions', 'extensions', array());

?>
<div class="wcsam-extensions">
	<h3><?php _e('Extensions', WCSAM_PLUGIN_SLUG) ?></h3>
	<ul class="wcsam-extension-list">
		<?php

		foreach ( $dir as $path )
		{
			$ext_name = md5($path);
			if ( ! isset(WCSAM()->modules[ $ext_name ]) )
				continue;

			$file_info = $path . DIRECTORY_SEPARATOR . 'info.dat';

			if ( ! file_exists($file_info) )
			{
				printf(__('Looks like its not the WCSAM extension here %s!', WCSAM_PLUGIN_SLUG), $dir);
				continue;
			}

			$info = parse_ini_file($file_info);
			if ( ! isset($info['title'], $info['version'], $info['description']) )
			{
				printf(__('Looks like its not the WCSAM extension here %s!', WCSAM_PLUGIN_SLUG), $dir);
				continue;
			}
			?>
			<li>
				<table class="wcsam-extension-table">
					<tbody>
						<tr valign="top">
							<th><img class="wcsam-extension-img" src="<?= WCSAM_ASSETS_URL ?>admin/img/extensions.png" /></th>
							<td>
								<div class="wcsam-extension-header">
									<div class="wcsam-exth-switch">
										<div class="ext-toggle toggle-light" data-toggle-on="<?= ($modules[ $ext_name ] ? 'true' : 'false' ) ?>"
										     data-toggle-height="24" data-toggle-width="62"></div>
										<input style="display: none" type="checkbox" name="extensions[]" <?= ($modules[ $ext_name ] ? 'CHECKED' : '' ) ?> value="<?= $ext_name ?>">
									</div>
									<div class="wcsam-exth-ver">ver.: <?= $info['version'] ?></div>
								</div>
								<div class="wcsam-extension-body">
									<div class="wcsam-exth-title"><?= $info['title'] ?></div>
									<div class="wcsam-exth-desc"><?= $info['description'] ?></div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</li>
			<?php
		} // END foreach

		?>
	</ul>
</div>