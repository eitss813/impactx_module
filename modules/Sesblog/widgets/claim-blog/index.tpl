<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?>
<?php
$base_url = $this->layout()->staticBaseUrl;
$this->headScript()
->appendFile($base_url . 'externals/autocompleter/Observer.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($base_url . 'externals/autocompleter/Autocompleter.Request.js');
?>

<?php $checkClaimRequest = Engine_Api::_()->getDbTable('claims', 'sesblog')->claimCount();?>
<div class="sesblog_profile_tebs sesbasic_clearfix">
<div class="sesblog_profile_tabs_top claim_request active sesbasic_clearfix">
	<a href="<?php echo $this->url(array('action' => 'claim'), 'sesblog_general', true);?>" class=" fa fa-plus"><?php echo $this->translate('Claim New Blog');?></a>
</div>
<?php if($checkClaimRequest):?>
	<div class="sesblog_profile_tabs_top claim_new_blog sesbasic_clearfix">
		<a href="<?php echo $this->url(array('action' => 'claim-requests'), 'sesblog_general', true);?>" class="far fa-file-alt"><?php echo $this->translate('Your Claim Requests');?></a>
	</div>
<?php endif;?>

</div>
<div>
  <?php echo $this->form->render($this) ?>
</div>

<script type="text/javascript">
	en4.core.runonce.add(function() {
		var contentAutocomplete = new Autocompleter.Request.JSON('title', "<?php echo $this->url(array('module' => 'sesblog', 'controller' => 'index', 'action' => 'get-blogs'), 'default', true) ?>", {
			'postVar': 'text',
			'minLength': 1,
			'selectMode': 'pick',
			'autocompleteType': 'tag',
			'customChoices': true,
			'filterSubset': true,
			'multiple': false,
			'className': 'sesbasic-autosuggest',
			'injectChoice': function(token) {
				var choice = new Element('li', {
					'class': 'autocompleter-choices', 
					'html': token.photo, 
					'id':token.label
				});
				new Element('div', {
					'html': this.markQueryValue(token.label),
					'class': 'autocompleter-choice'
				}).inject(choice);
				this.addChoiceEvents(choice).inject(this.choices);
				choice.store('autocompleteChoice', token);
			}
		});
		contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
			$('blog_id').value = selected.retrieve('autocompleteChoice').id;
		});
	});
	//Ajax error show before form submit
	var error = false;
	var objectError ;
	var counter = 0;
	function validateForm(){
		var errorPresent = false;
		counter = 0;
		sesJqueryObject('#sesblog_claim_create input, #sesblog_claim_create select,#sesblog_claim_create checkbox,#sesblog_claim_create textarea,#sesblog_claim_create radio').each(
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
					}
					else if(sesJqueryObject(this).prop('type') == 'textarea'){
						if(sesJqueryObject(this).css('display') == 'none'){
							var	content = tinymce.get(sesJqueryObject(this).attr('id')).getContent();
							if(!content)
							error= true;
							else
							error = false;
						}
						else if(sesJqueryObject(this).val() === '' || sesJqueryObject(this).val() == null)
						error = true;
						else
						error = false;
					}
					else{
						if(sesJqueryObject(this).val() === '' || sesJqueryObject(this).val() == null)
						error = true;
						else
						error = false;
					}
					if(error){
						if(counter == 0){
							objectError = this;
						}
						counter++
					}
					if(error)
					errorPresent = true;
					error = false;
				}
			}
		);
		return errorPresent ;
	}

	en4.core.runonce.add(function() {
		sesJqueryObject('#sesblog_claim_create').submit(function(e){
			var validationFm = validateForm();
			if(validationFm) {
				alert('<?php echo $this->translate("Please fill the red mark fields"); ?>');
				if(typeof objectError != 'undefined'){
					var errorFirstObject = sesJqueryObject(objectError).parent().parent();
					sesJqueryObject('html, body').animate({
					scrollTop: errorFirstObject.offset().top
					}, 2000);
				}
				return false;	
			}	
			else{
				sendDataToServer(this);
				return false;		
			}
		});
	});

	function sendDataToServer(object){
		//submit form 
		sesJqueryObject('.sesbasic_loading_cont_overlay').show();
		var formData = new FormData(object);
		formData.append('is_ajax', 1);
		var form = sesJqueryObject(object);
		sesJqueryObject.ajax({
			type:'POST',
			url: en4.core.baseUrl + "widget/index/mod/sesblog/name/claim-blog/",
			data:formData,
			cache:false,
			contentType: false,
			processData: false,
			success:function(data){
				sesJqueryObject('.sesbasic_loading_cont_overlay').hide();
				var data = sesJqueryObject.parseJSON(data);
				if(data.status){
					form.html(data.message);
				}else{
					form.before(data.message);
				}
				window.location.href = '<?php echo $this->url(array("action"=>"claim-requests"),"sesblog_general",true); ?>';
			},
			error: function(data){
			}
		});
	}
</script>

