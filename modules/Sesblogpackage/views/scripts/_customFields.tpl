<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: _customFields.tpl 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/sesJquery.js');?>
<?php $options = Engine_Api::_()->getDbTable('options','sesblog')->getAllOptions();
	if(count($options)){ ?>
<div id="custom-fields-container" style="display:none;">
  <?php foreach($options as $valueOptions){
  		//get all meta values related to options
      $metaValues = Engine_Api::_()->getDbTable('metas','sesblog')->getMetaData($valueOptions['option_id']);
      if(!count($metaValues))
      	continue;
   ?>
   <div>
  	<div style="font-weight:bold;"><a class="openClass" href="javascript:;"><?php echo $valueOptions['label']; ?></a></div>
    	<ul class="metaValues" style="display:none;">
    	<?php foreach($metaValues as $metaValue){ ?>
      		<li>
          	<input type="checkbox" name="1_<?php echo $valueOptions['option_id'].'_'.$metaValue['field_id']; ?>" value="<?php echo  $valueOptions['option_id']; ?>" <?php if(in_array('1_'.$valueOptions['option_id'].'_'.$metaValue['field_id'],$this->customFields)){ ?> checked="checked" <?php } ?> /><?php echo $metaValue['label']; ?> 
          </li>
      <?php } ?>
      </ul>
  </div>
  <?php } ?>  	
</div>
<?php } ?>
<script type="application/javascript">
function customField(value){
	if(value == 2){
		sesJqueryObject('#custom-fields-container').show();
	}else{
			sesJqueryObject('#custom-fields-container').hide();
	}
}
sesJqueryObject(document).ready(function(e){
	var valueS = (document.querySelector('input[name="custom_fields"]:checked').value);
	if(valueS == 2){
		sesJqueryObject('#custom_fields-'+valueS).trigger('click');	
	}
});
sesJqueryObject(document).on('click','.openClass',function(e){
	if(sesJqueryObject(this).hasClass('active')){
		sesJqueryObject(this).removeClass('active');
		sesJqueryObject(this).parent().parent().find('.metaValues').hide();
	}else{
		sesJqueryObject(this).addClass('active');
		sesJqueryObject(this).parent().parent().find('.metaValues').show();	
	}	
});
</script>
