<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: edit.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?>
<div class="layout_middle">	
  <div class="generic_layout_container">
<div class="headline">
  <h2>
    <?php echo $this->translate('Photo Albums');?>
  </h2>
</div>
<div class="clear sesblog_order_view_top" style="margin:10px;">
      <a href="<?php echo $this->blog->getHref(); ?>" class="buttonlink sesbasic_icon_back"><?php echo $this->translate("Back To Blog"); ?></a>
    </div>
<div class="sesalbum_album_form">
  <?php echo $this->form->render(); ?>
</div>
</div>
</div>
<script type="text/javascript">
	//Ajax error show before form submit
var error = false;
var objectError ;
var counter = 0;
function validateForm(){
		var errorPresent = false;
		sesJqueryObject('#albums_edit input, #albums_edit select,#albums_edit checkbox,#albums_edit textarea,#albums_edit radio').each(
				function(index){
						var input = sesJqueryObject(this);
						if(sesJqueryObject(this).closest('div').parent().css('display') != 'none' && sesJqueryObject(this).closest('div').parent().find('.form-label').find('label').first().hasClass('required') && sesJqueryObject(this).prop('type') != 'hidden' && sesJqueryObject(this).closest('div').parent().attr('class') != 'form-elements'){	
						  if(sesJqueryObject(this).prop('type') == 'checkbox'){
								value = '';
								if(sesJqueryObject('input[name="'+sesJqueryObject(this).attr('name')+'"]:checked').length > 0) { 
										value = 1;
								};
								if(value == '')
									error = true;
								else
									error = false;
							}else if(sesJqueryObject(this).prop('type') == 'select-multiple'){
								if(sesJqueryObject(this).val() === '' || sesJqueryObject(this).val() == null)
									error = true;
								else
									error = false;
							}else if(sesJqueryObject(this).prop('type') == 'select-one' || sesJqueryObject(this).prop('type') == 'select' ){
								if(sesJqueryObject(this).val() === '')
									error = true;
								else
									error = false;
							}else if(sesJqueryObject(this).prop('type') == 'radio'){
								if(sesJqueryObject("input[name='"+sesJqueryObject(this).attr('name').replace('[]','')+"']:checked").val() === '')
									error = true;
								else
									error = false;
							}else if(sesJqueryObject(this).prop('type') == 'textarea'){
								if(sesJqueryObject(this).val() === '' || sesJqueryObject(this).val() == null)
									error = true;
								else
									error = false;
							}else{
								if(sesJqueryObject(this).val() === '' || sesJqueryObject(this).val() == null)
									error = true;
								else
									error = false;
							}
							if(error){
							 if(counter == 0){
							 	objectError = this;
							 }
								//sesJqueryObject(this).closest('div').parent().css('border','1px dashed #ff0000');
								counter++
							}else{
							 	//sesJqueryObject(this).closest('div').parent().css('border','');
							}
							if(error)
								errorPresent = true;
							error = false;
						}
				}
			);
				
			return errorPresent ;
}
sesJqueryObject(document).on('submit', '#albums_edit',function(e) {
		var validation = validateForm();
		if(validation)
		{
			alert('<?php echo $this->translate("Please fill the red mark fields"); ?>');
			if(typeof objectError != 'undefined'){
			 var errorFirstObject = sesJqueryObject(objectError).parent().parent();
			 sesJqueryObject('html, body').animate({
        scrollTop: errorFirstObject.offset().top
    	 }, 2000);
			 window.location.hash = '#'+errorFirstObject;
			}
			return false;	
		}else
			return true;
});
</script>
