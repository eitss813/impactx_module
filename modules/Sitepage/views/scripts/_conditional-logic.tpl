<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Yndynamicform/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');
$this->headScript()
	->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-1.10.2.min.js')
	->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-ui-1.11.4.min.js')
	->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/conditional_logic.js');

// get option id by passing directly to the tpl, or from the request's param
$form_id = (isset($this->option_id)) ? $this->option_id : Zend_Controller_Front::getInstance()->getRequest()->getParam('form_id', 0);
if ($form_id) {
	$form = Engine_Api::_()->getItem('yndynamicform_form', $form_id);
	$option_id = $form->option_id;
} else {
	$option_id = (isset($this->option_id)) ? $this->option_id : Zend_Controller_Front::getInstance()->getRequest()->getParam('option_id', 0);
}
$field_id = (isset($this->field_id)) ? $this->field_id : Zend_Controller_Front::getInstance()->getRequest()->getParam('field_id', 0);
?>

<?php if($option_id): ?>
	<?php
	// Get fields for conditional logic
	$fieldMaps = Engine_Api::_()->fields()->getFieldsMaps('yndynamicform_entry')->getRowsMatching('option_id', $option_id);
	$fieldInfo = Engine_Api::_()->yndynamicform()->getFieldInfo('fields');
	$fieldData = array();

	foreach( $fieldMaps as $map ) {
		$field = $map->getChild();

		if( $field->type == 'profile_type' || !$fieldInfo[$field->type]['conditional_compare'] )
			continue;
		// exclude current field
		if ($field_id && ($field_id == $field->field_id))
			continue;
		$fieldItem = $field->toArray();
		unset($fieldItem['description']);
		$fieldItem['options'] = array();
		$fieldItem['conditional_predefined'] = 0;
		if ($field->canHaveDependents()) {
			$childOptions = $field->getOptions();
			// skip if predefined values is empty
			if (!count($childOptions))
				continue;
			$fieldItem['conditional_predefined'] = 1;
			foreach ($childOptions as $childOption)
			{
				$fieldItem['conditional_options'][$childOption->option_id] = $childOption->label;
			}
		}
		if (isset($fieldInfo[$field->type]['multiOptions'])) {
			$fieldItem['conditional_predefined'] = 1;
			foreach ($fieldInfo[$field->type]['multiOptions'] as $key => $value)
			{
				$fieldItem['conditional_options'][$key] = $value;
			}
		}
		if (isset($fieldInfo[$field->type]['conditional_multioptions'])) {
			$fieldItem['conditional_predefined'] = 1;
			$options = Engine_Api::_()->yndynamicform()->getConditionalMultiOptions($field->type);
			foreach ($options as $key => $value)
			{
				$fieldItem['conditional_options'][$key] = $value;
			}
		}
		$fieldItem['conditional_compare'] = $fieldInfo[$field->type]['conditional_compare'];
		$fieldData[$field->field_id] = $fieldItem;
	}
	?>
	<div id="yndform_conditional_container" style="display: none;">

		<div id="conditional_show-wrapper" class="form-wrapper">
			<div id="conditional_show-element" class="form-element">
				<select name="conditional_show" id="conditional_show">
					<option value="1"><?php echo $this->translate('Show') ?></option>
					<option value="0"><?php echo $this->translate('Hide') ?></option>
				</select>
			</div>
			<div id="conditional_show-label" class="form-label">
				<label class="optional"><?php echo $this->translate('this form field if') ?></label>
			</div>
		</div>

		<div id="conditional_scope-wrapper" class="form-wrapper">
			<div id="conditional_scope-element" class="form-element">
				<select name="conditional_scope" id="conditional_scope">
					<option value="all"><?php echo $this->translate('All') ?></option>
					<option value="any"><?php echo $this->translate('Any') ?></option>
				</select>
			</div>
			<div id="conditional_scope-label" class="form-label">
				<label class="optional"><?php echo $this->translate('of the following fields a matched') ?>:</label>
			</div>
		</div>

		<div id="yndform_conditional_list">
		</div>
	</div>

	<?php
		$mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('yndynamicform_entry');
		$map = $mapData->getRowMatching('child_id', $field_id);
	?>
	<?php if($map):?>
		<?php if($map->getChild()):?>
			<?php $field = $map->getChild();?>
			<?php // echo 'field_id'.$field->field_id;?>
			<?php // echo 'type'.$field->type ;?>

			<?php if(
				$field->type =='select' ||
				$field->type =='radio' ||
				$field->type =='gender' ||
				$field->type =='multiselect' ||
				$field->type == 'multi_checkbox'
			):?>
				<div id="option_container">
					<span id="option_msg"></span>
					<span id="option_spinner"></span>
					<?php echo $this->adminChoiceFieldMeta($map) ?>
				</div>
			<?php endif;?>

		<?php endif;?>
	<?php endif;?>

	<div id="hidden_ajax_data" style="display: none"></div>

	<script type='text/javascript'>
		var fieldData,
			conditionalLogicData = [],
			conditionalContainer = $('yndform_conditional_container'),
			conditionalList = $('yndform_conditional_list'),
			fieldList = new Element('select', {
				'class' : 'yndform_conditional_logic_field',
				'name': 'conditional_logic[field_id][]'
			});

		// build conditional logic list and data
		window.addEvent('domready', function(){
			fieldData = JSON.parse('<?php echo addslashes(json_encode($fieldData)) ?>');

			<?php foreach ($fieldData as $key => $field): ?>
		   <?php
					$fieldss= $field['label'];
			       $field['label'] =   str_replace("#540"," ",$fieldss);
			?>
			new Element('option', {
				'value': <?php echo $key ?>,
				'text': '<?php echo $field['label'] ?>'
			}).inject(fieldList);
			<?php endforeach; ?>

			// recreate conditional logic fields
			var conditionalLogicEle = $('conditional_logic');

			if (conditionalLogicEle && conditionalLogicEle.get('value') && conditionalLogicEle.get('value') != 'null') {
				conditionalLogicData = JSON.parse(conditionalLogicEle.get('value'));
				var length = conditionalLogicData.field_id.length;
				for (var i=0; i<length; i++) {
					if (fieldData.hasOwnProperty(conditionalLogicData.field_id[i])) {
						yndformSetItem(yndformCreateNewConditionalItem(), {
							field_id: conditionalLogicData.field_id[i],
							compare: conditionalLogicData.compare[i],
							value: conditionalLogicData.value[i]
						});
					}
				}
			}

			// remove the minus button on last item 
			var childrenList = conditionalList.getChildren('.yndform_conditional_logic_item');
			if (childrenList.length == 1) {
				childrenList[0].getChildren('.yndform_conditional_logic_buttons').addClass('yndform_nominus');
			}

			// remove the temporary input field
			if (conditionalLogicEle)
				conditionalLogicEle.destroy();

			// toggle the check-box
			if ($('conditional_enabled-1')) {
				yndformToggleConditionalLogic($('conditional_enabled-1'));
			} else if ($('conditional_enabled')) {
				yndformToggleConditionalLogic($('conditional_enabled'));
			}
		});


		function createOption(){
            var value = document.getElementById('option_text').value;
            $('option_msg').innerHTML = '';
            if(value) {
                var field_id = '<?php echo sprintf("%d", $field_id) ?>';
                var page_id = '<?php echo sprintf("%d", $this->page_id) ?>';
                var url = '<?php echo $this->url(array("action" => "custom-option-create")) ?>';
                if (page_id != null && page_id != '' && page_id != 0 && page_id != '0') {
                    url += '/field_id/' + field_id + '/page_id/' + page_id + '/format/smoothbox';
                } else {
                    url += '/field_id/' + field_id + '/format/smoothbox';
                }
                $('option_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
                var request = new Request.JSON({
                    'url': url,
                    'data': {
                        'format': 'json',
                        'fieldType': "select",
                        'field_id': field_id,
                        'label': document.getElementById('option_text').value
                    },
                    onSuccess: function (responseJSON) {
                        renderChoiceUI();
                    }
                });
                request.send();
            }else{
                $('option_msg').innerHTML = '<h4  style="color: red">Choice is empty, fill it up</h4>'
            }
		}

		function enableEdit(id,value){
			document.getElementById('field_extraoptions_custom_edit_'+id).style.display='block';
			document.getElementById('field_option_select_container_'+id).style.display='none';
			document.getElementById('option_edit_text_'+id).value=value;
		}

		function cancelEdit(id){
			document.getElementById('field_extraoptions_custom_edit_'+id).style.display='none';
			document.getElementById('field_option_select_container_'+id).style.display='block';
			document.getElementById('option_edit_text_'+id).value = '';
		}

		function editOption(id){
			var value = document.getElementById('option_edit_text_'+id).value;
            $('option_msg').innerHTML = '';
			if(value) {
                var page_id = '<?php echo sprintf("%d", $this->page_id) ?>';
                var url = '<?php echo $this->url(array("action" => "custom-option-edit")) ?>';
                $('option_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
                var request = new Request.JSON({
                    'url': url,
                    'data': {
                        'format': 'json',
                        'edit_id': id,
                        'text': document.getElementById('option_edit_text_' + id).value
                    },
                    onSuccess: function (responseJSON) {
                        renderChoiceUI();
                    }
                });
                request.send();
            }else{
                $('option_msg').innerHTML = '<h4  style="color: red">Choice is empty, fill it up</h4>'
            }
		}

		function deleteOption(id){
			var url = '<?php echo $this->url(array("action" => "custom-option-delete")) ?>';
			$('option_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
			var request = new Request.JSON({
				'url' : url,
				'data' : {
					'format' : 'json',
					'delete_id' : id
				},
				onSuccess : function(responseJSON) {
					$$('.field_option_select_' + id).destroy();
					$('option_spinner').innerHTML = '';
				}
			});
			request.send();
		}

		function renderChoiceUI(){
			var url = '<?php echo $this->url(array('action' => 'field-edit')) ?>';
			var page_id = '<?php echo sprintf('%d', $this->page_id) ?>';
			var field_id = '<?php echo sprintf('%d', $field_id) ?>';
			var option_id = '<?php echo sprintf('%d', $option_id) ?>';
			if(page_id != null && page_id != '' && page_id != 0 && page_id != '0'){
				url += '/option_id/' + option_id + '/page_id/' + page_id + '/field_id/' + field_id + '/format/smoothbox';
			} else {
				url += '/option_id/' + option_id + '/field_id/' + field_id + '/format/smoothbox';
			}
			en4.core.request.send(new Request.HTML({
				url: url,
				data: {
					format: 'html'
				},
				onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
					$('option_spinner').innerHTML = '';
					$('hidden_ajax_data').innerHTML = responseHTML;
					$('option_container').innerHTML = $('hidden_ajax_data').getElement('#option_container').innerHTML;
					$('hidden_ajax_data').innerHTML = '';
				}
			}));
		}

	</script>
<?php endif ?>
<style>
	#global_page_sitepage-manageforms-field-create .form-elements,
	#global_page_sitepage-manageforms-field-edit .form-elements{
		margin-bottom: 90px;
	}

	.field_extraoptions_contents_wrapper {
		-moz-border-radius: 5px;
		-webkit-border-radius: 5px;
		border-radius: 5px;
		-moz-border-radius-topleft: 0px;
		-webkit-border-top-left-radius: 0px;
		padding: 4px;
		background-color: #fdfeff;
		border: 1px solid #ccc;
	}
	.field_extraoptions_choices_label {
		display: block;
		overflow: hidden;
		padding: 7px;
		font-size: .8em;
	}
	.field_extraoptions_choices_options {
		float: right;
		overflow: hidden;
		color: #aaa;
		padding: 5px;
		margin-left: 5px;
	}
	.field_extraoptions_custom_add input {
		width: 89%;
	}
	.field_extraoptions_custom_edit input {
		width: 79%;
	}
</style>