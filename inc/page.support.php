<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "1a44d92e-2a78-42c3-a32e-414f78f9f484"}); </script> 
<script>
	jQuery(document).ready(function() {
		//ATTACHED EVENTS
		jQuery('#dup-support-kb-lnks').change(function() {
			if (jQuery(this).val() != "null") 
				window.open(jQuery(this).val())
		});
	});
</script>


<div class="wrap dup-wrap dup-support-all">

	<!-- h2 required here for general system messages -->
	<h2 style='display:none'></h2>
	<div class="dup-header widget">
		<!-- !!DO NOT CHANGE/EDIT OR REMOVE PRODUCT NAME!!
		If your interested in Private Label Rights please contact us at the URL below to discuss
		customizations to product labeling: http://lifeinthegrid.com/services/	-->
		<div style='float:left;height:45px'><img src="<?php echo DUPLICATOR_PLUGIN_URL  ?>img/logo.png" style='text-align:top'  /></div> 
		<div style='float:left;height:45px; text-align:center;'>
			<h2 style='margin:-12px 0px -7px 0px; text-align:center; width:100%;'>Duplicator &raquo;<span style="font-size:18px"> <?php _e("Support", 'wpduplicator') ?></span> </h2>
			<i style='font-size:0.8em'><?php _e("By", 'wpduplicator') ?> <a href='http://lifeinthegrid.com/duplicator' target='_blank'>lifeinthegrid.com</a></i>
		</div> 
		<br style='clear:both' />
	</div><br/>

	<div style="width:850px; margin:auto">
		<table>
			<tr>
				<td valign="top" class="dup-drop-cap">
				<?php 
				
					_e("Created for Admins, Developers and Designers the Duplicator will streamline your workflows and help you quickly clone a WordPress application.  
						If you run into an issue please read through the", 'wpduplicator');
						printf(" <a href='http://lifeinthegrid.com/duplicator-docs' target='_blank'>%s</a> ", __("knowledgebase", 'wpduplicator'));
						_e('in detail as it will have answers to most of your questions and issues.', 'wpduplicator')
				?>
				</td>
				<td>
					<a href="http://lifeinthegrid.com/labs/duplicator" target="_blank">
						<img src="<?php echo DUPLICATOR_PLUGIN_URL  ?>img/logo-box.png" style='text-align:top; margin:-10px 0px 0px 20px'  />
					</a>
				</td>
			</tr>
		</table>
		
		
		<!--  =================================================
		NEED HELP?
		==================================================== -->
		<h2 class="dup-support-headers" style="margin-top:-35px"><?php _e('Need Help?', 'wpduplicator') ?></h2>

		<!-- HELP LINKS -->
		<div class="dup-support-hlp-area">
			<table class="dup-support-hlp-hdrs">
				<tr >
					<td><img src="<?php echo DUPLICATOR_PLUGIN_URL  ?>img/books.png" /></td>
					<td><?php _e('Knowledgebase', 'wpduplicator') ?></td>
				</tr>
			</table>
			<div class="dup-support-hlp-txt">
				<?php  _e('Please review the online documentation for complete usage of the plugin.', 'wpduplicator');?>
				<select id="dup-support-kb-lnks" style="margin-top:10px; font-size:14px; min-width: 170px">
					<option> <?php _e('Choose A Section', 'wpduplicator') ?> </option>
					<option value="http://lifeinthegrid.com/duplicator-quick"><?php _e('Quick Start', 'wpduplicator') ?></option>
					<option value="http://lifeinthegrid.com/duplicator-guide"><?php _e('User Guide', 'wpduplicator') ?></option>
					<option value="http://lifeinthegrid.com/duplicator-faq"><?php _e('FAQs', 'wpduplicator') ?></option>
					<option value="http://lifeinthegrid.com/duplicator-log"><?php _e('Change Log', 'wpduplicator') ?></option>
					<option value="http://lifeinthegrid.com/labs/duplicator"><?php _e('Product Page', 'wpduplicator') ?></option>
				</select>
			</div>
		</div>
		

		<!-- APPROVED HOSTING -->
		<div class="dup-support-hlp-area">
			<table class="dup-support-hlp-hdrs">
				<tr >
					<td><img id="dup-support-approved" src="<?php echo DUPLICATOR_PLUGIN_URL  ?>img/approved.png"  /></td>
					<td><?php _e('Approved Hosting', 'wpduplicator') ?></td>
				</tr>
			</table>
			<div class="dup-support-hlp-txt">
				<?php _e('Need a solid hosting provider that will work well with the Duplicator?', 'wpduplicator'); ?>
				<div class="dup-support-txts-links" style="margin-top:10px">
					<?php printf("<a href='http://lifeinthegrid.com/duplicator-hosts' target='_blank'>%s</a>", __("Approved Hosting Program", 'wpduplicator')); ?>
				</div>
			</div>
		</div>
		

		<!-- ONLINE SUPPORT -->
		<div class="dup-support-hlp-area">
			<table class="dup-support-hlp-hdrs">
				<tr >
					<td><img src="<?php echo DUPLICATOR_PLUGIN_URL  ?>img/support.png" /></td>
					<td><?php _e('Online Support', 'wpduplicator') ?></td>
				</tr>
			</table>
			<div class="dup-support-hlp-txt">
				<?php _e("Online support  is available for issues not covered in the knowledgebase." , 'wpduplicator');	?>
				<div class="dup-support-txts-links">
					<a href="https://support.lifeinthegrid.com" target="_blank"><?php _e('Basic', 'wpduplicator') ?></a> &nbsp; | &nbsp;
					<a href="http://lifeinthegrid.com/services/" target="_blank"><?php _e('Premium', 'wpduplicator') ?></a>
				</div>	
				<i style="font-size:11px">
				 <?php _e('Basic: 2-5 business days', 'wpduplicator') ?> <br/>
				 <?php _e('Premium: 24-48hrs', 'wpduplicator') ?>
				</i>
			</div>
		</div> <br style="clear:both" /><br/><br/>
		
		
		
		
		<!--  ==================================================
		SUPPORT DUPLICATOR
		==================================================== -->
		<h2 class="dup-support-headers"><?php _e('Support Duplicator', 'wpduplicator') ?></h2>
		
		
		<!-- PARTNER WITH US -->
		<div class="dup-support-give-area">
			<table class="dup-support-hlp-hdrs">
				<tr >
					<td><img src="<?php echo DUPLICATOR_PLUGIN_URL  ?>img/check.png" /></td>
					<td><?php _e('Partner with Us', 'wpduplicator') ?></td>
				</tr>
			</table>
			<table style="text-align: center;width:100%; font-size:11px; font-style:italic; margin-top:15px">
				<tr>
					<td class="dup-support-grid-img" style="padding-left:40px">
						<div class="dup-support-cell" onclick="jQuery('#dup-donate-form').submit()">
							<form id="dup-donate-form" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" > 
								<input name="cmd" type="hidden" value="_s-xclick" /> 
								<input name="hosted_button_id" type="hidden" value="EYJ7AV43RTZJL" /> 
								<input alt="PayPal - The safer, easier way to pay online!" name="submit" src="<?php echo DUPLICATOR_PLUGIN_URL  ?>img/paypal.png" type="image" /> <br/>
								<?php _e('Keep Duplicator Active', 'wpduplicator') ?>
								<img src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/scr/pixel.gif" border="0" alt="" width="1" height="1" /> 
							</form>
						</div>
					</td>
					<td  style="padding-right:40px">
						<a href="http://wordpress.org/extend/plugins/duplicator" target="_blank"><img id="dup-img-5stars" src="<?php echo DUPLICATOR_PLUGIN_URL  ?>img/5star.png" /></a><br/>
						<?php _e('Leave 5 Stars', 'wpduplicator') ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<a href="http://lifeinthegrid.com/duplicator-survey" target="_blank"><?php _e('Take A Quick 60 Second Survey', 'wpduplicator') ?></a>
					</td>
				</tr>
			</table>
		</div> 
		 

		<!-- SPREAD THE WORD  -->
		<div class="dup-support-give-area">
			<table class="dup-support-hlp-hdrs">
				<tr >
					<td><img src="<?php echo DUPLICATOR_PLUGIN_URL  ?>img/mega.png" /></td>
					<td><?php _e('Spread the Word', 'wpduplicator') ?></td>
				</tr>
			</table>
			<div class="dup-support-hlp-txt">
				<?php
					$title = __("Duplicate Your WordPress", 'wpduplicator');
					$summary = __("Rapid WordPress Duplication by LifeInTheGrid.com", 'wpduplicator');
					$share_this_data = "st_url='" . DUPLICATOR_HOMEPAGE . "' st_title='{$title}' st_summary='{$summary}'";
				?>
				<div style="width:100%; padding:10px 10px 0px 10px" align="center">
					<span class='st_facebook_vcount' displayText='Facebook' <?php echo $share_this_data; ?> ></span>
					<span class='st_twitter_vcount' displayText='Tweet' <?php echo $share_this_data; ?> ></span>
					<span class='st_googleplus_vcount' displayText='Google +' <?php echo $share_this_data; ?> ></span>
					<span class='st_linkedin_vcount' displayText='LinkedIn' <?php echo $share_this_data; ?> ></span>
					<span class='st_email_vcount' displayText='Email' <?php echo $share_this_data; ?> ></span>
				</div>
				<!--div style="width:100%; padding:10px 10px 0px 10px" align="center">
					<table>
						<tr style="text-align:center">
							<td>
								<span class='st_reddit_large' displayText='Reddit' <?php echo $share_this_data; ?> ></span>
								<span class='st_slashdot_large' displayText='Slashdot' <?php echo $share_this_data; ?> ></span>
								<span class='st_stumbleupon_large' displayText='StumbleUpon' <?php echo $share_this_data; ?> ></span>
								<span class='st_technorati_large' displayText='Technorati' <?php echo $share_this_data; ?> ></span>
								<span class='st_digg_large' displayText='Digg' <?php echo $share_this_data; ?> ></span>
								<span class='st_blogger_large' displayText='Blogger' <?php echo $share_this_data; ?> ></span> 
								<span class='st_wordpress_large' displayText='WordPress' <?php echo $share_this_data; ?> ></span>		
								<span class='st_dzone_large' displayText='DZone' <?php echo $share_this_data; ?> ></span>					
							</td>
						</tr>
					</table>
				</div--><br/>
			</div>
		</div>
		<br style="clear:both" /><br/>
		
		<!--  ========================
		VISIT US -->
		
		<div style="width:100%; padding:10px 10px 0px 10px" align="center">
			<a href="http://lifeinthegrid.com" target="_blank">LifeInTheGrid</a> &nbsp; | &nbsp;
			<a href="http://lifeinthegrid.com/labs" target="_blank"><?php _e('Labs', 'wpduplicator') ?></a> &nbsp; | &nbsp; 
			<a href="http://www.youtube.com/lifeinthegridtv" target="_blank">YouTube</a>
		</div>
		
	</div>
</div><br/><br/><br/><br/>

