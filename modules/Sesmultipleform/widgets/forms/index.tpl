<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<div class="sesmultipleform_form">
	<?php echo $this->form->render($this); ?>
<div class="sesbasic_loading_cont_overlay sesbasic_loading_cont_overlay_<?php echo $this->identity; ?>" style="display:none"></div>
</div>
 <?php 
$defaultProfileFieldId = "0_0_$this->defaultProfileId";
$profile_type = 2;
$identity = '_'.$this->identity;
?>
<?php echo $this->partial('_customFields.tpl', 'sesmultipleform', array()); ?> 
<script type="text/javascript">
 var defaultProfileFieldId<?php echo $identity ?>  = '<?php echo $defaultProfileFieldId.'_'.$this->identity ?>';
  var profile_type<?php echo $identity ?> = '<?php echo $profile_type ?>';
  var previous_mapped_level<?php echo $identity ?> = 0;
  function showFields<?php echo $identity ?>(cat_value, cat_level,typed,isLoad) {
		if($('category_id<?php echo $identity ?>'))
			var categoryId = getProfileType<?php echo $identity ?>($('category_id<?php echo $identity ?>').value);
		else
			var categoryId = 0;
		if($('subcat_id<?php echo $identity ?>'))
			var subcatId = getProfileType<?php echo $identity ?>($('subcat_id<?php echo $identity ?>').value);
		else
			var subcatId = 0;
		if($('subsubcat_id<?php echo $identity ?>'))
			var subsubcatId = getProfileType<?php echo $identity ?>($('subsubcat_id<?php echo $identity ?>').value);
		else
			var subsubcatId=0;
		var type = categoryId+','+subcatId+','+subsubcatId;
    if (cat_level == 1 || (previous_mapped_level<?php echo $identity ?> >= cat_level && previous_mapped_level<?php echo $identity ?> != 1) || (profile_type<?php echo $identity ?> == null || profile_type<?php echo $identity ?> == '' || profile_type<?php echo $identity ?> == 0)) {
      profile_type<?php echo $identity ?> = getProfileType<?php echo $identity ?>(cat_value);
      if (profile_type<?php echo $identity ?> == 0) {
        profile_type<?php echo $identity ?> = '';
      } else {
        previous_mapped_level<?php echo $identity ?> = cat_level;
      }
      $(defaultProfileFieldId<?php echo $identity ?>).value = profile_type<?php echo $identity ?>;
      changeFields($(defaultProfileFieldId<?php echo $identity ?>),null,isLoad,type,'<?php echo $this->identity; ?>');
    }
  }
  var getProfileType<?php echo $identity ?> = function(category_id) {
    var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sesmultipleform')->getMapping(array('category_id', 'profile_type'))); ?>;
		  for (i = 0; i < mapping.length; i++) {	
      	if (mapping[i].category_id == category_id)
        return mapping[i].profile_type;
    	}
    return 0;
  }
  en4.core.runonce.add(function() {
    sesJqueryObject('.profile_type_<?php echo $this->identity; ?>').parent().parent().addClass('displayF');
  });
  function showSubCategory<?php echo $identity ?>(cat_id,selectedId) {
		var selected;
		if(selectedId != ''){
			var selected = selectedId;
		}
    var url = en4.core.baseUrl + 'sesmultipleform/index/subcategory/category_id/' + cat_id;
    new Request.HTML({
      url: url,
      data: {
				'selected':selected
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('subcat_id<?php echo $identity ?>') && responseHTML) {
          if ($('subcat_id<?php echo $identity ?>-wrapper')) {
            $('subcat_id<?php echo $identity ?>-wrapper').style.display = "block";
          }
          $('subcat_id<?php echo $identity ?>').innerHTML = responseHTML;
        } else {
          if ($('subcat_id<?php echo $identity ?>-wrapper')) {
            $('subcat_id<?php echo $identity ?>-wrapper').style.display = "none";
            $('subcat_id<?php echo $identity ?>').innerHTML = '';
          }
					 if ($('subsubcat_id<?php echo $identity ?>-wrapper')) {
            $('subsubcat_id<?php echo $identity ?>-wrapper').style.display = "none";
            $('subsubcat_id<?php echo $identity ?>').innerHTML = '';
          }
        }
				showFields<?php echo $identity ?>(cat_id,1);
      }
    }).send(); 
  }
	function showSubSubCategory<?php echo $identity ?>(cat_id,selectedId,isLoad) {
		if($('category_id<?php echo $identity ?>'))
			var categoryId = getProfileType<?php echo $identity ?>($('category_id<?php echo $identity ?>').value);
		else
			var categoryId = 0;
		if(cat_id == 0){
			if ($('subsubcat_id<?php echo $identity ?>-wrapper')) {
				$('subsubcat_id<?php echo $identity ?>-wrapper').style.display = "none";
				$('subsubcat_id<?php echo $identity ?>').innerHTML = '';
				sesJqueryObject('.profile_type_<?php echo $this->identity; ?>').val(categoryId);		
      }
			showFields<?php echo $identity ?>(cat_id,1,categoryId);
			return false;
		}
		showFields<?php echo $identity ?>(cat_id,1,categoryId);
		var selected;
		if(selectedId != ''){
			var selected = selectedId;
		}
    var url = en4.core.baseUrl + 'sesmultipleform/index/subsubcategory/subcategory_id/' + cat_id;
    (new Request.HTML({
      url: url,
      data: {
				'selected':selected
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if ($('subsubcat_id<?php echo $identity ?>') && responseHTML) {
          if ($('subsubcat_id<?php echo $identity ?>-wrapper')) {
            $('subsubcat_id<?php echo $identity ?>-wrapper').style.display = "block";
          }
          $('subsubcat_id<?php echo $identity ?>').innerHTML = responseHTML;
					// get category id value 
				if(isLoad == 'no')
				showFields<?php echo $identity ?>(cat_id,1,categoryId,isLoad);
        } else {
          if ($('subsubcat_id<?php echo $identity ?>-wrapper')) {
            $('subsubcat_id<?php echo $identity ?>-wrapper').style.display = "none";
            $('subsubcat_id<?php echo $identity ?>').innerHTML = '';
          }
        }
      }
    })).send();  
  }
	function showCustom<?php echo $identity ?>(value,isLoad){
		var categoryId = getProfileType<?php echo $identity ?>($('category_id<?php echo $identity ?>').value);
		var subcatId = getProfileType<?php echo $identity ?>($('subcat_id<?php echo $identity ?>').value);
		var id = categoryId+','+subcatId;
			showFields<?php echo $identity ?>(value,1,id,isLoad);
		if(value == 0)
			sesJqueryObject('.profile_type_<?php echo $this->identity; ?>').val(subcatId)
			return false;
	}
	
  en4.core.runonce.add(function() {
		showFields<?php echo $identity ?>(0,1,0);
		sesJqueryObject('#subcat_id<?php echo $identity ?>-wrapper').hide();
		sesJqueryObject('#subsubcat_id<?php echo $identity ?>-wrapper').hide();
		<?php if(isset($this->isSingleCategory)){ ?>
			sesJqueryObject('#category_id<?php echo $identity ?>-wrapper').hide();
		<?php } ?>
	});
  	//Ajax error show before form submit
var error<?php echo $identity ?> = false;
var objectError<?php echo $identity ?> ;
var counter<?php echo $identity ?> = 0;
function validateForm<?php echo $identity ?>(){
		counter<?php echo $identity ?> = 0;
		var error<?php echo $identity ?>Present = false;
		customErrorMessage<?php echo $identity ?>;
		sesJqueryObject('.sesmultipleform_create<?php echo $identity; ?> input, .sesmultipleform_create<?php echo $identity; ?> select, .sesmultipleform_create<?php echo $identity; ?> checkbox, .sesmultipleform_create<?php echo $identity; ?> textarea, .sesmultipleform_create<?php echo $identity; ?> radio').each(
				function(index){
						var input = sesJqueryObject(this);
						if(sesJqueryObject(this).closest('div').parent().css('display') != 'none' && sesJqueryObject(this).closest('div').parent().find('.form-label').find('label').first().hasClass('required') && sesJqueryObject(this).prop('type') != 'hidden' && sesJqueryObject(this).closest('div').parent().attr('class') != 'form-elements'){	
						  if(sesJqueryObject(this).prop('type') == 'checkbox'){
								value = '';
								if(sesJqueryObject('input[name="'+sesJqueryObject(this).attr('name')+'"]:checked').length > 0) { 
										value = 1;
								};
								if(value == '')
									error<?php echo $identity ?> = true;
								else
									error<?php echo $identity ?> = false;
							}else if(sesJqueryObject(this).prop('type') == 'select-multiple'){
								if(sesJqueryObject(this).val() === '' || sesJqueryObject(this).val() == null)
									error<?php echo $identity ?> = true;
								else
									error<?php echo $identity ?> = false;
							}else if(sesJqueryObject(this).prop('type') == 'select-one' || sesJqueryObject(this).prop('type') == 'select' ){
								if(sesJqueryObject(this).val() === '')
									error<?php echo $identity ?> = true;
								else
									error<?php echo $identity ?> = false;
							}else if(sesJqueryObject(this).prop('type') == 'radio'){
								if(sesJqueryObject("input[name='"+sesJqueryObject(this).attr('name').replace('[]','')+"']:checked").val() === '' || typeof sesJqueryObject("input[name='"+sesJqueryObject(this).attr('name').replace('[]','')+"']:checked").val() == 'undefined')
									error<?php echo $identity ?> = true;
								else
									error<?php echo $identity ?> = false;
							}else if(sesJqueryObject(this).prop('type') == 'textarea'){
								if(sesJqueryObject(this).css('display') == 'none'){
								 var	content = tinymce.get(sesJqueryObject(this).attr('id')).getContent();
								 if(!content)
								 	error<?php echo $identity ?> = true;
								 else
								 	error<?php echo $identity ?> = false;
								}else	if(sesJqueryObject(this).val() === '' || sesJqueryObject(this).val() == null)
									error<?php echo $identity ?> = true;
								else
									error<?php echo $identity ?> = false;
							}else{
								if(sesJqueryObject(this).val() === '' || sesJqueryObject(this).val() == null)
									error<?php echo $identity ?> = true;
								else
									error<?php echo $identity ?> = false;
							}
							if(error<?php echo $identity ?>){
							 if(counter<?php echo $identity ?> == 0){
							 	objectError<?php echo $identity ?> = this;
							 }
								counter<?php echo $identity ?>++
								}else{
							}
							if(error<?php echo $identity ?>)
								error<?php echo $identity ?>Present = true;
							error<?php echo $identity ?> = false;
						}
				}
			);
		if(!error<?php echo $identity ?>Present){
			if(sesJqueryObject('.sesmultipleform_create<?php echo $identity; ?>').find('div').find('div').find('.form-elements').find('#email-wrapper').length){
					var object = sesJqueryObject('.sesmultipleform_create<?php echo $identity; ?>').find('div').find('div').find('.form-elements');
					if(!checkEmail<?php echo $identity ?>(object.find('#email<?php echo $identity; ?>-element').find('#email<?php echo $identity; ?>').val())){
							objectError<?php echo $identity ?> = object.find('#email<?php echo $identity; ?>-element').find('#email<?php echo $identity; ?>');
							customErrorMessage<?php echo $identity ?> = '<?php echo $this->translate("Please enter valid email") ?>';
							error<?php echo $identity ?>Present = true;
					}else
							error<?php echo $identity ?>Present = false;
			}
			if(!error<?php echo $identity ?>Present && sesJqueryObject('.sesmultipleform_create<?php echo $identity; ?>').find('div').find('div').find('.form-elements').find('#captcha-wrapper').length){
					var object = sesJqueryObject('.sesmultipleform_create<?php echo $identity; ?>').find('div').find('div').find('.form-elements');
					if(object.find('#captcha-element').find('#captcha-input').val() != object.find('#captchValue').val()){
							//objectError<?php echo $identity ?> = object.find('#captcha-element').find('#captcha-input');
							//customErrorMessage<?php echo $identity ?> = '<?php echo $this->translate("Captcha value not match") ?>';
							//error<?php echo $identity ?>Present = true;
					}else
							error<?php echo $identity ?>Present = false;
			}
			if(!error<?php echo $identity ?>Present && sesJqueryObject('.sesmultipleform_create<?php echo $identity; ?>').find('div').find('div').find('.form-elements').find('#terms<?php echo $identity ?>-wrapper').length){
				var object = sesJqueryObject('.sesmultipleform_create<?php echo $identity; ?>').find('div').find('div').find('.form-elements');
				if(!object.find('#terms<?php echo $identity; ?>-wrapper').find('#terms<?php echo $identity; ?>-element').find('#terms<?php echo $identity; ?>').is(':checked')){
					objectError<?php echo $identity ?> = object.find('#terms<?php echo $identity; ?>-wrapper').find('#terms<?php echo $identity; ?>-element').find('#terms<?php echo $identity; ?>');
					customErrorMessage<?php echo $identity ?> = '<?php echo $this->translate("Please accept term of services") ?>';
					error<?php echo $identity ?>Present = true;
				}else
					error<?php echo $identity ?>Present = false;
			}
		}
			return error<?php echo $identity ?>Present ;
}
//validate email
function checkEmail<?php echo $identity ?>(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}
var customErrorMessage<?php echo $identity ?>;
sesJqueryObject(document).ready(function(e){
	var obj = sesJqueryObject('.sesmultipleform_create<?php echo $identity; ?>').find('div').find('div').find('.form-elements');
	if(obj.find('#captcha-wrapper').length){
		var captchid = 	obj.find('#captcha-element').find('#captcha-id').val();
		 var url = en4.core.baseUrl + 'sesmultipleform/index/getcaptchavalue/id/' + captchid;
		var captcha =
			new Request.HTML({
				url: url,
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					if(responseHTML){
						obj.find('#captchValue').val(responseHTML);	
					}
				}
			});
		captcha.send();
	}
});
if(typeof sesmultipleform_create<?php echo $identity; ?> == 'undefined'){
var	sesmultipleform_create<?php echo $identity; ?> = 1;
	sesJqueryObject(document).on('submit','.sesmultipleform_create<?php echo $identity; ?>',function(e){
		var validationFm = validateForm<?php echo $identity ?>();
		if(validationFm)
		{
			if(typeof customErrorMessage<?php echo $identity ?> != 'undefined'){
				alert(customErrorMessage<?php echo $identity ?>);
			}else{
				alert('<?php echo $this->translate("Please complete all * mark fields"); ?>');
			}
			if(typeof objectError<?php echo $identity ?> != 'undefined'){
				if(typeof customErrorMessage<?php echo $identity ?> != 'undefined'){
					var error<?php echo $identity ?>FirstObject = sesJqueryObject(objectError<?php echo $identity ?>).parent().parent();
				}else
					var error<?php echo $identity ?>FirstObject = sesJqueryObject(objectError<?php echo $identity ?>).parent().parent();	
				<?php if(!$this->isSmoothbox){ ?>						
				 sesJqueryObject('html, body').animate({
					scrollTop: error<?php echo $identity ?>FirstObject.offset().top
				 }, 2000);
				<?php } ?>
			}
			return false;	
		}else{
			sendDataToServer<?php echo $identity ?>(this);
			return false;		
		}
});
}
function sendDataToServer<?php echo $identity ?>(object){
			//submit form 
			sesJqueryObject('.sesbasic_loading_cont_overlay_<?php echo $this->identity; ?>').show();
			var formData = new FormData(object);
			formData.append('is_ajax', 1);
			formData.append('form_id' , '<?php echo $this->formtype; ?>');
			formData.append('identity' , '<?php echo $this->identity; ?>');
			var form = sesJqueryObject(object);
			 sesJqueryObject.ajax({
            type:'POST',
            url: en4.core.baseUrl + "widget/index/mod/sesmultipleform/name/forms/",
            data:formData,
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
							sesJqueryObject('.sesbasic_loading_cont_overlay_<?php echo $this->identity; ?>').hide();
              if(data == "captcha"){
							    alert('<?php echo $this->translate("Captcha value not match") ?>');
							    return;
							}
							var data = sesJqueryObject.parseJSON(data);
							if(data.status){
								<?php if($this->isSmoothbox){ ?>								
								var widthprev = sesJqueryObject('#sessmoothbox_container').width();
										form.before(data.message).fadeIn();
										<?php if($this->hideform){ ?>
											sesJqueryObject(form).fadeOut(1000);
										<?php }else{ ?>
										resetForm<?php echo $this->identity; ?>(sesJqueryObject('.sesmultipleform_create_<?php echo $this->identity; ?>'));
										setTimeout(function(){sesJqueryObject('.sesmultipleform_form_<?php echo $this->identity; ?>').fadeOut(1000,function(){
												sesJqueryObject('.sesmultipleform_form_<?php echo $this->identity; ?>').remove();
										})},3000);
									<?php } ?>
										resizesessmoothbox(widthprev);
									<?php if($this->closepopup){ ?>
									setTimeout(function(){ sessmoothboxclose();},5000);
									<?php if($this->redirect){ ?>
											window.location.href = '<?php echo $this->redirect; ?>';
											return false;
									<?php } ?>
									<?php } ?>
								<?php }else{ ?>
									form.before(data.message).fadeIn();
									<?php if($this->hideform){ ?>
										sesJqueryObject(form).fadeOut(1000);
									<?php }else{ ?>
									resetForm<?php echo $this->identity; ?>(sesJqueryObject('.sesmultipleform_create_<?php echo $this->identity; ?>'));
									setTimeout(function(){sesJqueryObject('.sesmultipleform_form_<?php echo $this->identity; ?>').fadeOut(1000,function(){
											sesJqueryObject('.sesmultipleform_form_<?php echo $this->identity; ?>').remove();
										})},3000);
									<?php } ?>
									<?php if($this->redirect){ ?>
											window.location.href = '<?php echo $this->redirect; ?>';
											return false;
									<?php } ?>
							<?php } ?>
							}else{
									form.before(data.message).fadeIn();
							}
						},
            error: function(data){
            	//silence
						}
        });
			
}
function resetForm<?php echo $this->identity; ?>(ele){
        sesJqueryObject(ele).find(':input').each(function() {
            switch(this.type) {
                case 'password':
                case 'select-multiple':
                case 'select-one':
                case 'text':
                case 'textarea':
                    sesJqueryObject(this).val('');
                    break;
                case 'checkbox':
                case 'radio':
                    this.checked = false;
            }
        });
}
</script>
<style type="text/css">
.sesmultipleform_create_<?php echo $this->identity; ?> div.form-label label.required:after{
	content: " *";
	color: <?php echo '#'.$this->formsettings->color_asterisk; ?> !important;
}
</style>
<?php if($this->isSmoothbox){ 
	die;
} ?>