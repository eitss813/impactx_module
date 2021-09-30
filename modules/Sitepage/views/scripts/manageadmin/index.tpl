<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manageadmins.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
	var submitformajax = 1;
	var manage_admin_formsubmit = 1;
</script>
<script type="text/javascript">
	var viewer_id = '<?php echo  $this->viewer_id; ?>';
	var url = '<?php  echo $this->url(array(), 'sitepage_general', true) ?>';
</script>

<?php
	$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>
<?php if (empty($this->is_ajax)) : ?>
<div class="generic_layout_container layout_middle">
	<div class="generic_layout_container layout_core_content">
		<?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
		<div class="layout_middle">
			<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
			<?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Manage Admins', 'sectionDescription' => '')); ?>
			<div class="sitepage_edit_content">
				<div id="show_tab_content">
					<?php endif; ?>
					<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/scripts/core.js'); ?>
					<div class="sitepage_form">
						<div>
							<div>
								<div class="sitepage_manageadmins">
									<h3> <?php echo $this->translate('Manage Page Admins'); ?> </h3>
									<p class="form-description"><?php echo $this->translate("Below you can see all the admins who can administer and manage your page, like you can do. You can add new members as admins of this page and remove any existing ones. Note that admins selected by you for this page will get complete authority like you to manage this page, including deleting it. Thus you should be specific in selecting them.") ?></p>
									<br />
									<?php if (!empty($this->message)): ?>
										<div class="tip">
											<span style="margin-bottom:0px !important;">
												<?php echo $this->message; ?>
											</span>
										</div>
									<?php  endif;?>

									<div class="manage_admin_form">
										<?php  $item = count($this->paginator) ?>
										<input type="hidden" id='count_div' value='<?php echo $item ?>' />
										<form id='video_selected' method='post' class="global_form mtop10" action='<?php echo $this->url(array('action' => 'index', 'page_id' => $this->page_id), 'sitepage_manageadmins') ?>'>
										<div class="fleft">
											<div class="form-content">
												<div class="sitepage_manageadmins_input">
													<?php echo $this->translate("Start typing the name of the member...") ?> <br />
													<input type="text" id="searchtext" name="searchtext" value="" />
													<input type="hidden" id="user_id" name="user_id" />
													<input type="hidden" id="user_email" name="user_email" />
												</div>
												<div class="sitepage_manageadmins_button">
													<button type="submit"  name="submit"><?php echo $this->translate("Add as Admin") ?></button>
												</div>
											</div>
										</div>
										</form>
										<br/>
									</div>


									<!-- Admin with userID -->
									<?php foreach ($this->manageAdminUsers as $item):?>
										<div id='<?php echo $item->manageadmin_id ?>_page_main'  class='sitepage_manageadmins_list'>
											<div class='sitepage_manageadmins_thumb' id='<?php echo $item->manageadmin_id ?>_pagethumb'>
												<?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
											</div>
											<div id='<?php echo $item->manageadmin_id ?>_page' class="sitepage_manageadmins_detail">
												<div class="sitepage_manageadmins_cancel">
									 				<?php $url = $this->url(array('action' => 'delete'), 'sitepage_manageadmins', true);?>
													<?php if ( $this->owner_id != $item->user_id ) :?>
														<a href="javascript:void(0);" onclick="manageinfo('<?php echo $item->manageadmin_id?>','<?php echo $item->getOwner()->getIdentity()?>', '<?php echo $url;?>', '<?php echo $this->page_id ?>')";><?php echo $this->translate('Remove');?></a>
													<?php endif;?>
												</div>
												<span>
													<?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
												</span>
											</div>
										</div>
									<?php endforeach; ?>

									<!-- Admin without userId (which was invited ) -->
									<?php foreach ($this->manageInvitedUsers as $item):?>
										<div id='<?php echo $item->manageadmin_id ?>_page_main'  class='sitepage_manageadmins_list'>
											<div class='sitepage_manageadmins_thumb' id='<?php echo $item->manageadmin_id ?>_pagethumb'>
												<!--	<?php $photo =  '/application/modules/Sitepage/externals/images/nophoto_user_thumb_icon.png'; ?> -->
												<?php $photo =  '/application/modules/Sitepage/externals/images/nophoto_user_thumb_icon.png'; ?>
												<img src="<?php echo $this->layout()->staticBaseUrl ?><?php echo $photo ?>" style="width: 53px;">
											</div>
											<div id='<?php echo $item->manageadmin_id ?>_page' class="sitepage_manageadmins_detail">
												<div class="sitepage_manageadmins_cancel">
													<?php $url = $this->url(array('action' => 'delete'), 'sitepage_manageadmins', true);?>
													<a href="javascript:void(0);" onclick="manageinfo('<?php echo $item->manageadmin_id?>','', '<?php echo $url;?>', '<?php echo $this->page_id ?>')";>
														<?php echo $this->translate('Remove');?>
													</a>
												</div>
												<span style="font-weight: 800;color: black;">
													<?php echo $item->member_email?> <br/>
												    <!--	Inivitation has been sent -->
												</span>
												Invitation sent, pending acceptance
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
					<br />
					<div id="show_tab_content_child">
					</div>
					<?php if (empty($this->is_ajax)) : ?>
		  		</div>
	  		</div>
  		</div>
	</div>
</div>
<?php endif; ?>

<script type="text/javascript">
	var selectionflag=false;
	en4.core.runonce.add(function() {

		var contentAutocomplete = new Autocompleter.Request.JSON('searchtext', '<?php echo $this->url(array( 'action' => 'manage-auto-suggest', 'page_id' => $this->page_id), 'sitepage_manageadmins', true) ?>', {
		'postVar' : 'text',
		'selectMode': 'pick',
		'autocompleteType': 'tag',
		'className': 'searchbox_autosuggest',
		'customChoices' : true,
		'filterSubset' : true,
		'multiple' : false,
		'injectChoice': function(token){
				var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label});
				new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice1'}).inject(choice);
				new Element('div', {'html': this.markQueryValue(token.email),'class': 'autocompleter-choice2'}).inject(choice);
				this.addChoiceEvents(choice).inject(this.choices);
				choice.store('autocompleteChoice', token);
			}
		});

		contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
			document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
		});

		//update email value
		$$('#searchtext').addEvent('change', function(event) {
			var searchtext = document.getElementById('searchtext').value;
			var user_id = 	document.getElementById('user_id').value;
			document.getElementById('user_email').value = searchtext;
			console.log('change',document.getElementById('user_email').value);
			if(!user_id) {
				document.getElementById('user_id').value=0;
			}
		});
	});

	function activ_autosuggest () {

		if ( $('searchtext') == null )
			return false;

		var contentAutocomplete = new Autocompleter.Request.JSON('searchtext', '<?php echo $this->url(array( 'action' => 'manage-auto-suggest', 'page_id' => $this->page_id), 'sitepage_manageadmins', true) ?>', {
			'postVar' : 'text',
			'minLength': 1,
			'maxChoices': 40,
			'selectMode': 'pick',
			'autocompleteType': 'tag',
			'className': 'searchbox_autosuggest',
			'customChoices' : true,
			'filterSubset' : true,
			'multiple' : false,
			'injectChoice': function(token){
				var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label});
				new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice1'}).inject(choice);
				new Element('div', {'html': this.markQueryValue(token.email),'class': 'autocompleter-choice2'}).inject(choice);
				this.addChoiceEvents(choice).inject(this.choices);
				choice.store('autocompleteChoice', token);
			}
		});
		contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
			document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
		});
	}

</script>
<style type="text/css">
.global_form > div > div{background:none;border:none;padding:0px;}
.sitepage_manageadmins_list{
	padding: 10px 0px !important;
}
.sitepage_manageadmins_input {
	margin: 0px 10px 0px 0px !important;
}


@media (max-width: 767px) {
	.form-content {
		flex-direction: column;
		align-items: center;
	}
	.sitepage_manageadmins_button {
		margin-left: 0px !important;
		margin-top: 24px;
	}
}
</style>