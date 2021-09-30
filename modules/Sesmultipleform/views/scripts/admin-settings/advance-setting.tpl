<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: advance-setting.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */ 
?>
<?php
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jscolor/jscolor.js');
  $id = $this->form_id;
?>
<script type="application/javascript">
	function showDescriptionSetting(value){
		if (value==0)
		{
			if(document.getElementById('description_required-wrapper'))
				document.getElementById('description_required-wrapper').style.display = 'none';
		}else{
			if(document.getElementById('description_required-wrapper'))
				document.getElementById('description_required-wrapper').style.display = 'block';
		}		
	}
		function showFileUpload(value)
		{
			if (value==0)
			{
			if(document.getElementById('file_upload-wrapper'))
				document.getElementById('file_upload-wrapper').style.display = 'none';
			if(document.getElementById('display_file_upload_required-wrapper'))
				document.getElementById('display_file_upload_required-wrapper').style.display = 'none';				
			if(document.getElementById('label_file_upload-wrapper'))
				document.getElementById('label_file_upload-wrapper').style.display = 'none';				
			}
			else
			{
			if(document.getElementById('file_upload-wrapper'))
				document.getElementById('file_upload-wrapper').style.display = 'block';
			if(document.getElementById('display_file_upload_required-wrapper'))
				document.getElementById('display_file_upload_required-wrapper').style.display = 'block';
			if(document.getElementById('label_file_upload-wrapper'))
				document.getElementById('label_file_upload-wrapper').style.display = 'block';				
			}
		}
		window.addEvent('domready', function() {
			showDescriptionSetting('<?php  echo $this->formObj->description; ?>');
			showFileUpload(document.getElementById('display_file_upload').value);
		});
</script>
<?php include APPLICATION_PATH .  '/application/modules/Sesmultipleform/views/scripts/dismiss_message.tpl';?>
<div class="sesbasic_search_reasult">
  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'forms', 'action' => 'index'), $this->translate("Back to Manage Forms"), array('class'=>'sesbasic_icon_back buttonlink')) ?>
  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmultipleform', 'controller' => 'categories', 'action' => 'index', 'id' => $id), $this->translate("Manage Categories"), array('class'=>'sesbasic_icon_add buttonlink')) ?>
</div>
<div class='clear sesbasic_admin_form'>
	<div class='settings'>
		<?php echo $this->form->render($this); ?>
	</div>
</div>
<style type="text/css">
#sesmultipleformheading-wrapper{border-bottom-width:1px;padding-bottom:5px;}
#sesmultipleformheading-label{font-size:17px;}
</style>