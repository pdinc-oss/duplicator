<?php
	$scan_run = (isset($_POST['action']) && $_POST['action'] == 'duplicator_recursion') ? true :false;	
	$ajax_nonce	= wp_create_nonce('DUP_CTRL_Tools_RunScanValidator');
?>

<style>
	div#hb-result {padding: 10px 5px 0 5px; line-height: 22px}
</style>

<!-- ==============================
SCAN VALIDATOR -->
<div class="dup-box">
	<div class="dup-box-title">
		<i class="fa fa-check-square-o"></i>
		<?php _e("Scan Validator", 'duplicator'); ?>
		<div class="dup-box-arrow"></div>
	</div>
	<div class="dup-box-panel" style="display: <?php echo $scan_run ? 'block' : 'none';  ?>">	
		<?php 
			_e("This utility will help to find unreadable files and sys-links in your environment  that can lead to issues during the scan process.  ", "duplicator"); 
			_e("The utility  will also show how many files and directories you have in your system.  This process may take several minutes to run.  ", "duplicator"); 
			_e("If there is a recursive loop on your system then the process has a built in check to stop after a large set of files and directories have been scanned.  ", "duplicator"); 
			_e("A message will show indicated that that a scan depth has been reached. ", "duplicator"); 
		?> 
		<br/><br/>


		<button id="scan-run-btn" type="button" class="button button-large button-primary" onclick="Duplicator.Tools.RunScanValidator()">
			<?php _e("Run Scan Integrity Validation", "duplicator"); ?>
		</button>

		<script id="hb-template" type="text/x-handlebars-template">
			<b>Scan Path:</b> <?php echo DUPLICATOR_WPROOTPATH ?> <br/>
			<b>Scan Results</b><br/>
			<table>
				<tr>
					<td><b>Files:</b></td>
					<td>{{Payload.[0].FileCount}} </td>
				</tr>
				<tr>
					<td><b>Dirs:</b></td>
					<td>{{Payload.[0].DirCount}} </td>
				</tr>
			</table>

			<b>Unreadable Files:</b> <br/>
			{{#if Payload.[0].Unreadable}}
				{{#each Payload.[0].Unreadable}}
					&nbsp; &nbsp; {{@index}} : {{this}}<br/>
				{{/each}}
			{{else}}
				<i>No Unreadable items found</i> <br/>
			{{/if}}
			
			<b>Symbolic Links:</b> <br/>
			{{#if Payload.[0].SymLinks}}
				{{#each Payload.[0].SymLinks}}
					&nbsp; &nbsp; {{@index}} : {{this}}<br/>
				{{/each}}
			{{else}}
				<i>No Sym-links found</i> <br/>
			{{/if}}
			<br/>
		</script>
		<div id="hb-result"></div>	

	</div> 
</div> 
<br/>

<script>	
jQuery(document).ready(function($) 
{
	//Run request to: admin-ajax.php?action=DUP_CTRL_Tools_RunScanValidator
	Duplicator.Tools.RunScanValidator = function() 
	{
		var result = confirm('<?php _e('This will run the scan validation check.  This may take several minutes.\nDo you want to Continue?', 'duplicator'); ?>');
		var data = {action : 'DUP_CTRL_Tools_RunScanValidator', nonce: '<?php echo $ajax_nonce; ?>', 'scan-recursive': true};
		
		if (! result) 	
			return;
		
		$('#hb-result').html('<?php _e("Scanning Enviroment... This may take a few minutes.", "duplicator"); ?>');
		$('#scan-run-btn').html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Running Please Wait...');
		
		$.ajax({
			type: "POST",
			url: ajaxurl,
			dataType: "json",
			data: data,
			success: function(data) {Duplicator.Tools.IntScanValidator(data)},
			error: function(data) {console.log(data)},
			done: function(data) {console.log(data)}
		});	
	}
	
	//Process Ajax Template
	Duplicator.Tools.IntScanValidator= function(data) 
	{
		var template = $('#hb-template').html();
		var templateScript = Handlebars.compile(template);
		var html = templateScript(data);
		$('#hb-result').html(html);
		$('#scan-run-btn').html('<?php _e("Run Scan Integrity Validation", "duplicator"); ?>');
	}
});	
</script>

