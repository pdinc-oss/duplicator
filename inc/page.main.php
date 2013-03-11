<?php
	$package_name = date('Ymd') . '_' . sanitize_title(get_bloginfo( 'name', 'display' ));
	$package_name = substr(str_replace('-', '', $package_name), 0 , 40);
	$package_name = sanitize_file_name($package_name);
	
	global $wpdb;
	$result = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}duplicator` ORDER BY id DESC", ARRAY_A);
	$total_elements = count($result);
	
	//Settings
	$email_me_enabled    = $GLOBALS['duplicator_opts']['email-me'] == "0" 	 	 ? false : true;

	
	//INLINE DIALOG WINDOWS
	require_once('javascript.php'); 
	require_once('view.package.php');
	require_once('view.options.php');
	require_once('view.system.php');
	require_once('inc.header.php'); 

?>

<!-- ==========================================
MAIN FORM: Lists all the backups 			-->
<div class="wrap">
<form id="form-duplicator" method="post">
	<!-- h2 required here for general system messages -->
	<h2 style='display:none'></h2>

	<?php duplicator_header(__("Packages", 'wpduplicator') ) ?>

	<!-- TOOLBAR -->
	<table border="0" id="toolbar-table" cellspacing="0" style="margin-top:15px">
		<tr valign="top">
			<td style="width:100%">
				<div class="alignleft actions">
					<select id="dup-pack-bulk-actions">
						<option value="-1" selected="selected">Bulk Actions</option>
						<option value="delete" title="<?php _e("Delete selected package(s)", 'wpduplicator') ?>">Delete</option>
					</select>
					<input type="button" name="" id="dup-pack-bulk-apply" class="button action" value="Apply" onclick="Duplicator.deletePackage()">
				</div>
				<br class="clear">
			</td>
			<!-- Create/Delete -->
			<td><input type="submit" id="btn-create-pack" class="btn-create-pack" value="" name="submit" title="<?php _e("Create Package", 'wpduplicator') ?>" ondblclick="javascript:return void(0);" /></td>	
			<!-- Options/Logs -->
			<td><img src="<?php echo DUPLICATOR_PLUGIN_URL  ?>assets/img/hdivider.png" class="toolbar-divider" /></td>
			<td align="center"><input type="button" id="btn-opts-dialog" class="btn-opts-dialog" title="<?php _e("Options", 'wpduplicator') ?>..." onclick="Duplicator.optionsOpen()" /></td>	
			<td align="center"><input type="button" id="btn-sys-dialog"  class="btn-sys-dialog" onclick="Duplicator.getSystemCheck()" title="<?php _e("System Check", 'wpduplicator') ?>..." /></td>
			<td align="center"><input type="button" id="btn-logs-dialog" class="btn-log-dialog" onclick="Duplicator.openLog()" title="<?php _e("Show Create Log", 'wpduplicator') ?>..." /></td>
			<td align="center"></td>
		</tr>
	</table>

	<!-- STATUS BAR -->
	<div class="widget" style="padding:6px; margin: 2px 0px 0px 0px; border-bottom: none; font-size:13px">
		<b><?php _e("Status", 'wpduplicator') ?>:</b>
		<span id="span-status"><?php _e("Ready to create new package", 'wpduplicator' ) ?>.</span>
		<img id="img-status-error" src="<?php echo DUPLICATOR_PLUGIN_URL ?>assets/img/error.png" style="height:16px; width:16px; display:none; margin-top:3px; margin:0px" valign="bottom" />
		<img id="img-status-progress" src="<?php echo DUPLICATOR_PLUGIN_URL ?>assets/img/progress.gif" style="height:10px; width:46px; display:none" />
		<span id="span-status-post" style="display:inline-block"></span>
	</div>

	<!-- TABLE LIST -->
	<table class="widefat dup-pack-table">
		<thead>
			<tr>
				<?php if($total_elements == 0)  :	?>
					<th colspan="7">&nbsp;</th>
				<?php else : ?>	
					<th><input type="checkbox" id="select-all"  title="<?php _e("Select all packages", 'wpduplicator') ?>" style="margin:0px;padding:0px 0px 0px 5px;" /></th>
					<th><?php _e("Details", 'wpduplicator') ?></th>
					<th><?php _e("User", 'wpduplicator') ?></th>
					<th><?php _e("Created", 'wpduplicator') ?></th>
					<th><?php _e("Size", 'wpduplicator') ?></th>
					<th><?php _e("Package Name", 'wpduplicator') ?></th>
					<th style="width:90%; text-align:right; padding-right:10px" colspan="2">
						<?php _e("Package Set",  'wpduplicator')?>
						<i style='font-size:10px'><?php _e("(Download Both)",  'wpduplicator')?></i>
					</th>
				<?php endif; ?>	
			</tr>
		</thead>
		<?php
		if($total_elements != 0)  {
			$ct = 0;
			$total_size = 0;
			while($ct < $total_elements) {
				$row	   = $result[$ct];
				$settings  = unserialize($row['settings']);
				$detail_id = "duplicator-detail-row-{$ct}";
				$packname  = empty($row['packname']) ? $row['zipname'] : $row['packname'];
				$total_size = $total_size + $row['zipsize'];
				$plugin_version = empty($settings['plugin_version']) ? 'unknown' : $settings['plugin_version'];
				$plugin_compat  = version_compare($plugin_version, '0.3.1');
				?>


				<?php if($plugin_compat >= 0)  :	?>
					<?php
						//Links
						$uniqueid  			= "{$row['token']}_{$row['packname']}";
						$sqlfilelink		= duplicator_snapshot_urlpath() . "{$uniqueid}_database.sql";
						$packagepath 		= duplicator_snapshot_urlpath() . "{$uniqueid}_package.zip";
						$installerpath		= duplicator_snapshot_urlpath() . "{$uniqueid}_installer.php";
						$installfilelink	= "{$installerpath}?get=1&file={$uniqueid}_installer.php";
					?>
					<tr class="dup-pack-info">
						<td style="padding-right:20px !important"><input name="delete_confirm" type="checkbox" id="<?php echo $uniqueid ;?>" onclick="Duplicator.rowColor(this)" /></td>
						<td><a href="javascript:void(0);" onclick="return Duplicator.toggleDetail('<?php echo $detail_id ;?>');">[<?php echo __("View", 'wpduplicator') . ' ' . $row['id'];?>]</a></td>
						<td><?php echo $row['owner'];?></td>
						<td><?php echo date( "m-d-y G:i", strtotime($row['created']));?></td>
						<td><?php echo duplicator_bytesize($row['zipsize']);?></td>
						<td class='pack-name'><?php echo $packname ;?></td>
						<td style="width:90%;" class="get-btns">	
							<button id="<?php echo "{$uniqueid}_installer.php" ?>" class="dup-installer-btn no-select" onclick="Duplicator.downloadFile('<?php echo $installfilelink; ?>', this); return false;"><?php _e("Installer", 'wpduplicator') ?></button> &nbsp;
							<button id="<?php echo "{$uniqueid}_package.zip" ?>" class="dup-installer-btn no-select" onclick="Duplicator.downloadFile('<?php echo $packagepath; ?>', this); return false;"><?php _e("Package", 'wpduplicator') ?></button>
						</td>
					</tr>
					<tr>
						<td colspan="8" id="<?php echo $detail_id; ?>" class="dup-pack-details">
							<div class="dup-details-area">
								<b><?php _e("Version", 'wpduplicator') ?>:</b> <?php echo $plugin_version ?>  &nbsp;
								<b><?php _e("Secure Name", 'wpduplicator')?>:</b> <?php echo "{$row['token']}_{$row['packname']}" ;?> <br/>
								<button class='dup-dlg-quick-path-database-link no-select' onclick="window.open(<?php echo "'{$sqlfilelink}', '_blank'" ;?>); return false;"><?php _e("Download SQL File", 'wpduplicator')?></button>
								<button class='dup-dlg-quick-path-download-link no-select' onclick="Duplicator.showQuickPath(<?php echo "'{$sqlfilelink}', '{$packagepath}', '{$installfilelink}' " ;?>); return false;"><?php _e("Show Download Links", 'wpduplicator')?></button>
							</div>
						</td>
					</tr>	

				<!-- LEGACY PRE 0.3.1 PACKS -->
				<?php else : ?>	
					<?php
						$legacy_package = duplicator_snapshot_urlpath() . "{$row['zipname']}";
					?>
					<tr class="dup-pack-info">
						<td style="padding-right:20px !important"><input name="delete_confirm" type="checkbox" id="<?php echo $row['zipname'] ;?>" onclick="Duplicator.rowColor(this)" /></td>
						<td><a href="javascript:void(0);" onclick="return Duplicator.toggleDetail('<?php echo $detail_id ;?>');">[<?php echo __("View", 'wpduplicator') . ' ' . $row['id'];?></a>]</td>
						<td><?php echo $row['owner'];?></td>
						<td><?php echo date( "m-d-y G:i", strtotime($row['created']));?></td>
						<td><?php echo duplicator_bytesize($row['zipsize']);?></td>
						<td class='pack-name'><?php echo $packname ;?></td>
						<td style="width:90%;" class="get-btns">	
							<span style='display:inline-block; padding:7px 10px 0px 0px'>
							<a href="javascript:void(0);" onclick="return Duplicator.toggleDetail('<?php echo $detail_id ;?>');">[Not Supported]</a></span>
							<button id="<?php echo "{$row['zipname']}" ?>" class="dup-installer-btn no-select" onclick="Duplicator.downloadFile('<?php echo $legacy_package; ?>', this); return false;"><?php _e("Package", 'wpduplicator') ?></button>
						</td>
					</tr>
					<tr>
						<td colspan="8" id="<?php echo $detail_id; ?>" class="dup-pack-details">
							<div class="dup-details-area ui-state-error">
								<b><?php _e("Legacy Version", 'wpduplicator') ?>:</b> <?php echo $plugin_version ?> <br/>
								<i style="color:#000"><?php
								printf("%s <a href='http://lifeinthegrid.com/duplicator-docs' target='_blank'>%s</a>",
								__("This package was built with a version that is no longer supported.  It is highly recommended that this package be deleted.  For more details see the", 'wpduplicator'),
								__("Online FAQs", 'wpduplicator')); 
								?></i>
							</div>
						</td>
					</tr>	
				<?php endif; ?>	


				<?php
				$ct++;
			}
		} else {
			$msg1 = __("No packages found", 'wpduplicator');
			$msg2 = __("To create a new package click the create button ", 'wpduplicator');
			$msg3 = sprintf("%s <a href='javascript:void(0)' onclick='Duplicator.getSystemCheck()'>%s</a> %s",
						__("Check your", 'wpduplicator'), 
						__("server's compatibility", 'wpduplicator'),
						__("with the Duplicator", 'wpduplicator'));
			$msg4 = __("This process will backup all your files and database", 'wpduplicator');
			$msg5 = __("Creating a package may take several minutes if you have a large site", 'wpduplicator');
			$msg6 = __("This window should remain open for the process to complete", 'wpduplicator');
			$msg7 = __("Please be patient while we work through this Beta version", 'wpduplicator');
			$msg8 = __("Please report any issues to", 'wpduplicator');

			echo "<tr>
					<td colspan='7'>
						<div style='padding:60px 20px;text-align:center'>
							<b style='font-size:14px'>{$msg1}.<br/> {$msg2} <input type='submit' class='btn-create-pack'  ondblclick='javascript:return void(0);' value=''  /><br/> {$msg3}.</b><br/><br/>
							<i>{$msg4}.<br/> {$msg5}.<br/> {$msg6}. <br/><br/> {$msg7}.<br/> {$msg8} <a href='http://support.lifeinthegrid.com' target='_blank'>support.lifeinthegrid.com</a></i>
						</div>
						</td>
					</tr>";
		}
		?>
		<tfoot>
			<tr>
				<?php if($total_elements == 0)  :	?>
					<th colspan="8">&nbsp;</th>
				<?php else : ?>	
					<th colspan="8" style='text-align:right; font-size:12px'><?php echo _e("Total Storage Used", 'wpduplicator') . ': ' . duplicator_bytesize($total_size); ?></th>
				<?php endif; ?>	
			</tr>
		</tfoot>
	</table>
</form>
</div>
