<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: import-location-file.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
	function showLightbox() 
	{	
		$('import_form1').innerHTML = "<div><center><b class='bold'>" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing file content...")) ?>' + "</b></center><center class='mtop10'><img src='<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitecrowdfunding/externals/images/loader.gif' alt='<?php echo $this->string()->escapeJavascript($this->translate("Importing file content...")) ?>' /></center></div>";
		$('import_form').style.display = 'none';
	}
</script>

<div id='import_form1' class="sitecrowdfunding_upload_csv_popup_loader"></div>

<div class='clear global_form_popup sitecrowdfunding_upload_csv_popup'>
  <div class='settings' id="import_form">
    <?php echo $this->form->render($this); ?>
  </div>
</div>