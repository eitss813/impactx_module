<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: import.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
	function showLightbox() 
	{	
		$('import_form1').innerHTML = "<div><center><b class='bold'>" + '<?php echo $this->string()->escapeJavascript($this->translate("Importing file content...")) ?>' + "</b></center><center class='mtop10'><img src='"+en4.core.staticBaseUrl+"application/modules/Sitepage/externals/images/admin/loader.gif' alt='<?php echo $this->string()->escapeJavascript($this->translate("Importing file content...")) ?>' /></center></div>";
		$('import_form').style.display = 'none';
	}
</script>

<div id='import_form1' class="sitepage_upload_csv_popup_loader"></div>

<div class='clear global_form_popup sitepage_upload_csv_popup'>
  <div class='settings' id="import_form">
    <?php echo $this->form->render($this); ?>
  </div>
</div>